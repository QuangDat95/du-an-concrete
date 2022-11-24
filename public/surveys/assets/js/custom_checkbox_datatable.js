var script = document.createElement('script');
script.src = 'https://code.jquery.com/jquery-3.4.1.min.js';
script.type = 'text/javascript';
var checkBoxeds;
$(document).ready(function(){
    $('.dropdown-set-role').hide();
    showCheckAllIcon();
    $(document).on('click','.role-check-user',function(){
        if($(this).is(':checked')){
            $('#select-all').prop('checked', true);
            checkBoxeds = true;
            $(".role-check-user").each(function(){
                if($(this).is(':not(:checked)')){
                    checkBoxeds = false;
                }
            });
            if(!checkBoxeds){
                hideCheckAllIcon();
            }
            else{
                showCheckAllIcon();
            }
            $('#button-confirm-set-role').show();
            $('.dropdown-set-role').show();
        }
        else{
            checkBoxeds = false;
            $(".role-check-user").each(function(){
                if($(this).is(':checked')){
                    checkBoxeds = true;
                }
            });
            if(checkBoxeds){
                $('#select-all').prop('checked', true);
                hideCheckAllIcon();
                $('#button-confirm-set-role').show();
                $('.dropdown-set-role').show();
            }
            else{
                $('#select-all').prop('checked', false);
                hideCheckAllIcon();
                $('#button-confirm-set-role').hide();
                $('.dropdown-set-role').hide();
            }
        }
    });
});
function showCheckAllIcon()
{
    $('.checkbox-checkall-checked-icon').show();
    $('.checkbox-checked-icon').hide();
}
function hideCheckAllIcon()
{
    $('.checkbox-checkall-checked-icon').hide();
    $('.checkbox-checked-icon').show();
    
}