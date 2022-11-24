<div class="row">
    <div class="col-md-6">
        <label>Đơn vị tính quy đổi</label>
        <div class="form-group">
            <select class="form-control success-custom" id="select-measuring_unit_id" name="measuring_unit_to_id">
                @foreach($paramSelects['measuring_unit_id'] as $paramSelect)
                    <option value="{{$paramSelect['id']}}">{{$paramSelect['name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <label>Hệ số chuyển đổi</label>
        <div class="form-group">
            <input type="text" required="" name="conversion_factor" placeholder="Hệ số chuyển đổi" class="form-control success-custom">
        </div>
    </div>
</div>
