@extends('layouts.master')
@section('content')
@push('css')
<link rel="stylesheet" href="{{mix('dashboards/css/index.css')}}">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
@endpush
<section id="basic-datatable">
    <div class="row">
        <div class="col-4">
            <b style="color:black">Kế toán phụ trách</b><br>
            <select class="form-select" id="accountant-detail-debt-collection" aria-label="Default select example"
                style="width:175px;height:38px;border:none;border-radius:12px;font-family: 'Segoe UI', Arial, sans-serif">
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
            <p><input type="text" name="date-debt-from" maxlength="10" id="date-detail-debtcollect1"
                    class="form-control"
                    style="width:175px;height:38px;border:none;border-radius:12px;font-family: 'Segoe UI', Arial, sans-serif">
            </p>
        </div>
        <div class="col-2">
            <b style="color:black">Đến ngày</b>
            <p><input type="text" name="date-debt-to" maxlength="10" id="date-detail-debtcollect2" class="form-control"
                    style="width:175px;height:38px;border:none;border-radius:12px;font-family: 'Segoe UI', Arial, sans-serif">
            </p>
        </div>
        <div class="col-4">
        </div>
        <div class="col-6">
            <div class="card" style="margin-top: 26px">
                <div class="card-header">
                    <h4 class="card-title" style="margin-top:-7px">Số tiền thu
                        được</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped nowrap" id="monney-receivables">
                        <thead>
                            <tr style="font-family: 'Segoe UI', Arial, sans-serif;" width="100%">
                                <th style="font-size:15px"></th>
                                <th style="font-size:15px">Đối tượng
                                </th>
                                <th style="font-size:15px">
                                    <div style="float:right">Số tiền thu được
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr style="font-family: 'Segoe UI', Arial, sans-serif;">
                                <th style="font-size:15px"></th>
                                <th style="font-size:15px">Đối tượng
                                </th>
                                <th style="font-size:15px">
                                    <div style="float:right">Số tiền thu được
                                    </div>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card" style="margin-top: 26px">
                <div class="card-header">
                    <h4 class="card-title" style="margin-top:-7px">Nợ phải thu
                    </h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped nowrap" id="debt-receivables">
                        <thead>
                            <tr style="font-family: 'Segoe UI', Arial, sans-serif;width:50%">
                                <th style="font-size:15px">Kế toán
                                </th>
                                <th style="font-size:15px">Khách hàng
                                </th>
                                <th style="font-size:15px">
                                    <div style="float:right">Nợ phải thu</div>
                                </th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr style="font-family: Arial, Helvetica, sans-serif;width:50%">
                                <th style="font-size:15px">Kế toán
                                </th>
                                <th style="font-size:15px">Khách hàng
                                </th>
                                <th style="font-size:15px">
                                    <div style="float:right">Nợ phải thu</div>
                                </th>
                            </tr>
                        </tfoot>
                        <tbody></tbody>
                    </table>
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
<script src="{{mix('dashboards/js/finances/detail-debt-collection.js')}}"></script>
@endpush
@endsection