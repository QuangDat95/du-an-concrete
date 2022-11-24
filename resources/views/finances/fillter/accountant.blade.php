
<option value = '0' selected>Tất cả</option>
@foreach($accountant as $value)
@if($value->accountant_name == null)
<option value="{{$value->accountant_name}}">
    Không xác định
</option>
@else
<option value="{{$value->accountant_name}}">
    {{$value->accountant_name}}
</option>
@endif
@endforeach