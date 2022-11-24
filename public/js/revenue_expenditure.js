$(document).ready(function () {
    function ajaxSetup() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }
    ajaxSetup();
    $('.datepicker').pickadate({
        editable: true,
        format: 'dd/mm/yyyy'
    });
    $('.select2').not('#customer-code,#customer-name').select2({
        placeholder: "Chọn",
        allowClear: true
    });

    $('#customer-name').select2({
        placeholder: "Tên khách hàng",
        allowClear: true
    });

    $('#customer-code').select2({
        placeholder: "Mã khách hàng",
        allowClear: true
    });

    $('#customer-code').change(function () {
        let idCustomer = $(this).val();
        $('#customer-name').val(idCustomer).trigger('change.select2');
        let getAdressUrl = '/getadress';
        $.ajax({
            url: getAdressUrl,
            type: "POST",
            data: {
                id: idCustomer
            }
        }).done(function (response) {
            $('#address').val(response);
        });
    });

    $('#table-receipt').DataTable({
        serverSide: false,
        scrollCollapse: true,
        scrollY: "410px",
        searching: false,
        info: false,
        ordering: false,
        paging: false,
        columns: [{
            data: 'debtAccount'
        },
        {
            data: 'ariseAccount'
        },
        {
            data: 'customerCode'
        },
        {
            data: 'customerName'
        },
        {
            data: 'Amount'
        },
        {
            data: 'VAT'
        },
        {
            data: 'explain'
        },
        {
            data: 'transactionType'
        }]
    });

    $('#form-add-revenue-expenditure').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData($('#form-add-revenue-expenditure')[0]);
        var getDataUrl = '/getdata/expenditure';
        $.ajax({
            url: getDataUrl,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false
        }).done(function (response) {
            var tablereceipt = $('#table-receipt').DataTable();
            tablereceipt.rows.add(response.data);
            tablereceipt.draw();
        });
    });
});