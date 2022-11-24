@extends('layouts.master')
@section('content')
<section id="basic-datatable" class="app-content content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Phân Quyền Sử Dụng Phiếu Khảo Sát</h4>     
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="row">
                            <div class="col-md-2 col-lg-2">
                                <div class="dropdown dropdown-set-role">
                                    <button class="btn btn-outline-primary dropdown-toggle waves-effect waves-light" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Phân Quyền Phiếu Khảo Sát
                                    </button>
                                    <form class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @foreach($permissions as $permission)
                                        <label class="dropdown-item">
                                            <fieldset>
                                                <div class="vs-checkbox-con vs-checkbox-primary">
                                                    <input type="checkbox" permission="{{$permission->id}}" name="permission" value="{{$permission->name}}" class="checkbox-permission-value">
                                                    <span class="vs-checkbox" id="vs-checkbox-{{$permission->id}}">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                    <span class="">{{$permission->name}}</span>
                                                </div>
                                            </fieldset>
                                        </label>
                                        @endforeach
                                            <button type="button" id="button-confirm-set-permission" class="btn btn-primary mb-2 ml-1 waves-effect waves-light">Xác nhận</button>
                                    </form>
                                </div>
                                <div class="clearfix"></div>    
                            </div>
                            <div class="col-md-1 col-lg-1">
                                @if(Auth::user()->hasRole('admin'))
                                    <div class="dropdown dropdown-set-role">
                                        <button class="btn btn-outline-primary dropdown-toggle waves-effect waves-light" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-left:-6rem">
                                        Roles
                                        </button>
                                        <form class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @foreach($roles as $role)
                                            <label class="dropdown-item">
                                                <fieldset>
                                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                                        <input type="checkbox" role="{{$role->id}}" name="roles" value="{{$role->name}}">
                                                        <span class="vs-checkbox" id="vs-checkbox-role-{{$role->id}}">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                        <span class="">{{$role->name}}</span>
                                                    </div>
                                                </fieldset>
                                            </label>
                                            @endforeach
                                                <button type="button" class="btn btn-primary mb-2 ml-1 waves-effect waves-light">Xác nhận</button>
                                        </form>
                                    </div>
                                @endif
                                @if(Auth::user()->hasRole('QS'))
                                <input type="hidden" id="check-role-manager" value="1">
                                @else
                                <input type="hidden" id="check-role-manager" value="0">
                                @endif
                            </div>
                        </div>
                        <h5 class="mt-1">Danh Sách Nhân Viên</h5>
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-set-role-users" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" id="select-all" class="checkbox-button__input" value="false">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check checkbox-checkall-checked-icon"></i>
                                                        <i class="vs-icon feather icon-square checkbox-checked-icon"></i>
                                                    </span>
                                                </span>
                                                <span class=""></span>
                                            </div>
                                        </th>
                                        <th>STT</th>
                                        <th>Tên Người Dùng</th>
                                        <th>Email</th>
                                        <th>Phòng Ban</th>
                                        <th>Vai Trò</th>
                                        <th>Sự Cho Phép</th>
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
</section>
@endsection
@push('page_css')
    <link rel="stylesheet" type="text/css" href="surveys/assets/css/custom.css">
    <link rel="stylesheet" type="text/css" href="/surveys/app-assets/vendors/css/charts/apexcharts.css">
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
    <script defer src="/surveys/app-assets/vendors/js/tables/datatable/datatables.min.js"></script>
    <script defer src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="/surveys/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script defer src="/surveys/app-assets/vendors/js/forms/select/select2.full.min.js"></script>
    <script defer src="{{ mix('dashboards/surveys/js/form-select2.js') }}"></script>
    <script defer src="/surveys/assets/js/customer_datatable_admin.js?v=2"></script>
    <script defer src="/surveys/assets/js/custom_checkbox_datatable.js?v=3"></script>
    <script defer src="/surveys/assets/js/data-table-list.js"></script>
    <script defer src="/surveys/assets/js/permission.js"></script>
@endpush