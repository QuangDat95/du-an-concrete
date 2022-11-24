<select class="form-control select2 select2-{{$column}}" id="select-{{$column}}" name="{{$column}}">
    <option value=""></option>
    @if(in_array($column,array('user_id')))
        {{getEmployee()}}
    @elseif(in_array($column,array('construction_id')))
        {{getConstruction()}}
    @elseif(in_array($column,array('customer_id')))
        {{getCustomer()}}
    @elseif(in_array($column,array('debit_account_id','credit_account_id')))
        {{getDebitCreditAccount()}}
    @elseif($column == 'contract_id')
        @foreach($paramSelects[$column] as $paramSelect)
            <option value="{{$paramSelect['id']}}" customer-id="{{$paramSelect['customer_id']}}" construction-id="{{$paramSelect['construction_id']}}">{{$paramSelect['contract_code']}}</option>
        @endforeach
    @else
        @foreach($paramSelects[$column] as $paramSelect)
            <option value="{{$paramSelect['id']}}">{{$paramSelect['name']}}</option>
        @endforeach
    @endif
</select>
