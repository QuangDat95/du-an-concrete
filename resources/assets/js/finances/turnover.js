$('#turnover-date1,#turnover-date2').pickadate({
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
$('#turnover-date1').pickadate('picker').set('select', date_first);
$('#turnover-date2').pickadate('picker').set('select', max_time);
var barChartStationSales = $("#station-sales");
var barChartStationSalesOptions = {
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
                return "Trạm: " + data.labels[idx];
            },
            label: function (tooltipItem, data) {
                return "Phát sinh nợ: " + tooltipItem.yLabel.toFixed().toString()
                    .replace(
                        /\B(?=(\d{3})+(?!\d))/g,
                        ",") + ' đ';
            },
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
        text: ''
    }
};
var barChartStationSalesData = {
    labels: STATION_NAME,
    datasets: [{
        label: "VND",
        data: SUM_STATION,
        backgroundColor: '#2B2087',
        borderColor: "transparent"
    }]
};
var barChartconfig = {
    type: 'bar',
    options: barChartStationSalesOptions,
    data: barChartStationSalesData
};
var StationSalesBarChart = new Chart(barChartStationSales, barChartconfig);
$('#turnover-date1,#turnover-date2,#customer-turnover,#company-turnover,#charge-of-sales,#station').on('change', function () {
    let customer = $('#customer-turnover').val();
    let date1 = $('#turnover-date1').val();
    let date2 = $('#turnover-date2').val();
    let company = $('#company-turnover').val();
    let employee = $('#charge-of-sales').val();
    let station = $('#station').val();
    let stationSaleUrl = '/turnover/station';
    $.ajax({
        url: stationSaleUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2,
            customer: customer,
            company: company,
            employee: employee,
            station: station
        }
    }).done(function (response) {
        StationSalesBarChart.data.labels = response['stationName'];
        StationSalesBarChart.data.datasets[0].data = response['sumStation'];
        StationSalesBarChart.update();
    });
});
//doanh số bê tông theo nhân viên bán hàng
var barChartStaffSalesCtx = $("#staff-sales");
var barChartStaffSalesOptions = {
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
                return "Phụ trách BH: " + data.labels[idx];
            },
            label: function (tooltipItem, data) {
                return "Phát sinh nợ: " + tooltipItem.yLabel.toFixed().toString()
                    .replace(
                        /\B(?=(\d{3})+(?!\d))/g,
                        ",") + ' đ';
            },
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
                display: true,
            },
            ticks: {
                callback: function (value) {
                    if (value.length > 5) {
                        return value.substr(0, 5) + '...';
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
        }],
    },
    title: {
        display: false,
        text: 'Predicted world population (millions) in 2050'
    }
};
var barChartStaffSalesData = {
    labels: EMPLOYEE_NAME,
    datasets: [{
        label: "Population (millions)",
        data: SUM_EMPLOYEE,
        backgroundColor: '#2B2087',
        borderColor: "transparent"
    }]
};
var barChartStaffSalesConfig = {
    type: 'bar',
    options: barChartStaffSalesOptions,
    data: barChartStaffSalesData
};
var barChartStaffSales = new Chart(barChartStaffSalesCtx, barChartStaffSalesConfig);
$('#turnover-date1,#turnover-date2,#customer-turnover,#company-turnover,#charge-of-sales,#station').on('change', function () {
    let customer = $('#customer-turnover').val();
    let date1 = $('#turnover-date1').val();
    let date2 = $('#turnover-date2').val();
    let company = $('#company-turnover').val();
    let employee = $('#charge-of-sales').val();
    let station = $('#station').val();
    let staffSaleUrl = '/turnover/employee';
    $.ajax({
        url: staffSaleUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2,
            customer: customer,
            company: company,
            employee: employee,
            station: station
        }
    }).done(function (response) {
        barChartStaffSales.data.labels = response['employeeName'];
        barChartStaffSales.data.datasets[0].data = response['sumEmployee'];
        barChartStaffSales.update();
    });
});
//doanh số bê tông theo khách hàng-----------------------------------
var barChartCustomerSalesCtx = $("#customer-sales");
var barChartCustomerSalesOptions = {
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
                return Math.round(value / 1000000000).toLocaleString().replaceAll('.',
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
                return "Tên khách hàng: " + data.labels[idx];
            },
            label: function (tooltipItem, data) {
                return "Phát sinh nợ: " + tooltipItem.yLabel.toFixed()
                    .toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + ' đ';
            }
        }
    },
    hover: {
        mode: 'index',
        intersect: true
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
var barChartCustomerSalesData = {
    labels: CUSTOMER_NAME,
    datasets: [{
        label: "Population (millions)",
        data: SUM_CUSTOMER,
        backgroundColor: '#2B2087',
        borderColor: "transparent"
    }]
};
var barChartCustomerSalesConfig = {
    type: 'bar',
    options: barChartCustomerSalesOptions,
    data: barChartCustomerSalesData
};
var barChartCustomerSales = new Chart(barChartCustomerSalesCtx, barChartCustomerSalesConfig);
$('#turnover-date1,#turnover-date2,#customer-turnover,#company-turnover,#charge-of-sales,#station').on('change', function () {
    let customer = $('#customer-turnover').val();
    let date1 = $('#turnover-date1').val();
    let date2 = $('#turnover-date2').val();
    let employee = $('#charge-of-sales').val();
    let company = $('#company-turnover').val();
    let station = $('#station').val();
    let customerSaleUrl = '/turnover/customer';

    $.ajax({
        url: customerSaleUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2,
            customer: customer,
            company: company,
            employee: employee,
            station: station
        }
    }).done(function (response) {
        if ($(".top10-customer-concreate-sales").hasClass("active")) {
            barChartCustomerSales.data.labels = response['customerNames10'];
            barChartCustomerSales.data.datasets[0].data = response['sumCustomers10'];
        } else if ($(".top20-customer-concreate-sales").hasClass("active")) {
            barChartCustomerSales.data.labels = response['customerNames20'];
            barChartCustomerSales.data.datasets[0].data = response['sumCustomers20'];
        } else if ($(".top30-customer-concreate-sales").hasClass("active")) {
            barChartCustomerSales.data.labels = response['customerNames30'];
            barChartCustomerSales.data.datasets[0].data = response['sumCustomers30'];
        } else if ($(".top40-customer-concreate-sales").hasClass("active")) {
            barChartCustomerSales.data.labels = response['customerNames40'];
            barChartCustomerSales.data.datasets[0].data = response['sumCustomers40'];
        }
        barChartCustomerSales.update();
    });
});
//-----------------------------------
function customerConcrete(types){
    $('#turnover-date1,#turnover-date2,#charge-of-sales,#station').prop('disabled', true);
    if(types == 1){
        $('.top20-customer-concreate-sales,.top30-customer-concreate-sales,.top40-customer-concreate-sales').addClass('disabled'); 
    }else if(types == 2){
        $('.top10-customer-concreate-sales,.top30-customer-concreate-sales,.top40-customer-concreate-sales').addClass('disabled'); 
    }else if(types == 3){
        $('.top10-customer-concreate-sales,.top20-customer-concreate-sales,.top40-customer-concreate-sales').addClass('disabled');
    }else if(types == 4){
        $('.top10-customer-concreate-sales,.top20-customer-concreate-sales,.top30-customer-concreate-sales').addClass('disabled');
    }
    $('.top10-construction-concreate-sales,.top20-construction-concreate-sales,.top30-construction-concreate-sales,.top40-construction-concreate-sales').addClass('disabled');
    let customer = $('#customer-turnover').val();
    let date1 = $('#turnover-date1').val();
    let date2 = $('#turnover-date2').val();
    let company = $('#company-turnover').val();
    let employee = $('#charge-of-sales').val();
    let station = $('#station').val();
    let customerSaleUrl = '/turnover/customer';
    $.ajax({
        url: customerSaleUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2,
            customer: customer,
            company: company,
            employee: employee,
            station: station
        }
    }).done(function (response) {
        if(types == 1){
            barChartCustomerSales.data.labels = response['customerNames10'];
            barChartCustomerSales.data.datasets[0].data = response['sumCustomers10'];
        }else if(types == 2){
            barChartCustomerSales.data.labels = response['customerNames20'];
            barChartCustomerSales.data.datasets[0].data = response['sumCustomers20'];
        }else if(types == 3){
            barChartCustomerSales.data.labels = response['customerNames30'];
            barChartCustomerSales.data.datasets[0].data = response['sumCustomers30'];
        }else if(types == 4){
            barChartCustomerSales.data.labels = response['customerNames40'];
            barChartCustomerSales.data.datasets[0].data = response['sumCustomers40'];
        }
        barChartCustomerSales.update();
        $('#turnover-date1,#turnover-date2,#charge-of-sales,#station').prop('disabled', false);
        if(types == 1){
            $('.top20-customer-concreate-sales,.top30-customer-concreate-sales,.top40-customer-concreate-sales').removeClass('disabled'); 
        }else if(types == 2){
            $('.top10-customer-concreate-sales,.top30-customer-concreate-sales,.top40-customer-concreate-sales').removeClass('disabled'); 
        }else if(types == 3){
            $('.top10-customer-concreate-sales,.top20-customer-concreate-sales,.top40-customer-concreate-sales').removeClass('disabled');
        }else if(types == 4){
            $('.top10-customer-concreate-sales,.top20-customer-concreate-sales,.top30-customer-concreate-sales').removeClass('disabled');
        }
        $('.top10-construction-concreate-sales,.top20-construction-concreate-sales,.top30-construction-concreate-sales,.top40-construction-concreate-sales').removeClass('disabled');
    });
}
$('.top10-customer-concreate-sales').click(function () {
    customerConcrete(1);
});
//-----------------------------------------
$('.top20-customer-concreate-sales').click(function () {
    customerConcrete(2);
});
//-------------------------------------
$('.top30-customer-concreate-sales').click(function () {
    customerConcrete(3);
});
//---------------------------------------
$('.top40-customer-concreate-sales').click(function () {
    customerConcrete(4);
});
//doanh số bê tông theo công trình bar chart
var ConcreateSalesConstructionCtx = $("#construction-sales");
var ConcreateSalesConstructionOptions = {
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
                return Math.round(value / 1000000000).toLocaleString().replaceAll('.',
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
                return "Tên công trình: " + data.labels[idx];
            },
            label: function (tooltipItem, data) {
                return "Phát sinh nợ: " + tooltipItem.yLabel.toFixed().toString()
                    .replace(
                        /\B(?=(\d{3})+(?!\d))/g,
                        ",") + ' đ';
            }
        }
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
var ConcreateSalesConstructionData = {
    labels: CONSTRUCTION_NAME,
    datasets: [{
        label: "VND",
        data: SUM_CONSTRUCTION,
        backgroundColor: '#2B2087',
        borderColor: "transparent"
    }],
};
var ConcreateSalesConstructionConfig = {
    type: 'bar',
    options: ConcreateSalesConstructionOptions,
    data: ConcreateSalesConstructionData
};
var ConcreateSalesConstructionBarChart = new Chart(ConcreateSalesConstructionCtx, ConcreateSalesConstructionConfig);
$('#turnover-date1,#turnover-date2,#customer-turnover,#company-turnover,#charge-of-sales,#station').on('change', function () {
    $('#turnover-date1,#turnover-date2,#charge-of-sales,#station').prop('disabled', true);
    $('.top10-customer-concreate-sales,.top20-customer-concreate-sales,.top30-customer-concreate-sales,.top40-customer-concreate-sales').addClass('disabled');
    $('.top10-construction-concreate-sales,.top20-construction-concreate-sales,.top30-construction-concreate-sales,.top40-construction-concreate-sales').addClass('disabled');
    let customer = $('#customer-turnover').val();
    let date1 = $('#turnover-date1').val();
    let date2 = $('#turnover-date2').val();
    let company = $('#company-turnover').val();
    let employee = $('#charge-of-sales').val();
    let station = $('#station').val();
    let concreateContructionSaleUrl = '/turnover/construction';
    $.ajax({
        url: concreateContructionSaleUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2,
            customer: customer,
            company: company,
            employee: employee,
            station: station
        }
    }).done(function (response) {
        if ($(".top10-construction-concreate-sales").hasClass("active")) {
            ConcreateSalesConstructionBarChart.data.labels = response['constructionNames10'];
            ConcreateSalesConstructionBarChart.data.datasets[0].data = response['sumConstructions10'];
        } else if ($(".top20-construction-concreate-sales").hasClass("active")) {
            ConcreateSalesConstructionBarChart.data.labels = response['constructionNames20'];
            ConcreateSalesConstructionBarChart.data.datasets[0].data = response['sumConstructions20'];
        } else if ($(".top30-construction-concreate-sales").hasClass("active")) {
            ConcreateSalesConstructionBarChart.data.labels = response['constructionNames30'];
            ConcreateSalesConstructionBarChart.data.datasets[0].data = response['sumConstructions30'];
        } else if ($(".top40-construction-concreate-sales").hasClass("active")) {
            ConcreateSalesConstructionBarChart.data.labels = response['constructionNames40'];
            ConcreateSalesConstructionBarChart.data.datasets[0].data = response['sumConstructions40'];
        }
        ConcreateSalesConstructionBarChart.update();
        $('#turnover-date1,#turnover-date2,#charge-of-sales,#station').prop('disabled', false);
        $('.top10-customer-concreate-sales,.top20-customer-concreate-sales,.top30-customer-concreate-sales,.top40-customer-concreate-sales').removeClass('disabled');
        $('.top10-construction-concreate-sales,.top20-construction-concreate-sales,.top30-construction-concreate-sales,.top40-construction-concreate-sales').removeClass('disabled');
    });
});
//-------------------------------------------------
function constructionConcrete(types)
{
    $('#turnover-date1,#turnover-date2,#charge-of-sales,#station').prop('disabled', true);
    $('.top10-customer-concreate-sales,.top20-customer-concreate-sales,.top30-customer-concreate-sales,.top40-customer-concreate-sales').addClass('disabled');
    if(types == 1){
        $('.top20-construction-concreate-sales,.top30-construction-concreate-sales,.top40-construction-concreate-sales').addClass('disabled');
    }else if(types == 2){
        $('.top10-construction-concreate-sales,.top30-construction-concreate-sales,.top40-construction-concreate-sales').addClass('disabled');
    }else if(types == 3){
        $('.top10-construction-concreate-sales,.top20-construction-concreate-sales,.top40-construction-concreate-sales').addClass('disabled');
    }else if(types == 4){
        $('.top10-construction-concreate-sales,.top20-construction-concreate-sales,.top30-construction-concreate-sales').addClass('disabled');
    }
    let customer = $('#customer-turnover').val();
    let date1 = $('#turnover-date1').val();
    let date2 = $('#turnover-date2').val();
    let company = $('#company-turnover').val();
    let employee = $('#charge-of-sales').val();
    let station = $('#station').val();
    let concreateContructionSaleUrl = '/turnover/construction';
    $.ajax({
        url: concreateContructionSaleUrl,
        type: "POST",
        data: {
            date1: date1,
            date2: date2,
            customer: customer,
            company: company,
            employee: employee,
            station: station
        },
    }).done(function (response) {
        if(types == 1){
            ConcreateSalesConstructionBarChart.data.labels = response['constructionNames10'];
            ConcreateSalesConstructionBarChart.data.datasets[0].data = response['sumConstructions10'];
        }else if(types == 2){
            ConcreateSalesConstructionBarChart.data.labels = response['constructionNames20'];
            ConcreateSalesConstructionBarChart.data.datasets[0].data = response['sumConstructions20'];
        }else if(types == 3){
            ConcreateSalesConstructionBarChart.data.labels = response['constructionNames30'];
            ConcreateSalesConstructionBarChart.data.datasets[0].data = response['sumConstructions30'];
        }else if(types == 4){
            ConcreateSalesConstructionBarChart.data.labels = response['constructionNames40'];
            ConcreateSalesConstructionBarChart.data.datasets[0].data = response['sumConstructions40'];
        }
        ConcreateSalesConstructionBarChart.update();
        $('#turnover-date1,#turnover-date2,#charge-of-sales,#station').prop('disabled', false);
        $('.top10-customer-concreate-sales,.top20-customer-concreate-sales,.top30-customer-concreate-sales,.top40-customer-concreate-sales').removeClass('disabled');
        if(types == 1){
            $('.top20-construction-concreate-sales,.top30-construction-concreate-sales,.top40-construction-concreate-sales').removeClass('disabled');
        }else if(types == 2){
            $('.top10-construction-concreate-sales,.top30-construction-concreate-sales,.top40-construction-concreate-sales').removeClass('disabled');
        }else if(types == 3){
            $('.top10-construction-concreate-sales,.top20-construction-concreate-sales,.top40-construction-concreate-sales').removeClass('disabled');
        }else if(types == 4){
            $('.top10-construction-concreate-sales,.top20-construction-concreate-sales,.top30-construction-concreate-sales').removeClass('disabled');
        }
    });
}

$(".top10-construction-concreate-sales").click(function () {
    constructionConcrete(1);
});
//--------------------------------------------------
$(".top20-construction-concreate-sales").click(function () {
    constructionConcrete(2);
});
//--------------------------------------------------
$(".top30-construction-concreate-sales").click(function () {
    constructionConcrete(3);
});
//--------------------------------------------------
$(".top40-construction-concreate-sales").click(function () {
    constructionConcrete(4);
});
//lọc trạm, lọc kahcsh
$('#customer-turnover').on('change', function () {
    let customer = $('#customer-turnover').val();
    let fillterStationSelectCustomerUrl = '/filterstation/customer';
    $.ajax({
        url: fillterStationSelectCustomerUrl,
        type: 'POST',
        data: {
            customer: customer
        },
    }).done(function (response) {
        $('#station').empty();
        $('#station').html(response);
    });

    let fillterSellSelectCustomerUrl = '/filtersales/customer';
    $.ajax({
        url: fillterSellSelectCustomerUrl,
        type: 'POST',
        data: {
            customer: customer
        },
    }).done(function (response) {
        $('#charge-of-sales').empty();
        $('#charge-of-sales').html(response);
    });
});
$('#station').on('change', function () {
    let station = $('#station').val();
    let sale = $('#charge-of-sales').val();
    let customer = $('#customer-turnover').val();
    let fillterSellSelectStationUrl = '/filtersales/station';
    $.ajax({
        url: fillterSellSelectStationUrl,
        type: 'POST',
        data: {
            station: station,
            customer: customer
        },
    }).done(function (response) {
        $('#charge-of-sales').empty();
        if (sale != 0)
            $('#charge-of-sales').append(response).val(sale);
        else
            $('#charge-of-sales').append(response);
    });
});
$('#charge-of-sales').on('change', function () {
    let sales = $('#charge-of-sales').val();
    let station = $('#station').val();
    let fillterStationSelectSellUrl = '/filterstation/sales';
    $.ajax({
        url: fillterStationSelectSellUrl,
        type: 'POST',
        data: {
            sales: sales
        },
    }).done(function (response) {
        $('#station').empty();
        if (station != 0)
            $('#station').append(response).val(station);
        else
            $('#station').append(response);
    });
});
