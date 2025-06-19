(function(){
    'use strict';

    const IthandechBuyNowAddressManager = function ($) {
        let _districtCacheKey = 'provinceDistrictData';
        let _wardCacheKey = 'wardData';
        
        let _provinceCodeOfDistricts = null;
        let _districtCodeOfWards = null;


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

        let $provinceSelect = $('#address-billing_state');
        let $districtSelect = $('#address-billing_city');
        let $wardSelect = $('#address-billing_ward');

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

        function _loadDistrictsAjax(provinceCode){
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
                    _clearSelect($districtSelect, 'Đang tải...');
                },
                success: function(response) {
                    _loadDataToSelect($districtSelect, response);
                    _provinceCodeOfDistricts = provinceCode;
                    if (typeof ithandechLoadShippingMethods === 'function'){
                        ithandechLoadShippingMethods('VN', _provinceCodeOfDistricts);
                    }
        
                    _districtData[provinceCode] = {
                        loaded: true,
                        data: response
                    };
                    
                    _saveDistrictsToCache(_districtData);
                    $districtSelect.trigger('click');
                },
                error: function() {
                    alert('Không thể tải danh sách quận/huyện. Vui lòng thử lại!');
                }
            });
        }

        function loadDistricts(provinceCode){        
            _clearSelect($districtSelect, 'Chọn quận/huyện/t.x');
            _provinceCodeOfDistricts = null;

            _clearSelect($wardSelect, 'Chọn xã/phường');
            _districtCodeOfWards = null;

            if (!provinceCode) {
                return;
            }
            
            if (_districtData[provinceCode] && _districtData[provinceCode].loaded) {
                    _loadDataToSelect($districtSelect, _districtData[provinceCode].data);
                    _provinceCodeOfDistricts = provinceCode;

                    if (typeof ithandechLoadShippingMethods === 'function'){
                        ithandechLoadShippingMethods('VN', _provinceCodeOfDistricts);
                    }
            } else{
                _loadDistrictsAjax(provinceCode);
            }
        }

        function _loadWardsAjax(districtCode){
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
                    _clearSelect($wardSelect, 'Đang tải...');
                },
                success: function(response) {
                    _loadDataToSelect($wardSelect, response);
        
                    if (!_wardData[_provinceCodeOfDistricts]) {
                        _wardData[_provinceCodeOfDistricts] = {};
                    }
                    _wardData[_provinceCodeOfDistricts][districtCode] = {
                        loaded: true,
                        data: response
                    };
                    _districtCodeOfWards = districtCode;
                    _saveWardsToCache(_wardData);
                },
                error: function() {
                    alert('Không thể tải danh sách xã/phường. Vui lòng thử lại!');
                }
            });
        }

        function _loadWards(districtCode){

            _clearSelect($wardSelect, 'Chọn xã/phường');
            _districtCodeOfWards = null;

            if (!districtCode) {
                return;
            }
        
            if (_wardData[_provinceCodeOfDistricts] &&
                _wardData[_provinceCodeOfDistricts][districtCode] &&
                _wardData[_provinceCodeOfDistricts][districtCode].loaded) {
                _loadDataToSelect($wardSelect, _wardData[_provinceCodeOfDistricts][districtCode].data);
        
                _districtCodeOfWards = districtCode;
            } else {
                _loadWardsAjax(districtCode);
            }
        }

        // --- Sự kiện cho selectbox Tỉnh/Thành ---
        $(document).on('change', '#address-billing_state', function() {
            let provinceCode = $(this).val();
            loadDistricts(provinceCode);
        });

        // --- Sự kiện cho selectbox Quận/Huyện ---
        // 1. Sự kiện click vào selectbox Quận/Huyện:
        $(document).on('click', '#address-billing_city', function() {
            if (_provinceCodeOfDistricts != $provinceSelect.val()){
                $provinceSelect.trigger('change');
            }
        });

        // 2. Sự kiện change của selectbox Quận/Huyện (để load danh sách Xã/Phường):
        $(document).on('change', '#address-billing_city', function() {
            let districtCode = $(this).val();
            _loadWards(districtCode);
        });

        // --- Sự kiện cho selectbox Xã/Phường ---
        $(document).on('click', '#address-billing_ward', function() {
            if (_districtCodeOfWards != $districtSelect.val()){
                $districtSelect.trigger('change');
            }
        });
    }

    const IthandechBuyNowGenderManager = function ($) {
        let _male_titles = $('#billing_hidden_male_titles').val().split(',');
        let _female_titles = $('#billing_hidden_female_titles').val().split(',');
        
        let _current_male_index = parseInt($('#billing_current_male_index').val());
        let _current_female_index = parseInt($('#billing_current_female_index').val());

        let $billFieldsWrapper = null;
        
        function popupGenderVals($container){
            $container.find('#billing_gender_male + label').html('<span></span> ' + _male_titles[_current_male_index]);
            $container.find('#billing_gender_female + label').html('<span></span> ' + _female_titles[_current_female_index]);
        
            // Cập nhật giá trị value của các radio button
            $container.find('#billing_gender_male').val(_male_titles[_current_male_index]);
            $container.find('#billing_gender_female').val(_female_titles[_current_female_index]);
        
            // Lưu lại số thứ tự hiện tại
            $container.find('#billing_current_male_index').val(_current_male_index);
            $container.find('#billing_current_female_index').val(_current_female_index);
        
            let genderSelected = $container.find('input.gender__radio:checked').val();
            $container.find('input[name="billing_first_name"]').val(genderSelected);
        }

        $(document).on('click', '.woocommerce-billing-fields__field-wrapper input.gender__radio', function() {
            if ($billFieldsWrapper == null){
                $billFieldsWrapper = $(this).closest('.woocommerce-billing-fields__field-wrapper');
            }

            let genderSelected = $(this).val();
            $billFieldsWrapper.find('input[name="billing_first_name"]').val(genderSelected);
        });

        // Chức năng đổi xưng hô khi click nút
        $(document).on('click', '#change-title-of-billing-gender-button-next', function(event) {
            event.preventDefault();
            let $this = $(this);

            if ($billFieldsWrapper == null){
                $billFieldsWrapper = $this.closest('.woocommerce-billing-fields__field-wrapper');
            }

            let last_index = _male_titles.length - 1;

            if (_male_titles.length > 1){

                if (_current_male_index < _male_titles.length - 1){

                    if (_current_male_index === 0){
                        $('#change-title-of-billing-gender-button-prevous').show();
                    }

                    // Tăng số thứ tự của các cặp xưng hô
                    _current_male_index = (_current_male_index + 1) % _male_titles.length;
                    _current_female_index = (_current_female_index + 1) % _female_titles.length;

                    if (_current_male_index == last_index){
                        $this.hide();
                    }
                }

            } else {
                _current_male_index = 0;
                _current_female_index = 0;

                $this.hide();
            }

            popupGenderVals($billFieldsWrapper); 
            
        });

        $(document).on('click', '#change-title-of-billing-gender-button-prevous', function(event) {
            event.preventDefault();
            let $this = $(this);

            if ($billFieldsWrapper == null){
                $billFieldsWrapper = $(this).closest('.woocommerce-billing-fields__field-wrapper');
            }

            if (_male_titles.length > 1){

                if (_current_male_index == _male_titles.length - 1){
                    $('#change-title-of-billing-gender-button-next').show();
                }

                if (_current_male_index > 0){
                    // Giảm số thứ tự của các cặp xưng hô
                    _current_male_index = (_current_male_index - 1) % _male_titles.length;
                    _current_female_index = (_current_female_index - 1) % _female_titles.length;
    
                    if (_current_male_index == 0){
                        $this.hide();
                    }
                }

            } else {
                _current_male_index = 0;
                _current_female_index = 0;

                $this.hide();
            }
            
            popupGenderVals($billFieldsWrapper);
        });

        $('#change-title-of-billing-gender-button-next').trigger('click');
        $('#change-title-of-billing-gender-button-prevous').trigger('click');
        // Tìm radio đầu tiên và kích hoạt click
        $('.woocommerce-billing-fields__field-wrapper input.gender__radio:first').trigger('click');
    }

    jQuery(document).ready(function($) {
        IthandechAddressManager = IthandechBuyNowAddressManager;
        IthandechGenderManager = IthandechBuyNowGenderManager;
    });
})();

let IthandechAddressManager = null;
let IthandechGenderManager = null;
