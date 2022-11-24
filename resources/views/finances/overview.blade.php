@extends('layouts.master')
@section('content')
@push('css')
<link rel="stylesheet" href="{{mix('dashboards/css/index.css')}}">
@endpush
    <section id="basic-tabs-components">
        <div class="row">
                <div class="card overflow-hidden">
                    <div class="card-content" style="background-color:#f6f5fb">
                        <div class="card-body">
                            <div class="tab-pane active" id="overview" aria-labelledby="home-tab" role="tabpanel">
                                <div class="row match-height">
                                    <div class="row fillter-overview" style="display: flex;justify-content: center">
                                        <div class="col-lg-6">
                                            <form id="filter-date-chart" method="POST" style="display: flex;justify-content:start">
                                                <div class="card" style="background-color:#f6f5fb;width:1000px;margin-left:12px">
                                                    <b style="color:black">Khách hàng</b>
                                                    <select class="select2 form-control" id="customer-overview" multiple="multiple"
                                                        style="height:27px;border:none">
                                                        <option value='0' selected>Tất cả</option>
                                                       @foreach($customer_overviews as $key => $value)
                                                       <option value="{{$key}}">{{$value}}</option>
                                                       @endforeach
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-lg-2">
                                            <form id="filter-date-chart" method="POST" style="display: flex;justify-content:start">
                                                <div class="card" style="background-color:#f6f5fb;width:200px;margin-left:12px">
                                                    <b style="color:black">Công ty</b>
                                                    <select class="select2 form-control" id="company-overview" multiple="multiple"
                                                        style="height:27px;border:none">
                                                        <option value='0' selected>Tất cả</option>
                                                       @foreach($organization as $value)
                                                       <option value="{{$value->id}}">{{$value->name}}</option>
                                                       @endforeach
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="card"
                                                style="background-color:#f6f5fb; display: flex;justify-content: center;padding-left:20px">
                                                <b style="color:black">Từ ngày</b>
                                                <p><input type="text" name="date-from" maxlength="10" id="date-overview1" class="form-control"
                                                        style="width:170px;height:38px;border:none; border-radius:12px" autocomplete="off"></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="card" style="background-color:#f6f5fb" style="display: flex;justify-content: center">
                                                <b style="color:black">Đến ngày</b>
                                                <p><input type="text" name="date-to" maxlength="10" id="date-overview2" class="form-control"
                                                        style="width:170px;height:38px;border:none; border-radius:12px" autocomplete="off"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row overview-justify" style="margin-left:0px">
                                        <div class="col-lg-4" style="display: flex;justify-content: center">
                                            <div class="card" style="border:none; width: 515px;height:130px;border-radius:20px">
                                                <div style="text-align:center">
                                                    <h2 style="padding-top:20px; font-size:40px" class="debt-incurred"></h2>
                                                    <p style="font-size:25px">
                                                        <b>Doanh số</b>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4" style="display: flex;justify-content: center">
                                            <div class="card" style="border:none; width: 515px;height:130px;border-radius:20px">
                                                <div style="text-align:center">
                                                    <h2 style="padding-top:20px; font-size:40px" class="arise-there"></h2>
                                                    <p style="font-size:25px">
                                                        <b>Đã
                                                            thu tiền</b>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4" style="display: flex;justify-content: center">
                                            <div class="card" style="border:none; width: 515px;height:130px;border-radius:20px">
                                                <div style="text-align:center">
                                                    <h2 style="padding-top:20px; font-size:40px" class="remain"></h2>
                                                    <p style="font-size:25px">
                                                        <b>Còn
                                                            lại</b>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" style="margin-top:7px">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Doanh số bê tông theo thời gian</h4>
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                                                        <button class="nav-link btn btn-light date-debt-incurred active" value="top10nocuoiky"
                                                            style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:11px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                                            id="home-tab" data-toggle="tab" aria-controls="home" role="tab"
                                                            aria-selected="true">Ngày</button>
                                                    </li>
                                                    <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                                                        <button class="nav-link btn btn-light month-debt-incurred" value="top20nocuoiky"
                                                            id="profile-tab"
                                                            style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:11px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                                            data-toggle="tab" aria-controls="profile" role="tab"
                                                            aria-selected="false">Tháng</button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-content">
                                                <div class="card-body" style="height:470px">
                                                    <div id="debt-collection-time-line-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" style="margin-top:7px">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Thu nợ theo thời gian</h4>
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                                                        <button class="nav-link btn btn-light date-arise-there active" value="top10nocuoiky"
                                                            style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:11px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                                            id="home-tab" data-toggle="tab" aria-controls="home" role="tab"
                                                            aria-selected="true">Ngày</button>
                                                    </li>
                                                    <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                                                        <button class="nav-link btn btn-light month-arise-there" value="top20nocuoiky"
                                                            id="profile-tab"
                                                            style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:11px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                                            data-toggle="tab" aria-controls="profile" role="tab"
                                                            aria-selected="false">Tháng</button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="card-content">
                                                <div class="card-body" style="height:470px">
                                                    <div id="arise-there-time-line-chart"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </section>
@push('js')
<!-- <script src="{{mix('dashboards/js/charts.js')}}"></script> -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $('.select2').select2();
    const MINMAX_DATE = JSON.parse('{!!json_encode($minmaxdate)!!}');
</script>
<script src="{{mix('dashboards/js/finances/defince-chart.js')}}"></script>
<script src="{{mix('dashboards/js/finances/overview.js')}}"></script>
@endpush
@endsection