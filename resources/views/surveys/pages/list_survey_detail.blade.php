@extends('layouts.master')
@section('content')
<section id="nav-filled" class="app-content content" style="margin-left:-54px;margin-right:-54px">
    <div class="card col-11" id="card-filter-survey">
        <div class="card-header d-flex justify-content-center d-sm-block d-md-block d-lg-none">
            <h3>{{$titleListSurveyRecords}}</h3>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3 col-sm-6" style="max-height:100px">
                        <fieldset class="form-group position-relative">
                            <input type="text" name="date-1" class="form-control datepicker1" maxlength="10" placeholder="Ngày đầu"
                                value="{{date('d/m/Y')}}" required>
                            <div class="form-control-position datepicker-icon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-lg-3 col-sm-6" style="max-height:100px">
                        <fieldset class="form-group position-relative">
                            <input type="text" name="date-2" class="form-control datepicker2" maxlength="10" placeholder="Ngày cuối"
                            value="{{date('d/m/Y')}}" required>
                            <div class="form-control-position datepicker-icon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-lg-4 col-sm-12">
                        <select id="selectbox-choose-survey" multiple="multiple" class="form-control">
                            @foreach($surveys as $survey)
                            <option value="{{$survey->id}}" selected>{{$survey->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-1">
                        <button class="btn btn-color-dufago" id="button-filter-survey-records" style="height:41px;width:113px">
                            Tìm kiếm
                        </button>
                    </div>
                    <div class="col-lg-1">
                        <div class="action-btns d-none">
                            <div class="btn-dropdown mr-1 mb-1">
                                @role('admin|QS')
                                <div class="btn-group-delete-survey dropdown actions-dropodown ml-2">
                                    <button type="button" style="margin-bottom:-15px"
                                        class="btn btn-primary px-1 py-1 dropdown-toggle waves-effect waves-light "
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu">
                                        <a id="delete-params" type="button" class="dropdown-item action-delete"><i
                                                class="feather icon-trash-2"></i>Xoá</a>
                                    </div>
                                </div>
                                @endrole
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card overflow-hidden col-11" id="card-list-survey-records">
        <div class="card-content">
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab-fill" data-toggle="tab" href="#home-fill" role="tab"
                            aria-controls="home-fill" aria-selected="true">Danh sách</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="statistic-tab-fill" data-toggle="tab" href="#statistic-fill" role="tab"
                            aria-controls="statistic-fill" aria-selected="false">Thống kê</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="idea-tab-fill" data-toggle="tab" href="#idea-fill" role="tab"
                            aria-controls="idea-fill" aria-selected="false">Ý kiến</a>
                    </li>
                </ul>
                <input type="hidden" id="value-survey-id" value="{{$surveyId}}">
                <input type="hidden" id="check-variable" value="1">
                <div class="tab-content p-1 border-color-dufago">
                    <div class="tab-pane active" id="home-fill" role="tabpanel" aria-labelledby="home-tab-fill">
                        <h4><span id="change-name-survey">Phiếu Đánh Giá Khảo Sát Dịch Vụ Tổng Hợp</span> : Danh sách chi tiết các khảo sát</h4>
                        <div class="table-responsive">
                            <table id="datatable-survey-records" class="table no-footer data-list-view" width="100%">
                                <thead>
                                    <tr>
                                        <th
                                            class="dt-checkboxes-cell dt-checkboxes-select-all filter-disable sorting_disabled">
                                            <input id="select-all"
                                                class=" dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled filter-disable"
                                                type="checkbox" value="0" style="margin-right:20px">
                                        </th>
                                        <th>Tên khách hàng</th>
                                        <th>Tên công trình</th>
                                        <th>Địa chỉ</th>
                                        <th>Nhân viên khảo sát</th>
                                        <th>Thời gian</th>
                                        <th>Tình trạng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="statistic-fill" role="tabpanel" aria-labelledby="statistic-tab-fill">
                        <div class="row">
                            <div class="col-lg-2 p-2">
                                <div class="statistic-item border-color-dufago text-center bg-transparent p-1">
                                    <h3 id="number-survey-records" class="font-large-5 mb-2 mt-2"></h3>
                                    <h4 class="text-danger" style="margin-bottom:0.9rem">Số lượng phiếu</h4>
                                </div>
                            </div>
                            <div class="col-lg-2 p-2">
                                <div class="statistic-item border-color-dufago text-center bg-transparent p-1">
                                    <h3 id="number-customers" class="font-large-5 mb-2 mt-2"></h3>
                                    <h4 class="text-danger" style="margin-bottom:0.9rem">Khách hàng</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 p-2">
                                <div class="statistic-item border-color-dufago bg-transparent p-1">
                                    <h4 class="text-center" style="margin-bottom:2.1rem">Tỷ lệ khách hàng tham gia khảo
                                        sát</h4>
                                    <div style="height:9rem">
                                        <canvas id="simple-doughnut-chart1"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 p-2">
                                <div class="statistic-item border-color-dufago bg-transparent p-1">
                                    <h4 class="text-center" style="margin-bottom:2.1rem">Tỷ lệ khách hàng tham gia khảo
                                        sát so với tháng trước</h4>
                                    <div style="height:9rem">
                                        <canvas id="simple-doughnut-chart2"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 p-2">
                                <div class="statistic-item border-color-dufago text-center bg-transparent p-1">
                                    <h4 class="mb-3">Biểu đồ tổng số câu trả lời đối với mỗi tiêu chí</h4>
                                    <div style="height:22rem">
                                        <canvas id="bar-chart-1" style="width:100%"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 p-2">
                                <div class="statistic-item border-color-dufago text-center bg-transparent p-1">
                                    <h4 class="mb-3">Tỷ lệ tham gia cho từng khảo sát</h4>
                                    <div style="height:22rem">
                                        <canvas id="pie-chart-1"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 p-2">
                                <div class="statistic-item border-color-dufago text-center bg-transparent p-1">
                                    <h4>Tỷ lệ câu trả lời tích cực,tiêu cực/tổng câu trả lời</h4>
                                    <div style="height:22rem" class="mt-2">
                                        <canvas id="pie-chart-2"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 p-2">
                                <div class="statistic-item border-color-dufago text-center bg-transparent p-1">
                                    <h4>Biểu đồ tổng số câu trả lời đối với mỗi câu hỏi</h4>
                                    <div style="height:22rem">
                                        <canvas id="bar-chart-2"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 p-2">
                                <div class="statistic-item border-color-dufago text-center bg-transparent p-1">
                                    <h4>Tỷ lệ câu trả lời tích cực theo thời gian</h4>
                                    <div id="line-chart-container">
                                        <canvas id="line-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="idea-fill" role="tabpanel" aria-labelledby="idea-tab-fill">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="table-other-opinions" class="table no-footer data-list-view"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center">STT</th>
                                                <th>Khách hàng</th>
                                                <th>Công trình</th>
                                                <th>Ngày khảo sát</th>
                                                <th>Tên câu hỏi</th>
                                                <th>Ý kiến</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
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
@push('page_css')
<link rel="stylesheet" type="text/css" href="/surveys/assets/css/custom_selectbox_date.css">
<link rel="stylesheet" type="text/css" href="/surveys/app-assets/vendors/css/extensions/tether-theme-arrows.css">
<link rel="stylesheet" type="text/css" href="/surveys/app-assets/vendors/css/extensions/tether.min.css">
<link rel="stylesheet" type="text/css" href="/surveys/app-assets/vendors/css/extensions/shepherd-theme-default.css">
<link rel="stylesheet" type="text/css" href="/surveys/app-assets/css/pages/dashboard-analytics.css">
<link rel="stylesheet" type="text/css" href="/surveys/app-assets/css/pages/card-analytics.css">
<link rel="stylesheet" type="text/css" href="/surveys/app-assets/css/plugins/tour/tour.css">
<link rel="stylesheet" type="text/css" href="/surveys/app-assets/css/plugins/forms/validation/form-validation.css">
<link rel="stylesheet" type="text/css" href="/surveys/assets/css/custom_checkbox.css">
<link rel="stylesheet" type="text/css" href="/surveys/assets/css/style.css?v=5">
@endpush
@push('page_scripts')
<!-- <script src="/surveys/assets/t-datepicker-master/public/theme/js/t-datepicker.min.js"></script> -->
<script src="/surveys/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
<script defer src="/surveys/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script defer src="/surveys/app-assets/vendors/js/charts/chart.min.js"></script>
<script defer src="/surveys/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script defer src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="{{ mix('dashboards/surveys/js/form-select2.js') }}"></script>
<script defer src="{{ mix('dashboards/surveys/js/datatable.js') }}"></script>
<script defer src="{{ mix('dashboards/surveys/js/chart-chartjs.js') }}"></script>
<script defer src="/surveys/assets/js/custom_selectbox_date.js"></script>
@endpush
@endsection