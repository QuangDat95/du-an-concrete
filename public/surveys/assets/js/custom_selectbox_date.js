var script = document.createElement('script');
script.src = 'https://code.jquery.com/jquery-3.4.1.min.js';
script.type = 'text/javascript';
var show = true;
function showCheckboxes() {
    var checkboxes = 
        document.getElementById("checkBoxes");

    if (show) {
        checkboxes.style.display = "block";
        show = false;
    } else {
        checkboxes.style.display = "none";
        show = true;
    }
}
// $(document).on('click',function(event){
//     var arrChildCheckboxes = $('#checkBoxes').children();
//     if(arrChildCheckboxes.includes(event.target.id)){

//     }
//     else if(show==false&&event.target.id!="over-select"&&event.target.id!="checkBoxes"){
//         var checkboxes = document.getElementById("checkBoxes");
//         checkboxes.style.display = "none";
//         show = true;
//     }
// });
$(document).ready(function(){
    $('#select_all').click(function(){
        $(':checkbox').prop('checked',this.checked);
    });
    $('.group-checkboxes-month').click(function(){
        var value = $(this).val();
        $('.'+value).prop('checked',this.checked);
    });
    $("#search_item").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".item").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    $('.option-parent-year').on('click',function(){
        $('.options-year-2022').slideToggle();
        $('.options-all-days').slideUp();
        // $('.option-parent-year').slideDown();
    });
    $('.option-parent-month').on('click',function(){
        var text = $(this).text();
        
        var monthNumber = text.replace('Tháng ','-');
        $('.options-days'+monthNumber).slideToggle();
    }); 
});
$('.datepicker1,.datepicker2').pickadate({
    editable: true,
    format: 'dd/mm/yyyy',
    monthsFull: [ 'Th 1', 'Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7', 'Th 8', 'Th 9', 'Th 10', 'Th 11', 'Th 12' ],
    selectMonths: true,
    selectYears: true,
    weekdaysShort: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7']
});