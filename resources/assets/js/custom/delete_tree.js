$(document).ready(function () {
    function deleteTreeAjax(id) {
        let deleteTreeUrl = URL_DELETE_TREE;
        $.ajax({
            url: deleteTreeUrl,
            method: "POST",
            data: {
                id: id,
            },
        })
            .done(function (data) {
                if (data.success) {
                    location.reload();
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                toastr.error("Lỗi", "Thông báo!");
            });
    }
    // -------------------------------
    function alertDeleteTree(id) {
        Swal.fire({
            title: "Bạn có muốn xoá không?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Xoá",
            confirmButtonClass: "btn btn-danger",
            cancelButtonClass: "btn btn-outline-danger waves-effect waves-light ml-1",
            cancelButtonText: "Huỷ",
            buttonsStyling: true,
        }).then(function (result) {
            if (result.value) {
                deleteTreeAjax(id);
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                e.preventDefault();
            }
        });
    }

    $("body").on("click", ".data-list-view tbody .delete-param", function () {
        let id = $(this).attr("data-id");
        alertDeleteTree(id);
    });
});