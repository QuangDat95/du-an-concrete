@extends('layouts.master')
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body" style="margin-right:170px;margin-left:-205px">
            <div class="row d-flex justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="content-header-title float-left mb-0">Cài đặt mật khẩu lần đầu</h2>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <form id="form-set-first-password" action="{{route('users.setPassword')}}" method="post" enctype="multipart/form-data"  class="form-horizontal error" novalidate>
                                    <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-new-password">Mật khẩu mới</label>
                                                    <input type="password" name="password" id="account-new-password" autocomplete="off" class="form-control" placeholder="Mật khẩu mới" required minlength="6" data-validation-minlength-message="Bạn phải nhập ít nhất 6 kí tự" data-validation-required-message="Bạn phải nhập mật khẩu mới">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="account-old-password" value="{{Auth::user()->password}}">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label for="account-retype-new-password">Nhập lại mật khẩu mới</label>
                                                    <input type="password" name="con-password" autocomplete="off" class="form-control" id="account-retype-new-password" data-validation-match-match="password" placeholder="Nhập lại mật khẩu mới" data-validation-required-message="Bạn phải nhập vào trường nhập lại mật khẩu mới" required data-validation-match-message="Trường nhập lại mật khẩu phải trùng mật khẩu mới">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-center">
                                            <button type="button" class="btn mr-sm-1 mb-1 mb-sm-0" style="background-color:#1C9AD6;color:#fff" id="btn-change-password">Lưu</button>
                                            <button type="reset" class="btn btn-outline-warning">Hủy</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/surveys/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
    <script defer src="/surveys/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js"></script>
    <script defer src="/surveys/app-assets/js/scripts/forms/validation/form-validation.js"></script>
    <script defer src="/surveys/assets/js/set_password.js"></script>
@endpush    
