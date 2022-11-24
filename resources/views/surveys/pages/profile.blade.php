@extends('layouts.master')
@section('content')
<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                </div>
            </div>
        </div>
        <div class="content-body" style="margin-left: -330px; margin-right: 110px; margin-top: -30px;">
            <section id="page-account-settings">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="content-header-title float-left mb-0">Thông Tin Cá Nhân</h2>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75 active" id="account-pill-general"
                                                data-toggle="pill" href="#account-vertical-general"
                                                aria-expanded="true">
                                                <i class="feather icon-globe mr-50 font-medium-3 icon-profile-page"></i>
                                                Thông Tin Chung
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link d-flex py-75" id="account-pill-password"
                                                data-toggle="pill" href="#account-vertical-password"
                                                aria-expanded="false">
                                                <i class="feather icon-lock mr-50 font-medium-3 icon-profile-page"></i>
                                                Đổi mật khẩu
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="account-vertical-general"
                                            aria-labelledby="account-pill-general" aria-expanded="true">
                                            <form action="{{route('users.edit',['user'=>$user[0]->id])}}" method="POST"
                                                enctype="multipart/form-data" novalidate>
                                                <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                                                <div class="media" style="margin-top:38px">
                                                    <a href="javascript: void(0);">
                                                        @php
                                                        if(Auth::user()->image==""){
                                                        $imageProfileUser = 'default-image.png';
                                                        }
                                                        else{
                                                        $imageProfileUser = Auth::user()->image;
                                                        }
                                                        @endphp
                                                        <img src="{{asset('surveys/images/user_images/'.$imageProfileUser)}}"
                                                            id="image-profile-user" class="rounded mr-75"
                                                            alt="Ảnh nhân viên" height="64" width="64">
                                                    </a>
                                                    <div class="media-body mt-75">
                                                        <div
                                                            class="col-12 px-0 d-flex flex-sm-row flex-column justify-content-start">
                                                            <label id="btn-upload-image"
                                                                class="btn btn-sm btn-primary ml-50 mb-50 mb-sm-0 cursor-pointer font-medium-1"
                                                                for="account-upload">Chọn hình đại diện</label>
                                                            <input type="file" name="image" class="frm-validate-image"
                                                                id="account-upload" accept=".jpeg,.png,.jpg" hidden>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="newImage" id="employee-new-image">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group" style="margin-top:30px">
                                                            <div class="controls">
                                                                <label for="account-username">Tên người dùng</label>
                                                                <input type="text" name="name" class="form-control"
                                                                    id="account-username" placeholder="Tên người dùng"
                                                                    minlength="3" required
                                                                    data-validation-required-message="This username field is required"
                                                                    value="{{$user[0]->hovaten}}" autocomplete="off"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="account-e-mail">E-mail</label>
                                                                <input type="email" name="email" class="form-control"
                                                                    id="account-e-mail" placeholder="Email"
                                                                    autocomplete="off" value="{{$user[0]->email}}"
                                                                    minlength="3" required
                                                                    data-validation-required-message="This email field is required"
                                                                    disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(session('message'))
                                                    <div class="col-12">
                                                        <div class="alert alert-success">{{session('message')}}</div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </form>
                                        </div>
                                        <div class="tab-pane fade " id="account-vertical-password" role="tabpanel"
                                            aria-labelledby="account-pill-password" aria-expanded="false">
                                            <section class="multiple-validation">
                                                <form id="myForm" novalidate>
                                                    <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <div class="controls">
                                                                    <label for="account-old-password">Mật khẩu hiện
                                                                        tại</label>
                                                                    <input type="password" name="old_password"
                                                                        class="form-control" autocomplete="off"
                                                                        id="account-old-password"
                                                                        placeholder="Mật khẩu hiện tại" required
                                                                        data-validation-required-message="Bạn phải nhập mật khẩu hiện tại">
                                                                    <input type="hidden" name="id"
                                                                        value="{{$user[0]->id}}">
                                                                </div>
                                                            </div>
                                                            <div id="error-message4" class="error-message-hide"
                                                                style="color:#ea5455">Mật khẩu hiện tại của bạn sai
                                                            </div>
                                                        </div>
                                                        <!-- <div class="col-12">
                                                            
                                                        </div> -->
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <div class="controls">
                                                                    <label for="account-new-password">Mật khẩu
                                                                        mới</label>
                                                                    <input type="password" name="password"
                                                                        id="account-new-password" autocomplete="off"
                                                                        class="form-control" placeholder="Mật khẩu mới"
                                                                        required minlength="6"
                                                                        data-validation-required-message="Bạn phải nhập mật khẩu mới"
                                                                        data-validation-minlength-message="Mật khẩu mới phải có ít nhất 6 kí tự">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <div class="controls">
                                                                    <label for="account-retype-new-password">Nhập lại
                                                                        mật khẩu mới</label>
                                                                    <input type="password" name="con-password"
                                                                        autocomplete="off" class="form-control"
                                                                        id="account-retype-new-password"
                                                                        data-validation-match-match="password"
                                                                        placeholder="Nhập lại mật khẩu mới"
                                                                        data-validation-match-message="Nhập lại mật khẩu phải giống mật khẩu mới"
                                                                        required
                                                                        data-validation-required-message="Bạn phải nhập vào trường nhập lại mật khẩu mới"
                                                                        minlength="6">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div id="success-message"
                                                                class="error-message-hide alert alert-success">Thay đổi
                                                                mật khẩu thành công</div>
                                                            <div id="error-message"
                                                                class="error-message-hide alert alert-danger">Mật khẩu
                                                                mới phải khác mật khẩu cũ</div>
                                                        </div>
                                                        <div
                                                            class="col-12 d-flex flex-sm-row flex-column justify-content-center">
                                                            <button type="submit" class="btn mr-sm-1 mb-1 mb-sm-0"
                                                                style="background-color:#1C9AD6;color:#fff"
                                                                id="btn-change-password">Lưu</button>
                                                            <button type="reset"
                                                                class="btn btn-outline-warning">Hủy</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
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
<script defer src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/surveys/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
<script defer src="/surveys/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js"></script>
<script defer src="{{ mix('dashboards/surveys/js/scripts.js') }}"></script>
<script defer src="/surveys/app-assets/js/scripts/forms/validation/form-validation.js"></script>
@endpush
<!-- END: Content-->
@endsection