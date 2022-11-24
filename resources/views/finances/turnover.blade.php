@extends('layouts.master')
@section('content')
@push('css')
<link rel="stylesheet" href="{{mix('dashboards/css/index.css')}}">
@endpush
<section id="basic-datatable">
    <div class="row">
        <div class="col-xl-2">
            <b style="color:black">Khách hàng</b><br>
            <select class="select2 form-control" name="revenue" id="customer-turnover" multiple="multiple"
                aria-label="Default select example" style="width:360px">
                <option value='0' selected>Tất cả</option>
                @foreach($customer_overviews as $key => $value)
                <option value="{{$key}}">{{trim($value)}}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-xl-2" id="congty" style="margin-left:120px">
            <b style="margin-left:12px">Công ty</b>
            <fieldset class="form-group" style="border:none !important">
                <select class="select2 form-control" id="company-turnover" multiple="multiple"
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
        <div class="col-xl-2" style="margin-left:-30px">
            <b style="color:black">Từ ngày</b>
            <p><input type="text" maxlength="10" id="turnover-date1" class="form-control"
                    style="width:175px;height:38px;border:none;border-radius:12px">
            </p>
        </div>
        <div class="col-xl-2" style="margin-left:-35px">
            <b style="color:black">Đến ngày</b>
            <p><input type="text" maxlength="10" id="turnover-date2" class="form-control"
                    style="width:175px;height:38px;border:none;border-radius:12px">
            </p>
        </div>
        <div class="col-xl-2" style="margin-left:-25px">
            <b style="color:black">Phụ trách BH</b><br>
            <select class="form-control" id="charge-of-sales" aria-label="Default select example"
                style="width:175px;height:38px;border:none;border-radius:12px">
                <option value='0' selected>Tất cả</option>
                @foreach($sell as $value)
                <option value="{{$value['id']}}">
                    {{trim($value['name'])}}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-xl-2"  style="margin-left:-30px">
            <b style="color:black">Trạm</b><br>
            <select class="form-control" id="station" aria-label="Default select example"
                style="width:175px;height:38px;border:none;border-radius:12px">
                <option value='0' selected>Tất cả</option>
                @foreach($station as $value)
                <option value="{{$value['id']}}">
                    {{trim($value['name'])}}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-xl-6">
            <div class="card" style="margin-top: 20px">
                <div class="card-header">
                    <h4 class="card-title" style="color:#2B2087">Doanh số bê
                        tông theo trạm</h4>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-300">
                            <canvas id="station-sales"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card" style="margin-top: 20px">
                <div class="card-header">
                    <h4 class="card-title" style="color:#2B2087">Doanh số bê
                        tông theo nhân viên bán hàng</h4>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-300">
                            <canvas id="staff-sales"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title" style="color:#2B2087">Doanh số bê
                        tông theo khách hàng</h4>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                            <button class="nav-link btn btn-light top10-customer-concreate-sales active"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                id="home-tab" data-toggle="tab" aria-controls="home" role="tab"
                                aria-selected="true">Top 10</button>
                        </li>
                        <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                            <button class="nav-link btn btn-light top20-customer-concreate-sales"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="profile" role="tab" aria-selected="false">Top
                                20</button>
                        </li>
                        <li class="nav-item" style="width:125px; border-radius:12px; height:38px; margin-right:2px">
                            <button class="nav-link btn btn-light top30-customer-concreate-sales"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="about" role="tab" aria-selected="false">Top
                                30</button>
                        </li>
                        <li class="nav-item" style="width:125px; border-radius:12px; height:38px; margin-right:2px">
                            <button class="nav-link btn btn-light top40-customer-concreate-sales"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="about" role="tab" aria-selected="false">Top
                                40</button>
                        </li>
                    </ul>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-800">
                            <canvas id="customer-sales"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title" style="color:#2B2087">Doanh số bê
                        tông theo công trình</h4>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                            <button class="nav-link btn btn-light top10-construction-concreate-sales active"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                id="home-tab" data-toggle="tab" aria-controls="home" role="tab"
                                aria-selected="true">Top 10</button>
                        </li>
                        <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                            <button class="nav-link btn btn-light top20-construction-concreate-sales"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="profile" role="tab" aria-selected="false">Top
                                20</button>
                        </li>
                        <li class="nav-item" style="width:125px; border-radius:12px; height:38px; margin-right:2px">
                            <button class="nav-link btn btn-light top30-construction-concreate-sales"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="about" role="tab" aria-selected="false">Top
                                30</button>
                        </li>
                        <li class="nav-item" style="width:125px; border-radius:12px; height:38px; margin-right:2px">
                            <button class="nav-link btn btn-light top40-construction-concreate-sales"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="about" role="tab" aria-selected="false">Top
                                40</button>
                        </li>
                    </ul>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-800">
                            <canvas id="construction-sales"></canvas>
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
    const MINMAX_DATE = JSON.parse('{!!json_encode($minmaxdate)!!}');
    const SUM_STATION = JSON.parse('{!!json_encode($sumStation)!!}');
    const STATION_NAME = JSON.parse('{!!json_encode($stationName)!!}');
    const SUM_EMPLOYEE = JSON.parse('{!!json_encode($sumEmployee)!!}');
    const EMPLOYEE_NAME = JSON.parse('{!!json_encode($employeeName)!!}');
    const SUM_CUSTOMER = JSON.parse('{!!json_encode($sumCustomer)!!}');
    const CUSTOMER_NAME = JSON.parse('{!!json_encode($customerName)!!}');
    const SUM_CONSTRUCTION = JSON.parse('{!!json_encode($sumConstruction)!!}');
    const CONSTRUCTION_NAME = JSON.parse('{!!json_encode($constructionName)!!}');
</script>
<script src="{{mix('dashboards/js/finances/defince-chart.js')}}"></script>
<script src="{{mix('dashboards/js/finances/turnover.js')}}"></script>
@endpush
@endsection