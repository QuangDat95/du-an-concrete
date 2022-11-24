<option value=""></option>
@foreach($customers as $customer)
<option value="{{$customer->id}}">{{$customer->name}}</option>
@endforeach