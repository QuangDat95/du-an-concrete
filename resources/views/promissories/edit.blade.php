@foreach($oldData->paymentItems as $key => $value)
<tr>
    @if($value->volumn_trackings_id != null)
    <?php
    $number_remain = $paymentItem::where('volumn_trackings_id',$value->volumn_trackings_id)->where('id','<',$value->id)->where('payment_id','<',$value->payment_id)->sum('amount'); 
    ?>
    @endif
    {{-- Tài khoản nợ --}}
    <td style="width:155px" class="text-center">
        <input type="hidden" name="row_id">
        <select class="form-control select2 debit_account_id" name="debit_account_id[]">
            <option value="">--- Root ---
            </option>
            {{getNumberEditDebitCreditAccount($value->debit_account_id)}}
        </select>
    </td>
    {{-- Tài khoản có --}}
    <td style="width:163.3px" class="text-center">
        <select class="form-control select2 credit_account_id" name="credit_account_id[]">
            <option value="">--- Root ---
            </option>
            {{getNumberEditDebitCreditAccount($value->credit_account_id)}}
        </select>
    </td>
    {{-- Số tiền --}}
    <td style="width:160px" class="text-center">
            <input class="form-control amount" value="{{number_format($value->amount)}}" name="amount[]" type="text"
            onkeyup='this.value = this.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");'
            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="background-color: white !important;
            border: solid white;text-align:center">
        <span class="text-danger amoutError" style="position:absolute"></span>
    </td>
    {{-- CT phiếu đổ --}}
    <td style="width:138.03px" class="text-center">
        <select class="form-control select2 volumn_trackings_id" name="volumn_trackings_id[]">
            <option value="">--- Root ---</option>
            @if($value->volumn_trackings_id != null)
            {{getEditVolumeTracking($value->volumn_trackings_id,$partyable_id)}}
            @else
            {{''}}
            @endif
        </select>
    </td>
    {{-- Trạm --}}
    <td style="width:147.03px" class="text-center">
        <select class="form-control select2 station_item_id" name="station_item_id[]">
            <option value="">--- Root ---</option>
            @if($value->volumn_trackings_id != null)
            {{getEditStation($value->volumetracking->station_id)}}
            @else
            {{getStation()}}
            @endif
        </select>
    </td>
    {{-- Số phải thu --}}
    <td style="width:147.03px" class="text-center">
        @if($value->volumn_trackings_id != null)
        <input class="form-control uncollected" value="{{number_format($value->volumetracking->total_price - $number_remain)}}" name="uncollected[]"
        disabled style="background-color: white !important; border: solid white;text-align:center">
        @else
        <input class="form-control uncollected" value="" name="uncollected[]"
        disabled style="background-color: white !important; border: solid white;text-align:center">
        @endif
    </td>
    {{-- Hạn thanh toán --}}
    <td style="width:135.42px" class="text-center">
        @if($value->volumn_trackings_id != null)
        <input class="form-control payment_term" name="payment_term[]" value="{{$value->volumetracking->due_date}}"
            disabled style="background-color: white !important; border: solid white;text-align:center">
        @else
        <input class="form-control payment_term" name="payment_term[]" value="" disabled style="background-color: white !important; border: solid white;text-align:center">
        {{''}}
        @endif
    </td>
    {{-- Diễn giải --}}
    <td style="width:154.42px" class="text-center">
        <input class="form-control description_payment_item" value="{{$value->description}}" name="description_payment_item[]" type="text"
        style="background-color: white !important; border: solid white;text-align:center">
    </td>
    {{-- action --}}
    <td style="width:68.27px"><button class="btn btn-danger btn-sm rounded-1 delete-receipt" id="delete-receipt"><i
        class="fa fa-trash"></i></button></td>
</tr>
@endforeach