var dataTableSurveyRecords;
$(document).ready(function () {
    var valueSurveyId = $('#selectbox-choose-survey').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var startDay = $('.datepicker1').val();
    var endDay = $('.datepicker2').val();
    loadDataTableSurveyRecords(valueSurveyId, startDay, endDay);
});
function loadDataTableSurveyRecords(valueSurveyId, startDay, endDay) {
    var arrSelectBoxSurveyValue = [];
    for (var i = 0; i < valueSurveyId.length; i++) {
        arrSelectBoxSurveyValue.push(valueSurveyId[i]);
    }
    if (valueSurveyId == "") {
        alert('Dữ liệu rỗng');
        dataTableSurveyRecords = $('#datatable-survey-records').DataTable();
    }
    else {
        dataTableSurveyRecords = $('#datatable-survey-records').DataTable({
            "maxLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "processing": true,
            "serverSide": true,
            "bAutoWidth": false,
            "columnDefs": [
                { "width": "5%", "targets": 0 },
                { "width": "15%", "targets": 1 },
                { "width": "15%", "targets": 2 },
                { "width": "15%", "targets": 3 },
                { "width": "10%", "targets": 4 },
                { "width": "10%", "targets": 5 },
                { "width": "10%", "targets": 6 },
            ],
            ajax: {
                url: "/surveys/users/survey/records",
                type: 'get',
                data: {
                    surveyId: arrSelectBoxSurveyValue,
                    startDay: startDay,
                    endDay: endDay
                }
            },
            columns: [
                { data: 'checkbox', orderable: false, searchable: false, class: "dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled filter-disable" },
                { data: 'customer.name', name: 'customer.name' },
                { data: 'construction.name', name: 'construction.name' },
                { data: 'construction.address', name: 'construction.address' },
                { data: 'employee.hovaten', name: 'user.name' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status' },
            ]
        });
    }
}
$('#home-tab-fill,#button-filter-survey-records').click(function () {
    var selectboxChooseSurvey = $('#selectbox-choose-survey').val();
    var startDay = $('.datepicker1').val();
    var endDay = $('.datepicker2').val();
    if(startDay.length == 0){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Bạn chưa chọn ngày bắt đầu'
          });
          return false;
    }else if(endDay.length == 0){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Bạn chưa chọn ngày kết thúc'
          });
          return false;
    }else if(startDay.length == 0 && endDay.length == 0){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Bạn chưa chọn ngày bắt đầu và kết thúc'
          });
          return false;
    }else if(startDay.length != 0 && endDay.length != 0){
        reloadDataTableSurveyRecords(selectboxChooseSurvey, startDay, endDay);
    }
});

$('#button-filter-survey-records').click(function(){
    let idsurvey = $('#selectbox-choose-survey').val();
    let changeLabelUrl = '/surveys/users/survey/change/label';
    var startDay = $('.datepicker1').val();
    var endDay = $('.datepicker2').val();
    if(startDay.length == 0){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Bạn chưa chọn ngày bắt đầu'
          });
          return false;
    }else if(endDay.length == 0){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Bạn chưa chọn ngày kết thúc'
          });
          return false;
    }else if(startDay.length == 0 && endDay.length == 0){
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Bạn chưa chọn ngày'
          });
          return false;
    }else if(startDay.length != 0 && endDay.length != 0){
    $.ajax({
      url:changeLabelUrl,
      type:"POST",
      data:{
        id:idsurvey
      }
    }).done(function(response){
      $('#change-name-survey').empty();
      $('#change-name-survey').html(response);
    });
}
});

function reloadDataTableSurveyRecords(selectboxChooseSurvey, startDay, endDay) {
    dataTableSurveyRecords.clear();
    dataTableSurveyRecords.destroy();
    loadDataTableSurveyRecords(selectboxChooseSurvey, startDay, endDay);
}

$(document).ready(function () {
    function getChecked() {
        var checkBoxeds = []
        $(".data-list-view tbody input[type=checkbox]:checked").each(function () {
            var value = $(this).val();
            if (value != 0)
                checkBoxeds.push(value);
        });
        return checkBoxeds;

    }

    function getIdSelectAll() {
        $('.data-list-view thead tr th:first-child input[type="checkbox"]').attr('id', 'select-all');
    }

    clickSelectAll();
    getIdSelectAll();

    function clickSelectAll() {
        $('#select-all').click(function (event) {
            event.stopPropagation();
            if (this.checked) {
                $('tbody tr').addClass('selected');
                $('tbody tr :checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $('tbody tr').removeClass('selected');
                $('tbody tr :checkbox').each(function () {
                    this.checked = false;
                });
            }
            disableAction(getChecked());
        });
    }

    function disableAction(checkBoxeds) {
        if (checkBoxeds.length) {
            $('#select-all').addClass('checkbox-all-custom');
            $('.action-btns').removeClass('d-none');
        }
        else {
            $('#select-all').removeClass('checkbox-all-custom');
            $('.action-btns').addClass('d-none');
            $('#select-all').prop('checked', false);
        }
    }
    function checkCountSelectBox() {
        var checkBoxed = 0;
        $(".data-list-view tbody input[type=checkbox]:checked").each(function () {
            checkBoxed++;
        });
        return checkBoxed;
    }
    function getCheckedAll() {
        var checkBoxed = 0;
        $(".data-list-view tbody input[type=checkbox]").each(function () {
            checkBoxed++;
        });
        var checked = checkCountSelectBox();

        if (checkBoxed > checked)
            $('#select-all').prop('checked', false);
        else if (checkBoxed == checked)
            $('#select-all').prop('checked', true);
    }

    function deleteRowAjax() {
        var dataTable = $('.dataTable').DataTable();
        $("#dataTableBuilder_processing").show();
        $.ajax({
            url: URL_DELETE_API,
            method: "DELETE",
            data: { checkboxed: $('#delete-params').attr('checkBoxeds') },
            type: 'json'
        }).done(function (data) {
            if (data.success) {
                dataTable.ajax.reload(null, false);
                $('#select-all').prop('checked', false);
                $('#select-all').removeClass('checkbox-all-custom');
                $('.btn-group-delete').css('display', 'none');
                toastr.success(data.message, 'Thông báo!');
            }
            $("#dataTableBuilder_processing").hide();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error('Lỗi', 'Thông báo!');
        });
    }
    showTabEditer();
    function showTabEditer() {
        $('body').on("click", ".data-list-view tbody tr", function (evt) {
            var checkClass = $(this).hasClass('selected');
            var $cell = $(evt.target).closest('td');
            if ($cell.index() > 0) {
                $(':checkbox').each(function () {
                    this.checked = false;
                });
                $('.selected').removeClass('selected');
                if (!checkClass) {
                    var rowId = $(this).find('.select-param').val();
                        $('#view-survey-detail' + rowId).click();
                }
            }
            if (checkClass) {
                $(this).removeClass('selected');
                $(this).find('.select-param').prop('checked', false);
            } else {
                $(this).addClass('selected');
                $(this).find('.select-param').prop('checked', true);
            }
            getCheckedAll();
            disableAction(getChecked());
        });
    }
    clickDeleteParams();
    function clickDeleteParams() {
        $('#delete-params').click(function (event) {
            var checkBoxeds = getChecked();
            if (checkBoxeds.length) {
                $('#delete-params').attr('checkBoxeds', checkBoxeds);
                alertsDelete(event);
                event.preventDefault();
            }
        });
    }

    function alertsDelete(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Bạn có muốn xoá không?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xoá',
            confirmButtonClass: 'btn btn-danger',
            cancelButtonClass: 'btn btn-outline-danger waves-effect waves-light ml-1',
            cancelButtonText: 'Huỷ',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                deleteRowAjax();
            }
            else if (result.dismiss === Swal.DismissReason.cancel) {
                e.preventDefault();
            }
        });
    }

    function deleteRowAjax() {
        var dataTable = $('.dataTable').DataTable();
        // $("#dataTableBuilder_processing").show();
        let deleteSurveyUrl = '/surveys/users/survey/record/delete';
        $.ajax({
            url: deleteSurveyUrl,
            method: "POST",
            data: { id: $('#delete-params').attr('checkBoxeds') },
            type: 'json'
        }).done(function (data) {
            if (data.success) {
                dataTable.ajax.reload(null, false);
                $('#select-all').prop('checked', false);
                $('#select-all').removeClass('checkbox-all-custom');
                $('.btn-group-delete').css('display', 'none');
                toastr.success(data.message);
            }
            $("#dataTableBuilder_processing").hide();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error('Lỗi', 'Thông báo!');
        });
    }
});