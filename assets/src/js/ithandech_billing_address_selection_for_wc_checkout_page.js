(function (){
    'use strict';

    const IthandechWCAddressManager = function ($) {
        let _districtCacheKey = 'provinceDistrictData';
        let _wardCacheKey = 'wardData';
        
        let _billProvinceCodeOfDistricts = null;
        let _billDistrictCodeOfWards = null;
        let _shipProvinceCodeOfDistricts = null;
        let _shipDistrictCodeOfWards = null;

        let $billProvinceSelect = $('#address-billing_state');
        let $billDistrictSelect = $('#address-billing_city');
        let $billWardSelect = $('#address-billing_ward');

        let $shipProvinceSelect = $('#address-shipping_state');
        let $shipDistrictSelect = $('#address-shipping_city');
        let $shipWardSelect = $('#address-shipping_ward');

        let _districtData = JSON.parse(localStorage.getItem(_districtCacheKey)) || {};;
        let _wardData = JSON.parse(localStorage.getItem(_wardCacheKey)) || {};

        // Lắng nghe sự kiện storage để cập nhật cache từ các tab khác
        window.addEventListener('storage', function(e) {
            if (e.key === _districtCacheKey) {
                _districtData = JSON.parse(e.newValue) || {};
            }
            if (e.key === _wardCacheKey) {
                _wardData = JSON.parse(e.newValue) || {};
            }
        });

        function _saveToCache(key, dataToSave){
            localStorage.setItem(key, JSON.stringify(dataToSave));
        }
        
        function _saveDistrictsToCache(dataToSave){
            _saveToCache(_districtCacheKey, dataToSave)
        }
        
        function _saveWardsToCache(dataToSave){
            _saveToCache(_wardCacheKey, dataToSave)
        }

        function _clearSelect(select, emptyOptDisplayName){
            select.empty().append(
                $('<option></option>').val('').text(emptyOptDisplayName)
            );
        }
        
        function _addOptionToSelect(select, optVal, optDisplayName){
            select.append(
                $('<option></option>').val(optVal).text(optDisplayName)
            );
        }
        
        function _loadDataToSelect(select, data){
            select.empty();
            $.each(data, function(val, disName) {
                _addOptionToSelect(select, val, disName)
            });
        }

        function _loadDistrictsAjax(provinceCode, districtSelect){
            // Gọi Ajax hoặc lấy từ cache
            $.ajax({
                url: ithandech_billing_address_ajax_obj.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'ithandech_load_districts',
                    nonce: ithandech_billing_address_ajax_obj.nonce,
                    province_code: provinceCode
                },
                beforeSend: function() {
                    _clearSelect(districtSelect, 'Đang tải...');
                },
                success: function(response) {
                    _loadDataToSelect(districtSelect, response);
                    if (districtSelect.is($billDistrictSelect)){
                        _billProvinceCodeOfDistricts = provinceCode;
                        _clearSelect($billWardSelect, 'Chọn xã/phường');
                    } else{
                        _shipProvinceCodeOfDistricts = provinceCode;
                        _clearSelect($shipWardSelect, 'Chọn xã/phường');
                    }
    
                    _districtData[provinceCode] = {
                        loaded: true,
                        data: response
                    };
                    _saveDistrictsToCache(_districtData);
                },
                error: function() {
                    alert('Không thể tải danh sách quận/huyện. Vui lòng thử lại!');
                }
            });
        }

        function _loadDistricts(provinceCode, districtSelect){    
            if (!provinceCode) {
                return;
            }
            
           _clearSelect(districtSelect, 'Chọn quận/huyện/t.x');

           if (districtSelect.is($billDistrictSelect)){
                _clearSelect($billWardSelect, 'Chọn xã/phường');
            }else{
                _clearSelect($shipWardSelect, 'Chọn xã/phường');
            }

            if (_districtData[provinceCode] && _districtData[provinceCode].loaded) {
                _loadDataToSelect(districtSelect, _districtData[provinceCode].data);
                if (districtSelect.is($billDistrictSelect)){
                    _billProvinceCodeOfDistricts = provinceCode;
                }else{
                    _shipProvinceCodeOfDistricts = provinceCode;
                }
            } else{
                _loadDistrictsAjax(provinceCode, districtSelect);
            }
        }

        function _loadWardsAjax(districtCode, wardSelect){
            $.ajax({
                url: ithandech_billing_address_ajax_obj.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'ithandech_load_wards',
                    district_code: districtCode,
                    nonce: ithandech_billing_address_ajax_obj.nonce
                },
                beforeSend: function() {
                    // Hiển thị "Đang tải..." trên select Xã/Phường
                    _clearSelect(wardSelect, 'Đang tải...');
                    wardSelect.select2('close');
                },
                success: function(response) {
                    _loadDataToSelect(wardSelect, response);
                    let provinceCodeOfDistricts = null;
                    if (wardSelect.is($billWardSelect)){
                        provinceCodeOfDistricts = _billProvinceCodeOfDistricts;
                        _billDistrictCodeOfWards = districtCode;
                    } else {
                        provinceCodeOfDistricts = _shipProvinceCodeOfDistricts;
                        _shipDistrictCodeOfWards = districtCode;
                    }
        
                    if (!_wardData[provinceCodeOfDistricts]) {
                        _wardData[provinceCodeOfDistricts] = {};
                    }
                    _wardData[provinceCodeOfDistricts][districtCode] = {
                        loaded: true,
                        data: response
                    };
                   
                    _saveWardsToCache(_wardData);
                },
                error: function() {
                    alert('Không thể tải danh sách xã/phường. Vui lòng thử lại!');
                }
            });
        }

        function _loadWards(districtCode, wardSelect){
            if (!districtCode) {
                return;
            }

            _clearSelect(wardSelect, 'Chọn xã/phường');
            let provinceCodeOfDistricts = null;
            if (wardSelect.is($billWardSelect)){
                provinceCodeOfDistricts = _billProvinceCodeOfDistricts;
                _billDistrictCodeOfWards = districtCode;
            } else {
                provinceCodeOfDistricts = _shipProvinceCodeOfDistricts;
                _shipDistrictCodeOfWards = districtCode;
            }
        
            if (_wardData[provinceCodeOfDistricts] &&
                _wardData[provinceCodeOfDistricts][districtCode] &&
                _wardData[provinceCodeOfDistricts][districtCode].loaded) {
                _loadDataToSelect(wardSelect, _wardData[provinceCodeOfDistricts][districtCode].data);
            } else {
                _loadWardsAjax(districtCode, wardSelect);
            }
        }

        /****************************************
        * 2) SỰ KIỆN TỈNH/THÀNH
        ****************************************/

        $shipProvinceSelect.on('change', function(){
            let proCode = $(this).val();
            $('#shipping_state').val(proCode);

            _loadDistricts($(this).val(), $shipDistrictSelect);

            // Kích hoạt cập nhật lại checkout
            $('body').trigger('update_checkout');
        });

        $billProvinceSelect.on('change', function() {
            let proCode = $(this).val();
            $('#billing_state').val(proCode);

            _loadDistricts(proCode, $billDistrictSelect);

            // Kích hoạt cập nhật lại checkout
            $('body').trigger('update_checkout');
        });

        /****************************************
        * 3) SỰ KIỆN QUẬN/HUYỆN
        ****************************************/
        // Click -> Kiểm tra cache quận/huyện (logic cũ)
        $billDistrictSelect.on('select2:open', function() {
            $billProvinceSelect.trigger('click');
        });

        $billDistrictSelect.on('click', function() {
            if (_billProvinceCodeOfDistricts != $billProvinceSelect.val()){
                $billProvinceSelect.trigger('change');
            }
        });

        $shipDistrictSelect.on('select2:open', function() {
            $shipProvinceSelect.trigger('change');
        });

        $shipDistrictSelect.on('click', function() {
            if (_shipProvinceCodeOfDistricts != $shipProvinceSelect.val()){
                $shipProvinceSelect.trigger('change');
            }
        });

        $billDistrictSelect.on('change', function() {
            let districtCode = $(this).val();
            _loadWards(districtCode, $billWardSelect);
        });
    
        $shipDistrictSelect.on('change', function() {
            let districtCode = $(this).val();
            _loadWards(districtCode, $shipWardSelect);
        });

        /****************************************
        * 4) SỰ KIỆN XÃ/PHƯỜNG
        ****************************************/
        $billWardSelect.on('click', function() {
            if (_billDistrictCodeOfWards != $billDistrictSelect.val()){
                $billDistrictSelect.trigger('change');
            }
        });

        $billWardSelect.on('select2:open', function() {
            $billWardSelect.trigger('click');
        });

        $shipWardSelect.on('click', function() {
            if (_shipDistrictCodeOfWards != $shipDistrictSelect.val()){
                $shipDistrictSelect.trigger('change');
            }
        });

        $shipWardSelect.on('select2:open', function() {
            $shipWardSelect.trigger('click');
        });

        /****************************************
        * 5) TỰ ĐỘNG LOAD QUẬN/HUYỆN CHO TỈNH NẾU ĐÃ CÓ GIÁ TRỊ
        ****************************************/
        let defaultProvinceCode = $billProvinceSelect.val();
        if (defaultProvinceCode) {
            $billProvinceSelect.trigger('change');
        }

        // Khi focus vào một input hiển thị label
        let inputs = $('.woocommerce-billing-fields__field-wrapper input');
        inputs.on('focus', function() {
            // $(this).closest('.wc__form-field').find('label').show();
            $(this).closest('.wc__form-field').find('label').removeClass('display__none');
        });

        // Khi mất focus khỏi input ẩn label
        inputs.on('blur', function() {
            // $(this).closest('.wc__form-field').find('label').hide();
            $(this).closest('.wc__form-field').find('label').addClass('display__none');
        });
    }

    const IthandechWCGenderManager = function (
        $, 
        $maleTitlesData,
        $femaleTitlesData, 
        $nextButton,
        $prevousButton,
        currentMaleIndexSelector,
        currentFemaleIndexSelector,
        maleRadioSelector,
        femalRadioSelector,
        bindValueSeloctor,
        containerSelector,
    ){
        let male_titles = $maleTitlesData.val().split(',');
        let female_titles = $femaleTitlesData.val().split(',');

        let current_male_index = parseInt($(currentMaleIndexSelector).val());;
        let current_female_index = parseInt($(currentFemaleIndexSelector).val());

        let $wrapperContainer = null;

        function _updateGenderVals($container){
            $container.find(maleRadioSelector + ' + label').html('<span></span> ' + male_titles[current_male_index]);
            $container.find(femalRadioSelector + ' + label').html('<span></span> ' + female_titles[current_female_index]);
        
            // Cập nhật giá trị value của các radio button
            $container.find(maleRadioSelector).val(male_titles[current_male_index]);
            $container.find(femalRadioSelector).val(female_titles[current_female_index]);
        
            // Lưu lại số thứ tự hiện tại
            $container.find(currentMaleIndexSelector).val(current_male_index);
            $container.find(currentFemaleIndexSelector).val(current_female_index);
        
            let genderSelected = $container.find('input.gender__radio:checked').val();
            $container.find(bindValueSeloctor).val(genderSelected);
        }

        function _getContainer($childField){
            $wrapperContainer = $childField.closest(containerSelector);
        }

        function bindEvents(){
            let $genderRadios = $(containerSelector + ' input.gender__radio');
            $genderRadios.on('click', function() {
                if ($wrapperContainer == null){
                    _getContainer($(this));
                }
        
                let genderSelected = $(this).val();
                $wrapperContainer.find(bindValueSeloctor).val(genderSelected);
            });

            // Chức năng đổi xưng hô khi click nút
            $nextButton.on('click', function(event) {
                event.preventDefault();
                if ($wrapperContainer == null){
                    _getContainer($(this));
                }
                let last_index = male_titles.length - 1; 
                if (male_titles.length > 1){
                    if (current_male_index < last_index){
                        if (current_male_index === 0){
                            $prevousButton.show();
                        }

                        // Tăng số thứ tự của các cặp xưng hô
                        current_male_index = (current_male_index + 1) % male_titles.length;
                        current_female_index = (current_female_index + 1) % female_titles.length;

                        if (current_male_index == last_index){
                            $nextButton.hide();
                        }
                    }
                } else {
                    current_male_index = 0;
                    current_female_index = 0;

                    $nextButton.hide();
                }

                _updateGenderVals($wrapperContainer);
                
            });

            $prevousButton.on('click', function(event) {
                event.preventDefault();
                if ($wrapperContainer == null){
                    _getContainer($(this));
                }
        
                if (male_titles.length > 1){
                    if (current_male_index > 0){

                        if (current_male_index == male_titles.length - 1){
                            $nextButton.show();
                        }

                        // Giảm số thứ tự của các cặp xưng hô
                        current_male_index = (current_male_index - 1) % male_titles.length;
                        current_female_index = (current_female_index - 1) % female_titles.length;

                        if (current_male_index == 0){
                            $prevousButton.hide();
                        }
                    }
                } else {
                    current_male_index = 0;
                    current_female_index = 0;

                    $prevousButton.hide();
                }

                _updateGenderVals($wrapperContainer);
                
            });

            $nextButton.trigger('click');
            $prevousButton.trigger('click');
            
            $genderRadios.first().trigger('click');
        }

        return {
            bindEvents: bindEvents
        }
    }

    const IthandechWCValidateManager = function ($){

        function _isRegexValid(inputText, regex){
            return regex.test(inputText);
        }
        
        // Hàm kiểm tra email hợp lệ bằng regex
        function _isEmailValid(email) {
            // Regex để kiểm tra định dạng email hợp lệ
            var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return _isRegexValid(email, regex);
        }
        
        // Hàm kiểm tra số điện thoại hợp lệ
        function _isPhoneNumberValid(phoneNumber) {
            // Regex để kiểm tra số điện thoại hợp lệ
            // Ví dụ: Kiểm tra số điện thoại VN (10 chữ số, bắt đầu bằng 0 và có thể theo sau là các chữ số từ 3 đến 9)
            var regex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/g;
            return _isRegexValid(phoneNumber, regex);
        }

        function _showErrorTooltip($field, message = " là bắt buộc."){
            var $container = $field.closest('.input-container');
            var $errorSpan = $container.find('span.error');

            if ($errorSpan.length === 0){
                $container.append('<span class="error"></span>');
                $errorSpan = $container.find('span.error');
            }

            var label = $container.closest('.wc__form-field').find('label').text() || '';
            $errorSpan.text(label + message);
            $field.addClass('woocommerce-invalid');
            $errorSpan.show();
        }

        function _hideErrorTooltip($field){
            var $container = $field.closest('.input-container');
            var $errorSpan = $container.find('span.error');
            $errorSpan.hide();
        }

        function bindEvents(){
            // Ẩn tooltip cũ
            $('span.error').hide();

            /****************************************
            *  ẨN TOOLTIP KHI FOCUS / CHANGE
            ****************************************/
            $('#billing_last_name, #address-billing_state, #address-billing_city, #address-billing_ward, #billing_address_2, #billing_phone, #billing_email')
            .on('focus change', function() {
                _hideErrorTooltip($(this));
            });

            /****************************************
            *  KHAI BÁO LẮNG NGHE SỰ KIỆN THÊM THÔNG BÁO LỖI
            ****************************************/
            // Chọn phần tử cha để theo dõi (thêm phần thông báo .woocommerce-NoticeGroup)
            const targetNode = document.querySelector('.woocommerce');

            // Cấu hình MutationObserver để theo dõi các thay đổi trong DOM
            const config = { childList: true, subtree: true };

            const swipeHandler = new SwipeHandler();
            const toastsFactory = new ToastsFactory(swipeHandler, $);

            // Callback sẽ được gọi khi có sự thay đổi trong DOM
            const callback = function(mutationsList, observer) {
                let allowed = false;
                // Lặp qua tất cả các mutations được phát hiện
                for (let mutation of mutationsList) {
                    if (mutation.type === 'childList') {
                        allowed = true;
                        break;
                    }
                }

                if (allowed){
                    // Kiểm tra nếu có phần tử .woocommerce-error được thêm vào
                    const errorMessages = document.querySelectorAll('.toast__panel-container-hold-toasts');
                    if (errorMessages.length > 0) {
                        toastsFactory.findAllNotificationToCreateToast($);
                    }
                }
            };

            // Tạo MutationObserver và bắt đầu theo dõi
            const observer = new MutationObserver(callback);
            observer.observe(targetNode, config);

            // Sau khi hoàn thành, bạn có thể ngừng theo dõi sự thay đổi (nếu cần)
            // observer.disconnect();

            /****************************************
            *  VALIDATE SỰ KIỆN ĐẶT HÀNG
            ****************************************/
            $('form.checkout').on('checkout_place_order', function(e) {

                var formValid = true;
                var firstErrorField = null;  // Biến để lưu phần tử có lỗi
                let toDifferentAddress = $('input#ship-to-different-address-checkbox').is(':checked');

                function _validFormRequired($requiredFields){
                    $requiredFields.each(function() {
                        var $field = $(this);
                        var value = $field.val().trim();
    
                        if (value === ""){
                            formValid = false;
                            if (!firstErrorField) {
                                firstErrorField = $field;
                            }
                            _showErrorTooltip($field, " là bắt buộc.");
                        } else {
                            $field.removeClass('woocommerce-invalid');
                        }
                    });
                }
                if (! toDifferentAddress){
                    _validFormRequired($('.woocommerce-checkout .woocommerce-billing-fields__field-wrapper input[aria-required="true"], .woocommerce-checkout .woocommerce-billing-fields__field-wrapper select[aria-required="true"]'));
                } else {
                    _validFormRequired($('.woocommerce-checkout input[aria-required="true"], .woocommerce-checkout select[aria-required="true"]'));
                }

                function _validFormPhonenumber($phoneField){
                    var value = $phoneField.val().trim();

                    if ( value !== "" ){
                        if (! _isPhoneNumberValid(value)){
                            formValid = false;
                            if (!firstErrorField) {
                                firstErrorField = $phoneField;
                            }
                            _showErrorTooltip($phoneField, " không hợp lệ.");
                        } else {
                            $phoneField.removeClass('woocommerce-invalid');
                        }
                    } 
                }

                _validFormPhonenumber($('.woocommerce-checkout input[name="billing_phone"]'));
                let $shippingPhone = $('.woocommerce-checkout input[name="shipping_phone"]');
                if (toDifferentAddress && ($shippingPhone.length > 0)){
                    _validFormPhonenumber($shippingPhone);
                }

                function _validFormEmail($emailField){
                    var value = $emailField.val().trim();

                    if (value !== ""){
                        if (! _isEmailValid(value)){
                            formValid = false;
                            if (!firstErrorField) {
                                firstErrorField = $emailField;
                            }
                            _showErrorTooltip($emailField, " không hợp lệ.");
                        } else {
                            $emailField.removeClass('woocommerce-invalid');
                        }
                    } 
                }

                _validFormEmail($('.woocommerce-checkout input[name="billing_email"]'));
                let $shippingEmail = $('.woocommerce-checkout input[name="shipping_email"]');
                if (toDifferentAddress && $shippingEmail.length > 0){
                    _validFormEmail($shippingEmail);
                }

                // Nếu form không hợp lệ, ngừng submit
                if (!formValid) {
                    return false; // Ngừng submit nếu có lỗi
                }

                return formValid; // CHO phép submit hoặc không
              
            });
        }

        return {
            bind: bindEvents
        }
    }


    jQuery(document).ready(function($) {
        IthandechWCAddressManager($);

        let gender_on = $('#billing_hidden_male_titles');

        if (gender_on.length > 0){
            IthandechWCGenderManager(
                $, 
                $('#billing_hidden_male_titles'), 
                $('#billing_hidden_female_titles'),
                $('#change-title-of-billing-gender-button-next'),
                $('#change-title-of-billing-gender-button-prevous'),
                '#billing_current_male_index',
                '#billing_current_female_index',
                '#billing_gender_male',
                '#billing_gender_female',
                'input[name="billing_first_name"]',
                '.woocommerce-billing-fields__field-wrapper'
            ).bindEvents();

            IthandechWCGenderManager(
                $, 
                $('#shipping_hidden_male_titles'), 
                $('#shipping_hidden_female_titles'),
                $('#change-title-of-shipping-gender-button-next'),
                $('#change-title-of-shipping-gender-button-prevous'),
                '#shipping_current_male_index',
                '#shipping_current_female_index',
                '#shipping_gender_male',
                '#shipping_gender_female',
                'input[name="shipping_first_name"]',
                '.woocommerce-shipping-fields__field-wrapper'
            ).bindEvents();
        }

        IthandechWCValidateManager($).bind();

        // Theme
        // $('#main').addClass(
        //     ithandech_billing_address_ajax_obj.theme
        // );
    });
})();

jQuery(document).ready(function($) {    
   
   /****************************************
    * 5) SỰ KIỆN HIỂN THỊ TOOLTIP LỖI (checkout_error)
    ****************************************/
   $(document.body).on('checkout_error', function() {
       let $noticeGroup = $('.woocommerce-NoticeGroup-checkout .woocommerce-error.message-wrapper');
       if ($noticeGroup.length) {
           // Ẩn mọi tooltip cũ
           $('span.error').hide();

           let errorFields = [];
           $noticeGroup.find('li[data-id]').each(function(){
               let fieldId = $(this).data('id');
               let $field = $('[name="' + fieldId + '"]');
               if ($field.length) {
                   $field.closest('.input-container').find('span.error').show();
                   errorFields.push($field);
               }
           });

           if (errorFields.length > 0) {
               $('html, body').animate(
                   { scrollTop: errorFields[0].offset().top - 50 },
                   400,
                   function(){
                       errorFields[0].focus();
                   }
               );
           }
       }
   });

   const $couponCheckbox = $('#have-coupon-code-checkbox');
   $couponCheckbox.prop('checked', false);
   // Bắt sự kiện khi checkbox được click hoặc unclick
   $couponCheckbox.on('change', function() {
        // Khi trạng thái của checkbox thay đổi, gọi sự kiện click của thẻ a.showcoupon
        $('.showcoupon').click();
    });

    $('button[name="apply_coupon"]').on('click', function (){
        $couponCheckbox.prop('checked', false);
    });         

}); // End of jQuery(document).ready

// SWIPE
class SwipeHandler {
    getSwipeDirection({ touchstartX, touchstartY, touchendX, touchendY }) {
        const delx = touchendX - touchstartX;
        const dely = touchendY - touchstartY;

        if (Math.abs(delx) > Math.abs(dely)) {
            return delx > 0 ? 'right' : 'left';
        }
        if (Math.abs(delx) < Math.abs(dely)) {
            return dely > 0 ? 'down' : 'up';
        }

        return 'tap';
    }
}

// SVG Icons
const svgIcons = [
    {
        name: 'x-lg',
        svg: `
        <svg xmlns='http://www.w3.org/2000/svg' class='t-close' width='16' height='16' fill='currentColor' viewBox='0 0 16 16'>
            <path d='M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z'/>
        </svg>
        `,
    },
];

class ToastsFactory {
    constructor(swipeHandler, $) {
        this.toastMessageCount = 0;
        this.swipeHandler = swipeHandler;
        this.createToastsFromNotifications($);
    }

    createToastsFromNotifications($) {
        // Tạo wrapper và container cho toasts
        let toastsWrapper = document.createElement('div');
        toastsWrapper.classList.add('toast__panel-wrapper');
        document.body.appendChild(toastsWrapper);

        let toastsContainer = document.createElement('div');
        toastsContainer.classList.add('toast__panel-container');
        toastsWrapper.appendChild(toastsContainer);

        this.toastsContainer = $('.toast__panel-container');
    }

    findAllNotificationToCreateToast($) {
        this.toastsContainer.empty();

        let $allNotificaitons = $('.toast__panel-container-hold-toasts');
        $allNotificaitons.each((idx, container) => {
            // Dùng $(container) để đảm bảo đối tượng jQuery
            $(container).find('.notification').each((index, toastElement) => {
                const $toast = $(toastElement);

                // Tạo bản copy HTML của .notification
                const $toastClone = $toast.clone();
                // Thêm bản copy vào this.toastsContainer của ToastsFactory
                this.toastsContainer.append($toastClone);

                const dataset = $toastClone.data(); // Lấy data attributes bằng jQuery

                const config = {
                    type: dataset.type ? dataset.type : 'error', // Nếu không có type, mặc định là 'error'
                    toastContainer: $toastClone[0], // Lấy DOM element gốc để tương thích
                    duration: dataset.duration ? parseInt(dataset.duration, 10) : undefined
                };

                // Gọi hàm createToast theo logic ban đầu (ví dụ hiển thị thông báo)
                this.createToast(config, $);
                this.toastMessageCount++;
            });
        });

        $allNotificaitons.remove();

        this.toastsContainer.css('transform', 'translateX(0)');
    }

    createToast({ toastContainer, duration }, $) {
        const toast = this.createToastContainer('error', toastContainer, $); // Assuming 'error' type here

        this.addCloseButtonEvent(toast, toastContainer);

        const progressBar = this.getProgressBar(duration, toastContainer);

        this.observeSwipe(toast, 'right', $);

        if (!progressBar) return;

        progressBar.onanimationend = () => this.removeToast(toast);
    }

    createToastContainer(type, toastContainer, $) {
        const $toast = $(toastContainer);
        $toast.addClass('toast ' + type + ' active');
        
        return $toast[0]; // Convert back to DOM element
    }

    addToastElement(toast, className, content) {
        const element = document.createElement('div');
        element.classList.add(className);
        element.innerHTML = content;
        toast.appendChild(element);
        return element;
    }

    addCloseButtonEvent(toast, toastContainer) {
        const closeButton = toastContainer.querySelector('.icon-close.t-close'); // Dùng DOM thuần
        closeButton.onclick = () => {
            this.removeToast(toast);
        }
    }

    getProgressBar(duration, toastContainer) {
        if (duration === 0) return;

        const progressBar = toastContainer.querySelector('.t-progress-bar'); // Dùng DOM thuần
        duration && progressBar.style.setProperty('--toast-duration', `${duration}ms`);
        return progressBar;
    }

    removeToast(toast) {
        toast.classList.remove('active');
        this.toastMessageCount--;
        if (this.toastMessageCount === 0){
            this.toastsContainer.css('transform', 'translateX(200%)');
        }

        toast.onanimationend = (evt) => {
            evt.target === toast && toast.remove();
        };
    }

    observeSwipe(toast, direction, $) {
        let touchstartX = 0, touchstartY = 0, touchendX = 0, touchendY = 0;

        $(toast).on('touchstart', (event) => {
            window.document.body.style.overflow = 'hidden';
            touchstartX = event.changedTouches[0].screenX;
            touchstartY = event.changedTouches[0].screenY;
        });

        $(toast).on('touchend', (event) => {
            window.document.body.style.overflow = 'unset';
            touchendX = event.changedTouches[0].screenX;
            touchendY = event.changedTouches[0].screenY;
            const swipeConfig = { touchstartX, touchstartY, touchendX, touchendY };

            if (this.swipeHandler.getSwipeDirection(swipeConfig) === direction) {
                this.removeToast(toast);
            }
        });
    }
}


