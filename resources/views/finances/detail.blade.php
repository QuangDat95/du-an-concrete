@extends('layouts.master')
@section('content')
@push('css')
<link rel="stylesheet" href="{{mix('dashboards/css/index.css')}}">
@endpush
<section id="basic-datatable">
    <div class="row">
        <div class="col-xl-3">
            <b>Khách hàng</b><br>
            <select class="select2 form-control" id="customer-detail" multiple="multiple"
                style="height:27px;margin-top:5px">
                <option value='0' selected>Tất cả</option>
                @foreach($customer_overviews as $key => $value)
                <option value="{{$key}}">{{$value}}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-xl-2" style="margin-left:20px">
            <b>Từ ngày</b>
            <p><input type="text" name="date-detail1" maxlength="10" id="date-detail1" class="form-control"
                    style="width:175px;height:38px;border:none;border-radius:12px" autocomplete="off"></p>
        </div>
        <div class="col-xl-2" style="margin-left:-40px">
            <b>Đến ngày</b>
            <p><input type="text" name="date-detail2" maxlength="10" id="date-detail2" class="form-control"
                    style="width:175px;height:38px;border:none;border-radius:12px" autocomplete="off"></p>
        </div>
        <div class="col-xl-2" id="congty" style="margin-left:-40px">
            <b style="margin-left:12px">Công ty</b>
            <fieldset class="form-group" style="border:none !important">
                <select class="select2 form-control" id="company-detail" multiple="multiple"
                    style="width:180px;border:none;border-radius:12px">
                    <option value='0' selected>Tất cả</option>
                    @foreach($organization as $value)
                    <option value="{{$value->id}}">
                        {{$value->name}}
                    </option>
                    @endforeach
                </select>
            </fieldset>
        </div>
        <div class="col-xl-1" id="tinhtrang" style="margin-left:-30px">
            <b style="margin-left:12px">Tình trạng</b>
            <fieldset class="form-group" style="border:none !important">
                <select class="form-control" id="detail-status"
                    style="width:180px;border:none;border-radius:12px">
                    <option value="0">Tất cả</option>
                    @foreach($customer_status as $value)
                    <option value="{{$value->status_id}}">
                        {{$value->status_id}}
                    </option>
                    @endforeach
                </select>
            </fieldset>
        </div>
        <div class="col-xl-2" id="phanloai" style="margin-left:90px">
            <b style="margin-left:12px">Phân loại</b>
            <fieldset class="form-group" style="border:none !important">
                <select class="form-control" id="detail-classify"
                    style="width:180px;border:none;border-radius:12px">
                    <option value="0">Tất cả</option>
                    @foreach($types as $value)
                    <option value="{{$value['id']}}">
                        {{$value['name']}}
                    </option>
                    @endforeach
                </select>
            </fieldset>
        </div>
        <div class="col-12">
            <div class="card-content">
                <div class="card-body card-dashboard" style="margin:-19px -18px 0px -23px">
                    <div class="table-responsive">
                        <table class="table table-striped" id="detail-debt"
                            style="background-color:white;width:100%">
                            <thead>
                                <tr style="font-family: 'Segoe UI', Arial, sans-serif">
                                    <th style="font-size:16px;text-align:center">Tên khách hàng</th>
                                    <th style="font-size:16px;text-align:left"><div style="margin-left:-6px">Phải thu đầu kỳ</div></th>
                                    <th style="font-size:16px;text-align:left"><div style="margin-left:-8px">KH trả trước đầu kỳ</div></th>
                                    <th style="font-size:16px;text-align:left"><div style="margin-left:-10px">Phát sinh nợ</div></th>
                                    <th style="font-size:16px;text-align:left"><div style="margin-left:-12px">Phát sinh có</div></th>
                                    <th style="font-size:16px;text-align:left"><div style="margin-left:-14px">Phải thu cuối kỳ</div></th>
                                    <th style="font-size:16px;text-align:left"><div style="margin-left:-16px">KH trả trước cuối kỳ</div></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr style="font-family: Arial, Helvetica, sans-serif">
                                    <th style="font-size:16px;text-align:center">
                                        Tên khách hàng</th>
                                    <th style="font-size:16px;text-align:left">
                                        Phải thu đầu kỳ</th>
                                    <th style="font-size:16px;text-align:left">
                                        KH trả trước đầu kỳ</th>
                                    <th style="font-size:16px;text-align:left">
                                        Phát sinh nợ</th>
                                    <th style="font-size:16px;text-align:left">
                                        Phát sinh có</th>
                                    <th style="font-size:16px;text-align:left">
                                        Phải thu cuối kỳ</th>
                                    <th style="font-size:16px;text-align:left">
                                        KH trả trước cuối kỳ</th>
                                </tr>
                            </tfoot>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('js')
<script src="{{mix('dashboards/js/charts.js')}}"></script>
<script>
    $('.select2').select2();
    const MINMAX_DATE = JSON.parse('{!!json_encode($minmaxdate)!!}');
</script>
<script src="{{mix('dashboards/js/finances/defince-chart.js')}}"></script>
<script src="{{mix('dashboards/js/finances/detail.js')}}"></script>
@endpush
@endsection