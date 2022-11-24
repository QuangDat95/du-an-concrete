@foreach($contract_codes as $contract_code)
<option value="{{$contract_code->id}}" customer-id="{{$contract_code->customer_id}}" construction-id="{{$contract_code->construction_id}}">{{$contract_code->contract_code}}</option>
@endforeach