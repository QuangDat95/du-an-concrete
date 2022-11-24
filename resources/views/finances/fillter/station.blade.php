
<option value = '0' selected>Tất cả</option>
@foreach($station as $key => $value)
<option value="{{$key}}">{{$value}}</option>
@endforeach