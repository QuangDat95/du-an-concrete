$('#date-overview1,#date-overview2').pickadate({
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
$('#date-overview1').pickadate('picker').set('select', date_first);
$('#date-overview2').pickadate('picker').set('select', max_time);
$.getJSON("/overview/home", function (data) {
    var lineChartDebtIncurredOptions = {
        chart: {
            toolbar: {
                show: true,
                offsetX: 0,
                offsetY: 0,
                tools: {
                  download: false,
                  selection: true,
                  zoom: true,
                  zoomin: true,
                  zoomout: true,
                  pan: true,
                  reset: true | '<img src="/static/icons/reset.png" width="20">',
                  customIcons: []
                },
                autoSelected: 'zoom' 
              },
            height: 455,
            type: 'line',
            fontFamily: "'Segoe UI', Arial, sans-serif"
        },
        colors: ['#2B2087'],
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return Intl.NumberFormat().format(Number((val / 1000000)
                    .toString()).toFixed()) + 'M';
            }
        },
        stroke: {
            curve: 'straight'
        },
        series: [{
            name: "Phát sinh nợ",
            data: data['sum_debit']
        }],
        title: {
            text: '',
            align: 'left'
        },
        grid: {
            row: {
                colors: ['#ffffff'],
                opacity: 0
            },
        },
        xaxis: {
            categories: data['date_debit'],
            labels: {
                show: true,
                rotate: -30,
                rotateAlways: true,
                minHeight: 120,
                maxHeight: 120
            },
            ticks: {
                autoSkip: false
            },
            scrollbar: {
                enabled: true
            }
        },
        tooltip: {
            custom: function ({
                series,
                seriesIndex,
                dataPointIndex,
                w
            }) {
                var data = w.globals.initialSeries[seriesIndex].data[dataPointIndex];
                return '<ul>' +
                    '<li style="padding-top:10px;list-style-type:none;margin-right:30px;font-family:Segoe UI, Arial, sans-serif"><b>Phát sinh nợ</b>: <b>' +
                    Intl.NumberFormat().format(Number((data).toString()).toFixed())
                        .replaceAll('.',
                            ',') + '</b> <b>đ</b>' +
                    '</li>' + '</ul>';
            }
        },
        yaxis: {
            tickAmount: 5,
            opposite: yaxis_opposite,
            labels: {
                formatter: function (value) {
                    return Intl.NumberFormat().format(Number((value / 1000000)
                        .toString()).toFixed()) + 'M';
                }
            },
            scrollbar: {
                enabled: true
            }
        }
    }
    var lineChartDebtIncurred = new ApexCharts(
        document.querySelector("#debt-collection-time-line-chart"),
        lineChartDebtIncurredOptions
    );
    lineChartDebtIncurred.render();
    //--------------------------------------------
    var lineChartAriseThereOptions = {
        chart: {
            toolbar: {
                show: true,
                offsetX: 0,
                offsetY: 0,
                tools: {
                  download: false,
                  selection: true,
                  zoom: true,
                  zoomin: true,
                  zoomout: true,
                  pan: true,
                  reset: true | '<img src="/static/icons/reset.png" width="20">',
                  customIcons: []
                },
                autoSelected: 'zoom' 
              },
            height: 455,
            type: 'line',
            zoom: {
                enabled: false
            },
            fontFamily: "'Segoe UI', Arial, sans-serif"
        },
        colors: ['#2B2087'],
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return Intl.NumberFormat().format(Number((val / 1000000)
                    .toString()).toFixed()) + 'M';
            }
        },
        stroke: {
            curve: 'straight'
        },
        series: [{
            name: "Phát sinh nợ",
            data: data['sum_credit']
        }],
        title: {
            text: '',
            align: 'left'
        },
        grid: {
            row: {
                colors: ['#ffffff'],
                opacity: 0
            },
        },
        xaxis: {
            categories: data['date_credit'],
            labels: {
                show: true,
                rotate: -30,
                rotateAlways: true,
                minHeight: 120,
                maxHeight: 120
            },
            ticks: {
                autoSkip: false
            }
        },
        tooltip: {
            custom: function ({
                series,
                seriesIndex,
                dataPointIndex,
                w
            }) {
                var data = w.globals.initialSeries[seriesIndex].data[dataPointIndex];
                return '<ul>' +
                    '<li style="padding-top:10px;list-style-type:none;margin-right:30px;font-family:Segoe UI, Arial, sans-serif"><b>Phát sinh có</b>: <b>' +
                    Intl.NumberFormat().format(Number((data).toString()).toFixed())
                        .replaceAll('.',
                            ',') + '</b> <b>đ</b>' +
                    '</li>' + '</ul>';
            }
        },
        yaxis: {
            tickAmount: 5,
            opposite: yaxis_opposite,
            labels: {
                formatter: function (value) {
                    return Intl.NumberFormat().format(Number((value / 1000000)
                        .toString()).toFixed()) + 'M';
                }
            }
        }
    }
    var lineChartAriseThere = new ApexCharts(
        document.querySelector("#arise-there-time-line-chart"),
        lineChartAriseThereOptions
    );
    lineChartAriseThere.render();
    if (data['debit_sums'] != 0) {
        $('.debt-incurred').html(Intl.NumberFormat().format(Number((data['debit_sums'])
            .toString()).toFixed()).replaceAll('.', ',') + ' <u>đ</u>');
    } else {
        $('.debt-incurred').html('0 <u>đ</u>');
    }
    if (data['credit_sums'] != 0) {
        $('.arise-there').html(Intl.NumberFormat().format(Number((data['credit_sums'])
            .toString()).toFixed()).replaceAll('.', ',') + ' <u>đ</u>');
    } else {
        $('.arise-there').html('0 <u>đ</u>');
    }
    $('.remain').html(Intl.NumberFormat().format(Number((data['remain_receive'])
        .toString())
        .toFixed()).replaceAll('.', ',') + ' <u>đ</u>');
    //sự kiện khi thay đổi các phần chọn ngày, chọn khách hàng
    $('#date-overview1,#date-overview2,#customer-overview,#company-overview').change(function () {
        $('#date-overview1,#date-overview2').prop('disabled', true);
        $('.date-debt-incurred,.month-debt-incurred,.date-arise-there,.month-arise-there').addClass('disabled');
        let customer = $('#customer-overview').val();
        let company = $('#company-overview').val();
        let date1 = $('#date-overview1').val();
        let date2 = $('#date-overview2').val();
        let debtGerenatingSaleUrl = '/overview/request';
        $.ajax({
            url: debtGerenatingSaleUrl,
            type: "POST",
            data: {
                customer: customer,
                company: company,
                date1: date1,
                date2: date2
            }
        }).done(function (response) {
            if ($('.date-debt-incurred').hasClass("active")) {
                lineChartDebtIncurred.updateOptions({
                    series: [{
                        name: "Phát sinh nợ",
                        data: response['sum_debit']
                    }],
                    xaxis: {
                        categories: response['date_debit']
                    }
                });
            } else if ($('.month-debt-incurred').hasClass("active")) {
                lineChartDebtIncurred.updateOptions({
                    series: [{
                        name: "Phát sinh nợ",
                        data: response['month_sum_debit']
                    }],
                    xaxis: {
                        categories: response['month_debit']
                    }
                });
            }
            //cập nhật linechart phát sinh có theo ngày
            if ($('.date-arise-there').hasClass("active")) {
                lineChartAriseThere.updateOptions({
                    series: [{
                        name: "Phát sinh có",
                        data: response['sum_credit']
                    }],
                    xaxis: {
                        categories: response['date_credit']
                    }
                });
                //cập nhật PS có theo tháng
            } else if ($('.month-arise-there').hasClass("active")) {
                lineChartAriseThere.updateOptions({
                    series: [{
                        name: "Phát sinh có",
                        data: response['month_sum_credit']
                    }],
                    xaxis: {
                        categories: response['month_credit']
                    }
                });
            }
            if (response['debit_sums'] != 0) {
                $('.debt-incurred').html(Intl.NumberFormat().format(Number((
                    response['debit_sums']).toString()).toFixed())
                    .replaceAll('.', ',') + ' <u>đ</u>');
            } else {
                $('.debt-incurred').html('0 <u>đ</u>');
            }
            if (response['credit_sums'] != 0) {
                $('.arise-there').html(Intl.NumberFormat().format(Number((
                    response['credit_sums']).toString()).toFixed())
                    .replaceAll('.', ',') + ' <u>đ</u>');
            } else {
                $('.arise-there').html('0 <u>đ</u>');
            }
            $('.remain').html(Intl.NumberFormat().format(Number((response['remain_receive']).toString()).toFixed()).replaceAll('.', ',') + ' <u>đ</u>');
            $('#date-overview1,#date-overview2').prop('disabled', false);
            $('.date-debt-incurred,.month-debt-incurred,.date-arise-there,.month-arise-there').removeClass('disabled');
        });
    });
    //click chọn ngày phát sinh nợ
    function debtGenaratingSale(types) {
        $('#date-overview1,#date-overview2').prop('disabled', true);
        if (types == 1) {
            $('.month-debt-incurred,.date-arise-there,.month-arise-there').addClass('disabled');
        } else if (types == 2) {
            $('.date-debt-incurred,.date-arise-there,.month-arise-there').addClass('disabled');
        }else if(types == 3){
            $('.date-debt-incurred,.month-debt-incurred,.month-arise-there').addClass('disabled');
        }else if(types == 4){
            $('.date-debt-incurred,.month-debt-incurred,.date-arise-there').addClass('disabled');
        }
        let customer = $('#customer-overview').val();
        let company = $('#company-overview').val();
        let date1 = $('#date-overview1').val();
        let date2 = $('#date-overview2').val();
        let debtGerenatingSaleUrl = '/overview/request';
        $.ajax({
            url: debtGerenatingSaleUrl,
            type: "POST",
            data: {
                customer: customer,
                company: company,
                date1: date1,
                date2: date2
            }
        }).done(function (response) {
            if (types == 1) {
                lineChartDebtIncurred.updateOptions({
                    series: [{
                        name: "Phát sinh nợ",
                        data: response['sum_debit']
                    }],
                    xaxis: {
                        categories: response['date_debit']
                    }
                });
            } else if (types == 2) {
                lineChartDebtIncurred.updateOptions({
                    series: [{
                        name: "Phát sinh nợ",
                        data: response['month_sum_debit']
                    }],
                    xaxis: {
                        categories: response['month_debit']
                    }
                });
            } else if (types == 3) {
                lineChartAriseThere.updateOptions({
                    series: [{
                        name: "Phát sinh có",
                        data: response['sum_credit']
                    }],
                    xaxis: {
                        categories: response['date_credit']
                    }
                });
            } else if (types == 4) {
                lineChartAriseThere.updateOptions({
                    series: [{
                        name: "Phát sinh có",
                        data: response['month_sum_credit']
                    }],
                    xaxis: {
                        categories: response['month_credit']
                    }
                });
            }
            $('#date-overview1,#date-overview2').prop('disabled', false);
            if (types == 1) {
                $('.month-debt-incurred,.date-arise-there,.month-arise-there').removeClass('disabled');
            }else if(types == 2){
                $('.date-debt-incurred,.date-arise-there,.month-arise-there').removeClass('disabled');
            }else if(types == 3){
                $('.date-debt-incurred,.month-debt-incurred,.month-arise-there').removeClass('disabled');
            }else if(types == 4){
                $('.date-debt-incurred,.month-debt-incurred,.date-arise-there').removeClass('disabled');
            }
        });
    }
    //chọn ngày ps nợ
    $('.date-debt-incurred').click(function () {
        debtGenaratingSale(1);
    });
    //chọn tháng phát sinh nợ
    $('.month-debt-incurred').click(function () {
        debtGenaratingSale(2);
    });
    //chọn ngày phát sinh có
    $('.date-arise-there').click(function () {
        debtGenaratingSale(3);
    });
    //chọn tháng phát sinh có
    $('.month-arise-there').click(function () {
        debtGenaratingSale(4);
    });
});