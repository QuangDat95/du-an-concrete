$('#date-detail-debtcollect1,#date-detail-debtcollect2').pickadate({
    format: 'dd/mm/yyyy',
    min: [min_year, min_month, min_date],
    max: [max_year, max_month, max_date],
    selectYears: true,
    selectMonths: true,
    monthsFull: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7',
        'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
    ],
    weekdaysShort: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']
});
$('#date-detail-debtcollect1').pickadate('picker').set('select', date_first);
$('#date-detail-debtcollect2').pickadate('picker').set('select', max_time);
$('#accountant-detail-debt-collection,#date-detail-debtcollect1,#date-detail-debtcollect2').prop('disabled', true);
// ------------------------
var groupMonneyReceivables = {};
// ------------------------
var monneyReceivables = $('#monney-receivables').DataTable({
    serverSide: false,
    scrollCollapse: false,
    paging: false,
    scrollX: false,
    scrollY: "540px",
    searching: false,
    info: false,
    ordering: false,
    "autoWidth": true,
    fixedHeader: {
        header: true,
        footer: true
    },
    ajax: {
        url: '/money/collected',
        type: 'POST'
    },
    buttons: [{
        extend: 'print',
        footer: true
    }],
    columns: [{
        data: 'nameAccountant'
    },
    {
        data: 'nameCustomer'
    },
    {
        data: 'ariseThere',
        render: $.fn.dataTable.render.number(',', 3, '')
    }
    ],
    columnDefs: [{
        "visible": false,
        "targets": 0
    },
    {
        targets: 2,
        className: 'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    }
    ],
    defaultColDef: {
        flex: 0,
        minWidth: 100,
        sortable: true,
        resizable: true
    },
    autoGroupColumnDef: {
        minWidth: 200
    },
    order: [
        [0, 'asc']
    ],
    rowGroup: {
        startRender: function (rows, group) {
            var intVals = function (i) {
                if (typeof i === 'string') {
                    let multiplier = /[\(\)]/g.test(i) ? -1 : 1;

                    return (i.replace(/[\$,\(\)]/g, '') * multiplier)
                }

                return typeof i === 'number' ?
                    i : 0;
            };

            var total = rows
                .data()
                .pluck('ariseThere')
                .reduce(function (a, b) {
                    return intVals(a) + intVals(b);
                }, 0);

            var groupMR = !!groupMonneyReceivables[group];

            rows.nodes().each(function (r) {
                r.style.display = '';
                if (groupMR) {
                    r.style.display = 'none';
                }
            });

            // Add category name to the <tr>. NOTE: Hardcoded colspan
            return $('<tr/>')
                .append(
                    '<td>' + group + ' (' + rows.count() + ')</td>')
                .append('<td><div style="float:right">' + Intl.NumberFormat().format(Number(
                    total.toString()).toFixed()).replaceAll('.', ',') + '</div></td>')
                .attr('data-name', group)
                .toggleClass('collapsed', groupMR);
        },
        endRender: null,
        dataSrc: 'nameAccountant'
    },
    animateRows: true,
    "footerCallback": function (row, data, start, end, display) {
        var api = this.api(),
            data;
        // Remove the formatting to get integer data for summation
        var intVal = function (i) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                    i : 0;
        };
        // Tổng
        var tong = api.column(2).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        $(api.column(1).footer()).html('Tổng (VNĐ)');
        $(api.column(2).footer()).html('<div style="float:right">' + Intl.NumberFormat()
            .format(Number(tong.toString()).toFixed()).replaceAll('.', ',') +
            '</div>');
    }
});
$('#monney-receivables tbody').on('click', 'tr', function () {
    var name = $(this).data('name');
    groupMonneyReceivables[name] = !groupMonneyReceivables[name];
    monneyReceivables.draw(false);
});
//Nợ phải thu
var GroupDebtReceivables = {};
var debtReceivables = $('#debt-receivables').DataTable({
    serverSide: false,
    scrollCollapse: false,
    paging: false,
    scrollX: false,
    scrollY: "540px",
    searching: false,
    info: false,
    ordering: false,
    "autoWidth": true,
    fixedHeader: {
        header: true,
        footer: true
    },
    ajax: {
        url: '/account/receivable',
        type: 'POST'
    },
    buttons: [{
        extend: 'print',
        footer: true
    }],
    columns: [{
        data: 'nameAccountant'
    },
    {
        data: 'nameCustomer'
    },
    {
        data: 'receivableEndPeriod',
        render: $.fn.dataTable.render.number(',', 3, '')
    }
    ],
    columnDefs: [{
        "visible": false,
        "targets": 0
    },
    {
        targets: 2,
        className:'dt-body-right',
        render: function (data) {
            return data == 0 ? '-' : data;
        }
    }
    ],
    defaultColDef: {
        flex: 0,
        minWidth: 100,
        sortable: true,
        resizable: true
    },
    autoGroupColumnDef: {
        minWidth: 200
    },
    order: [
        [0, 'asc']
    ],
    rowGroup: {
        startRender: function (rows, group) {
            var intVals = function (i) {
                if (typeof i === 'string') {
                    let multiplier = /[\(\)]/g.test(i) ? -1 : 1;

                    return (i.replace(/[\$,\(\)]/g, '') * multiplier)
                }

                return typeof i === 'number' ?
                    i : 0;
            };

            var total = rows
                .data()
                .pluck('receivableEndPeriod')
                .reduce(function (a, b) {
                    return intVals(a) + intVals(b);
                }, 0);

            var groupReceive = !!GroupDebtReceivables[group];

            rows.nodes().each(function (r) {
                r.style.display = '';
                if (groupReceive) {
                    r.style.display = 'none';
                }
            });

            return $('<tr/>')
                .append('<td>' + group + ' (' + rows.count() + ')</td>')
                .append('<td><div style="float:right">' + Intl.NumberFormat().format(Number(
                    total.toString()).toFixed()).replaceAll('.', ',') + '</div></td>')
                .attr('data-name', group)
                .toggleClass('collapsed', groupReceive);
        },
        endRender: null,
        dataSrc: 'nameAccountant'
    },
    animateRows: true,
    "footerCallback": function (row, data, start, end, display) {
        var api = this.api(),
            data;
        var intVal = function (i) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                    i : 0;
        };
        var tong = api.column(2).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);
        $(api.column(1).footer()).html('Tổng (VNĐ)');
        $(api.column(2).footer()).html('<div style="float:right">' + Intl.NumberFormat()
            .format(Number(tong.toString()).toFixed()).replaceAll('.', ',') +
            '</div>');
    }
}).on('draw.dt', function () {
    $('#accountant-detail-debt-collection,#date-detail-debtcollect1,#date-detail-debtcollect2').prop('disabled', false);
});
$('#debt-receivables tbody').on('click', 'tr', function () {
    var names = $(this).data('name');
    GroupDebtReceivables[names] = !GroupDebtReceivables[names];
    debtReceivables.draw(false);
});

$('#accountant-detail-debt-collection,#date-detail-debtcollect1,#date-detail-debtcollect2').change(function () {
    $('#accountant-detail-debt-collection,#date-detail-debtcollect1,#date-detail-debtcollect2').prop('disabled', true);
    let accountant = $('#accountant-detail-debt-collection').val();
    let date1 = $('#date-detail-debtcollect1').val();
    let date2 = $('#date-detail-debtcollect2').val();
    let amountEarnedUrl = '/money/collect/request';
    $.ajax({
        url: amountEarnedUrl,
        type: 'POST',
        data: {
            accountant: accountant,
            date1: date1,
            date2: date2
        },
    }).done(function (response) {
        var tablemonneyreceivables = $('#monney-receivables').DataTable();
        tablemonneyreceivables.clear();
        tablemonneyreceivables.rows.add(response.data);
        tablemonneyreceivables.draw();
    });
    let accountReceivableUrl = '/account/receivable/request';
    $.ajax({
        url: accountReceivableUrl,
        type: 'POST',
        data: {
            accountant: accountant,
            date1: date1,
            date2: date2
        },
    }).done(function (response) {
        var tabledebtreceivables = $('#debt-receivables').DataTable();
        tabledebtreceivables.clear();
        tabledebtreceivables.rows.add(response.data);
        tabledebtreceivables.draw();
        $('#accountant-detail-debt-collection,#date-detail-debtcollect1,#date-detail-debtcollect2').prop('disabled', false);
    });
});