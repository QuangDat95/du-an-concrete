$('#accountant-responsible,#debt-structure-status').prop('disabled', true);
$('.debt-structure-overdue-of-month,.debt-structure-overdue-of-precious,.debt-structure-overdue-of-years').addClass('disabled');
$.getJSON("/debt-struct", function (datas) {
    var DebtStructionOptions = {
        chart: {
            type: 'donut',
            height: 350,
            fontFamily: "'Segoe UI', Arial, sans-serif"
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return Intl.NumberFormat().format(value).replaceAll(".", ",") +
                        ' đ';
                }
            }
        },
        colors: ['#ed1e24', '#00b050'],
        labels: ['Tổng nợ quá hạn', 'Tổng nợ trong hạn'],
        series: datas['array_debt'],
        legend: {
            itemMargin: {
                horizontal: 2
            },
            fontSize: "18px"
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 350
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    }
    var DebtStructionChart = new ApexCharts(
        document.querySelector("#debt-struction"),
        DebtStructionOptions
    );
    DebtStructionChart.render();
    //chi tiết cơ cấu nợ
    var DetailDebtStructionOptions = {
        chart: {
            type: 'donut',
            height: 350,
            fontFamily: "'Segoe UI', Arial, sans-serif"
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return Intl.NumberFormat().format(value).replaceAll(".", ",") +
                        ' đ';
                }
            }
        },
        colors: ['#00b050', '#70c5a4', '#40b8ea', '#436fb6', '#8750a1', '#d1499b',
            '#fba51a',
            '#ed1e24'
        ],
        labels: ['Trong hạn', 'Quá hạn <= 1 tháng', 'Quá hạn <= 2 tháng',
            'Quá hạn <= 3 tháng',
            'Quá hạn <= 4 tháng', 'Quá hạn <= 5 tháng', 'Quá hạn <= 6 tháng', 'Khó đòi'
        ],
        series: datas['array_debt_overdue'],
        legend: {
            itemMargin: {
                horizontal: 2
            },
            fontSize: "18px"
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 350
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
    }
    var DetailDebtStructionChart = new ApexCharts(
        document.querySelector("#detail-debt-struction"),
        DetailDebtStructionOptions
    );
    DetailDebtStructionChart.render();
    //biểu đồ chi tiết cơ cấu nợ, cơ cấu nợ
    $('#debt-structure-customer,#date-debtstruct').change(function () {
        $('#accountant-responsible,#debt-structure-status').prop('disabled', true);
        $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-precious,.debt-structure-overdue-of-years').addClass('disabled');
        let date = $('#date-debtstruct').val();
        let customer = $('#debt-structure-customer').val();
        let debtStructionUrl = '/debt-structure/request';
        $.ajax({
            url: debtStructionUrl,
            type: "POST",
            data: {
                date: date,
                customer: customer
            },
        }).done(function (response) {
            DebtStructionChart.updateSeries(response['arrayDebtRequest']);
            DetailDebtStructionChart.updateSeries(response['arrayDebtOverdueRequest']);
            $('#accountant-responsible,#debt-structure-status').prop('disabled', false);
            $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-precious,.debt-structure-overdue-of-years').removeClass('disabled');
        });
    });
});
$.getJSON("/debt-struct-time", function (datas) {
    //cơ cấu nợ quá hạn theo thời gian
    var lineChartDebtOverDueCtx = $("#debt-struction-overdue-of-time");
    var lineChartDebtOverDueOptions = {
        plugins: {
            datalabels: {
                display: false
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            position: 'top'
        },
        hover: {
            mode: 'point'
        },
        tooltips: {
            mode: 'label',
            label: 'mylabel',
            callbacks: {
                label: function (tooltipItem, data) {
                    return tooltipItem.yLabel.toString().replace(
                        /\B(?=(\d{3})+(?!\d))/g,
                        ",") + ' đ';
                },
            },
        },
        scales: {
            xAxes: [{
                display: true,
                gridLines: {
                    color: grid_line_color,
                },
                scaleLabel: {
                    display: true
                }
            }],
            yAxes: [{
                display: true,
                gridLines: {
                    color: grid_line_color,
                },
                scaleLabel: {
                    display: true
                },
                ticks: {
                    beginAtZero: true,
                    callback: function (label, index, labels) {
                        return Intl.NumberFormat().format(Number((label /
                            1000000)
                            .toString())) + 'M';
                    },
                    fontFamily: "'Segoe UI', Arial, sans-serif"
                }
            }]
        },
        title: {
            display: false,
            text: 'World population per region (in millions)'
        }
    };
    var linechartDebtOverDueData = {
        labels: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"],
        datasets: [{
            data: datas['debtLess1Month'],
            label: "Nợ dưới 1 tháng",
            borderColor: '#70c5a4',
            fill: false
        }, {
            data: datas['debtLess2Month'],
            label: "Nợ dưới 2 tháng",
            borderColor: '#40b8ea',
            fill: false
        }, {
            data: datas['debtLess3Month'],
            label: "Nợ dưới 3 tháng",
            borderColor: '#436fb6',
            fill: false
        }, {
            data: datas['debtLess4Month'],
            label: "Nợ dưới 4 tháng",
            borderColor: '#8750a1',
            fill: false
        }, {
            data: datas['debtLess5Month'],
            label: "Nợ dưới 5 tháng",
            borderColor: '#d1499b',
            fill: false
        }, {
            data: datas['debtLess6Month'],
            label: "Nợ dưới 6 tháng",
            borderColor: '#fba51a',
            fill: false
        }, {
            data: datas['debtOverDue'],
            label: "Nợ khó đòi",
            borderColor: '#ed1e24',
            fill: false
        }]
    };
    var lineChartDebtOverDueConfig = {
        type: 'line',
        options: lineChartDebtOverDueOptions,
        data: linechartDebtOverDueData
    };
    var lineChartDebtOverDue = new Chart(lineChartDebtOverDueCtx, lineChartDebtOverDueConfig);

    $('#debt-structure-customer,#accountant-responsible,#debt-structure-status').on('change', function () {
        $('#accountant-responsible,#debt-structure-status').prop('disabled', true);
        $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-precious,.debt-structure-overdue-of-years').addClass('disabled');
        let customer = $('#debt-structure-customer').val();
        let accountant = $('#accountant-responsible').val();
        let status = $("#debt-structure-status").val();
        let debtStructionOverdueUrl = '/debt-structure-time/request';

        $.ajax({
            url: debtStructionOverdueUrl,
            type: 'POST',
            data: {
                customer: customer,
                accountant: accountant,
                status: status
            },
        }).done(function (response) {
            if ($(".debt-structure-overdue-of-month").hasClass("active")) {
                lineChartDebtOverDue.data.labels = response['months'];
                lineChartDebtOverDue.data.datasets[0].data = response['debtLess1Month'];
                lineChartDebtOverDue.data.datasets[1].data = response['debtLess2Month'];
                lineChartDebtOverDue.data.datasets[2].data = response['debtLess3Month'];
                lineChartDebtOverDue.data.datasets[3].data = response['debtLess4Month'];
                lineChartDebtOverDue.data.datasets[4].data = response['debtLess5Month'];
                lineChartDebtOverDue.data.datasets[5].data = response['debtLess6Month'];
                lineChartDebtOverDue.data.datasets[6].data = response['debtOverDue'];
            } else if ($(".debt-structure-overdue-of-precious").hasClass("active")) {
                lineChartDebtOverDue.data.labels = response['precious'];
                lineChartDebtOverDue.data.datasets[0].data = response['debtPreciousLess1Month'];
                lineChartDebtOverDue.data.datasets[1].data = response['debtPreciousLess2Month'];
                lineChartDebtOverDue.data.datasets[2].data = response['debtPreciousLess3Month'];
                lineChartDebtOverDue.data.datasets[3].data = response['debtPreciousLess4Month'];
                lineChartDebtOverDue.data.datasets[4].data = response['debtPreciousLess5Month'];
                lineChartDebtOverDue.data.datasets[5].data = response['debtPreciousLess6Month'];
                lineChartDebtOverDue.data.datasets[6].data = response['debtPreciousOverDue'];
            } else if ($(".debt-structure-overdue-of-years").hasClass("active")) {
                lineChartDebtOverDue.data.labels = response['years'];
                lineChartDebtOverDue.data.datasets[0].data = response['debtYearsLess1Month'];
                lineChartDebtOverDue.data.datasets[1].data = response['debtYearsLess2Month'];
                lineChartDebtOverDue.data.datasets[2].data = response['debtYearsLess3Month'];
                lineChartDebtOverDue.data.datasets[3].data = response['debtYearsLess4Month'];
                lineChartDebtOverDue.data.datasets[4].data = response['debtYearsLess5Month'];
                lineChartDebtOverDue.data.datasets[5].data = response['debtYearsLess6Month'];
                lineChartDebtOverDue.data.datasets[6].data = response['debtYearsOverDue'];
            }
            lineChartDebtOverDue.update();
            $('#accountant-responsible,#debt-structure-status').prop('disabled', false);
            $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-precious,.debt-structure-overdue-of-years').removeClass('disabled');
        });
    });

    function debtStructOverdue(types)
    {
        $('#debt-structure-customer,#accountant-responsible,#debt-structure-status').prop('disabled', true);
        if(types == 1){
            $('.debt-structure-overdue-of-precious,.debt-structure-overdue-of-years').addClass('disabled');
        }else if(types == 2){
            $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-years').addClass('disabled');
        }else if(types == 3){
            $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-precious').addClass('disabled');
        }
        let customer = $('#debt-structure-customer').val();
        let accountant = $('#accountant-responsible').val();
        let status = $("#debt-structure-status").val();
        let debtStructionOverdueUrl = '/debt-structure-time/request';
        $.ajax({
            url: debtStructionOverdueUrl,
            type: 'POST',
            data: {
                customer: customer,
                accountant: accountant,
                status: status
            },
        }).done(function (response) {
            if(types == 1){
                lineChartDebtOverDue.data.labels = response['months'];
                lineChartDebtOverDue.data.datasets[0].data = response['debtLess1Month'];
                lineChartDebtOverDue.data.datasets[1].data = response['debtLess2Month'];
                lineChartDebtOverDue.data.datasets[2].data = response['debtLess3Month'];
                lineChartDebtOverDue.data.datasets[3].data = response['debtLess4Month'];
                lineChartDebtOverDue.data.datasets[4].data = response['debtLess5Month'];
                lineChartDebtOverDue.data.datasets[5].data = response['debtLess6Month'];
                lineChartDebtOverDue.data.datasets[6].data = response['debtOverDue'];
            }else if(types == 2){
                lineChartDebtOverDue.data.labels = response['precious'];
                lineChartDebtOverDue.data.datasets[0].data = response['debtPreciousLess1Month'];
                lineChartDebtOverDue.data.datasets[1].data = response['debtPreciousLess2Month'];
                lineChartDebtOverDue.data.datasets[2].data = response['debtPreciousLess3Month'];
                lineChartDebtOverDue.data.datasets[3].data = response['debtPreciousLess4Month'];
                lineChartDebtOverDue.data.datasets[4].data = response['debtPreciousLess5Month'];
                lineChartDebtOverDue.data.datasets[5].data = response['debtPreciousLess6Month'];
                lineChartDebtOverDue.data.datasets[6].data = response['debtPreciousOverDue'];
            }else if(types == 3){
                lineChartDebtOverDue.data.labels = response['years'];
                lineChartDebtOverDue.data.datasets[0].data = response['debtYearsLess1Month'];
                lineChartDebtOverDue.data.datasets[1].data = response['debtYearsLess2Month'];
                lineChartDebtOverDue.data.datasets[2].data = response['debtYearsLess3Month'];
                lineChartDebtOverDue.data.datasets[3].data = response['debtYearsLess4Month'];
                lineChartDebtOverDue.data.datasets[4].data = response['debtYearsLess5Month'];
                lineChartDebtOverDue.data.datasets[5].data = response['debtYearsLess6Month'];
                lineChartDebtOverDue.data.datasets[6].data = response['debtYearsOverDue'];
            }
            lineChartDebtOverDue.update();
            $('#debt-structure-customer,#accountant-responsible,#debt-structure-status').prop('disabled', false);
            if(types == 1){
                $('.debt-structure-overdue-of-precious,.debt-structure-overdue-of-years').removeClass('disabled');
            }else if(types == 2){
                $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-years').removeClass('disabled');
            }else if(types == 3){
                $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-precious').removeClass('disabled');
            }
        });
    }
    $('.debt-structure-overdue-of-month').click(function () {
        debtStructOverdue(1);
    });
    $('.debt-structure-overdue-of-precious').click(function () {
        debtStructOverdue(2);
    });
    $(".debt-structure-overdue-of-years").click(function () {
        debtStructOverdue(3);
    });
    $('#accountant-responsible,#debt-structure-status').prop('disabled', false);
    $('.debt-structure-overdue-of-month,.debt-structure-overdue-of-precious,.debt-structure-overdue-of-years').removeClass('disabled');
});

$('#accountant-responsible,#debt-structure-customer').change(function () {
    let accountant = $('#accountant-responsible').val();
    let customer = $('#debt-structure-customer').val();
    let status = $('#debt-structure-status').val();
    let filterStatusSelectAccountantCustomerUrl = '/fillterStatus/SelectAccountantCustomer';
    if (customer == "Tất cả" && status == "Tất cả") {
        $.ajax({
            url: filterStatusSelectAccountantCustomerUrl,
            type: 'POST',
            data: {
                accountant: accountant,
                customer: customer
            },
        }).done(function (response) {
            $('#debt-structure-status').empty();
            $('#debt-structure-status').html(response);
        });
    }
});

$('#debt-structure-status,#debt-structure-customer').change(function () {
    let accountant = $('#accountant-responsible').val();
    let customer = $('#debt-structure-customer').val();
    let status = $('#debt-structure-status').val();
    let fillterAccountantSelectCustomerStatusUrl = '/fillterAccountant/SelectCustomerStatus';
    if (customer == "Tất cả" && accountant == "Tất cả") {
        $.ajax({
            url: fillterAccountantSelectCustomerStatusUrl,
            type: 'POST',
            data: {
                status: status,
                customer: customer
            },
        }).done(function (response) {
            $('#accountant-responsible').empty();
            $('#accountant-responsible').html(response);
        });
    }
});