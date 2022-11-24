@if($column != 'deleted_at')
@if($column == 'sales_user_id')
<input type="hidden" name="{{$column}}" class="form-control" value="{{auth()->user()->id}}"
    {{($column == 'code') ? 'disabled' : ''}}>
@elseif($column == 'total_price')
<input type="text" name="{{$column}}" class="form-control" {{($column == 'code') ? 'disabled' : ''}}>
@elseif($column == 'actual_weight' || $column == 'payment_volume' || $column == 'concreate_price' || $column ==
'additive_price' || $column == 'minus_volume' || $column == 'sending_volume' || $column ==
'pump_price'
|| $column == 'shipping_surcharge' || $column == 'tip' || $column == 'sendprice_concreate' || $column ==
'sendprice_pump'
|| $column == 'sendprice_addditive' || $column == 'pump_surcharge')
<input type="text" name="{{$column}}"
    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" @if($column=='minus_volume' ||
    $column=='sending_volume' || $column=='additive_price' || $column=='pump_price' ) placeholder="0" @endif
    placeholder="Nhập" class="form-control" {{($column == 'code') ? 'disabled' : ''}}>
@elseif($column == 'phone_director' || $column == 'phone_accountant' || $column == 'phone_qs' || $column == 'phone_cht')
<input type="text" name="{{$column}}"
    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="12"
    placeholder="Nhập {{str_replace('*','',__($columnPrefix))}}" class="form-control"
    {{($column == 'code') ? 'disabled' : ''}}>
@elseif($column == 'pumping_time_begin' || $column == 'pumping_time_finish')
<input type="text" name="{{$column}}" maxlength="5" placeholder="Nhập {{str_replace('*','',__($columnPrefix))}}"
    class="form-control" {{($column == 'code') ? 'disabled' : ''}}>
@elseif($column == 'password')
<input type="password" name="{{$column}}" id="password" placeholder="Nhập {{str_replace('*','',__($columnPrefix))}}"
    class="form-control" {{($column == 'code') ? 'disabled' : ''}}>
<div class="form-check">
    <input class="form-check-input" type="checkbox" onclick="window.showpass()">
    <label class="form-check-label" for="flexCheckChecked">
        Hiển thị mật khẩu
    </label>
</div>
@elseif($column == 'date_owned')
<input type="number" name="{{$column}}" placeholder="Nhập {{str_replace('*','',__($columnPrefix))}}"
    class="form-control" {{($column == 'code') ? 'disabled' : ''}}>
@else
<input type="text" name="{{$column}}" placeholder="Nhập {{str_replace('*','',__($columnPrefix))}}" class="form-control"
    {{($column == 'code') ? 'disabled' : ''}}>
@endif
@endif