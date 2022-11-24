$("#show_hide_password a").on('click', function(event) {
    event.preventDefault();
    if($('#show_hide_password input').attr("type") == "text"){
        $('#show_hide_password input').attr('type', 'password');
        $('#show_hide_password #change-eye').addClass( "fa-eye-slash" );
        $('#show_hide_password #change-eye').removeClass( "fa-eye" );
    }else if($('#show_hide_password input').attr("type") == "password"){
        $('#show_hide_password input').attr('type', 'text');
        $('#show_hide_password #change-eye').removeClass( "fa-eye-slash" );
        $('#show_hide_password #change-eye').addClass( "fa-eye" );
    }
});