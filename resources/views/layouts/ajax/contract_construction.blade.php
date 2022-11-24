@foreach($constructions as $construction)
<option value="{{$construction->id}}">{{$construction->name}}</option>
@endforeach