@extends('layouts.master')
@section('content')
@push('css')
<link rel="stylesheet" href="{{mix('dashboards/css/index.css')}}">
@endpush
<section id="basic-datatable">
    <div class="row">
        <div class="col-4">
            <b style="color:black">Kế toán phụ trách</b><br>
            <select class="form-control" id="accountant-responsible-debt-collection" aria-label="Default select example"
                style="width:175px;height:38px;border:none;border-radius:12px">
                <option value='0' selected>Tất cả</option>
                @foreach($accountants as $value)
                @if($value->accountant_name == null)
                <option value="Không xác định">Không xác định</option>
                @else
                <option value="{{$value->accountant_name}}">{{$value->accountant_name}}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="col-2">
            <b style="color:black">Từ ngày</b>
            <p><input type="text" name="date-debtcollect1" maxlength="10" id="date-debtcollect1" class="form-control"
                    style="width:175px;height:38px;border:none;border-radius:12px">
            </p>
        </div>
        <div class="col-2">
            <b style="color:black">Đến ngày</b>
            <p><input type="text" name="date-debtcollect2" maxlength="10" id="date-debtcollect2" class="form-control"
                    style="width:175px;height:38px;border:none;border-radius:12px">
            </p>
        </div>
        <div class="col-4">
        </div>
        <div class="col-6">
            <div class="card" style="margin-top:26px">
                <div class="card-header">
                    <h4 class="card-title">Tỷ lệ thu nợ trong kỳ</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="height: 395px">
                        <div id="ratio-receivable-in-period" class="mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card" style="margin-top:26px">
                <div class="card-header">
                    <h4 class="card-title">Tỷ lệ nợ phải thu cuối kỳ</h4>
                </div>
                <div class="card-content">
                    <div class="card-body" style="height: 395px">
                        <div id="ratio-receivable-end-period" class="mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Nợ phải thu cuối kỳ theo khách hàng
                    </h4>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                            <button class="nav-link btn btn-light top10debt-end-period-customer active"
                                value="top10nocuoiky"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                id="home-tab" data-toggle="tab" aria-controls="home" role="tab" aria-selected="true">Top
                                10</button>
                        </li>
                        <li class="nav-item" style="width:125px; height:38px; border-radius:12px; margin-right:2px">
                            <button class="nav-link btn btn-light top20debt-end-period-customer" value="top20nocuoiky"
                                id="profile-tab"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="profile" role="tab" aria-selected="false">Top
                                20</button>
                        </li>
                        <li class="nav-item" style="width:125px; border-radius:12px; height:38px; margin-right:2px">
                            <button class="nav-link btn btn-light top30debt-end-period-customer" value="top30nocuoiky"
                                id="about-tab"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="about" role="tab" aria-selected="false">Top 30</button>
                        </li>
                        <li class="nav-item" style="width:125px; border-radius:12px; height:38px; margin-right:2px">
                            <button class="nav-link btn btn-light top40debt-end-period-customer" value="top40nocuoiky"
                                id="about-tab"
                                style="border-radius:12px;border:1px solid #2b2087;height:35px;padding-top:9px;font-family: 'Segoe UI', Arial, sans-serif;font-size:14px;width:125px"
                                data-toggle="tab" aria-controls="about" role="tab" aria-selected="false">Top 40</button>
                        </li>
                    </ul>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-700">
                            <canvas id="debt-end-period-customer"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card" style="height: 445px">
                <div class="card-header">
                    <h4 class="card-title">Tỷ lệ thu nợ trong kỳ theo kế toán
                    </h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div id="ratio-debt-collection-in-period-accountant" class="mx-auto">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card" style="height: 445px">
                <div class="card-header">
                    <h4 class="card-title">Số lượng khách hàng</h4>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-300">
                            <canvas id="amount-customer"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card" style="height: 445px">
                <div class="card-header">
                    <h4 class="card-title">tỷ lệ nợ thu được trên Nợ đầu kỳ và
                        Phát sinh nợ trong kỳ</h4>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-300">
                            <canvas id="ratio-debt-receivable-in-debt-beginning-period"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card" style="height: 445px">
                <div class="card-header">
                    <h4 class="card-title">Nợ phải thu còn lại theo kế toán</h4>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-300">
                            <canvas id="debt-receivable-remain-of-accountant"></canvas>
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
<script src="{{mix('dashboards/js/finances/debt-collection.js')}}"></script>
@endpush
@endsection