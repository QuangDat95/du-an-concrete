@extends('layouts.master')
@section('content')
<div class="card" id="card-form-survey">
    <div class="card-header d-flex justify-content-center d-lg-none d-md-block d-sm-block">
        <h4>{{$survey->name}}</h4>
    </div>
    <div class="card-content">
        <div class="card-body">
            <form id="form-add-survey-record-customer" action="{{route('surveyDetails.storeCustomer')}}"
                method="post" enctype="multipart/form-data" class="form-horizontal error" novalidate>
                <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="label-textbox-form-survey">Tên công trình:</span>
                        </div>
                        <div class="col-md-8 text-box-form-survey">
                            <div class="form-group">
                                <div class="controls">
                                    <select name="construction_id" id="select-box-constructions"
                                        class="select2 form-control" required
                                        data-validation-required-message="Bạn phải chọn tên công trình" disabled>
                                        <option value="">--Chọn công trình--</option>
                                        {{getConstructionFromId($construction)}}
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
                                        required data-validation-required-message="Bạn phải chọn tên khách hàng" disabled>
                                        <option value="">--Chọn khách hàng--</option>
                                        {{getCustomerFromId($customer)}}
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
                        </div>
                    </div>
                    <input type="hidden" name="survey_id" value="{{$survey->id}}">
                    <input type="hidden" id="survey-sort" value="{{$survey->sort}}">
                    <input type="hidden" name="user_id" value="1">
                    <input type="hidden" name="sort" value="{{$sort}}">
                    <input type="hidden" name="status" value="0">
                    <input type="hidden" name="customer_id" value="{{$customer}}">
                    <input type="hidden" name="construction_id" value="{{$construction}}">
                    @php $i = 0;
                    @endphp
                    @foreach($survey->questions as $question)
                    @php $i++;
                    @endphp
                    @if(count($question->answers)>0)
                    @if(count($question->answers)>1)
                    <fieldset class="scheduler-border">
                        <legend class="scheduler-border">
                            {{$question->name}}
                        </legend>
                        @foreach($question->answers as $answer)
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                <span style="font-weight:bold">
                                    {{$answer->key.":"}}
                                </span>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 form-group" id="item-radio-{{$answer->id}}">
                                <div class="controls">
                                    @if($answer->value_1!=""&&$answer->value_2!="")
                                    <ul class="list-unstyled mb-0" style="margin-right:1rem">
                                        <li class="d-inline-block mr-2">
                                            <fieldset>
                                                <div class="vs-radio-con">
                                                    <input type="radio" class="answer-value-{{$answer->sort}}"
                                                        name="answer_value_{{$answer->id}}"
                                                        onclick="showTextAreaReason({{$answer->sort}},'value_1')"
                                                        value="value_1" required
                                                        data-validation-required-message="Bạn phải chọn đánh giá {{$answer->key}}" />
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">{{$answer->value_1}}</span>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block mr-2">
                                            <fieldset>
                                                <div class="vs-radio-con">
                                                    <input type="radio" class="answer-value-{{$answer->sort}}"
                                                        name="answer_value_{{$answer->id}}" value="value_2"
                                                        onclick="showTextAreaReason({{$answer->sort}},'value_2')" />
                                                    <span class="vs-radio">
                                                        <span class="vs-radio--border"></span>
                                                        <span class="vs-radio--circle"></span>
                                                    </span>
                                                    <span class="">{{$answer->value_2}}</span>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>
                                    <textarea type="text" id="textarea-reason-{{$answer->sort}}" style="display:none"
                                        rows="3" name="answer_reason_{{$answer->id}}" autocomplete="off"
                                        class="form-control class-textarea-reason"
                                        placeholder="Lý do bạn đánh giá không đạt"></textarea>
                                    @else
                                    <input type="text" autocomplete="off" class="form-control"
                                        name="answer_value_{{$answer->id}}" placeholder="Nhập ý kiến">
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </fieldset>
                    @else
                    @foreach($question->answers as $answer)
                    <div class="row mb-1">
                        <div class="col-md-4">
                            <span class="label-textbox-form-survey">
                                {{$answer->key.":"}}
                            </span>
                        </div>
                        <div class="col-md-8 text-box-form-survey form-group" id="item-radio-{{$answer->id}}">
                            <div class="controls">
                                @if($answer->value_1!=""&&$answer->value_2!="")
                                <ul class="list-unstyled mb-0 answer-radio-mobile">
                                    <li class="d-inline-block mr-2">
                                        <fieldset>
                                            <div class="vs-radio-con">
                                                <input type="radio" class="answer-value-{{$answer->sort}}"
                                                    name="answer_value_{{$answer->id}}"
                                                    onclick="showTextAreaReason({{$answer->sort}},'value_1')"
                                                    value="value_1" required
                                                    data-validation-required-message="Bạn phải chọn đánh giá {{$answer->key}}" />
                                                <span class="vs-radio">
                                                    <span class="vs-radio--border"></span>
                                                    <span class="vs-radio--circle"></span>
                                                </span>
                                                <span class="">{{$answer->value_1}}</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                    <li class="d-inline-block mr-2">
                                        <fieldset>
                                            <div class="vs-radio-con">
                                                <input type="radio" class="answer-value-{{$answer->sort}}"
                                                    name="answer_value_{{$answer->id}}" value="value_2"
                                                    onclick="showTextAreaReason({{$answer->sort}},'value_2')" />
                                                <span class="vs-radio">
                                                    <span class="vs-radio--border"></span>
                                                    <span class="vs-radio--circle"></span>
                                                </span>
                                                <span class="">{{$answer->value_2}}</span>
                                            </div>
                                        </fieldset>
                                    </li>
                                </ul>
                                <textarea type="text" id="textarea-reason-{{$answer->sort}}" style="display:none"
                                    rows="3" name="answer_reason_{{$answer->id}}" autocomplete="off"
                                    class="form-control class-textarea-reason"
                                    placeholder="Lý do bạn đánh giá không đạt"></textarea>
                                @else
                                <textarea type="text" rows="3" autocomplete="off" class="form-control"
                                    name="answer_value_{{$answer->id}}" placeholder="Nhập ý kiến"></textarea>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                    @endif
                    @endforeach
                    <div class="col-md-8 offset-md-4">
                        @if(session('message'))
                        <div class="alert alert-success" id="info_success_message">{{session('message')}}</div>

                        @endif
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-color-dufago mr-1 mb-1 placement">Gửi</button>
                        <button type="reset" class="btn btn-outline-danger mr-1 mb-1">Reset</button>
                    </div>
                </div>
            </form>
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