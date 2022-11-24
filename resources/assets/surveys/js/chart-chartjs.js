/*=========================================================================================
    File Name: chart-chartjs.js
    Description: Chartjs Examples
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
// var urlGetGoodResponse = location.origin+ '/ajax/survey_detail/';
var arrDoughnutChart = [];
var arrBarChart = [];
var arrPieChart = [];
var lineChart;
var dataTableSurveyDetail;
const unique = (value, index, self) => {
  return self.indexOf(value) === index
}
$(window).on("load", function () {
});
$('#idea-tab-fill,#button-filter-survey-records,#statistic-tab-fill').click(function(){
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
          text: 'Bạn chưa chọn ngày'
        });
        return false;
  }else if(startDay.length != 0 && endDay.length != 0){
  getDataOfStatisticChart(selectboxChooseSurvey,startDay,endDay);
  if(dataTableSurveyDetail==undefined){
    loadDataTableSurveyDetail(selectboxChooseSurvey,startDay,endDay);
  }
  else{
    reloadDataTableSurveyDetail(selectboxChooseSurvey,startDay,endDay);
  }
}
});
function getDataOfStatisticChart(selectboxChooseSurvey,startDay,endDay)
{
  var arrSelectBoxSurveyValue = [];
  for(var i = 0 ; i< selectboxChooseSurvey.length ; i++){
    arrSelectBoxSurveyValue.push(selectboxChooseSurvey[i]);
  }
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  if(selectboxChooseSurvey==""){
    $('#number-survey-records').text("");
    $('#number-customers').text("");
  }
  else{
    $.ajax({
      url: '/surveys/ajax/statistic',
      type: 'post',
      data:{
        surveyId : arrSelectBoxSurveyValue,
        startDay: startDay,
        endDay: endDay,
      },
      success:function(data){
        if(data!=""){
          $('#number-survey-records').text(data['number'][0]);
          $('#number-customers').text(data['number'][1]);
          showStatisticSemiCircle(data['number'][2],data['number'][1],data['number'][3],data['number'][4]);
          showStatiscticBarChart(data['list_answers'],data['list_survey_detail'],data['list_questions']);
          showStatisticPieChart(data['list_surveys'],data['list_all_survey_record'],data['list_survey_detail']);
          showStatisticLineChart(data['list_survey_records'],data['list_survey_detail'],data['list_answers']);
        }
        else{
          alert('dữ liệu rỗng');
        }
      },
      error:function(xhr,status,error){
        alert('lấy dữ liệu thất bại');
      },
      dataType: 'json',
    });
  }
}
function showStatisticSemiCircle(totalNumberCustomers,numberCustomerJoinSurvey,numberCustomersThisMonth,numberCustomersPreviosMonth)
{
  var themeColor1 = ['#4da6ff','#999999'];
  var themeColor2 = ['#4dff4d','#999999'];
  var themeColor = [];
  themeColor[1] = themeColor1;
  themeColor[2] = themeColor2;
  var minusCustomers = totalNumberCustomers-numberCustomerJoinSurvey;
  var divideCustomers = numberCustomersThisMonth/numberCustomersPreviosMonth*100;
  var minusDivideCustomers = 100-divideCustomers;
  var arrCustomer1 = [numberCustomerJoinSurvey,minusCustomers];
  var arrCustomer2 = [divideCustomers,minusDivideCustomers];
  var arrCustomer = [];
  arrCustomer[1] = arrCustomer1;
  arrCustomer[2] = arrCustomer2;
  var arrLabel = [];
  arrLabel[1] = ["Số khách hàng tham gia khảo sát","Số khách hàng chưa tham gia khảo sát"];
  arrLabel[2] = ["Tỉ lệ khách hàng tham gia khảo sát","Tỉ lệ khách hàng chưa tham gia khảo sát"];
  for(var i = 1 ; i <=2 ; i++){
    var doughnutChartctx = $("#simple-doughnut-chart"+i);
    if(i==1){
      var doughnutchartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 500,
        title: {
          display: false,
          text: 'Predicted world population (millions) in 2050'
        },
        rotation: 1 * Math.PI,
        circumference: 1 * Math.PI,
        legend:{
          display: false,
          position:'bottom',
          align: "start",
          fullSize:true,
          labels:{
            textAlign:"left",
            color: 'rgb(255, 99, 132)'
          }
        },
        tooltips:{
          callbacks:{
            label: function(tooltipItem, data) {
              var allData = data.datasets[tooltipItem.datasetIndex].data;
              var tooltipLabel = data.labels[tooltipItem.index];
              var tooltipData = allData[tooltipItem.index];
              return tooltipLabel + ': ' + tooltipData + ' người';
            },
          }
        }
      };
    }
    else{
      var doughnutchartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 500,
        title: {
          display: false,
          text: 'Predicted world population (millions) in 2050'
        },
        rotation: 1 * Math.PI,
        circumference: 1 * Math.PI,
        legend:{
          display: false,
          position:'bottom',
          align: "start",
          fullSize:true,
          labels:{
            textAlign:"left",
            color: 'rgb(255, 99, 132)'
          }
        },
        tooltips:{
          callbacks:{
            label: function(tooltipItem, data) {
              var allData = data.datasets[tooltipItem.datasetIndex].data;
              var tooltipLabel = data.labels[tooltipItem.index];
              var tooltipData = allData[tooltipItem.index];
              return tooltipLabel + ': ' + tooltipData + '%';
            },
          }
        }
      };
    }
    var doughnutchartData = {
      labels: arrLabel[i],
      datasets: [{
        label: "My First dataset",
        data: arrCustomer[i],
        backgroundColor: themeColor[i],
        hoverOffset: 4,
      }]
    };
    var doughnutChartconfig = {
      type: 'doughnut',
      options: doughnutchartOptions,
      data: doughnutchartData
    };
    doughnutSimpleChart = new Chart(doughnutChartctx,doughnutChartconfig);
    arrDoughnutChart.push(doughnutSimpleChart);
  }
}
function showStatiscticBarChart(listAnswersKeys,listSurveysDetail,listQuestions)
{
  var $primary = '#4d94ff';
  var $danger = '#EA5455';
  var grid_line_color = '#dae1e7';  
  var themeCorlors1 = [$primary,$danger];
  var themeColors2 = ['#0000ff','#66b3ff'];
  var grid_line_color = '#dae1e7';
  var countGoodResponses;
  var countBadResponses;
  var arrLabel1 = [];
  var arrDataGoodResponses1 = [];
  var arrDataBadResponses1= [];
  var answerKey;
  for(var i = 0 ; i <listAnswersKeys.length; i++)
  {
    if(listAnswersKeys[i]['sort']!=8&&listAnswersKeys[i]['sort']!=13&&listAnswersKeys[i]['sort']!=14)
    {
      answerKey = listAnswersKeys[i]['key'];
      answerKey = answerKey.substring(0,10)+'...';
      arrLabel1.push(answerKey);
      countGoodResponses = getCountResponsesByAnswerId(listSurveysDetail,'value_1',listAnswersKeys[i]['id']);
      countBadResponses = getCountResponsesByAnswerId(listSurveysDetail,'value_2',listAnswersKeys[i]['id']);
      arrDataGoodResponses1.push(countGoodResponses);
      arrDataBadResponses1.push(countBadResponses);
    }   
  }
  var arrLabel2 = [];
  var arrDataGoodResponses2 = [];
  var arrDataBadResponses2 = [];
  var questionName;
  for(var i = 0 ; i<listQuestions.length; i++){
    if(listQuestions[i]['sort']!=5&&listQuestions[i]['sort']!=10&&listQuestions[i]['sort']!=11){
      questionName = listQuestions[i]['name'];
      questionName = questionName.substring(0,15)+'...';
      arrLabel2.push(questionName);
      arrDataGoodResponses2.push(getCountResponsesByQuestionId(listSurveysDetail,'value_1',listQuestions[i]['id']));
      arrDataBadResponses2.push(getCountResponsesByQuestionId(listSurveysDetail,'value_2',listQuestions[i]['id']));
    }
  }
  var themeColors = [[],themeCorlors1,themeColors2];
  var arrLabel = [[],arrLabel1,arrLabel2];
  var arrDataGoodResponses = [[],arrDataGoodResponses1,arrDataGoodResponses2];
  var arrDataBadResponses = [[],arrDataBadResponses1,arrDataBadResponses2];
  var arrDatasetsLabel = [[],['Đạt','Không đạt'],['Tích cực','Tiêu cực']];
  for(var i = 1 ; i<=2;i++){
    var barChartctx = $("#bar-chart-"+i);
    var barchartOptions = {
      elements: {
        rectangle: {
          borderWidth: 1,
          borderSkipped: 'left'
        }
      },
      responsive: true,
      maintainAspectRatio: false,
      // responsiveAnimationDuration: 500,
      categoryPercentage: 1,
      plugins:{
        datalabels: {
          color: 'black',
          anchor: "end",
          align: "right",
          offset: 10,
          display: function (context) {
              return context.dataset.data[context.dataIndex];
          },
        }
      },
      legend: { 
        display: true,
        position:'bottom',
        labels:{
          // fontSize:10,
        } 
      },
      scales: {
        xAxes: [{
          display: true,
          stacked:true,
          gridLines: {
            color: grid_line_color,
          },
          scaleLabel: {
            display: true,
          },
          ticks:{
            // fontSize:10,
          }
        }],
        yAxes: [{
          display: true,
          stacked:true,
          gridLines: {
            color: grid_line_color,
          },
          scaleLabel: {
            display: false,
            labelString: 'Số câu trả lời',
          },
          ticks: {
            max: 100,
            steps: 10,
            stepValue:10,
            beginAtZero:true,
            // fontSize:10,
          },
        }],
      },
      title: {
        display: false,
        text: 'Predicted world population (millions) in 2050'
      },

    };
    var barchartData = {
      labels: arrLabel[i],
      datasets: [{
        label: arrDatasetsLabel[i][0],
        data: arrDataGoodResponses[i],
        backgroundColor: themeColors[i][0],
        borderColor: "transparent"
      },
      {
        label: arrDatasetsLabel[i][1],
        data: arrDataBadResponses[i],
        backgroundColor: themeColors[i][1],
        borderColor: "transparent"
      }
    ],
    };

    var barChartconfig = {
      type: 'bar',

      // Chart Options
      options: barchartOptions,
      data: barchartData
    };

    // Create the chart
    var barChart = new Chart(barChartctx, barChartconfig);
    arrBarChart.push(barChart);
  }
  
}
function showStatisticPieChart(listSurveys,listAllSurveyRecords,listSurveysDetail)
{
  var themeColors = [[],['#ff4dd2','#e6e600'],['#00e600','#ffa31a']];
  var numberSurveys;
  var arrNumberSurveys= [];
  var arrNumberAnswers = [];
  var sumNumberSurveys = 0;
  for(var i = 0 ; i < listSurveys.length ; i++)
  {
    numberSurveys = getNumberSurveyBySurveyId(listAllSurveyRecords,listSurveys[i]['id']);
    arrNumberSurveys.push(numberSurveys);
    sumNumberSurveys += numberSurveys;
  }
  for(var i = 1 ; i<=2 ; i++)
  {
    arrNumberAnswers.push(getNumberAnswer(listSurveysDetail,'value_'+i));
  }
  var arrLabel = [[],["Phiếu đánh giá chất lượng dịch vụ 1","Phiếu đánh giá chất lượng dịch vụ 2"],['Tích cực','Tiêu cực']];
  var arrData = [[],arrNumberSurveys,arrNumberAnswers];
  for(var i = 1 ; i<= 2; i++){
    var pieChartctx = $("#pie-chart-"+i);
    var piechartOptions = {
      responsive: true,
      maintainAspectRatio: false,
      responsiveAnimationDuration: 500,
      title: {
        display: false,
        text: 'Predicted world population (millions) in 2050'
      },
      legend:{
        position:'bottom',
      },
      tooltips:{
        callbacks:{
          label: function(tooltipItem, data) {
            var allData = data.datasets[tooltipItem.datasetIndex].data;
            var tooltipLabel = data.labels[tooltipItem.index];
            var tooltipData = allData[tooltipItem.index];
            var total = 0;
            for (var i in allData) {
              total += allData[i];
            }
            var tooltipPercentage = Math.round((tooltipData / total) * 100);
            return tooltipLabel + ': ' + tooltipData + ' (' + tooltipPercentage + '%)';
          },
          // afterLabel: function(tooltipItem, data) {
          //   var item = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
          //   return '(' + item + '%)';
          // }
        }
      }
    };
    var piechartData = {
      labels: arrLabel[i],
      datasets: [{
        label: 'Dataset 1',
        data: arrData[i],
        backgroundColor: themeColors[i],
      }]
    };

    var pieChartconfig = {
      type: 'pie',
      options: piechartOptions,

      data: piechartData
    };
    var pieSimpleChart = new Chart(pieChartctx, pieChartconfig);
    arrPieChart.push(pieSimpleChart);
  }
  
}
function showStatisticLineChart(listSurveysRecords,listSurveysDetail,listAnswersKeys)
{
  var $primary = '#4d94ff';
  var $danger = '#EA5455';
  var grid_line_color = '#dae1e7';
  var $success ='#28C76F';
  var $warning ='#FF9F43';
  var $label_color = '#1E1E1E';
  var $pink_color = '#ff4dc4';
  var $yellow_color = '#ffff00';
  var $gray_color='#737373';
  var $violet_color='#9900ff';
  var $brown_color='#ac7339';
  var $blue_color = '#4dd2ff';
  var themeColors = [$primary,$danger,$success,$warning,$label_color,$pink_color,$yellow_color,'',$violet_color,$brown_color,$blue_color,$gray_color];
  var arrRatioXAxisChart = ['20%','40%','60%','80%','100%'];
  var dayTime;
  var arrDays = [];
  var arrUniqueDays = [];
  var arrDaysDisplay = [];
  var arrUniqueDaysDisplay = [];
  var arrAnswersKeys = [];
  var arrRatioAnswerResponses = [];
  var arrRatioAnswerResponses1= [];
  for(var i = 0 ; i<listSurveysRecords.length; i++){
    dayTime = new Date(listSurveysRecords[i]['created_at']);
    arrDays.push(dayTime.getDate()+"/"+dayTime.getMonth()+"/"+dayTime.getFullYear());
    arrDaysDisplay.push(dayTime.getDate()+"/"+(dayTime.getMonth()+1)+"/"+dayTime.getFullYear());
  }
  arrUniqueDays = arrDays.filter(unique);
  // arrUniqueDays = arrUniqueDays.sort();  
  arrUniqueDaysDisplay = arrDaysDisplay.filter(unique);
  // arrUniqueDaysDisplay = arrUniqueDaysDisplay.sort();  
  for(var i = 0 ; i< listAnswersKeys.length ; i++){
    if(listAnswersKeys[i]['sort']!="8"&&listAnswersKeys[i]['sort']!="13"&&listAnswersKeys[i]['sort']!="14"){
      for(var j = 0 ; j<arrUniqueDays.length ; j++){
        arrRatioAnswerResponses1.push(getRatioAnswerKeyByDay(listSurveysDetail,listAnswersKeys[i]['id'],arrUniqueDays[j]));
      }
      arrRatioAnswerResponses.push(arrRatioAnswerResponses1);
      arrRatioAnswerResponses1 = [];
    }
    else{
      arrRatioAnswerResponses.push(arrRatioAnswerResponses1);
    }
  }
  var arrDataSets=[];
  var dataSet=[];
  for(var i = 0 ; i< listAnswersKeys.length ; i++){
    if(listAnswersKeys[i]['sort']!="8"&&listAnswersKeys[i]['sort']!="13"&&listAnswersKeys[i]['sort']!="14"){
      dataSet = { 
        label: listAnswersKeys[i]['key'],
        data: arrRatioAnswerResponses[i],
        borderColor: themeColors[i],
        fill: false
      };
      arrDataSets.push(dataSet);
      dataSet = [];
    }
  }
  var lineChartctx = $("#line-chart");
  var linechartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    // responsiveAnimationDuration: 500,
    legend: {
      position: 'bottom',
    },
    hover: {
      mode: 'label'
    },
    scales: {
      xAxes: [{
        display: true,
        gridLines: {
          color: grid_line_color,
        },
        scaleLabel: {
          display: true,
          labelString: 'Thời gian',
        }
      }],
      yAxes: [{
        display: true,
        gridLines: {
          color: grid_line_color,
        },
        scaleLabel: {
          display: false,
          labelString: 'Tỉ lệ'
        },
        ticks:{
          max: 100,
          steps: 10,
          stepValue:10,
          beginAtZero:true,
          callback:function(value,index,ticks){
            return value + "%";
          }
        }
      }],
    },
    title: {
      display: false,
      text: 'World population per region (in millions)'
    }
  };
  var linechartData = {
    labels: arrUniqueDaysDisplay,
    datasets: arrDataSets,
  };

  var lineChartconfig = {
    type: 'line',

    // Chart Options
    options: linechartOptions,

    data: linechartData
  };

  // Create the chart
  lineChart = new Chart(lineChartctx, lineChartconfig);
  
}
function getCountResponsesByAnswerId($arrSurveyDetails,$value,$answerId)
{
  var count = 0;
  for( var i = 0 ;i<$arrSurveyDetails.length ; i++){
    if($arrSurveyDetails[i]['value']==$value&&$arrSurveyDetails[i]['answer_id']==$answerId){
      count++;
    }    
  }
  return count;
}
function getNumberSurveyBySurveyId($listSurveyRecords,$survey_id)
{
  var count = 0;
  for( var i = 0 ;i<$listSurveyRecords.length ; i++){
    if($listSurveyRecords[i]['survey_id']==$survey_id){
      count++;
    }
  }
  return count;
}
function getNumberAnswer($listSurveysDetail,$value)
{
  var count = 0;
  for(var i = 0 ; i < $listSurveysDetail.length ; i++){  
    if($listSurveysDetail[i]['value']==$value){
      count++;
    }
  }
  return count;
}
function getCountResponsesByQuestionId($arrSurveyDetails,$value,$questionId)
{
  var count = 0;
  for( var i = 0 ;i<$arrSurveyDetails.length ; i++){
    if($arrSurveyDetails[i]['value']==$value&&$arrSurveyDetails[i]['question_id']==$questionId){
      count++;
    }    
  }
  return count;
}
function getRatioAnswerKeyByDay($listSurveysDetail,$answerId,$day)
{
  var dayTimeSurveyDetail;
  var daySurveyDetail;
  var count = 0;
  var sum = 0;
  for(var i = 0 ; i < $listSurveysDetail.length; i++)
  {
    if($listSurveysDetail[i]['sort']!="8"&&$listSurveysDetail[i]['sort']!="13"&&$listSurveysDetail[i]['sort']!="14"){
      dayTimeSurveyDetail = new Date($listSurveysDetail[i]['created_at']);
      daySurveyDetail = dayTimeSurveyDetail.getDate()+"/"+dayTimeSurveyDetail.getMonth()+"/"+dayTimeSurveyDetail.getFullYear();
      if($listSurveysDetail[i]['answer_id']==$answerId&&daySurveyDetail==$day){
        if($listSurveysDetail[i]['value']=='value_1'){
          count++;
        }
        sum++;
      }
    }
  }
  if(sum==0){
    return 0;
  }
  else{
    return count/sum*100;
  }
}
function loadDataTableSurveyDetail(selectboxChooseSurvey,startDay,endDay)
{
  var arrSelectBoxSurveyValue = [];
    for(var i = 0 ; i< selectboxChooseSurvey.length ; i++){
        arrSelectBoxSurveyValue.push(selectboxChooseSurvey[i]);
    }
    if(selectboxChooseSurvey==""){
      alert('Dữ liệu rỗng');
      dataTableSurveyDetail = $('#table-other-opinions').DataTable();
    }
    else{
      dataTableSurveyDetail = $('#table-other-opinions').DataTable({
        "maxLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "processing": true,
        "serverSide": true,
        ajax: {
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            url: "/surveys/users/survey/detail",
            type: 'post',
            data:{
                survey: arrSelectBoxSurveyValue,
                startDay: startDay,
                endDay: endDay,
            }
        },
        columns:[
            {data : 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'customer_name', name: 'customer_name'},
            {data: 'construction_name', name: 'construction_name'},
            {data: 'created_at',name:'created_at'},
            {data: 'answer.key', name: 'answer_key'},
            {data: 'value', name: 'value'},
        ]
      });
    }
}
function reloadDataTableSurveyDetail(selectboxChooseSurvey,startDay,endDay)
{
  dataTableSurveyDetail.clear();
  dataTableSurveyDetail.destroy();
  loadDataTableSurveyDetail(selectboxChooseSurvey,startDay,endDay);
}
function removeDataOfChart() 
{
  for(var i = 0 ; i < arrBarChart.length ; i++){
    arrDoughnutChart[i].destroy();
  }
  for(var i = 0 ; i < arrBarChart.length ; i++){
    arrBarChart[i].destroy();
  }
  for(var i = 0 ; i <arrPieChart.length; i++){
    arrPieChart[i].destroy();
  }
  lineChart.destroy();
}
function changeStatisticSurvey(selectboxChooseSurvey,startDay,endDay)
{
  removeDataOfChart();
  getDataOfStatisticChart(selectboxChooseSurvey,startDay,endDay);
}
$("#button-filter-survey-records,#statistic-tab-fill").click(function(){
  var startDay = $('.datepicker1').val();
    var endDay = $('.datepicker2').val();
  var selectboxChooseSurvey = $('#selectbox-choose-survey').val();
  var arrFilterDays = [];

  $(":checkbox[name='filter-day-value']:checked").each(function(){
    arrFilterDays.push($(this).val());
  });
  var checkVariable = $('#check-variable').val();
  if(checkVariable=="1"){
    reloadDataTableSurveyRecords(selectboxChooseSurvey,startDay,endDay);
  }
  else{
    changeStatisticSurvey(selectboxChooseSurvey,startDay,endDay);
    reloadDataTableSurveyDetail(selectboxChooseSurvey,startDay,endDay);
  }
});