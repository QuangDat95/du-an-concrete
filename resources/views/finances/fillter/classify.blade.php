
<option value = '0' selected>Tất cả</option>
@foreach($classify as $value)
<option value="{{$value->type_id}}">
    {{config('default.classify.'.$value->type_id)}}
</option>
@endforeach