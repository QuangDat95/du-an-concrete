<fieldset class="form-group position-relative">
    @if($column == 'received_date' || $column == 'contract_date')
    <input type="text" class="form-control datepicker" name="{{$column}}" placeholder="Chọn ngày" value="" required>
    @else
    <input type="text" class="form-control datepicker" name="{{$column}}" placeholder="{{__($column)}}" value="{{date('d-m-Y')}}" required>
    @endif
    <div class="form-control-position datepicker-icon icon-date-{{$column}}">
        <i class="fa fa-calendar"></i>
    </div>
</fieldset>