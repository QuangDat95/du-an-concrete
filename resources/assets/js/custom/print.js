$(document).ready(function () {
    $('tbody').on('change', '.debit_account_id', function () {
        $(".credit_account_id option").prop('disabled', false);
        let debitId = $(this).closest('tr').find('.debit_account_id').val();
        if (debitId != '') {
            $('.credit_account_id option[value="' + debitId + '"]').prop('disabled', true);
        } else {
            $(".credit_account_id option[value='" + debitId + "']").prop('disabled', false);
        }
    });

    $('tbody').on('change', '.credit_account_id', function () {
        $(".debit_account_id option").prop('disabled', false);
        let creditId = $(this).closest('tr').find('.credit_account_id').val();
        if (creditId != '') {
            $('.debit_account_id option[value="' + creditId + '"]').prop('disabled', true);
        } else {
            $(".debit_account_id option[value='" + creditId + "']").prop('disabled', false);
        }
    });

    window.emptySelect = function () {
        $('#receipt-table tbody').empty();
        $(".empty-select").show();
        $(".customer-select,.employee-select,.supplier-select").hide();
        $('.select2').select2({
            placeholder: "Chọn",
            allowClear: true,
            dropdownAutoWidth: true,
            width: '100%'
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
    }

    function printPayment(paymentUrl) {
        let id = getChecked();
        let address_receipt = $('#object_address').val();
        $.ajax({
            url: paymentUrl,
            type: "POST",
            data: {
                id: id,
                address: address_receipt
            }
        }).done(function (response) {
            document.write(response);
            window.print();
        });
    }

    $('.button-print').click(function () {
        if (TABLE_NAME == 'receipts') {
            let receiptPrintUrl = '/receipts/print/Url';
            printPayment(receiptPrintUrl);
        }
        if (TABLE_NAME == 'payments') {
            let paymentPrintUrl = '/payments/print/Url';
            printPayment(paymentPrintUrl);
        }
        if (TABLE_NAME == 'debits') {
            let debitPrintUrl = '/debits/print/Url';
            printPayment(debitPrintUrl);
        }
        if (TABLE_NAME == 'alerts') {
            let alertPrintUrl = '/alerts/print/Url';
            printPayment(alertPrintUrl);
        }
    });

    window.sumPaymentItem = function () {
        var sum1 = 0;
        $('#receipt-table tbody tr').each(function () {
            let val1 = $(this).find('td:eq(2) input').val();
            var val11 = val1.replaceAll(',', '', val1);
            if (val1 != '') {
                sum1 += parseInt(val11);
            }
        });
        var sum_payment_item = sum1.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        $('#sum_payment_item').html(sum_payment_item + '<sup>đ</sup>');
    }

    $("#transaction_type_id").on('change', function () {
        var transaction_type_id = $(this).val();
        let addReceiptUrl = "/addReceipt/table";
            $.ajax({
                url: addReceiptUrl,
                type: "POST",
                data:{
                    id:transaction_type_id
                }
            }).done(function (response) {
                $("#receipt-table tbody").empty();
                $("#receipt-table tbody").append(response.html);
                if (response.debit_id != null && response.credit_id != null) {
                    $(".debit_account_id,.credit_account_id").prop('disabled', true);
                } else if (response.debit_id != null && response.credit_id == null) {
                    $(".debit_account_id").prop('disabled', true);
                    $(".credit_account_id").prop('disabled', false);
                } else if (response.debit_id == null && response.credit_id != null) {
                    $(".debit_account_id").prop('disabled', false);
                    $(".credit_account_id").prop('disabled', true);
                } else if (response.debit_id == null && response.credit_id == null) {
                    $(".debit_account_id,.credit_account_id").prop('disabled', false);
                }
                $(".debit_account_id").val(response.debit_id).trigger("change.select2");
                $(".credit_account_id").val(response.credit_id).trigger("change.select2");
                $(".credit_account_id option,.debit_account_id option").prop('disabled', false);
                //loại trừ tài khoản này vs tk kia
                if (response.credit_id != null) {
                    $(".credit_account_id option[value='" + response.debit_id + "']").prop('disabled', false);
                } else {
                    $(".credit_account_id option[value='" + response.debit_id + "']").prop('disabled', true);
                }
                if (response.debit_id != null) {
                    $(".debit_account_id option[value='" + response.credit_id + "']").prop('disabled', false);
                } else {
                    $(".debit_account_id option[value='" + response.credit_id + "']").prop('disabled', true);
                }
                let description_payment = $('input[name="description_payment"]').val();
                $('.description_payment_item').val(description_payment);
                $(".select2").select2({
                        placeholder: "Chọn",
                        allowClear: true,
                        dropdownAutoWidth: true
                    });
            });
    });

    $("#transaction_type_id").on('change', function () {
        let object_group = $('#object_group').val();
        let getVolumeIdUrl = '/getVolumeId/Url';
        let object_id1 = $('#object_id1').val();
        $.ajax({
            url: getVolumeIdUrl,
            type: "POST",
            data: {
                id: object_id1
            }
        }).done(function (response) {
            $('.volumn_trackings_id').empty();
            if (object_group == 1 && (TABLE_NAME == 'receipts' || TABLE_NAME == 'debits')) {
                $('.volumn_trackings_id').html(response);
            }
        });
    });

    if (TABLE_NAME == "receipts" || TABLE_NAME == "payments" || TABLE_NAME == "debits" || TABLE_NAME == "alerts") {
        $("#object_group").change(function () {
            $('.select2').select2({
                placeholder: "Chọn",
                allowClear: true,
                dropdownAutoWidth: true,
                width: '100%'
            });
            $("#object_name").val("");
            $("#object_address").val("");
            let id_object = $(this).val();
            if (id_object == 1) {
                $("#object_id1").val("").trigger("change.select2");
            } else if (id_object == 2) {
                $("#object_id2").val("").trigger("change.select2");
            } else if (id_object == 3) {
                $("#object_id3").val("").trigger("change.select2");
            }
            loadIdObject(id_object);
        });

        function loadIdObject(id) {
            if (id == 1) {
                $(".empty-select,.employee-select,.supplier-select").hide();
                $(".customer-select").show();
            } else if (id == 2) {
                $(".empty-select,.customer-select,.supplier-select").hide();
                $(".employee-select").show();
            } else if (id == 3) {
                $(".empty-select,.customer-select,.employee-select").hide();
                $(".supplier-select").show();
            } else {
                $(".customer-select,.employee-select,.supplier-select").hide();
                $(".empty-select").show();
            }
        }

        function loadNameAddressObject(element) {
            element.change(function () {
                if ($("#object_group").val() == 1) {
                    $(".customer-name-select").val($("#object_id1").val()).trigger("change.select2");
                    $(".customer-address-select").val($("#object_id1").val()).trigger("change.select2");
                    $("#object_name").val($(".customer-name-select option:selected").text());
                    $("#object_address").val($(".customer-address-select option:selected").text());
                } else if ($("#object_group").val() == 2) {
                    $(".employee-name-select").val($("#object_id2").val()).trigger("change.select2");
                    $(".employee-address-select").val($("#object_id2").val()).trigger("change.select2");
                    $("#object_name").val($(".employee-name-select option:selected").text());
                    $("#object_address").val($(".employee-address-select option:selected").text());
                } else if ($("#object_group").val() == 3) {
                    $(".supplier-name-select").val($("#object_id3").val()).trigger("change.select2");
                    $(".supplier-address-select").val($("#object_id3").val()).trigger("change.select2");
                    $("#object_name").val($(".supplier-name-select option:selected").text());
                    $("#object_address").val($(".supplier-address-select option:selected").text());
                }
            });
        }

        loadNameAddressObject($("#object_id1"));
        loadNameAddressObject($("#object_id2"));
        loadNameAddressObject($("#object_id3"));

        $(document).on("click", ".delete-receipt", function () {
            $(this).closest("tr").remove();
        });

        $("#add-new-receipt").on("click", function () {
            let object_group = $('#object_group').val();
            $.ajax({
                url: "/addReceipt/table",
                type: "POST"
            }).done(function (response) {
                $("#receipt-table tbody").append(response.html);
                $(".select2").select2({
                    placeholder: "Chọn",
                    allowClear: true,
                    dropdownAutoWidth: true,
                    width: '100%'
                });
                let description_payment = $('input[name="description_payment"]').val();
                $('.description_payment_item').val(description_payment);
            });

            let getVolumeIdUrl = '/getVolumeId/Url';
            let customer_id = $('#object_id1').val();
            $.ajax({
                url: getVolumeIdUrl,
                type: "POST",
                data: {
                    id: customer_id
                }
            }).done(function (response) {
                if (object_group == 1 && (TABLE_NAME == 'receipts' || TABLE_NAME == 'debits')) {
                    $('#receipt-table tbody tr:last').find('td:eq(3) select').empty();
                    $('#receipt-table tbody tr:last').find('td:eq(3) select').html(response);
                } else if (TABLE_NAME == 'payments' || TABLE_NAME == 'alerts') {
                    $('#receipt-table tbody tr:last').find('td:eq(3) select').empty();
                }
            });
        });

        $('#object_id1,#object_id2,#object_id3').change(function () {
            let object_group = $('#object_group').val();
            let getVolumeIdUrl = '/getVolumeId/Url';
            let object_id1 = $('#object_id1').val();
            $.ajax({
                url: getVolumeIdUrl,
                type: "POST",
                data: {
                    id: object_id1
                }
            }).done(function (response) {
                if (object_group == 1 && (TABLE_NAME == 'receipts' || TABLE_NAME == 'debits')) {
                    $('.volumn_trackings_id').empty();
                    $('.volumn_trackings_id').html(response);
                } else if (TABLE_NAME == 'payments' || TABLE_NAME == 'alerts') {
                    $('.volumn_trackings_id').html('');
                }
            });

        });

        $('tbody').on('keyup', '.amount', function () {
            sumPaymentItem();
            var row = $(this).closest("tr");
            var amount_input = $(this).val();
            var amount_uncollect = row.find("td:eq(5) input").val();
            if(parseInt(amount_input.replaceAll(',','')) > parseInt(amount_uncollect.replaceAll(',',''))){
                row.find("td:eq(2) .amoutError").text('Số tiền lớn hơn số chưa thu');
            }else{
                row.find("td:eq(2) .amoutError").text('');
            }
        });

        $("tbody").on("change", ".volumn_trackings_id", function () {
            var row = $(this).closest("tr");
            var id = $(this).val();
            var payment_id = getChecked();
            var amount_input = row.find('td:eq(2) input').val();
            let getReceivableUrl = "/getVolumeValue/Url";
            $.ajax({
                url: getReceivableUrl,
                type: "POST",
                data: {
                    id: id,
                    amount_input: amount_input,
                    payment_id: payment_id
                },
            }).done(function (response) {
                (response[2] > 0) ? row.find("td:eq(2) .amoutError").text('Số tiền lớn hơn số chưa thu')
                : row.find("td:eq(2) .amoutError").text('');
                row.find('td:eq(4) .station_item_id').val(response[3]).trigger('change.select2');
                row.find("td:eq(5) input").val(response[0]);
                row.find("td:eq(6) input").val(response[1]);
                (id != '') ? row.find('td:eq(4) .station_item_id').prop('disabled', true) 
                : row.find('td:eq(4) .station_item_id').prop('disabled', false);
            });
        });
    }

    window.editObjectItem = function (index, value) {
        if (index == 'description') {
            $('input[name="description_payment"]').val(value);
        }
        if (index == "partyable_type") {
            if (value == "App\\Models\\Concrete\\Customer") {
                $(".customer-select").show();
                $(".empty-select,.employee-select,.supplier-select").hide();
                $("#object_group").val(1).trigger("change.select2");
            } else if (value == "App\\Models\\Survey\\Employee") {
                $(".empty-select,.customer-select,.supplier-select").hide();
                $(".employee-select").show();
                $("#object_group").val(2).trigger("change.select2");
            } else if (value == "App\\Models\\Concrete\\Supplier") {
                $(".empty-select,.customer-select,.employee-select").hide();
                $(".supplier-select").show();
                $("#object_group").val(3).trigger("change.select2");
            }
        }

        if (index == "partyable_id") {
            $("#object_id1").val(value).trigger("change.select2");
            $("#object_id2").val(value).trigger("change.select2");
            $("#object_id3").val(value).trigger("change.select2");
        }

        if ($("#object_group").val() == 1) {
            $(".customer-name-select").val($("#object_id1").val()).trigger("change.select2");
            $(".customer-address-select").val($("#object_id1").val()).trigger("change.select2");
            $("#object_name").val($(".customer-name-select option:selected").text());
            $("#object_address").val($(".customer-address-select option:selected").text());
        } else if ($("#object_group").val() == 2) {
            $(".employee-name-select").val($("#object_id2").val()).trigger("change.select2");
            $(".employee-address-select").val($("#object_id2").val()).trigger("change.select2");
            $("#object_name").val($(".employee-name-select option:selected").text());
            $("#object_address").val($(".employee-address-select option:selected").text());
        } else if ($("#object_group").val() == 3) {
            $(".supplier-name-select").val($("#object_id3").val()).trigger("change.select2");
            $(".supplier-address-select").val($("#object_id3").val()).trigger("change.select2");
            $("#object_name").val($(".supplier-name-select option:selected").text());
            $("#object_address").val($(".supplier-address-select option:selected").text());
        }
    }
});