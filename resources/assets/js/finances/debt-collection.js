$('#date-debtcollect1,#date-debtcollect2').pickadate({
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
$('#date-debtcollect1').pickadate('picker').set('select', date_first);
$('#date-debtcollect2').pickadate('picker').set('select', max_time);
$.getJSON("/debt-collection/home", function (datas) {
    console.log(datas)
var DebtIncurredInPeriodOptions = {
    chart: {
        type: 'donut',
        height: 350,
        fontFamily: "'Segoe UI', Arial, sans-serif"
    },
    tooltip: {
        y: {
            formatter: function (value) {
                let value1 = Math.round(value);
                return Intl.NumberFormat().format(value1).replaceAll('.', ',') +
                    ' đ';
            }
        }
    },
    colors: themeColors,
    labels: ['Nợ còn phải thu', 'Nợ đã thu trong kỳ'],
    series: datas['receivable_in_period'],
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
var DebtIncurredInPeriodChart = new ApexCharts(
    document.querySelector("#ratio-receivable-in-period"),
    DebtIncurredInPeriodOptions
);
DebtIncurredInPeriodChart.render();
//tỷ lệ nợ phải thu cuối kỳ
var DebtIncurredEndPeriodChartOptions = {
    chart: {
        type: 'donut',
        height: 350,
        fontFamily: "'Segoe UI', Arial, sans-serif"
    },
    tooltip: {
        y: {
            formatter: function (value) {
                let value1 = Math.round(value);
                return Intl.NumberFormat().format(value1).replaceAll('.', ',') +
                    ' đ';
            }
        }
    },
    colors: themeColors,
    labels: ['Đang hoạt động', 'Khởi kiện', 'Không phát sinh', 'Không hoạt động'],
    series: datas['array_receivableEndPeriod'],
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
var DebtIncurredEndPeriodSimpleChart = new ApexCharts(
    document.querySelector("#ratio-receivable-end-period"),
    DebtIncurredEndPeriodChartOptions
);
DebtIncurredEndPeriodSimpleChart.render();
//nợ phải thu cuối kỳ theo khách hàng trên 500
var DebtEndPeriodCustomerCtx = $("#debt-end-period-customer");
var DebtEndPeriodCustomerOptions = {
    plugins: {
        datalabels: {
            color: 'black',
            display: true,
            anchor: 'end',
            align: 'top',
            font: {
                weight: 'bold'
            },
            formatter: function (value, context) {
                return Math.round(value / 1000000).toLocaleString().replaceAll('.',
                    ',') + 'M';
            }
        }
    },
    elements: {
        rectangle: {
            borderWidth: 2,
            borderSkipped: 'left'
        }
    },
    responsive: true,
    maintainAspectRatio: false,
    responsiveAnimationDuration: 500,
    legend: {
        display: false
    },
    tooltips: {
        enable: true,
        mode: 'index',
        intersect: false,
        callbacks: {
            title: function (tooltipItems, data) {
                var idx = tooltipItems[0].index;
                return "Tên rút gọn: " + data.labels[idx];
            },
            label: function (tooltipItem, data) {
                return "Số dư cuối kỳ: " + tooltipItem.yLabel.toFixed().toString()
                    .replace(
                        /\B(?=(\d{3})+(?!\d))/g, ",") + ' đ';
            }
        },
    },
    scales: {
        y: {
            beginAtZero: true
        },
        xAxes: [{
            display: true,
            maxBarThickness: 100,
            gridLines: {
                color: ['#ffffff']
            },
            scaleLabel: {
                display: true
            },
            ticks: {
                callback: function (value) {
                    if (value.length > 10) {
                        return value.substr(0, 10) + '...';
                    } else {
                        return value
                    }
                },
                minRotation: 90,
                maxRotation: 90
            }
        }],
        yAxes: [{
            display: true,
            gridLines: {
                color: ['#ffffff']
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
        }],
    },
    title: {
        display: false,
        text: 'Predicted world population (millions) in 2050'
    }
};
var DebtEndPeriodCustomerData = {
    labels: datas['nameCustomer10'],
    datasets: [{
        label: "Thu nợ cuối kỳ khách hàng",
        data: datas['debtCollectEndPeriod10'],
        backgroundColor: '#2B2087',
        borderColor: "transparent"
    }]
};
var DebtEndPeriodCustomerConfig = {
    type: 'bar',
    options: DebtEndPeriodCustomerOptions,
    data: DebtEndPeriodCustomerData
};
var DebtEndPeriodCustomer = new Chart(DebtEndPeriodCustomerCtx, DebtEndPeriodCustomerConfig);

function debtCollectionEndPeriod(url,types,date1,date2)
{
    $.ajax({
        url: url,
        type: "POST",
        data: {
            date1: date1,
            date2: date2
        },
    }).done(function (response) {
        if(types == 1){
            DebtEndPeriodCustomer.data.labels = response.data[0];
            DebtEndPeriodCustomer.data.datasets[0].data = response.data[1];
        }else if(types == 2){
            DebtEndPeriodCustomer.data.labels = response.data[2];
            DebtEndPeriodCustomer.data.datasets[0].data = response.data[3];
        }else if(types == 3){
            DebtEndPeriodCustomer.data.labels = response.data[4];
            DebtEndPeriodCustomer.data.datasets[0].data = response.data[5];
        }else if(types == 4){
            DebtEndPeriodCustomer.data.labels = response.data[6];
            DebtEndPeriodCustomer.data.datasets[0].data = response.data[7];
        }
        DebtEndPeriodCustomer.update();
    });
}

$('#date-debtcollect1,#date-debtcollect2').change(function () {
    let date1 = $('#date-debtcollect1').val();
    let date2 = $('#date-debtcollect2').val();
    let debtCollectionInPeriodUrl = '/receivable/inperiod';
    $.ajax({
        url: debtCollectionInPeriodUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2
        },
    }).done(function (response) {
        DebtIncurredInPeriodChart.updateSeries(response.data);
    });
    let debtCollectionEndPeriodUrl = '/debtCollection/EndPeriod';
    $.ajax({
        url: debtCollectionEndPeriodUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2
        },
    }).done(function (response) {
        DebtIncurredEndPeriodSimpleChart.updateSeries(response.data);
    });
    let debtCollectionEndPeriodCustomerUrl = '/debtEndPeriod/Customer';
    if ($(".top10debt-end-period-customer").hasClass("active")) {
        debtCollectionEndPeriod(debtCollectionEndPeriodCustomerUrl,1,date1,date2);
    } else if ($(".top20debt-end-period-customer").hasClass("active")) {
        debtCollectionEndPeriod(debtCollectionEndPeriodCustomerUrl,2,date1,date2);
    } else if ($(".top30debt-end-period-customer").hasClass("active")) {
        debtCollectionEndPeriod(debtCollectionEndPeriodCustomerUrl,3,date1,date2);
    } else if ($(".top40debt-end-period-customer").hasClass("active")) {
        debtCollectionEndPeriod(debtCollectionEndPeriodCustomerUrl,4,date1,date2);
    }
});

function debtCollectionEndPeriodCustomer(types)
{
    $('#accountant-responsible-debt-collection,#date-debtcollect1,#date-debtcollect2').prop('disabled', true);
    if(types == 1){
        $('.top20debt-end-period-customer,.top30debt-end-period-customer,.top40debt-end-period-customer').addClass('disabled');
    }else if(types == 2){
        $('.top10debt-end-period-customer,.top30debt-end-period-customer,.top40debt-end-period-customer').addClass('disabled');
    }else if(types == 3){
        $('.top10debt-end-period-customer,.top20debt-end-period-customer,.top40debt-end-period-customer').addClass('disabled');
    }else if(types == 4){
        $('.top10debt-end-period-customer,.top20debt-end-period-customer,.top30debt-end-period-customer').addClass('disabled');
    }
    let date1 = $('#date-debtcollect1').val();
    let date2 = $('#date-debtcollect2').val();
    let debtCollectionEndPeriodCustomerUrl = '/debtEndPeriod/Customer';
    $.ajax({
        url: debtCollectionEndPeriodCustomerUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2
        },
    }).done(function (response) {
        if(types == 1){
            DebtEndPeriodCustomer.data.labels = response.data[0];
            DebtEndPeriodCustomer.data.datasets[0].data = response.data[1];
        }else if(types == 2){
            DebtEndPeriodCustomer.data.labels = response.data[2];
            DebtEndPeriodCustomer.data.datasets[0].data = response.data[3];
        }else if(types == 3){
            DebtEndPeriodCustomer.data.labels = response.data[4];
            DebtEndPeriodCustomer.data.datasets[0].data = response.data[5];
        }else if(types == 4){
            DebtEndPeriodCustomer.data.labels = response.data[6];
            DebtEndPeriodCustomer.data.datasets[0].data = response.data[7];
        }
        DebtEndPeriodCustomer.update();
        $('#accountant-responsible-debt-collection,#date-debtcollect1,#date-debtcollect2').prop('disabled', false);
        if(types == 1){
        $('.top20debt-end-period-customer,.top30debt-end-period-customer,.top40debt-end-period-customer').removeClass('disabled');
        }else if(types == 2){
        $('.top10debt-end-period-customer,.top30debt-end-period-customer,.top40debt-end-period-customer').removeClass('disabled');
        }else if(types == 3){
        $('.top10debt-end-period-customer,.top20debt-end-period-customer,.top40debt-end-period-customer').removeClass('disabled');
        }else if(types == 4){
        $('.top10debt-end-period-customer,.top20debt-end-period-customer,.top30debt-end-period-customer').removeClass('disabled');
        }
    });
}
$('.top10debt-end-period-customer').click(function () {
    debtCollectionEndPeriodCustomer(1);
});
//--------------------------------------
$('.top20debt-end-period-customer').click(function () {
    debtCollectionEndPeriodCustomer(2);
});
//-----------------------------------
$('.top30debt-end-period-customer').click(function () {
    debtCollectionEndPeriodCustomer(3);
});
//------------------------------
$('.top40debt-end-period-customer').click(function () {
    debtCollectionEndPeriodCustomer(4);
});
///Nợ thu trong kỳ theo kế toán
var DebtInPerriodAccountantOptions = {
    chart: {
        type: 'donut',
        height: 350,
        fontFamily: "'Segoe UI', Arial, sans-serif"
    },
    tooltip: {
        y: {
            formatter: function (value) {
                let value1 = Math.round(value);
                return Intl.NumberFormat().format(value1).replaceAll('.', ',') +
                    ' đ';
            }
        }
    },
    colors: themeColors,
    labels: datas['accountant'],
    series: datas['debtAccountant'],
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
var debtIncurredInPeriodAccountantChart = new ApexCharts(
    document.querySelector("#ratio-debt-collection-in-period-accountant"),
    DebtInPerriodAccountantOptions
);
debtIncurredInPeriodAccountantChart.render();
//số lượng khách hàng
var AmountCustomerCtx = $("#amount-customer");
var AmountCustomerOptions = {
    plugins: {
        datalabels: {
            color: 'black',
            display: false,
            font: {
                weight: 'bold'
            },
            formatter: Math.round
        }
    },
    elements: {
        rectangle: {
            borderWidth: 2,
            borderSkipped: 'left'
        }
    },
    responsive: true,
    maintainAspectRatio: false,
    responsiveAnimationDuration: 500,
    legend: {
        display: false
    },
    tooltips: {
        enable: true,
        mode: 'index',
        intersect: false,
        callbacks: {
            title: function (tooltipItems, data) {
                var idx = tooltipItems[0].index;
                return "Kế toán phụ trách: " + data.labels[idx];
            }
        },
    },
    scales: {
        y: {
            beginAtZero: true
        },
        xAxes: [{
            display: true,
            stacked: true,
            maxBarThickness: 100,
            gridLines: {
                color: ["#ffffff"]
            },
            scaleLabel: {
                display: true
            },

        }],
        yAxes: [{
            display: true,
            stacked: true,
            gridLines: {
                color: ["#ffffff"],
            },
            scaleLabel: {
                display: true,
            },
            ticks: {
                stepSize: 100,
                fontFamily: "'Segoe UI', Arial, sans-serif"
            }
        }]
    },
    title: {
        display: false,
        text: 'Biểu đồ số lượng khách hàng theo kế toán'
    }
};
var AmountCustomerData = {
    labels: datas['customer_accountant'],
    datasets: [{
        label: "Đang hoạt động",
        data: datas['customerAccountantActive'],
        backgroundColor: primary,
        borderColor: "transparent"
    }, {
        label: "Khởi kiện",
        data: datas['customerAccountantSue'],
        backgroundColor: success,
        borderColor: "transparent"
    }, {
        label: "Không hoạt động",
        data: datas['customerAccountantUnActive'],
        backgroundColor: danger,
        borderColor: "transparent"
    }, {
        label: "Không phát sinh",
        data: datas['customerAccountantUnArise'],
        backgroundColor: warning,
        borderColor: "transparent"
    }]
};
var AmountCustomerConfig = {
    type: 'bar',
    options: AmountCustomerOptions,
    data: AmountCustomerData
};
var amountCustomerChart = new Chart(AmountCustomerCtx, AmountCustomerConfig);
//tỷ lệ nợ thu được trên nợ đầu kỳ và phát sinh nợ trong kỳ
var RatioDebtReceivablesCtx = $("#ratio-debt-receivable-in-debt-beginning-period");
var RatioDebtReceivablesOptions = {
    plugins: {
        datalabels: {
            color: 'black',
            display: true,
            anchor: 'end',
            align: 'top',
            font: {
                weight: 'bold'
            },
            formatter: function (value, context) {
                return value + '%';
            }
        }
    },
    elements: {
        rectangle: {
            borderWidth: 2,
            borderSkipped: 'left'
        }
    },
    responsive: true,
    maintainAspectRatio: false,
    responsiveAnimationDuration: 500,
    legend: {
        display: false
    },
    tooltips: {
        enable: true,
        mode: 'index',
        intersect: false,
        callbacks: {
            title: function (tooltipItems, data) {
                var idx = tooltipItems[0].index;
                return "Kế toán phụ trách: " + data.labels[idx];
            },
            label: function (tooltipItem, data) {
                return "Tỷ lệ thu nợ thành công: " + tooltipItem.yLabel + '%';
            }
        },
    },
    scales: {
        y: {
            beginAtZero: true
        },
        xAxes: [{
            display: true,
            maxBarThickness: 100,
            gridLines: {
                color: ["#ffffff"],
            },
            scaleLabel: {
                display: true,
            }
        }],
        yAxes: [{
            display: true,
            stacked: true,
            gridLines: {
                color: ["#ffffff"],
            },
            scaleLabel: {
                display: true,
            },
            ticks: {
                stepSize: 1000,
                fontFamily: "'Segoe UI', Arial, sans-serif"
            }
        }]
    },
    title: {
        display: false,
        text: 'Biểu đồ số lượng khách hàng theo kế toán'
    },

};
var RatioDebtReceivablesData = {
    labels: datas['nameAccountantRatio'],
    datasets: [{
        label: "%",
        data: datas['ratioDebtCollects'],
        backgroundColor: '#2B2087',
        borderColor: "transparent"
    }]
};
var RatioDebtReceivablesConfig = {
    type: 'bar',
    options: RatioDebtReceivablesOptions,
    data: RatioDebtReceivablesData
};
var ratioDebtReceivableChart = new Chart(RatioDebtReceivablesCtx, RatioDebtReceivablesConfig);
// //biểu đồ nợ phải thu còn lại theo kế toán
var debtReceivablesRemainCtx = $("#debt-receivable-remain-of-accountant");
var debtReceivablesRemainOptions = {
    plugins: {
        datalabels: {
            color: 'black',
            display: false,
            anchor: 'end',
            align: 'top',
            font: {
                weight: 'bold'
            },
            formatter: function (value, context) {
                return Math.round(value / 1000000).toLocaleString().replaceAll('.',
                    ',') + 'M';
            }
        }
    },
    elements: {
        rectangle: {
            borderWidth: 2,
            borderSkipped: 'left'
        }
    },
    responsive: true,
    maintainAspectRatio: false,
    responsiveAnimationDuration: 500,
    legend: {
        display: false
    },
    tooltips: {
        enable: true,
        mode: 'index',
        intersect: false,
        callbacks: {
            title: function (tooltipItems, data) {
                var idx = tooltipItems[0].index;
                return "Kế toán phụ trách: " + data.labels[idx];
            },
            label: function (tooltipItem, data) {
                return "Số dư cuối kỳ: " + tooltipItem.yLabel.toFixed().toString()
                    .replace(
                        /\B(?=(\d{3})+(?!\d))/g, ",") + ' đ';
            }
        },
    },
    scales: {
        y: {
            beginAtZero: true
        },
        xAxes: [{
            display: true,
            maxBarThickness: 100,
            gridLines: {
                color: ["#ffffff"],
            },
            scaleLabel: {
                display: true,
            }
        }],
        yAxes: [{
            display: true,
            gridLines: {
                color: ["#ffffff"],
            },
            scaleLabel: {
                display: true,
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
        text: 'Biểu đồ số lượng khách hàng theo kế toán'
    },

};
var debtReceivablesRemainData = {
    labels: datas['nameAccountant'],
    datasets: [{
        label: "VND",
        data: datas['remain'],
        backgroundColor: '#2B2087',
        borderColor: "transparent"
    }]
};
var debtReceivablesRemainConfig = {
    type: 'bar',
    options: debtReceivablesRemainOptions,
    data: debtReceivablesRemainData
};
var debtReceivablesRemainChart = new Chart(debtReceivablesRemainCtx, debtReceivablesRemainConfig);

//đưa vào chuyển đồi
$('#date-debtcollect1,#date-debtcollect2,#accountant-responsible-debt-collection').change(function () {
    $('#accountant-responsible-debt-collection,#date-debtcollect1,#date-debtcollect2').prop('disabled', true);
    $('.top10debt-end-period-customer,.top20debt-end-period-customer,.top30debt-end-period-customer,.top40debt-end-period-customer').addClass('disabled');
    let date1 = $('#date-debtcollect1').val();
    let date2 = $('#date-debtcollect2').val();
    let accountant = $('#accountant-responsible-debt-collection').val();
    let ratioDebtCollectAccountantUrl = '/ratioDebt/CollectAccountant';
    $.ajax({
        url: ratioDebtCollectAccountantUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2,
            accountant: accountant
        },
    }).done(function (response) {
        debtIncurredInPeriodAccountantChart.updateOptions({
            labels: response.data[0],
            series: response.data[1]
        });
    });
    //số lượng khách hàng
    let amountCustomerUrl = '/number/Customer';
    $.ajax({
        url: amountCustomerUrl,
        type: 'POST',
        data: {
            accountant: accountant
        },
    }).done(function (response) {
        amountCustomerChart.data.datasets[0].data = response.data[1];
        amountCustomerChart.data.datasets[1].data = response.data[2];
        amountCustomerChart.data.datasets[2].data = response.data[3];
        amountCustomerChart.data.datasets[3].data = response.data[4];
        amountCustomerChart.data.labels = response.data[0];
        amountCustomerChart.update();
    });
    let debtCollectionRemainUrl = '/ratioDebtPeriod/Accountant';
    //nợ phải thu còn lại theo kế toán
    $.ajax({
        url: debtCollectionRemainUrl,
        type: 'POST',
        data: {
            accountant: accountant,
            date1: date1,
            date2: date2
        },
    }).done(function (response) {
        ratioDebtReceivableChart.data.datasets[0].data = response.data[1];
        ratioDebtReceivableChart.data.labels = response.data[0];
        ratioDebtReceivableChart.update();
        debtReceivablesRemainChart.data.datasets[0].data = response.data[3];
        debtReceivablesRemainChart.data.labels = response.data[2];
        debtReceivablesRemainChart.update();
        $('#accountant-responsible-debt-collection,#date-debtcollect1,#date-debtcollect2').prop('disabled', false);
        $('.top10debt-end-period-customer,.top20debt-end-period-customer,.top30debt-end-period-customer,.top40debt-end-period-customer').removeClass('disabled');
    });
});
});