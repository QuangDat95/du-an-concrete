@extends('layouts.master')
@section('content')
@push('css')
<link rel="stylesheet" href="{{mix('dashboards/css/index.css')}}">
@endpush
<section id="basic-datatable">
    <div class="row">
        <div class="col-xl-6">
            <b style="color:black">Tên rút gọn</b><br>
            <select class="select2 form-control" id="customer-overdue" multiple="multiple"
                style="width:1000px;height:38px;border:none;border-radius:12px">
                <option value='0' selected>Tất cả</option>
                @foreach($customer_details as $key => $value)
                <option value="{{$key}}">{{$value}}</option>
                @endforeach
            </select>
        </div>
    </div>
</section>
<div class="col-12">
    <div class="card-content">
        <div class="card-body card-dashboard" style="margin:0px -30px 0px -36px">
            <div class="table-responsive">
                <table class="table table-striped" id="over-due">
                    <thead>
                        <tr style="font-family: Arial, Helvetica, sans-serif">
                            <th style="font-size:16px;text-align:center">
                                Tên khách hàng</th>
                            <th style="font-size:16px;text-align:center">
                                Tổng dư nợ</th>
                            <th style="font-size:16px;text-align:center">
                                Trong hạn</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 1 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 2 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 3 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 4 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 5 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 6 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Khó đòi</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr style="font-family: Arial, Helvetica, sans-serif">
                            <th style="font-size:16px;text-align:center">
                                Tên
                                khách hàng</th>
                            <th style="font-size:16px;text-align:center">
                                Tổng dư nợ</th>
                            <th style="font-size:16px;text-align:center">
                                Trong hạn</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 1 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 2 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 3 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 4 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 5 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Dưới 6 tháng</th>
                            <th style="font-size:16px;text-align:center">
                                Khó
                                đòi</th>
                        </tr>
                    </tfoot>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@push('js')
<script src="{{mix('dashboards/js/charts.js')}}"></script>
<script>
    $('.select2').select2();
    const MINMAX_DATE = JSON.parse('{!!json_encode($minmaxdate)!!}');
</script>
<script src="{{mix('dashboards/js/finances/defince-chart.js')}}"></script>
<script src="{{mix('dashboards/js/finances/over-due.js')}}"></script>
@endpush
@endsection