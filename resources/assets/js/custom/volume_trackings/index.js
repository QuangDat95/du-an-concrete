$(document).ready(function () {
    window.isChange = false;
    function activeTabDefault() {
        $(".tab-pane,.nav-tabs .nav-link").removeClass("active");
        $(".nav-tabs").find(".nav-link").first().addClass("active");
        $(".tab-content").find(".tab-pane").first().addClass("active");
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
    $('#select-area_id').on('change', function () {
        let areaId = $(this).val();
        if (areaId != '') {
            $('#select-station_id option').prop('disabled', true);
            $('#select-station_id').val('').trigger('change.select2');
            $('#select-station_id option[area_id="' + areaId + '"]').prop('disabled', false);
        } else {
            $('#select-station_id option').prop('disabled', true);
        }
    });
    $("#select-customer_id,#select-construction_id").change(function () {
        let customer_id = $("#select-customer_id").val();
        let construction_id = $("#select-construction_id").val();
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
    });

    $("#select-contract_id").change(function () {
        let customer_id = $(this).find(":selected").attr("customer-id");
        let construction_id = $(this).find(":selected").attr("construction-id");
        $("#select-customer_id").val(customer_id).trigger('change.select2');
        $("#select-construction_id").val(construction_id).trigger('change.select2');
    });

    $('input[name="from_date"],#select-contract_id').change(function () {
        let from_date = $('input[name="from_date"]').val();
        let contract_id = $('#select-contract_id').val();
        let DueDateUrl = '/DueDate/Url';
        $.ajax({
            url: DueDateUrl,
            type: "POST",
            data: {
                from_date: from_date,
                id: contract_id
            }
        }).done(function (response) {
            $('input[name="due_date"]').val(response);
        });
    });

    $("#btn-submit").on("click", function () {
        if ($(".import-volumetrack").hasClass("show-import-volume")) {
            if (document.getElementById("inputGroupFile01").value == "") {
                toastr.error("Bạn chưa chọn tệp");
            } else {
                Swal.fire({
                    title: "Bạn có muốn import không?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Có",
                    confirmButtonClass: "btn  btn-primary",
                    cancelButtonClass: "btn btn-outline-danger waves-effect waves-light ml-1",
                    cancelButtonText: "Không",
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value && document.getElementById("inputGroupFile01").value != "") {
                        $.blockUI({
                            message: '<h1><div class="spinner-border text-primary" role="status">\
                            <span class="sr-only">Loading...</span>\
                          </div> Please wait </h1>', onBlock:
                                $("#import-volume-tracking").submit()
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                    }
                });
            }
        }
    });

    $("body").on("click", "#click-modal", function () {
        $(".data-items").hide();
        $(".import-volumetrack").addClass("show-import-volume");
        $("#basic-tabs-components").hide();
        $(".add-data-footer").addClass("d-flex");
        $(".sidebar-actions").hide();
        document.getElementById("inputGroupFile01").value = "";
        $(".custom-file-label").html("Chọn tệp");
        $(".import-volumetrack").show();
        $(".add-new-data .title").html("Import theo dõi khối lượng");
        $(".overlay-bg").not(".add-new-data-sidebar-2 .overlay-bg,.add-new-data-sidebar-2 .add-new-data").addClass("show");
        $(".add-new-data").not(".add-new-data-sidebar-2 .overlay-bg,.add-new-data-sidebar-2 .add-new-data").addClass("show");
    });

    window.loadCustomer = function () {
        let loadCustomerUrl = '/loadCustomer';
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: loadCustomerUrl,
            type: "POST",
        }).done(function (data) {
            $('select[name="customer_id"]').html("<option value=''>---root---</option>");
            $('select[name="customer_id"]').append(data);
        });
    }

    window.loadConstruction = function () {
        let loadConstructionUrl = '/loadConstruction';
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: loadConstructionUrl,
            type: "POST",
        }).done(function (data) {
            $('select[name="construction_id"]').html("<option value=''>---root---</option>");
            $('select[name="construction_id"]').append(data);
        });
    }

    window.loadContract = function () {
        let loadContractUrl = '/loadContract';
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: loadContractUrl,
            type: "POST",
        }).done(function (data) {
            $('select[name="contract_id"]').html("<option value=''>---root---</option>");
            $('select[name="contract_id"]').append(data);
        });
    }

    $("#import-volume-tracking").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        var importVolumeTracking = "/import-volumetrackings";
        $.ajax({
            url: importVolumeTracking,
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        }).done(function (response) {
            if (response) {
                closeSidebar();
                $.unblockUI();
                toastr.success(response.message);
                let dataTable = $(".dataTable").DataTable();
                dataTable.ajax.reload(null, false);
            }
        }).fail(function () {
            $.unblockUI();
            toastr.error("Có trường dữ liệu bị lỗi");
        });
    });

    $("body").on("keyup", ".table-edit .tab-content #tab1 input", function (e) {
        var nameColumn = $(this).attr("name");
        let numberValue = $(this).val().replaceAll(",", "");
        if (INPUT_FORMAT_PRICE.includes(nameColumn)) {
            if (numberValue > 0) {
                let numberFormat = numberValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                $(this).val(numberFormat);
            }
        }
        let actualWeight = $('input[name="actual_weight"]').val().replaceAll(",", ""); //KL thực tế
        let sendingVolume = $('input[name="sending_volume"]').val().replaceAll(",", ""); //KL gửi
        let minusVolume = $('input[name="minus_volume"]').val().replaceAll(",", ""); //KL bị trừ

        actualWeight = parseFloat(actualWeight) ? parseFloat(actualWeight) : 0;
        sendingVolume = parseFloat(sendingVolume) ? parseFloat(sendingVolume) : 0;
        minusVolume = parseFloat(minusVolume) ? parseFloat(minusVolume) : 0;

        let paymentVolume = actualWeight + sendingVolume - minusVolume; //KL thanh toán
        $('input[name="payment_volume"]').val(
            paymentVolume
                .toFixed(2)
                .toString()
                .replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        );
        let concreatePrice = $('input[name="concreate_price"]').val().replaceAll(",", ""); //đơn giá bê tông
        let additivePrice = $('input[name="additive_price"]').val().replaceAll(",", ""); //đơn giá phụ gia
        let shippingSurcharge = $('input[name="shipping_surcharge"]').val().replaceAll(",", ""); //phụ phí vc
        let pumpPrice = $('input[name="pump_price"]').val().replaceAll(",", ""); //đơn giá bơm
        let pumpSurcharge = $('input[name="pump_surcharge"]').val().replaceAll(",", "");

        concreatePrice = parseFloat(concreatePrice) ? parseFloat(concreatePrice) : 0;
        additivePrice = parseFloat(additivePrice) ? parseFloat(additivePrice) : 0;
        shippingSurcharge = parseFloat(shippingSurcharge) ? parseFloat(shippingSurcharge) : 0;
        pumpPrice = parseFloat(pumpPrice) ? parseFloat(pumpPrice) : 0;
        pumpSurcharge = parseFloat(pumpSurcharge) ? parseFloat(pumpSurcharge) : 0;
        if (pumpPrice > 500000) {
            totalPrice = paymentVolume * (concreatePrice + additivePrice) + pumpPrice + pumpSurcharge;
        } else {
            totalPrice = (concreatePrice + additivePrice + pumpPrice) * paymentVolume + shippingSurcharge + pumpSurcharge;
        }
        let totalPriceFormat = totalPrice
            .toFixed()
            .toString()
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        $('input[name="total_price"]').val(totalPriceFormat);
    });

    window.checkVatflag = function () {
        if ($('#vat_flag').is(':checked')) {
            $('#vat_flag').val(1);
            $('.icon-date-vat_date').css('display', 'inline');
            $('input[name="vat_company"],input[name="vat_address"],input[name="tax_number"],input[name="serial_number"],\
            input[name="vat_number"],input[name="vat_date"],input[name="vat_amount"]').prop('disabled', false);
        } else {
            $('#vat_flag').val(0);
            $('.icon-date-vat_date').css('display', 'none');
            $('input[name="vat_company"],input[name="vat_address"],input[name="tax_number"],input[name="serial_number"],\
            input[name="vat_number"],input[name="vat_date"],input[name="vat_amount"]').prop('disabled', true);
        }
    }

    $('#vat_flag').change(function () {
        checkVatflag();
    });

    window.checkAddVolumeTracking = function () {
        $('#vat_flag').val(0);
        let fillter_column = $(".filter-table-column").val();
        let area_id = $('.filter-table-column option[value="' + fillter_column + '"]').attr('area_id');
        $('#select-station_id option').prop('disabled', true);
        if (fillter_column == 0) {
            $("#select-area_id").prop("disabled", false);
            $("#form-data-list select").not("#select-user_id").val("").trigger("change.select2");
        } else {
            $('#select-station_id option').prop('disabled', true);
            $("#select-area_id").prop("disabled", true);
            $('#select-area_id').val(area_id).trigger('change.select2');
            $('#select-station_id option[area_id="' + area_id + '"]').prop('disabled', false);
            $("#form-data-list select").not("#select-area_id,#select-user_id").val("").trigger("change.select2");
        }
        $('.icon-date-due_date').css('display', 'none');
        $('.icon-date-from_date').css('display', 'inline');
        $('.icon-date-received_date').css('display', 'inline');
        $('.icon-date-vat_date').css('display', 'none');
        $("#select-user_id").val(USER_ID).trigger("change.select2");
        $("#select-user_id,input[name='total_price'],input[name='payment_volume'],input[name='due_date']").prop("disabled", true);
        $("#form-data-list input,#form-data-list select").not('#select-area_id,#select-user_id,\
        input[name="total_price"],input[name="payment_volume"],input[name="due_date"]').prop("disabled", false);
    }

    window.setVolumeTrackingEdit = function (index, value) {
        let customer_name = $(".select2-customer_id option:selected").text();
        let customer_address = $('#customer-address-hidden option:selected').text();
        $('input[name="vat_company"]').val(customer_name);
        $('input[name="vat_address"]').val(customer_address);
        $(".add-new-data .title").html(customer_name);
        if (index == "vat_flag") {
            if (value == 1) {
                $("input[name='vat_flag']").attr("checked", true);
            } else {
                $("input[name='vat_flag']").attr("checked", false);
            }
        }
        if (index == 'customer_id') {
            $('#customer-address-hidden').val(value).trigger('change.select2');
        }
        if (index == 'vat_rate') {
            $('#vat_rate').val(value);
        }
        if (index == 'station_id') {
            $('#select-station_id option').prop('disabled', true);
            let areaId = $('#select-station_id option[value="' + value + '"]').attr('area_id');
            $('#select-station_id option[area_id="' + areaId + '"]').prop('disabled', false);
        }
    }
});