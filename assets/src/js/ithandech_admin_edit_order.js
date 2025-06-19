let provinceDistrictData = null;
let wardData = null;

let billingProvinceOfDistrictList = null;
let shippingProvinceOfDistrictList = null;

let billingDistrictOfWardList = null;
let shippingDistrictOfWardList = null;

let $billingProvinceSelect = null;
let $billingDistrictSelect = null;
let $billingWardSelect = null;

let $shippingProvinceSelect = null;
let $shippingDistrictSelect = null;
let $shippingWardSelect = null;

function _ithandechSaveCache(key, dataToSave){
    localStorage.setItem(key, JSON.stringify(dataToSave));
}

function _ithandechSaveProviceToCache(dataToSave){
    _ithandechSaveCache('provinceDistrictData', dataToSave)
}

function _ithandechSaveWardsToCache(dataToSave){
    _ithandechSaveCache('wardData', dataToSave)
}

function _clearAddressSelect(select, emptyOpt){
    $ = jQuery;
    select.empty().append(
        $('<option></option>').val('').text(emptyOpt)
    );
}

function _addOptionToSelect(select, optVal, optDisplayName){
    $ = jQuery;
    select.append(
        $('<option></option>').val(optVal).text(optDisplayName)
    );
}

function _reloadDataToSelect(select, data){
    $ = jQuery;
    select.empty();

    $.each(data, function(val, disName) {
        _addOptionToSelect(select, val, disName)
    });
}

function _ithandechPopulateDistrictSelect(data, districtSelect, wardSelect){
    _reloadDataToSelect(districtSelect, data);
    // Reset Xã/Phường
    _clearAddressSelect(wardSelect, 'Chọn xã/phường');
}

function _ithandechPopulateWardSelect(data, wardSelect) {
    _reloadDataToSelect(wardSelect, data);
}

function _loadDistrictByProvinceAjax(provinceCode, isShipping, districtSelect, wardSelect){
    $ = jQuery;
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
            _clearAddressSelect(districtSelect, 'Đang tải...');
        },
        success: function(response) {
            _ithandechPopulateDistrictSelect(response, districtSelect, wardSelect);

            provinceDistrictData[provinceCode] = {
                loaded: true,
                data: response
            };
            if (isShipping){
                shippingProvinceOfDistrictList = provinceCode;
            } else {
                billingProvinceOfDistrictList = provinceCode;
            }
            _ithandechSaveProviceToCache(provinceDistrictData);

            districtSelect.trigger('click');
        },
        error: function() {
            alert('Không thể tải danh sách quận/huyện. Vui lòng thử lại!');
        }
    });
}

function _refeshToLoadProvince(districtSelect, wardSelect, isShipping){
    _clearAddressSelect(districtSelect, 'Chọn quận/huyện/t.x');
    _clearAddressSelect(wardSelect, 'Chọn xã/phường');
    
    if (!isShipping){
        billingProvinceOfDistrictList = null;
        billingDistrictOfWardList = null;
    } else{
        shippingProvinceOfDistrictList = null;
        shippingDistrictOfWardList = null;
    }
}

function _refeshToLoadDistrict(wardSelect, isShipping){
    _clearAddressSelect(wardSelect, 'Chọn xã/phường');
    
    if (!isShipping){
        billingDistrictOfWardList = null;
    } else{
        shippingDistrictOfWardList = null;
    }
}

function _loadDistrictsByProvince(provinceCode, isShipping){
    $ = jQuery;

    let districtSelect = null;
    let wardSelect = null;

    if (isShipping){
        // Reset Quận/Huyện & Xã/Phường
        shippingDistrictOfWardList = null;

        districtSelect = $shippingDistrictSelect;
        wardSelect = $shippingWardSelect;
    } else {
        // Reset Quận/Huyện & Xã/Phường
        billingDistrictOfWardList = null;

        districtSelect = $billingDistrictSelect;
        wardSelect = $billingWardSelect;
    }

    if (!provinceCode) {
        _refeshToLoadProvince(districtSelect, wardSelect, isShipping);
        return;
    }
        
    _clearAddressSelect(districtSelect, 'Chọn quận/huyện/t.x');
    _clearAddressSelect(wardSelect, 'Chọn xã/phường');

    if (provinceDistrictData[provinceCode] && provinceDistrictData[provinceCode].loaded) {
            _ithandechPopulateDistrictSelect(provinceDistrictData[provinceCode].data, districtSelect, wardSelect);

        if (!isShipping){
            billingProvinceOfDistrictList = selectedProvince;
        } else {
            shippingProvinceOfDistrictList = selectedProvince;
        }
        
    } else{
        _loadDistrictByProvinceAjax(provinceCode, isShipping, districtSelect, wardSelect);
    }
}

function _loadWardsByDistrictAjax(selectedProvinceCode, districtCode, isShipping, wardSelect){
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
            _clearAddressSelect(wardSelect, 'Đang tải...');
        },
        success: function(response) {
            _ithandechPopulateWardSelect(response, wardSelect);

            if (!wardData[selectedProvinceCode]) {
                wardData[selectedProvinceCode] = {};
            }
            wardData[selectedProvinceCode][districtCode] = {
                loaded: true,
                data: response
            };
            
            if(isShipping){
                shippingDistrictOfWardList = districtCode;
            } else{
                billingDistrictOfWardList = districtCode;
            }
            _ithandechSaveWardsToCache(wardData);

            // wardSelect.trigger('click');
        },
        error: function() {
            alert('Không thể tải danh sách xã/phường. Vui lòng thử lại!');
        }
    });
}

function _loadWardsByDistrict(districtCode, isShipping){
    $ = jQuery;

    let wardSelect = null;
    let provinceCode = null;

    if (isShipping){
        wardSelect = $shippingWardSelect;
        provinceCode = shippingProvinceOfDistrictList;
    } else {
        wardSelect = $billingWardSelect;
        provinceCode = billingProvinceOfDistrictList;
    }

    if (!districtCode) {
        _refeshToLoadDistrict(wardSelect, isShipping);
        return;
    }

    _clearAddressSelect(wardSelect, 'Chọn xã/phường');

    if (wardData[provinceCode] &&
        wardData[provinceCode][districtCode] &&
        wardData[provinceCode][districtCode].loaded) {
        _ithandechPopulateWardSelect(wardData[provinceCode][districtCode].data, wardSelect);

        if(isShipping){
            shippingDistrictOfWardList = districtCode;
        } else{
            billingDistrictOfWardList = districtCode;
        }
    } else {
        _loadWardsByDistrictAjax(provinceCode, districtCode, isShipping, wardSelect);
    }
}

function _isRegexValid(inputText, regex){
    return regex.test(inputText);
}

// Hàm kiểm tra email hợp lệ bằng regex
function isEmailValid(email) {
    // Regex để kiểm tra định dạng email hợp lệ
    var regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return _isRegexValid(email, regex);
}

// Hàm kiểm tra số điện thoại hợp lệ
function isPhoneNumberValid(phoneNumber) {
    // Regex để kiểm tra số điện thoại hợp lệ
    // Ví dụ: Kiểm tra số điện thoại VN (10 chữ số, bắt đầu bằng 0 và có thể theo sau là các chữ số từ 3 đến 9)
    var regex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/g;
    return _isRegexValid(phoneNumber, regex);
}

function _requiredNotEmpty(val, message_selector, message, errors_result){
    $ = jQuery;
    if(!val || val.trim() === ''){
        errors_result.push(message);
        $(message_selector).show();
    }
}

function _requiredField(val, validFunc, message_selector, field_name, errors_result){
    $ = jQuery;
    if(!val || val.trim() === ''){
        message = field_name + ' không được để trống.';
        errors_result.push(message);
        $(message_selector).text(message).show();
    } else {
        if (!validFunc(val)){
            message = field_name + ' không không hợp lệ.';
            errors_result.push(message);
            $(message_selector).text(message).show();
        }
    }
}

function _emptyOrValid(val, validFunc, message_selector, field_name, errors_result){
    if (val !== ''){
        if(!validFunc(val)){
            message = field_name + ' không được để trống.';
            errors_result.push(message);
            $(message_selector).show();
        }
    }
}

const ava__alert = ($) => {
    const $modal = $('#ithandech--order-alert');

    const $alert_html_template = () => $(`
        <div class="ava-alert">
            <div class="danger alert">
                <div class="content">
                    <div class="icon">
                        <svg height="50" viewBox="0 0 512 512" width="50" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#fff" d="M449.07,399.08L278.64,82.58c-12.08-22.44-44.26-22.44-56.35,0L51.87,399.08A32,32,0,0,0,80,446.25H420.89A32,32,0,0,0,449.07,399.08Zm-198.6-1.83a20,20,0,1,1,20-20A20,20,0,0,1,250.47,397.25ZM272.19,196.1l-5.74,122a16,16,0,0,1-32,0l-5.74-121.95v0a21.73,21.73,0,0,1,21.5-22.69h.21a21.74,21.74,0,0,1,21.73,22.7Z"/>
                        </svg>
                    </div>
                    <p class="ava-alert__text"></p>
                </div>
                <button class="close ava-alert__btn">
                    <svg height="18px" viewBox="0 0 512 512" width="18px" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#69727D" d="M437.5,386.6L306.9,256l130.6-130.6c14.1-14.1,14.1-36.8,0-50.9c-14.1-14.1-36.8-14.1-50.9,0L256,205.1L125.4,74.5c-14.1-14.1-36.8-14.1-50.9,0c-14.1,14.1-14.1,36.8,0,50.9L205.1,256L74.5,386.6c-14.1,14.1-14.1,36.8,0,50.9c14.1,14.1,36.8,14.1,50.9,0L256,306.9l130.6,130.6c14.1,14.1,36.8,14.1,50.9,0C451.5,423.4,451.5,400.6,437.5,386.6z"/>
                    </svg>
                </button>
            </div>
        </div>
    `);

    function loadTextToShow(messages = []) {
        // Xóa alert cũ nếu có
        $modal.find('.ava-alert').remove();

        // Tạo lại alert mới từ template
        const $alert_html = $alert_html_template();

        // Chèn nội dung vào thẻ p
        const $text_container = $alert_html.find('.ava-alert__text');
        messages.forEach(msg => {
            $text_container.append($('<span></span>').text(msg)).append('<br/>');
        });

        // Thêm alert vào modal
        $modal.append($alert_html).removeClass('ithan__display-none');
    }

    // Gắn sự kiện click để xóa alert khi đóng
    $modal.on('click', '.ava-alert__btn', function () {
        $(this).closest('.ava-alert').remove();
        $modal.addClass('ithan__display-none');
    });

    return { loadTextToShow };
};


jQuery(document).ready(function($) {
    provinceDistrictData = JSON.parse(localStorage.getItem('provinceDistrictData')) || {};
    wardData = JSON.parse(localStorage.getItem('wardData')) || {};

    window.addEventListener('storage', function(e) {
        if (e.key === 'provinceDistrictData') {
            provinceDistrictData = JSON.parse(e.newValue) || {};
        }
        if (e.key === 'wardData') {
            wardData = JSON.parse(e.newValue) || {};
        }
    });

    $billingProvinceSelect = $('#_billing_province_code');
    $billingDistrictSelect = $('#_billing_district_code');
    $billingWardSelect = $('#_billing_ward_code');

    $shippingProvinceSelect = $('#_shipping_province_code');
    $shippingDistrictSelect = $('#_shipping_district_code');
    $shippingWardSelect = $('#_shipping_ward_code');

    // lấy giá trị có sẵn lần đầu load lên
    billingProvinceOfDistrictList = $billingProvinceSelect.val();
    shippingProvinceOfDistrictList = $shippingProvinceSelect.val();

    billingDistrictOfWardList = $billingDistrictSelect.val();
    shippingDistrictOfWardList = $shippingDistrictSelect.val();

    /****************************************
    * 1) SỰ KIỆN TỈNH/THÀNH
    ****************************************/

    $billingProvinceSelect.on('change', function() {
        let proCode = $(this).val();
        $('#billing_state').val(proCode);

        _loadDistrictsByProvince(proCode, false);
    });

    $shippingProvinceSelect.on('change', function(){
        let proCode = $(this).val();
        $('#shipping_state').val(proCode);

        _loadDistrictsByProvince(proCode, true);

        // $shippingDistrictSelect.trigger('change');
    });
    
    /****************************************
    * 2) SỰ KIỆN QUẬN/HUYỆN
    ****************************************/
    $billingDistrictSelect.on('change', function() {
        let disCode = $(this).val();

        _loadWardsByDistrict(disCode, false);
    });

    $shippingDistrictSelect.on('change', function() {
        let disCode = $(this).val();
        
        _loadWardsByDistrict(disCode, true);
    });

    /****************************************
    * 3) SỰ KIỆN XÃ/PHƯỜNG
    ****************************************/

    $billingWardSelect.on('click', function() {
        if (billingDistrictOfWardList == null){
            $billingDistrictSelect.trigger('change');
        }
    });

    $shippingWardSelect.on('click', function() {
        if (shippingDistrictOfWardList == null){
            $shippingDistrictSelect.trigger('change');
        }
    });

});

jQuery(document).ready(function($){
    
    const alertBox = ava__alert($); // gọi hàm trước

    $('p.form-field input.ithandech_required + span.description, p.form-field select.ithandech_required + span.description').hide();

    $('p.form-field input, p.form-field select').on('focus', function () {
        // Kiểm tra nếu phần tử cha <p.form-field> chứa <span class="description">
        if ($(this).closest('p.form-field').has('span.description').length > 0) {
            $(this).closest('p.form-field').find('span.description').hide();
        }
    });
    

    $('#order').on('submit', function(e){
        var errors = [];

        _requiredNotEmpty($('input[name="_billing_last_name"]').val(), 'p.form-field input[name="_billing_last_name"] + span.description', 'Họ tên người thanh toán không được để trống.', errors);
        _requiredField($('input[name="_billing_phone"]').val(), isPhoneNumberValid, 'p.form-field input[name="_billing_phone"] + span.description', 'Số Điện Thoại thanh toán', errors);
        _emptyOrValid($('input[name="_billing_email"]').val(), isEmailValid, 'p.form-field input[name="_billing_email"] + span.description', 'Email', errors);   

        _requiredNotEmpty($('select[name="_billing_province_code"]').val(), 'p.form-field select[name="_billing_province_code"] + span.description', 'Tỉnh/Thành thanh toán là bắt buộc chọn.', errors);
        _requiredNotEmpty($('select[name="_billing_district_code"]').val(), 'p.form-field select[name="_billing_district_code"] + span.description', 'Quận/Huyện thanh toán là bắt buộc chọn.', errors);
        _requiredNotEmpty($('select[name="_billing_ward_code"]').val(), 'p.form-field select[name="_billing_ward_code"] + span.description', 'Xã/Phường thanh toán là bắt buộc chọn.', errors);

        _requiredNotEmpty($('input[name="_billing_address_2"]').val(), 'p.form-field input[name="_billing_address_2"] + span.description', 'Địa chỉ không được để trống.', errors);

        
        _requiredNotEmpty($('input[name="_shipping_last_name"]').val(), 'p.form-field input[name="_shipping_last_name"] + span.description', 'Họ tên người nhận hàng không được để trống.', errors);
        _requiredField($('input[name="_shipping_phone"]').val(), isPhoneNumberValid, 'p.form-field input[name="_shipping_phone"] + span.description', 'Số Điện Thoại nhận hàng', errors);

        _requiredNotEmpty($('select[name="_shipping_province_code"]').val(), 'p.form-field select[name="_shipping_province_code"] + span.description', 'Tỉnh/Thành giao hàng là bắt buộc chọn.', errors);
        _requiredNotEmpty($('select[name="_shipping_district_code"]').val(), 'p.form-field select[name="_shipping_district_code"] + span.description', 'Quận/Huyện giao hàng là bắt buộc chọn.', errors);
        _requiredNotEmpty($('select[name="_shipping_ward_code"]').val(), 'p.form-field select[name="_shipping_ward_code"] + span.description', 'Xã/Phường giao hàng là bắt buộc chọn.', errors);

        if(errors.length > 0){
            e.preventDefault(); // Chặn submit nếu có lỗi
            // alert(errors.join("\n"));

            alertBox.loadTextToShow(errors); // sau đó mới gọi method
        }
    });
});





