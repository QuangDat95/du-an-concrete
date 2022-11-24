$(document).ready(function() {
    $('.action-btn-custom').hide();
    var actionDropdown = $(".actions-dropodown")
    actionDropdown.insertAfter($(".top .actions .dt-buttons"));
    $('#select-all').click(function(event) {
        event.stopPropagation();
        if(this.checked) {
            $('tbody tr').addClass('selected');
            $(':checkbox').each(function() {
                this.checked = true;
            });
        } else {
            $('tbody tr').removeClass('selected');
            $(':checkbox').each(function() {
                this.checked = false;
            });
        }
        disableAction(getChecked());
    });

    $('body').on("click", ".dataTable tbody tr", function(evt) {
        var checkClass = $(this).hasClass('selected');
        var $cell=$(evt.target).closest('td');
        if( $cell.index()>0){
            $(':checkbox').each(function() {
                this.checked = false;
            });
            $('.selected').removeClass('selected');
        }
        if(checkClass){
            $(this).removeClass('selected');
            $(this).find('.select-param').prop('checked', false);
        }else{
            $(this).addClass('selected');
            $(this).find('.select-param').prop('checked', true);
        }
        getCheckedAll();
        disableAction(getChecked());

    });

    function getChecked(){
        var checkBoxeds = []
        $(".dataTable tbody input[type=checkbox]:checked").each(function(){
            var value = $(this).val();
            if(value != 0)
                checkBoxeds.push(value);
        });
        return checkBoxeds;
    }
    function disableAction(checkBoxeds) {
        if(checkBoxeds.length)
            $('.action-btn-custom').show();
        else{
            $('.action-btn-custom').hide();
            $('#select-all').prop('checked', false);
        }

    }

    function checkCountSelectBox(){
        var checkBoxed = 0;
        $(".dataTable tbody input[type=checkbox]:checked").each(function(){
            checkBoxed++;
        });
        return checkBoxed;
    }
    function getCheckedAll(){
        var checkBoxed = 0;
        $(".dataTable tbody input[type=checkbox]").each(function(){
            checkBoxed++;
        });
        var checked = checkCountSelectBox();

        if(checkBoxed > checked)
            $('#select-all').prop('checked', false);
        else if(checkBoxed == checked)
            $('#select-all').prop('checked', true);
    }
});