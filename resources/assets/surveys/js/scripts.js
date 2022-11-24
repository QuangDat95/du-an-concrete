var urlGetCustomer = '/surveys/ajax/customers';
var urlAddSurveyRecords = '/surveys/users/survey/detail/';
var urlUploadImageUser = '/surveys/users/image/upload';
window.onload = function () {
    hideElement('.class-textarea-reason');
    hideElement('.error-message-hide');
};

$('#select-box-constructions').change(function () {
    //lấy danh sách công trình
    var listConstructions = $('#list-constructions').text();
    //lấy id công trình
    var constructionId = $(this).val();
    //nếu danh sách ct khác '' và id công trình khác rỗng
    if (listConstructions != "" && constructionId != "") {
        //đổi dang json list công trình
        listConstructions = JSON.parse(listConstructions);

        for (var i = 0; i < listConstructions.length; i++) {
            if (listConstructions[i]['id'] == constructionId) {
                $('#input-address').val(listConstructions[i]['address']);
            }
        }
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: urlGetCustomer,
        type: 'post',
        data: {
            construction_id: constructionId,
        },
        success: function (data) {
            if (data.length == 1) {
                var strOption = "";
            }
            else {
                var strOption = "<option value=''>--Chọn khách hàng--</option>";
            }
            for (var i = 0; i < data.length; i++) {
                strOption += "<option value='" + data[i]['id'] + "'>" + data[i]['name'] + "</option>";
            }
            $('#selectbox-list-customer').html(strOption);
        },
        error: function () {
            alert('Lỗi lấy danh sách khách hàng');
        },
        dataType: 'json',
    });
});

$('#form-add-survey-record').on('submit', function (event) {
    event.preventDefault();
    Swal.fire(
        'Cảm ơn',
        'Bạn đã khảo sát thành công',
        'success'
    ).then(function () {
        $('#form-add-survey-record').submit();
    });
});

$('#form-add-survey-record-customer').on('submit', function (event) {
    event.preventDefault();
    Swal.fire(
        'Cảm ơn',
        'Bạn đã khảo sát thành công',
        'success'
    ).then(function () {
        $('#form-add-survey-record-customer').submit();
    });
});

const url_changepassword = location.origin + '/surveys/users/password/change';
var script = document.createElement('script');
script.src = 'https://code.jquery.com/jquery-3.4.1.min.js';
script.type = 'text/javascript';
document.getElementsByTagName('head')[0].appendChild(script);
function showPopover($id_name) {
    $('#' + $id_name).fadeToggle(1000);
}

$('#myForm').on('submit', function (e) {
    loadAjaxChangePassword();
    return false;
});

function loadAjaxChangePassword() {
    hideErrorMessage('error-message-hide');
    var account_old_password = $('#account-old-password').val();
    var account_new_password = $('#account-new-password').val();
    var account_retype_newpassword = $('#account-retype-new-password').val();

    if (account_old_password != account_new_password) {
        $.ajax({
            url: url_changepassword,
            type: 'get',
            data: {
                old_password: account_old_password,
                new_password: account_new_password,
                retype_password: account_retype_newpassword
            },
            success: function (data) {
                if (data == '1') {
                    // $('#success-message').fadeIn();
                    Swal.fire(
                        'Thông báo!',
                        'Thay đổi mật khẩu thành công!',
                        'success'
                    );
                    $('input[type="password"]').reset();
                }
                else if (data == '2') {
                    // $('#error-message4').fadeIn();
                    Swal.fire(
                        'Thông báo!',
                        'Mật khẩu mới phải khác mật khẩu cũ!',
                        'error'
                    );
                }
            },
            error: function (xhr, status, error) {
                alert('Error change password');
            },
            dataType: 'text',
        });
    }
    else if (account_old_password != "" && account_new_password != "") {
        $('#error-message').fadeIn();
    }
}

function showErrorMessage($id_name) {
    $('#' + $id_name).fadeIn();
}

function hideErrorMessage($id_name) {
    $('.' + $id_name).fadeOut();
}

function hideElement($idOrClassName) {
    $($idOrClassName).fadeOut();
}

function showTextAreaReason($index, $value) {
    if ($value == "value_2") {
        $('#textarea-reason-' + $index).show();
    }
    else {
        $('#textarea-reason-' + $index).hide();
    }

}

// Update image - validation
function validateImageUpload() {
    var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
    var size = Math.round(($('#account-upload')[0].files[0].size / 1024));
    // check image file
    if ($.inArray($('#account-upload').val().split('.').pop().toLowerCase(), fileExtension) == -1) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Hình ảnh không hợp lệ'
        });
        $('#account-upload').val('');
        $('.custom-file-label').text('');
        return false;
    }
    // check file size
    if (size > 1024) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Kích thước hình ảnh vượt quá 1024 KB'
        });
        $('#account-upload').val('');
        $('.custom-file-label').text('');
        return false;
    }
    return true;
}

$('#account-upload').on('change', function (e) {
    if (validateImageUpload() == true) {
        var formData = new FormData();
        formData.append('image', e.target.files[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: urlUploadImageUser,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                $("#image-profile-user").attr("src", "/surveys/images/user_images/" + data);
                $('#img-avatar-employee').attr("src", "/surveys/images/user_images/" + data);
                $('#user-new-image').val(data);
                Swal.fire(
                    'Thông báo!',
                    'Bạn đã cập nhật ảnh đại diện thành công!',
                    'success'
                );
            },
            error: function () {
                alert('Lỗi tải ảnh');
            },
        });
    }
});

$('#form-make-link-survey').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData($("#form-make-link-survey")[0]);
    let makeLinkUrl = '/surveys/users/make-link/request';
    $.ajax({
        url: makeLinkUrl,
        type: 'post',
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    }).done(function (response) {
        if(response.error){
            Swal.fire(
                'Thông báo!',
                response.message,
                'error'
            );
        }else{
            $('#show-link-survey').empty();
            $('#show-link-survey').val(response);
        }
        
    });
});

var userText = $("#show-link-survey");
var btnCopy = $("#btn-copy-survey-link");

btnCopy.on("click", function () {
  userText.select();
  document.execCommand("copy");
});