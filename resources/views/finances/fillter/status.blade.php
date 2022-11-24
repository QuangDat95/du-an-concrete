
<option value = '0' selected>Tất cả</option>
@foreach($status as $value)
<option value="{{$value->status_id}}">
    {{$value->status_id}}
</option>
@endforeach