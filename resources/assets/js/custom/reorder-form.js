(function (window, undefined) {
    'use strict';
    var csrf = { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") };
    let ROW_EDIT_OLD = [];
    // $('body').on("keyup", "#table-reorder .reorder_quantity, #table-reorder .price", function (e) {
    //     var that = $(this);
    //     var reorderQty = 0;
    //     var price = 0;

    //     reorderQty = $(this).closest('tr').find('.reorder_quantity').val();
    //     price = $(this).closest('tr').find('.price').val();
    //     var total = reorderQty * price;
    //     var totalFormat = new Intl.NumberFormat('vi-VN', {}).format(total);
    //     that.closest('tr').find('.total-price').html(totalFormat);
    // });

    $('body').on("keyup change", "#form-data-list .transaction-tab input, #form-data-list .transaction-tab select", function (e) {
        window.isChange = true;
        var isDetail = $('.tab-reorder').attr('is-detail');
        e.preventDefault();
        if (isDetail) {
            $('.button-transaction').removeClass('d-none');
            $('.button-transaction').addClass('cssanimation fadeInBottom');
        }
    });

    $('body').on("change", "#select-warehouse_from_id, #select-warehouse_to_id", function (e) {
        var selectName = $(this).attr('id');
        var postURL = '/' + TABLE_NAME + '/local/transfer';
        $.ajax({
            url: postURL,
            method: "POST",
            data: { select_id: $(this).val() },
            type: 'json'
        }).done(function (data) {
            if (data.success) {
                if (selectName == 'select-warehouse_from_id')
                    selectName = 'select-warehouse_to_id'
                else
                    selectName = 'select-warehouse_from_id'
                $('#' + selectName).html(data.optionHTML);
            }
        });
    });

    $('body').on("click", ".action-edit-item", function (e) {
        e.preventDefault();
        var rowId = $(this).closest('tr').find('input[name="row_id"]').val();
        ROW_EDIT_OLD[rowId] = $(this).closest('tr').html();
        $(this).parent().addClass('d-none');
        $(this).closest('th,td').find('.action-submit-edit').removeClass('d-none');
        $(this).closest('tr').find('.select2:not(.station_item_id),input').prop('disabled',false);
        $(this).closest('tr').find('input').css('border','solid 1px #d9d9d9');
    });

    $('body').on("click", ".action-delete-item", function (e) {
        var that = this;
        var params = getValueForm(that);
        deleteReorderItem(params, that);
    });

    $('body').on("click", ".action-approve-item", function (e) {
        var that = this;
        e.preventDefault();
        var params = getValueForm(that);
        $('.error .message').html('');
        editReorderItem(params, that)
    });

    function getValueForm(that) {
        let params = [];
        $(that).closest('tr').find('input,textarea').each(function () {
            var name = $(this).attr('name');
            var value = $(this).val();
            params.push({ name: name, value: value });
        });
        return params;
    }

    // $('body').on("click", "#action-approve-transaction", function (e) {
    //     editTransactions();
    // });
    // $('body').on("click", "#action-cancel-transaction", function (e) {
    //     $('.btn-submit').addClass('d-none');
    //     $('#action-cancel-transaction').addClass('d-none');
    // });

    //ok
    $('body').on("click", ".action-cancel-item", function (e) {
        var rowId = $(this).closest('tr').find('input[name="row_id"]').val();
        var that = this;
        $(this).closest('tr').html(ROW_EDIT_OLD[rowId]);
        displayAction(that)
    });

    //ok
    function displayAction(that) {
        $(that).closest('th,td').find('.action-submit-edit').addClass('d-none');
        $(that).closest('th,td').find('.action-edit-delete').removeClass('d-none');
    }

    //upload image
    $('body').on("change", ".upload-image", function (event) {
        $('.image-thumbnail .row').html('');
        var files = event.target.files;
        var countFiles = $(this)[0].files.length;
        for (var i = 0; i <= countFiles; i++) {
            var image = URL.createObjectURL(files[i]);
            var imageThumbnailHtml = '<img src="' + image + '" class="mr-1 click-image-thumbnail" alt="img placeholder" height="40" width="40" data-toggle="modal" data-backdrop="false" data-target="#backdrop"></div>';
            $('.image-thumbnail .row').append(imageThumbnailHtml);
        }
    });

    // function editTransactions() {
    //     window.isSuccessSubmit = false;
    //     var postURL = '/' + TABLE_NAME;
    //     $('.error .message').html('');
    //     $.ajax({
    //         url: postURL,
    //         method: "POST",
    //         data: new FormData($('#form-data-list-transection')[0]),
    //         type: 'json',
    //         contentType: false,
    //         processData: false
    //     }).done(function (data) {
    //         if (data.success) {
    //             window.isSuccessSubmit = true;
    //             window.isChange = false;
    //             closeSidebar();
    //             var dataTable = $('.dataTable').DataTable();
    //             dataTable.ajax.reload(null, false);
    //             $('#select-all').removeClass('checkbox-all-custom');
    //             toastr.success(data.message, 'Thông báo!');
    //         } else {
    //             toastr.error(data.message, 'Thông báo!');
    //         }
    //     }).fail(function (jqXHR, textStatus, errorThrown) {
    //         var errors = jqXHR.responseJSON.errors;
    //         $('.form-group .error').remove();
    //         $.each(errors, function (index, value) {
    //             $('.row-error .message').html(value);
    //             var errorHtml = document.getElementsByClassName("row-error");
    //             $('input[name="' + index + '"]')
    //                 .after(errorHtml[0].innerHTML);
    //         });
    //         toastr.error('Trường không hợp lệ!', 'Thông báo!');
    //     });
    // }

    function editReorderItem(params, that) {
        var postURL = '/' + TABLE_NAME + '/reorder_items/edit';
        $.ajax({
            url: postURL,
            method: "POST",
            data: params,
            type: 'json'
        }).done(function (data) {
            if (data.success) {
                displayAction(that);
                $(that).closest('tr').find('input[name="row_id"]').val(data.rowId);
                $.each(data.model, function (index, value) {
                    var elm = $('<th class="editer" name="' + index + '[]">' + value + '</th>');
                    $(that)
                        .closest('tr')
                        .find('input[name="' + index + '[]"],textarea[name="' + index + '[]"]')
                        .closest('th,td').replaceWith(elm);
                });
                window.isChange = false;
                toastr.success(data.message, 'Thông báo!');
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            var errors = jqXHR.responseJSON.errors;
            $('.form-group .error').remove();
            $.each(errors, function (index, value) {
                if (index == 'reorder_quantity.0')
                    var message = value.map(function (x) { return x.replace('reorder_quantity.0', '') });
                toastr.error(message, 'Thông báo!');
                let fieldName = index.replace(".0", "[]");
                $(that).closest('tr').find('input[name="' + fieldName + '"]').addClass('error-custom');
            });

        });
    }

    function deleteReorderItem(params, that) {
        var postURL = '/' + TABLE_NAME + '/reorder_items/delete';
        Swal.fire({
            title: 'Bạn có muốn xoá không?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Có',
            confirmButtonClass: 'btn  btn-primary',
            cancelButtonClass: 'btn btn-outline-danger waves-effect waves-light ml-1',
            cancelButtonText: 'Không',
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: postURL,
                    method: "POST",
                    data: params,
                    type: 'json'
                }).done(function (data) {
                    if (data.success) {
                        $(that).closest('tr').remove();
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {

                });
            }
            else if (result.dismiss === Swal.DismissReason.cancel) {
            }
        });

    }
    function closeSidebar() {
        $(".add-new-data").removeClass("show")
        $(".overlay-bg").removeClass("show")
        $('.form-group .error').remove();
        $('.add-data-footer').removeClass('d-flex');
        $('.table-edit .data-items').addClass('data-items-unset');
        $('#action-approve-transaction').addClass('d-none');
        $('#action-cancel-transaction').addClass('d-none');
    }

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.rounded').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#account-upload").change(function () {
        readURL(this);
    });
    $('.click_submit').click(function () {
        $('#upload-image-form').submit();
    });
    // upload image
    $('#upload-image-form').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        var userImageUrl = '/setting/upload/image';
        $.ajax({
            url: userImageUrl,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: csrf
        }).done(function (response) {
            if (response) {
                toastr.success(response.message, 'Cập nhật ảnh thành công!');
                $("#account-img-reset").load(location.href + " #account-img-reset");
            }
        }).fail(function () {
            alert("Định dạng file không phù hợp hoặc dung lượng file quá lớn");
        });
    });
    //set roles
    $('#set-role-users').on('click', function () {
        let id = window.getChecked();
        let name_role = $('input[name="roles"]:checked').val();
        let insertRoleUrl = '/insert-roles';
        var dataTable = $('.dataTable').DataTable();
        $.ajax({
            url: insertRoleUrl,
            type: "POST",
            data: {
                id: id,
                name_role: name_role
            },
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
        }).done(function (response) {
            toastr.success(response.message);
            $('.action-btns .dropdown,.action-btns .dropdown-menu').removeClass('show');
            $('#select-all').removeClass('checkbox-all-custom');
            $('input[name="roles"]').prop('checked', false);
            dataTable.ajax.reload(null, false);
        }).fail(function (response) {
            toastr.error('Bạn chưa chọn người dùng');
        });
    });

    $('input[name="permissions"]').change(function () {
        $('#select-all').removeClass('checkbox-all-custom');
        $('.actions-dropodown').css('display', 'none');
    })
    //permission phân quyền
    $('#set-permission-users').on('click', function () {
        let permission = [];
        $('input[name="permissions"]:checked').each(function () {
            permission.push($(this).val());
        });
        let id = window.getChecked();
        let insertPermisionUrl = '/insert-permissions';
        var dataTable = $('.dataTable').DataTable();
        $.ajax({
            url: insertPermisionUrl,
            type: 'POST',
            data: {
                id: id,
                permission: permission
            },
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") }
        }).done(function (response) {
            toastr.success(response.message);
            $('.action-btns .dropdown,.action-btns .dropdown-menu').removeClass('show');
            $('#select-all').removeClass('checkbox-all-custom');
            $('input[name="permissions"]').prop('checked', false);
            dataTable.ajax.reload(null, false);
        }).fail(function (response) {
            toastr.error('Bạn chưa chọn người dùng');
        });
    });
    //show password
    window.showpass = function () {
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
})(window);
