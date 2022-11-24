@extends('layouts.master')
@section('content')
@push('css')
<link rel="stylesheet" href="{{mix('dashboards/css/index.css')}}">
@endpush
<section id="basic-datatable">
    <div class="row">
        <div class="col-3">
            <b style="color:black">Tên rút gọn</b><br>
            <select class="select2 form-control" id="debt-structure-customer" multiple="multiple"
                style="width:175px;height:38px;border:none;border-radius:12px">
                <option value='0' selected>Tất cả</option>
                @foreach($customer_details as $key => $value)
                <option value='{{$key}}'>{{$value}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3">
            <b style="color:black">Kế toán phụ trách</b><br>
            <select class="form-control" id="accountant-responsible" aria-label="Default select example"
                style="width:175px;border:none;border-radius:12px">
                <option value='0' selected>Tất cả</option>
                @foreach($accountants as $value)
                @if($value->accountant_name == null)
                <option value="{{$value->accountant_name}}">Không xác định</option>
                @else
                <option value="{{$value->accountant_name}}">{{$value->accountant_name}}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="col-3">
            <b style="margin-left:12px">Tình trạng</b>
            <fieldset class="form-group" style="border:none !important">
                <select class="form-control" id="debt-structure-status"
                    style="width:175px;border:none;border-radius:12px">
                    <option value='0' selected>Tất cả</option>
                    @foreach($status as $value)
                    @if($value->status_id == null)
                    <option value="{{$value->status_id}}">Không xác định</option>
                    @else
                    <option value="{{$value->status_id}}">{{$value->status_id}}</option>
                    @endif
                    @endforeach
                </select>
            </fieldset>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cơ cấu nợ</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="height:370px">
                        <div id="debt-struction" class="mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Chi tiết cơ cấu nợ</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="height:370px">
                        <div id="detail-debt-struction" class="mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cơ cấu nợ quá hạn theo thời gian</h4>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                            <button class="nav-link btn btn-light debt-structure-overdue-of-month active"
                                value="debtstructureoverdueofmonth"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:11px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                id="home-tab" data-toggle="tab" aria-controls="home" role="tab"
                                aria-selected="true">Tháng</button>
                        </li>
                        <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                            <button class="nav-link btn btn-light debt-structure-overdue-of-precious"
                                value="debtstructureoverdueofprecious" id="profile-tab"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:11px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="profile" role="tab" aria-selected="false">Quý</button>
                        </li>
                        <li class="nav-item" style="width:125px; border-radius:12px; height:38px; margin-right:2px">
                            <button class="nav-link btn btn-light debt-structure-overdue-of-years"
                                value="debtstructureoverdueofyears" id="about-tab"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:11px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="about" role="tab" aria-selected="false">Năm</button>
                        </li>
                    </ul>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-400">
                            <canvas id="debt-struction-overdue-of-time"></canvas>
                        </div>
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
    const ROUTE_NAME = '{!!Route::currentRouteName()!!}';
    const MINMAX_DATE = JSON.parse('{!!json_encode($minmaxdate)!!}');
</script>
<script src="{{mix('dashboards/js/finances/defince-chart.js')}}"></script>
<script src="{{mix('dashboards/js/finances/debt-structure.js')}}"></script>
@endpush
@endsection