$('#date-detail1,#date-detail2').pickadate({
    format: 'dd/mm/yyyy',
    min: [min_year, min_month, min_date],
    max: [max_year, max_month, max_date],
    selectYears: true,
    selectMonths: true,
    monthsFull: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7',
        'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
    weekdaysShort: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']
});
$('#date-detail1').pickadate('picker').set('select', date_first);
$('#date-detail2').pickadate('picker').set('select', max_time);
$('#date-detail1,#date-detail2,#detail-status,#detail-classify').prop('disabled', true);
$('#detail-debt').DataTable({
    serverSide: false,
    scrollCollapse: false,
    paging: false,
    scrollY: "590px",
    searching: false,
    info: false,
    "autoWidth": true,
    fixedHeader: {
        header: true,
        footer: true
    },
    columnDefs: [{
        targets: 1,
        className: 'dt-body-left',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 2,
        className: 'dt-body-left',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 3,
        className: 'dt-body-left',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 4,
        className: 'dt-body-left',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 5,
        className: 'dt-body-left',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 6,
        className: 'dt-body-left',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    }
    ],
    ajax: {
        url: '/detail/datatables',
        type: 'POST'
    },
    buttons: [{
        extend: 'print',
        footer: true
    }],
    columns: [{
        data: 'customerName'
    },
    {
        data: 'outstandingBalanceBeginning'
    },
    {
        data: 'balanceBeginning'
    },
    {
        data: 'DebtIncurred'
    },
    {
        data: 'Generate'
    },
    {
        data: 'receivableEndTerm'
    },
    {
        data: 'CustomerPrepayEndTerm'
    }
    ],
    "footerCallback": function (row, data, start, end, display) {
        var api = this.api(),
            data;
        var intVal = function (i) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                    i : 0;
        };
        // Tổng phải thu đầu kỳ
        let totalReceivableBeginningPeriod = api.column(1).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // Tổng khách hàng trả trước đầu kỳ
        let totalCustomerPrepayBeginningPeriod = api.column(2).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // Tổng phát sinh nợ
        let totalDebtIncurred = api.column(3).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // Tổng phát sinh có
        let totalAriseThere = api.column(4).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // Tổng phải thu cuối kỳ
        let totalReceivableEndPeriod = api.column(5).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // Tổng khách hàng trả trước cuối kỳ
        let totalCustomerPrepayEndPeriod = api.column(6).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // Update footer
        $(api.column(0).footer()).html(
            '<div style="text-align:center">Tổng (VNĐ)</div>'
        );
        $(api.column(1).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(totalReceivableBeginningPeriod
                .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(2).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(totalCustomerPrepayBeginningPeriod
                .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(3).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(totalDebtIncurred
                .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(4).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(totalAriseThere
                .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(5).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(totalReceivableEndPeriod
                .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(6).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(totalCustomerPrepayEndPeriod
                .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
    }
}).on('draw.dt', function () {
    // remove disable sau khi load xong dữ liệu
    $('#date-detail1,#date-detail2,#detail-status,#detail-classify').prop('disabled', false);
});
//--------------------------------------------------------//
$('#customer-detail,#date-detail1,#date-detail2,#detail-status,#detail-classify,#company-detail').change(
    function () {
        $('#date-detail1,#date-detail2,#detail-status,#detail-classify').prop('disabled', true);
        let customer = $('#customer-detail').val();
        let date1 = $('#date-detail1').val();
        let date2 = $('#date-detail2').val();
        let status = $('#detail-status').val();
        let classify = $('#detail-classify').val();
        let company = $('#company-detail').val();
        let fillterDetailUrl = '/detail/fillter';
        $.ajax({
            url: fillterDetailUrl,
            type: "POST",
            data: {
                customer: customer,
                date1: date1,
                date2: date2,
                status: status,
                classify: classify,
                company: company
            }
        }).done(function (response) {
            var tabledetail = $('#detail-debt').DataTable();
            tabledetail.clear();
            tabledetail.rows.add(response.data);
            tabledetail.draw();
            $('#date-detail1,#date-detail2,#detail-status,#detail-classify').prop('disabled', false);
        });
    });
//lọc tình trạng, phân loại
$('#customer-detail').change(function () {
    let customer = $('#customer-detail').val();
    let classify = $('#detail-classify').val();
    let status = $('#detail-status').val();
    let fillterStatusUrl = '/status/customer';

        $.ajax({
            url: fillterStatusUrl,
            type: 'POST',
            data: {
                customer: customer
            }
        }).done(function (response) {
            $('#detail-status').empty();
            if(classify != 0)
            $('#detail-status').append(response).val(status);
            else
            $('#detail-status').append(response).val(status);
        });

        let fillterClassifyUrl = '/classify/customer';
        $.ajax({
            url: fillterClassifyUrl,
            type: 'POST',
            data: {
                customer: customer
            }
        }).done(function (response) {
            $('#detail-classify').empty();
            if(classify != 0)
            $('#detail-classify').append(response).val(classify);
            else
            $('#detail-classify').append(response).val(classify);
        });
});

$('#detail-status').change(function () {
    if ($(this).val() == 'Khởi kiện' || $(this).val() == 'Không hoạt động' || $(this).val() == 'Không phát sinh') {
        $('#detail-classify option[value="CÁ NHÂN"],#detail-classify option[value="NỘI BỘ"]').hide();
    } else {
        $('#detail-classify option[value="CÁ NHÂN"],#detail-classify option[value="NỘI BỘ"]').show();
    }
});
$('#detail-classify').change(function () {
    if ($(this).val() == 'CÁ NHÂN' || $(this).val() == 'NỘI BỘ') {
        $('#detail-status option[value="Khởi kiện"],#detail-status option[value="Không hoạt động"],#detail-status option[value="Không phát sinh"]').hide();
    } else {
        $('#detail-status option[value="Khởi kiện"],#detail-status option[value="Không hoạt động"],#detail-status option[value="Không phát sinh"]').show();
    }
});