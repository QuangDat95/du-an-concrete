
<option value = '0' selected>Tất cả</option>
@foreach($customer as $key => $value)
<option value="{{$key}}">{{$value}}
</option>
@endforeach