$(document).ready(function () {
    $("body").on("click", "#button-editer", function (e) {
        if (USER_ROLE_NAME != 'accountant') {
            $("#form-data-list input,#form-data-list select,#form-data-list textarea,#add-new-receipt,#delete-receipt")
                .not('input[name="total_price"],input[name="payment_volume"],\
                #select-user_id,#object_name,#object_address,#created_by,.receivable,\
                .uncollected,.station_item_id,.payment_term,.action-edit-item,.action-delete-item')
                .prop("disabled", false);
            $(".add-data-footer").addClass("d-flex cssanimation fadeInBottom");
            $(".table-edit .data-items").removeClass("data-items-unset");
            $('.icon-date-from_date').css('display', 'inline');
            $('.icon-date-due_date').css('display', 'inline');
            $('.icon-date-received_date').css('display', 'inline');
            $('.amount,.description_payment_item').css('border', 'solid 1px #d9d9d9');
        } else {
            $('input[name="due_date"],input[name="vat_flag"],select[name="company_id"],\
            select[name="debit_account_1_id"],select[name="credit_account_1_id"],\
            select[name="debit_account_2_id"],select[name="credit_account_2_id"],\
            input[name="revenue_entry_amount"],input[name="description_revenue"],\
            input[name="tax_entry_amount"],input[name="description_tax"]').prop('disabled', false);
            $('.icon-date-due_date').css('display', 'inline');
            $(".add-data-footer").addClass("d-flex cssanimation fadeInBottom");
            $(".table-edit .data-items").removeClass("data-items-unset");
        }
        if (TABLE_NAME == 'volume_trackings') {
            window.parent.checkVatflag();
        }
    });

    $(".datepicker").pickadate({
        editable: true,
        format: "dd-mm-yyyy"
    });

    $(".datepicker").off("click focus");
    $("body").on("click", ".datepicker-icon", function (e) {
        $picker = $(this).closest(".form-group").find(".datepicker");
        e.stopPropagation();
        e.preventDefault();
        picker = $picker.data("pickadate");
        picker.open();
    });

    if ($(".table-edit").find("#basic-tabs-components").length !== 0) {
        $(".data-items").addClass("data-items-bottom-fixed");
    }
    // Add filtering
    $(".is-filter-grid").on("change", function () {
        if ($(this).is(":checked")) $(".filters").removeClass("d-none");
        else $(".filters").addClass("d-none");
    });

    var rowHtml = "<tr>";
    $(".table thead tr:first th").each(function (index) {
        var style = $(this).attr("style");
        var filterDisable = "";
        if ($(this).hasClass("filter-disable")) filterDisable = "filter-disable";
        rowHtml += '<th style="' + style + ' d-none" class="' + filterDisable + '"></th>';
    });

    rowHtml += "</tr>";
    $(".filters").html(rowHtml);
    var filters = $(".filters");
    filters.insertAfter($(".table thead tr"));

    window.isChange = false;
    $("#form-data-list input,#form-data-list select").on("keyup change", function () {
        $(".add-data-footer").addClass("d-flex cssanimation fadeInBottom");
        $(".table-edit .data-items").removeClass("data-items-unset");
        window.isChange = true;
    });

    $(document).on("keyup", function (e) {
        if (e.key == "Escape") {
            $(".hide-data-sidebar").click();
        }
    });
    insertElement();
    defaultGrid();
    getIdSelectAll();
    ajaxSetup();
    clickButtonAddNew();
    showTabEditer();
    submitDataForm();
    clickSelectAll();
    clickDeleteParams();
    disableSorting();
    clickApprove();
    imageThumbnail();
    //Functions
    function removeElementMeasuring() {
        document.querySelectorAll(".measuring-unit-conversion").forEach((el) => el.remove());
    }

    function insertElement() {
        var actionDropdown = $(".actions-dropodown");
        actionDropdown.insertAfter($(".top .actions .dt-buttons"));

        var actionWarehousehLocation = $("#warehouse-location");
        actionWarehousehLocation.insertBefore($(".top .dt-buttons"));

        var exportBtn = $("#export-btn");
        exportBtn.insertAfter($(".top .dt-buttons"));

        var filterOnOff = $(".filter-on-off");
        filterOnOff.insertAfter($("#dataTableBuilder_filter"));
    }

    function defaultGrid() {
        if (TABLE_NAME != "gl_accounts") {
            $(".table").addClass("data-list-view");
        }
        $(".row-error").hide();
        $(".action-btns .actions-dropodown").hide();

        $("body").on("click", ".action-edit", function (e) {
            e.stopPropagation();
            $(".add-new-data").addClass("show");
            $(".overlay-bg").addClass("show");
            removeElementMeasuring();
        });
        // Scrollbar
        if ($(".data-items").length > 0) {
            new PerfectScrollbar(".data-items", { wheelPropagation: false });
        }
        // Close sidebar
        $(".hide-data-sidebar, .cancel-data-btn, .overlay-bg").on("click", function (e) {
            if (window.isChange) {
                alertsChange(e);
                return;
            }
            closeSidebar();
        });
        // mac chrome checkbox fix
        if (navigator.userAgent.indexOf("Mac OS X") != -1) {
            $(".dt-checkboxes-cell input, .dt-checkboxes").addClass("mac-checkbox");
        }
    }

    function imageThumbnail() {
        $(".upload-image").on("change", function (event) {
            $(".image-thumbnail .row").html("");
            var files = event.target.files;
            var countFiles = $(this)[0].files.length;
            for (var i = 0; i <= countFiles; i++) {
                var image = URL.createObjectURL(files[i]);
                var imageThumbnailHtml = '<img src="' + image + '" class="mr-1 click-image-thumbnail" \
                alt="img placeholder" height="100" width="100" data-toggle="modal" data-backdrop="false" data-target="#backdrop"></div>';
                $(".image-thumbnail .row").append(imageThumbnailHtml);
            }
        });
    }

    function clickApprove() {
        $("body").on("click", "#button-approve", function (e) {
            var rowId = $(this).attr("param-id");
            alertsApprove(e, rowId);
        });
    }

    function submitDataForm() {
        $(".add-new-btn").attr("route", TABLE_NAME);
        $("#btn-submit").on("click", function () {
            var urlPOST = $("#form-data-list").attr("action");
            addNewAjax(urlPOST);
        });
    }

    function getIdSelectAll() {
        $('.data-list-view thead tr th:first-child input[type="checkbox"]').attr("id", "select-all");
    }

    function ajaxSetup() {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
    }

    function clickButtonAddNew() {
        $("body").on("click", ".action-btns .add-new-btn,.content-header .objective-create-btn", function (e) {
            $(".add-new-data,.overlay-bg").addClass("show");
            if (TABLE_NAME == "payment_conditions") {
                $(".pour_date").css("display", "none");
            }
            $(".add-data-footer").removeClass("d-flex cssanimation fadeInBottom");
            $(".error .message").html("");
            if (TABLE_NAME == 'transaction_entries') {
                window.parent.loadStationTransactionEntry();
            }
            if (TABLE_NAME == "organizations") {
                window.parent.loadOrganizationAjax();
            }
            if (TABLE_NAME == "gl_accounts") {
                window.parent.loadGlAccountAjax();
                $("#customer_flag").attr("checked", false);
            }
            $('#receipt-old').val('');
            $("#code-old").val("");
            $("#name-old").val("");
            $("#address-company-old").val("");
            $("#tax-old").val("");
            $("#email-old").val("");
            $("#account-number-old").val("");
            $("#bank-old").val("");
            $("#account-code-old").val("");
            $("#nature-old").val("");
            $(".data-items").show();
            $(".import-volumetrack").removeClass("show-import-volume");
            $("#basic-tabs-components").show();
            $(".import-volumetrack").css("display", "none");
            $(".edit-volumetracking").addClass("text-uppercase");
            if (TABLE_NAME == "volume_trackings") {
                $(".add-new-data .title").html("Thêm mới khối lượng");
            } else if (TABLE_NAME == "receipts") {
                $(".add-new-data .title").html("Phiếu thu");
            } else if (TABLE_NAME == "payments") {
                $(".add-new-data .title").html("Phiếu chi");
            } else if (TABLE_NAME == "debits") {
                $(".add-new-data .title").html("Báo nợ");
            } else if (TABLE_NAME == 'alerts') {
                $('.add-new-data .title').html("Báo có");
            } else {
                $(".add-new-data .title").html("Thêm");
            }
            if (TABLE_NAME == "users") {
                $(".add-data-footer").removeClass("d-flex cssanimation fadeInBottom");
                $(".table-edit .data-items").addClass("data-items-unset");
                window.isChange = false;
            }
            $(".table-edit .sidebar-actions").hide();
            $(".table-edit .data-items").addClass("data-items-unset");
            $("#form-data-list select").not("#select-area_id,#select-user_id").val("").trigger("change.select2");
            $("#form-data-list").trigger("reset");
            if (TABLE_NAME == "volume_trackings") {
                $('#vat_flag').prop('checked', false);
                window.parent.loadCustomer();
                window.parent.loadConstruction();
                window.parent.loadContract();
                window.parent.checkAddVolumeTracking();
                window.parent.checkVatflag();
            } else {
                $("#form-data-list input,#form-data-list select,#form-data-list textarea,#add-new-receipt").prop("disabled", false);
            }
            if (TABLE_NAME == "receipts" || TABLE_NAME == "payments" || TABLE_NAME == "debits" || TABLE_NAME == "alerts") {
                window.parent.emptySelect();
                $("#created_by").val(USER_ID).trigger("change.select2");
                $("#created_by,#object_name,#object_address").prop("disabled", true);
                $(".delete-receipt").closest("tr").remove();
                var rowCount = $("#receipt-table tbody tr").length;
                $('input[name="rowCount"]').val(rowCount);
                $('#sum_payment_item').html('0<sup>đ</sup>');
            }

            $("#form-data-list").attr("action", $(this).attr("route"));
            $(".form-group .error").remove();
            $(".click-image-thumbnail").addClass("d-none");
        });
    }

    function showTabEditer() {
        if (TABLE_NAME != "gl_accounts" && TABLE_NAME != "organizations") {
            $("body").on("click", ".data-list-view:not('#receipt-table') tbody tr", function (evt) {
                if (TABLE_NAME == "receipts" || TABLE_NAME == "payments" || TABLE_NAME == "debits" || TABLE_NAME == "alerts") {
                    $(".delete-receipt").closest("tr").remove();
                }

                if (TABLE_NAME == 'volume_trackings') {
                    $('.icon-date-due_date').css('display', 'none');
                    $('.icon-date-from_date').css('display', 'none');
                    $('.icon-date-received_date').css('display', 'none');
                    window.parent.checkVatflag();
                    $('.icon-date-vat_date').css('display', 'none');
                }

                if (TABLE_NAME == 'transaction_entries') {
                    $('#station_id').select2({
                        placeholder: "",
                        allowClear: true,
                        dropdownAutoWidth: true,
                        width: '100%'
                    });
                }

                $(".error .message").html("");
                var checkClass = $(this).hasClass("selected");
                var $cell = $(evt.target).closest("td");
                $(".import-volumetrack").removeClass("show-import-volume");
                $(".import-volumetrack").css("display", "none");
                if ($cell.index() > 0) {
                    $(".add-new-data .title").html("Sửa");
                    $(".table-edit .sidebar-actions").show();
                    $(":checkbox").each(function () {
                        this.checked = false;
                    });
                    $(".selected").removeClass("selected");
                    if (!checkClass) {
                        if (TABLE_NAME == "volume_trackings") {
                            $("#basic-tabs-components").show();
                            $(".data-items").show();
                            $(".import-volumetrack").removeClass("show-import-volume");
                            $(".import-volumetrack").css("display", "none");
                        }
                        var rowId = $(this).find(".select-param").val();
                        $(".click-image-thumbnail").addClass("d-none");
                        setDataEditerAjax(rowId);
                        removeElementMeasuring();
                        if (TABLE_NAME != "users") {
                            $(".overlay-bg,.add-new-data").addClass("show");
                        }
                    }
                }
                if (checkClass) {
                    $(this).removeClass("selected");
                    $(this).find(".select-param").prop("checked", false);
                } else {
                    $(this).addClass("selected");
                    $(this).find(".select-param").prop("checked", true);
                }
                getCheckedAll();
                disableAction(getChecked());
            });
        } else {
            $("body").on("click", ".data-list-view:not('#receipt-table') tbody .select-param", function (evt) {
                $(".error .message").html("");
                if (TABLE_NAME == "organizations") {
                    window.parent.loadOrganizationAjax();
                }
                if (TABLE_NAME == "gl_accounts") {
                    window.parent.loadGlAccountAjax();
                }
                var checkClass = $(this).hasClass("selected");
                var $cell = $(evt.target).closest("td");
                $(".import-volumetrack").removeClass("show-import-volume");
                $(".import-volumetrack").css("display", "none");
                if ($cell.index() > 0) {
                    $(".add-new-data .title").html("Sửa");
                    $(".table-edit .sidebar-actions").show();
                    $(":checkbox").each(function () {
                        this.checked = false;
                    });
                    $(".selected").removeClass("selected");
                    if (!checkClass) {
                        var rowId = $(this).attr("data-id");
                        $(".click-image-thumbnail").addClass("d-none");
                        setDataEditerAjax(rowId);
                        removeElementMeasuring();
                        $(".overlay-bg,.add-new-data").addClass("show");
                    }
                }
                if (checkClass) {
                    $(this).removeClass("selected");
                    $(this).find(".select-param").prop("checked", false);
                } else {
                    $(this).addClass("selected");
                    $(this).find(".select-param").prop("checked", true);
                }
                getCheckedAll();
                disableAction(getChecked());
            });
        }
    }

    function setDataEditerAjax(rowId) {
        $("#field-id").val(rowId);
        var postURL = TABLE_NAME + "/edit/" + rowId;
        $.ajax({
            url: postURL,
            method: "POST",
            data: {},
            type: "json",
        }).done(function (data) {
            if (TABLE_NAME == 'receipts' || TABLE_NAME == 'payments' || TABLE_NAME == 'debits' || TABLE_NAME == 'alerts') {
                $('#receipt-table tbody').html(data.html);
                window.parent.sumPaymentItem();
                $('#add-new-receipt,#delete-receipt').prop("disabled", true);
                $('.select2').select2({
                    placeholder: "Chọn",
                    allowClear: true,
                    dropdownAutoWidth: true,
                    width: '100%'
                });
            }
            if (TABLE_NAME == 'payments' || TABLE_NAME == 'alerts') {
                $('.volumn_trackings_id').empty();
            }
            $('input[name="total_price"],input[name="payment_volume"]').prop("disabled", true);
            $("#form-data-list").attr("action", data.route);
            $("#form-data-list").trigger("reset");
            if (data.customer_id && data.construction_id) {
                let customer_id = data.customer_id;
                let construction_id = data.construction_id;
                let changeContractCodeUrl = "/changeContractcode";
                $.ajax({
                    url: changeContractCodeUrl,
                    type: "POST",
                    data: {
                        customer_id: customer_id,
                        construction_id: construction_id,
                    },
                }).done(function (response) {
                    $("#select-contract_id").html(response);
                });
            }
            $.each(data.old, function (index, value) {
                if (index == 'id') {
                    $('#receipt-old').val(value);
                }
                if (index == "name" || index == "contract_code" || index == "account_name") {
                    $("#name-old").val(value);
                }
                if (index == "address") {
                    $("#address-company-old").val(value);
                }
                if (index == "account_number") {
                    $("#account-number-old").val(value);
                }
                if (index == "bank") {
                    $("#bank-old").val(value);
                }
                if (index == "tax_number") {
                    $("#tax-old").val(value);
                }
                if (index == "email") {
                    $("#email-old").val(value);
                }

                if (index == "area_id") {
                    $('select[name="area_id"]').val(value).trigger("change.select2");
                }

                if (index == "contract_code") {
                    $("input[name=" + index + "]").val(value);
                }

                var IdArea = $('select[name="area_id"]').val();
                $("#area_id_primary").val(IdArea);
                $(".edit-volumetracking").removeClass("text-uppercase");

                if (TABLE_NAME == "receipts" || TABLE_NAME == "payments" || TABLE_NAME == "debits" || TABLE_NAME == "alerts") {
                    window.parent.editObjectItem(index, value);
                }
                if (TABLE_NAME == "suppliers") {
                    if (index == "code") {
                        $("#code-old").val(value);
                    }
                }
                if (TABLE_NAME == 'transaction_entries') {
                    window.parent.checkEditTransactionEntry(index, value);
                }

                if (TABLE_NAME == "payment_conditions") {
                    if (index == "from_pour_date") {
                        if (value == 1 || value == null) {
                            $(".pour_date").css("display", "none");
                        } else if (value == 2) {
                            $(".pour_date").css("display", "inline");
                        }
                    }
                }
                if (TABLE_NAME == "volume_trackings") {
                    window.setVolumeTrackingEdit(index, value);
                } else {
                    $(".add-new-data .title").html("Sửa");
                }

                if (TABLE_NAME == "gl_accounts") {
                    window.parent.checkVatflagGlaccount(index, value);
                }

                if (TABLE_NAME == 'organizations') {
                    if (index == 'parent_id') {
                        $('#organization-parent-create').val(value).trigger('change.select2');
                    }
                }

                if (INPUT_TYPE_SELECT.includes(index)) {
                    $("select[name=" + index + "]").val(value).trigger("change.select2");
                } else {
                    if (INPUT_FORMAT_PRICE.includes(index)) {
                        if (value > 0) {
                            value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    }
                    $("input[name=" + index + "]").val(value);
                }
            });
            $("#form-data-list input,#form-data-list select,#form-data-list textarea").prop("disabled", true);
        });
    }

    function clickSelectAll() {
        $("#select-all").click(function (event) {
            event.stopPropagation();
            if (this.checked) {
                $("tbody tr").addClass("selected");
                $("tbody tr :checkbox").each(function () {
                    this.checked = true;
                });
            } else {
                $("tbody tr").removeClass("selected");
                $("tbody tr :checkbox").each(function () {
                    this.checked = false;
                });
            }
            disableAction(getChecked());
        });
    }

    function clickDeleteParams() {
        $("#delete-params").click(function (event) {
            var checkBoxeds = getChecked();
            if (checkBoxeds.length) {
                $("#delete-params").attr("checkBoxeds", JSON.stringify(checkBoxeds));
                alertsDelete(event);
                event.preventDefault();
            }
        });
    }

    function disableSorting() {
        $("body").on("click", ".data-list-view thead th", function (evt) {
            var checkClass = $(this).hasClass("sorting_desc");
            if (checkClass) {
                $(document).ready(function () {
                    var table = $(".data-list-view:not(#receipt-table)").DataTable();
                    table.order([]).draw(false);
                });
            }
        });
    }

    function getChecked() {
        var checkBoxeds = [];
        $(".data-list-view tbody input[type=checkbox]:checked")
            .not('input[name="permissions"]')
            .each(function () {
                var value = $(this).val();
                if (value != 0) checkBoxeds.push(value);
            });
        return checkBoxeds;
    };

    function disableAction(checkBoxeds) {
        if (checkBoxeds.length) {
            $("#select-all").addClass("checkbox-all-custom");
            $(".action-btns .actions-dropodown").show();
        } else {
            $("#select-all").removeClass("checkbox-all-custom");
            $(".action-btns .actions-dropodown").hide();
            $("#select-all").prop("checked", false);
        }
    }

    function checkCountSelectBox() {
        var checkBoxed = 0;
        $(".data-list-view tbody input[type=checkbox]:checked").each(function () {
            checkBoxed++;
        });
        return checkBoxed;
    }

    function getCheckedAll() {
        var checkBoxed = 0;
        $(".data-list-view tbody input[type=checkbox]").each(function () {
            checkBoxed++;
        });
        var checked = checkCountSelectBox();

        if (checkBoxed > checked) $("#select-all").prop("checked", false);
        else if (checkBoxed == checked) $("#select-all").prop("checked", true);
    }

    if (TABLE_NAME == "users") {
        var actionPermission = $(".permission-row");
        actionPermission.insertAfter($(".top .actions .dt-buttons"));
    }

    function addNewAjax(postURL) {
        window.isSuccessSubmit = false;
        var dataTable = $(".dataTable").DataTable();
        $("#dataTableBuilder_processing").show();
        var disabled = $("#form-data-list").find(":input:disabled").removeAttr("disabled");
        if (!$(".import-volumetrack").hasClass("show-import-volume")) {
            $.ajax({
                url: postURL,
                method: "POST",
                data: new FormData($("#form-data-list")[0]),
                type: "json",
                contentType: false,
                processData: false,
            })
                .done(function (data) {
                    if (data == 1 && TABLE_NAME == "gl_accounts") {
                        toastr.error("Chỉ được tạo tối đa 4 bậc");
                    }
                    if (data.success) {
                        if (TABLE_NAME == "gl_accounts" || TABLE_NAME == "organizations") {
                            location.reload();
                        } else {
                            dataTable.ajax.reload(null, false);
                        }
                        $("#select-all").removeClass("checkbox-all-custom");
                        successChange();
                        closeSidebar();
                        toastr.success(data.message, "Thông báo!");
                        $(".action-btns .actions-dropodown").hide();
                    } else if (data.error) {
                        toastr.error(data.message, "Thông báo!");
                    }
                })
                .fail(function (xhr, textStatus, errorThrown) {
                    var errors = xhr.responseJSON.errors;
                    $(".form-group").parent().find(".error").remove();
                    var firstError = true;
                    $.each(errors, function (index, value) {
                        var errorHtml = document.getElementsByClassName("row-error");
                        if (firstError) {
                            var tabPanel = $('input[name="' + index + '"],select[name="' + index + '"]').closest(".tab-pane");
                            var tabPanelId = tabPanel.attr("aria-labelledby");
                            $(".tab-pane,.nav-tabs .nav-link").removeClass("active");
                            tabPanel.addClass("active");
                            $("#" + tabPanelId).addClass("active");
                            firstError = false;
                        }
                        $(".row-error .message").html(value);
                        $('input[name="' + index + '"]').after(errorHtml[0].innerHTML);
                        $('select[name="' + index + '"]')
                            .parent()
                            .find(".select2-container")
                            .after(errorHtml[0].innerHTML);
                    });
                    toastr.error("Trường không hợp lệ!", "Thông báo!");
                });
        }
        $("#dataTableBuilder_processing").hide();
        disabled.attr("disabled", "disabled");
    }

    function deleteRowAjax() {
        var dataTable = $(".dataTable").DataTable();
        $("#dataTableBuilder_processing").show();
        $.ajax({
            url: URL_DELETE_API,
            method: "DELETE",
            data: { checkboxed: $("#delete-params").attr("checkBoxeds") },
            type: "json",
        }).done(function (data) {
            if (data.success) {
                dataTable.ajax.reload(null, false);
                $("#select-all").prop("checked", false);
                $("#select-all").removeClass("checkbox-all-custom");
                $(".btn-group-delete").css("display", "none");
                toastr.success(data.message, "Thông báo!");
            }
            $("#dataTableBuilder_processing").hide();
        })
            .fail(function (jqXHR, textStatus, errorThrown) {
                toastr.error("Lỗi", "Thông báo!");
            });
    }

    function approveAjax(rowId, isApprove, comment) {
        var postURL = "/" + TABLE_NAME + "/approve/" + rowId;
        $.ajax({
            url: postURL,
            method: "POST",
            data: { isApprove: isApprove, comment: comment },
            type: "json",
        })
            .done(function (data) {
                if (data.success) {
                    var dataTable = $(".dataTable").DataTable();
                    dataTable.ajax.reload(null, false);
                    $("#select-all").removeClass("checkbox-all-custom");
                    closeSidebar();
                    toastr.success(data.message, "Thông báo!");
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                toastr.error("Lỗi", "Thông báo!");
            });
    }

    function buildMeasuringAjax(rowId) {
        var postURL = "/" + TABLE_NAME + "/measuring/conversion";
        $.ajax({
            url: postURL,
            method: "POST",
            data: { rowId: rowId },
            type: "json",
        })
            .done(function (data) {
                if (data.success) {
                    $(".measuring-conversion").after(data.html);
                }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                toastr.error("Lỗi", "Thông báo!");
            });
    }

    function alertsDelete(e) {
        e.preventDefault();
        Swal.fire({
            title: "Bạn có muốn xoá không?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Xoá",
            confirmButtonClass: "btn btn-danger",
            cancelButtonClass: "btn btn-outline-danger waves-effect waves-light ml-1",
            cancelButtonText: "Huỷ",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                deleteRowAjax();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                e.preventDefault();
            }
        });
    }

    function alertsApprove(e, rowId) {
        e.preventDefault();
        Swal.fire({
            title: '<textarea class="form-control" id="approve-comment" rows="3" placeholder="Ghi chú"></textarea>',
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Duyệt",
            confirmButtonClass: "btn  btn-primary",
            cancelButtonClass: "btn btn-outline-danger waves-effect waves-light ml-1",
            cancelButtonText: "Không duyệt",
            buttonsStyling: false,
        }).then(function (result) {
            var comment = $("#approve-comment").val();
            if (result.value) {
                var isApprove = 1;
                approveAjax(rowId, isApprove, comment);
                event.preventDefault();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                var isApprove = 0;
                approveAjax(rowId, isApprove, comment);
                event.preventDefault();
            }
        });
    }

    function alertsChange(e) {
        e.preventDefault();
        Swal.fire({
            title: "Bạn có muốn thoát không?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Có",
            confirmButtonClass: "btn  btn-primary",
            cancelButtonClass: "btn btn-outline-danger waves-effect waves-light ml-1",
            cancelButtonText: "Không",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                closeSidebar();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
            }
        });
    }

    function closeSidebar() {
        $(".add-new-data").not(".add-new-data-sidebar-2 .overlay-bg,.add-new-data-sidebar-2 .add-new-data").removeClass("show");
        $(".overlay-bg").not(".add-new-data-sidebar-2 .overlay-bg,.add-new-data-sidebar-2 .add-new-data").removeClass("show");
        $(".form-group .error").remove();
        $(".add-data-footer").removeClass("d-flex");
        $(".table-edit .data-items").addClass("data-items-unset");
        activeTabDefault();
        window.isChange = false;
    }

    function successChange() {
        $("#select-all").prop("checked", false);
        $(".action-btns .actions-dropodown").hide();
        $("#select-all").removeClass("checkbox-all-custom");
    }

    function activeTabDefault() {
        $(".tab-pane,.nav-tabs .nav-link").removeClass("active");
        $(".nav-tabs").find(".nav-link").first().addClass("active");
        $(".tab-content").find(".tab-pane").first().addClass("active");
    }

    /*hide button add table users*/
    if (TABLE_NAME == "users") {
        $(".dt-buttons,.btn-group-delete").css("display", "none");
        $(".btn-group-delete button").addClass("hidden");
    }
});