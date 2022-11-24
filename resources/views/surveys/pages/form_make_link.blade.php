@extends('layouts.master')
@section('content')
<div class="card" id="card-form-survey">
    <div class="card-content">
        <div class="card-body">
            <form id="form-make-link-survey" method="post" enctype="multipart/form-data"
                action="{{route('users.make-link-request')}}" class="form-horizontal error" novalidate>
                <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="label-textbox-form-survey">Chọn phiếu:</span>
                        </div>
                        <div class="col-md-8 text-box-form-survey">
                            <div class="form-group">
                                <div class="controls">
                                    <select name="survey_id" id="select-survey" class="select2 form-control" required
                                        data-validation-required-message="Bạn phải chọn phiếu">
                                        <option value="">--Chọn phiếu--</option>
                                        @foreach($survey as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="label-textbox-form-survey">Tên công trình:</span>
                        </div>
                        <div class="col-md-8 text-box-form-survey">
                            <div class="form-group">
                                <div class="controls">
                                    <select name="construction_id" id="select-box-constructions"
                                        class="select2 form-control" required
                                        data-validation-required-message="Bạn phải chọn tên công trình">
                                        <option value="">--Chọn công trình--</option>
                                        @forelse($constructions as $construction)
                                        <option value="{{$construction->id}}">{{$construction->name}}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="label-textbox-form-survey">Tên khách hàng:</span>
                        </div>
                        <div class="col-md-8 text-box-form-survey">
                            <div class="form-group">
                                <div class="controls">
                                    <select name="customer_id" id="selectbox-list-customer" class="select2 form-control"
                                        required data-validation-required-message="Bạn phải chọn tên khách hàng">
                                        <option value="">--Chọn khách hàng--</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="label-textbox-form-survey">Địa chỉ:</span>
                        </div>
                        <div class="col-md-8 text-box-form-survey">
                            <div class="form-group">
                                <div class="controls">
                                    <input type="text" id="input-address" name="address" readonly
                                        class="form-control no-enter-submit" placeholder="Địa chỉ">
                                </div>
                            </div>
                            <div class="d-flex justify-content-left">
                                <button type="submit" class="btn btn-color-dufago mr-1 mb-1 placement">Tạo link</button>
                            </div>
                        </div>
                    </div>
            </form>
            <div class="row">
                <div class="col-md-4">
                    <span class="label-textbox-form-survey">Link khảo sát:</span>
                </div>
                <div class="col-md-8 text-box-form-survey">
                    <div class="form-group">
                        <div class="controls">
                            <input type="text" id="show-link-survey" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="d-flex justify-content-left">
                        <button type="submit" id="btn-copy-survey-link" class="btn btn-color-dufago mr-1 mb-1 placement">Copy link</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('page_css')
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
<script defer src="/surveys/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
<script defer src="{{ mix('dashboards/surveys/js/form-select2.js') }}"></script>
<script defer src="/surveys/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js"></script>
<script defer src="/surveys/app-assets/js/scripts/forms/validation/form-validation.js"></script>
<script defer src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script defer src="{{ mix('dashboards/surveys/js/scripts.js') }}"></script>
@endpush