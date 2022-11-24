var script = document.createElement('script');
script.src = 'https://code.jquery.com/jquery-3.4.1.min.js';
script.type = 'text/javascript';
var urlSetPassword = '/surveys/users/password/set';
$('#btn-change-password').on('click',function(e){
    var newPassword = $('#account-new-password').val();
    var conNewPassword = $('#account-retype-new-password').val();
    if(newPassword!=""&&conNewPassword!=""){
        if(newPassword==conNewPassword){
            e.preventDefault();
            $.ajaxSetup({
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: urlSetPassword,
                type:'post',
                dataType:'text',
                data:{
                    newPassword: newPassword
                }
                }).done(function(data){
                    if(data==true){
                        Swal.fire(
                            'Thông báo!',
                            'Bạn đã cài lại mật khẩu thành công!',
                            'success'
                        );
                        location.replace('/');
                    }
                    else{
                        Swal.fire(
                            'Thông báo!',
                            'Mật khẩu mới không được trùng với mật khẩu cũ!',
                            'error'
                        );
                    }
                    }).fail(function(){
                    alert('Cài lại mật khẩu thất bại');
                });
        }
    }
});