/*=========================================================================================
    File Name: form-select2.js
    Description: Select2 is a jQuery-based replacement for select boxes.
    It supports searching, remote data sets, and pagination of results.
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
    Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
var stringMonthGroup;
(function(window, document, $) {
	'use strict';
  // Basic Select2 select
	$("#selectbox-choose-survey").select2({
    dropdownAutoWidth: true,
    placeholder:'Chọn phiếu khảo sát',
    width: '100%'
  });

  $('#select-box-constructions').select2({
    dropdownAutoWidth: true,
    placeholder:'Chọn công trình',
    width: '100%'
  });

  $('#selectbox-list-customer').select2({
    dropdownAutoWidth: true,
    placeholder:'Chọn khách hàng',
    width: '100%'
  });

  $('#permission_list').select2({
    dropdownAutoWidth: true,
    placeholder:'Chọn phiếu khảo sát',
    width: '100%'
  });

  $('#select-survey').select2({
    dropdownAutoWidth: true,
    placeholder:'Chọn phiếu khảo sát',
    width: '100%'
  });

  $('#select-construction').select2({
    dropdownAutoWidth: true,
    placeholder:'Chọn công trình',
    width: '100%'
  });

  $('#select-customer').select2({
    dropdownAutoWidth: true,
    placeholder:'Chọn khách hàng',
    width: '100%'
  });
})(window, document, jQuery);



