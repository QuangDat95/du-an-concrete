jQuery( document ).ready(function($) {
    savePermisstionUser();
});

function savePermisstionUser() {
    $('.checkbox-permissions').change(function() {
        var checkerValue = 0;
        var permisstionId = $(this).attr('permission');
        var url = $('#url-post').val();
        if ($(this).is(':checked'))
            checkerValue = 1;
        var params = {permisstionId: permisstionId, value: checkerValue}
        ajax(url,params);
    });
}
function ajax(url,params){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        type:'POST',
        url: url,
        data: params,
        success:function(data) {
            if(data.success){
                toastr.success('Updated....', 'Permistion');
            }else {
                toastr.error('Update Fail...', 'Error!');
            }
        }
    });
}
