<tr>
    <!-- Tài khoản nợ -->
    <td style="width:155px">
        <select class="form-control select2 debit_account_id" name="debit_account_id[]">
            <option value="">--- Root ---
            </option>
            {{getNumberDebitCreditAccount()}}
        </select>
    </td>
    <!-- Tài khoản có -->
    <td style="width:163.3px">
        <select class="form-control select2 credit_account_id" name="credit_account_id[]">
            <option value="">--- Root ---
            </option>
            {{getNumberDebitCreditAccount()}}
        </select>
    </td>
    <!-- Số tiền -->
    <td style="width:160px">
        <input class="form-control amount" name="amount[]" type="text"
            onkeyup='this.value = this.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");'
            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="text-align:center">
        <span class="text-danger amoutError" style="position:absolute"></span>
    </td>
    <!-- CT phiếu đổ -->
    <td style="width:138.03px">
        <select class="form-control select2 volumn_trackings_id" name="volumn_trackings_id[]">
            <option value="">--- Root ---
            </option>
            {{getVolumeTracking()}}
        </select>
    </td>
    <!-- Trạm -->
    
    <td style="width:147.03px">
        <select class="form-control select2 station_item_id" name="station_item_id[]">
            <option value="">--- Root ---
            </option>
            {{getStation()}}
        </select>
    </td>
    <!-- Số phải thu -->
    <td style="width:147.03px">
        <input class="form-control uncollected" name="uncollected[]" disabled style="background-color: white !important; border: solid white;">
    </td>
    <!-- Hạn thanh toán -->
    <td style="width:135.42px">
        <input class="form-control payment_term" name="payment_term[]" disabled style="background-color: white !important; border: solid white;">
    </td>
    <!-- Diễn giải -->
    <td style="width:154.42px">
        <input class="form-control description_payment_item" name="description_payment_item[]" style="text-align:center">
    </td>
    <!-- action -->
    <td style="width:68.27px"><button class="btn btn-danger btn-sm rounded-1 delete-receipt" id="delete-receipt"><i
        class="fa fa-trash"></i></button></td>
</tr>