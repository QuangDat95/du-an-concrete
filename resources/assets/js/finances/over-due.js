$('#customer-overdue').prop('disabled', true);
$('#over-due').DataTable({
    serverSide: false,
    scrollCollapse: false,
    paging: false,
    scrollX: false,
    scrollY: 410,
    searching: false,
    stateSave: true,
    info: false,
    "autoWidth": true,
    fixedHeader: {
        header: true,
        footer: true
    },
    columnDefs: [{
        targets: 1,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 2,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 3,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 4,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 5,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 6,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 7,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 8,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    },
    {
        targets: 9,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    }
    ],
    ajax: {
        url: '/overdue/datatables',
        type: 'POST'
    },
    buttons: [{
        extend: 'print',
        footer: true
    }],
    columns: [{
        data: 'name'
    },
    {
        data: 'sumDebt'
    },
    {
        data: 'debtIncurred'
    },
    {
        data: 'debtLessThan1Month'
    },
    {
        data: 'debtLessThan2Month'
    },
    {
        data: 'debtLessThan3Month'
    },
    {
        data: 'debtLessThan4Month'
    },
    {
        data: 'debtLessThan5Month'
    },
    {
        data: 'debtLessThan6Month'
    },
    {
        data: 'debtOverDue'
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
        // T???ng n???
        var totalSumDebt = api.column(1).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // T???ng n??? trong h???n
        var totalDueDebt = api.column(2).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // T???ng n??? d?????i 1 th??ng
        var totalDebtLess1Month = api.column(3).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // T???ng n??? d?????i 2 th??ng
        var totalDebtLess2Month = api.column(4).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // T???ng n??? d?????i 3 th??ng
        var totalDebtLess3Month = api.column(5).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // T???ng n??? d?????i 4 th??ng
        var totalDebtLess4Month = api.column(6).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // T???ng n??? d?????i 5 th??ng
        var totalDebtLess5Month = api.column(7).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // T???ng n??? d?????i 6 th??ng
        var totalDebtLess6Month = api.column(8).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        // T???ng n??? kh?? ????i
        var totalDebtDifficultClaim = api.column(9).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        $(api.column(0).footer()).html(
            '<div style="text-align:center">T???ng (VN??)</div>'
        );
        $(api.column(1).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(totalSumDebt
                .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(2).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(
                totalDueDebt
                    .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(3).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(
                totalDebtLess1Month
                    .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(4).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(
                totalDebtLess2Month
                    .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(5).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(
                totalDebtLess3Month
                    .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(6).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(
                totalDebtLess4Month
                    .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(7).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(
                totalDebtLess5Month
                    .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(8).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(
                totalDebtLess6Month
                    .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
        $(api.column(9).footer()).html(
            '<div style="float:left">' + Intl.NumberFormat().format(Number(
                totalDebtDifficultClaim
                    .toString()).toFixed()).replaceAll('.', ',') + '</div>'
        );
    }
}).on('draw.dt', function () {
    $('#customer-overdue').prop('disabled', false);
});
var table = $('#over-due').DataTable();
table.column( 3, {order:'current'} ).data();
$('#customer-overdue').change(function () {
    let customer = $('#customer-overdue').val();
    let overDueUrl = '/overdue/customer';
    $.ajax({
        url: overDueUrl,
        type: "POST",
        data: {
            customer: customer
        },
    }).done(function (response) {
        var tableoverdue = $('#over-due').DataTable();
        tableoverdue.clear();
        tableoverdue.rows.add(response.data);
        tableoverdue.draw();
    });
});