@extends('layouts.master')
@section('content')
<div class="content-header row">
    @role('admin|QS')
    <div class="content-header-left col-md-9 col-12 mb-1">
        <button type="button" class="btn bg-gradient-primary mr-1 mb-1 objective-create-btn">Thêm mới tài khoản</button>
    </div>
    @endrole
    <div class="action-btns d-none">
        <div class="btn-dropdown mr-1 mb-1">
            <div class="btn-group-delete dropdown actions-dropodown ml-2">
                <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light "
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Actions
                </button>
                <div class="dropdown-menu">
                    <a id="delete-params" type="button" class="dropdown-item action-delete"><i
                            class="feather icon-trash-2"></i>Xoá</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <section id="dashboard-analytics">
        <div class="row">
            <div class="col-sm-12">
                <div class="card overflow-hidden">
                    <div class="card-content">
                        <div class="card-body">
                            <ul class="nav nav-tabs label-treeview" role="tablist">
                                <li style="padding-left: 87%;margin-top:-84px">
                                    <section id="data-list-view" class="data-list-view-header"
                                        style="position: relative">
                                        <div class="objective-create">
                                            <div class="add-new-data-sidebar">
                                                <div class="overlay-bg"></div>
                                                <div class="add-new-data">
                                                    <div class="div px-2 d-flex new-data-title justify-content-between">
                                                        <div class="sidebar-arrow-left hide-data-sidebar">
                                                            <i class="feather icon-arrow-left"></i>
                                                        </div>
                                                        <div class="sidebar-title">
                                                            <h5
                                                                class="ml-1 mr-1 text-break text-center text-uppercase fw-bold title">
                                                                Thêm mới tài khoản kế toán</h5>
                                                        </div>
                                                    </div>
                                                    <div class="data-items pb-3">
                                                        <form method="POST" id="form-data-list">
                                                            @csrf
                                                            <input type="hidden" id="name-old" name="name_old">
                                                            <input type="hidden" id="account-code-old"
                                                                name="account_code_old">
                                                            <div class="add-new-data-sidebar">
                                                                <div class="add-new-data">
                                                                    <div
                                                                        class="div px-2 d-flex new-data-title justify-content-between ">
                                                                        <div
                                                                            class="sidebar-arrow-left hide-data-sidebar">
                                                                            <i class="feather icon-arrow-left"></i>
                                                                        </div>
                                                                        <div class="sidebar-title">
                                                                            <h4
                                                                                class="text-center ml-1 mr-1 text-break text-uppercase fw-bold title">
                                                                                Sửa</h4>
                                                                        </div>
                                                                        <div class="sidebar-more-vertical">
                                                                            <div class="sidebar-actions">
                                                                                <div class="d-flex flex-row-reverse">
                                                                                    <div class="btn-group">
                                                                                        @role('admin|QS')
                                                                                        <div class="dropdown">
                                                                                            <button type="text"
                                                                                                class="btn waves-effect waves-light"
                                                                                                id="dropdownMenuButton"
                                                                                                data-toggle="dropdown"
                                                                                                aria-haspopup="true"
                                                                                                aria-expanded="false">
                                                                                                <i
                                                                                                    class="feather icon-more-vertical"></i>
                                                                                            </button>
                                                                                            <div class="dropdown-menu"
                                                                                                aria-labelledby="dropdownMenuButton">
                                                                                                <a class="from-edit dropdown-item button-editer"
                                                                                                    id="button-editer"><i
                                                                                                        class="feather icon-edit"></i>Sửa</a>
                                                                                            </div>
                                                                                        </div>
                                                                                        @endrole
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="data-items fix-scroll-sidebar data-items-unset pb-3">
                                                                        <div class="data-fields px-2">
                                                                            <div class="row">
                                                                                <div class="col-sm-12 data-field-col">
                                                                                    <label for="data-name">Chọn tài
                                                                                        khoản cha</label>
                                                                                    <select class="form-control select2"
                                                                                        name="parent_id"
                                                                                        id="gl-account-parent-create">
                                                                                        <option value="">---Root---
                                                                                        </option>
                                                                                        {{getglAccount()}}
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-sm-12 data-field-col">
                                                                                    <label for="data-name">Mã Tài
                                                                                        khoản*</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="account_code"
                                                                                        placeholder="Nhập tài khoản">
                                                                                </div>
                                                                                <div class="col-sm-12 data-field-col">
                                                                                    <label for="data-name">Tên tài
                                                                                        khoản*</label>
                                                                                    <input type="text"
                                                                                        class="form-control" name="name"
                                                                                        placeholder="Nhập địa chỉ">
                                                                                </div>
                                                                                <div class="col-sm-12 data-field-col">
                                                                                    <label for="data-name">Tính
                                                                                        chất*</label>
                                                                                    <select class="form-control select2" id="select2-nature_id" name="nature_id">
                                                                                        <option value="">---Root---</option>
                                                                                        <option value="1">Dư nợ</option>
                                                                                        <option value="2">Dư có</option>
                                                                                        <option value="3">Lưỡng tính
                                                                                        </option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="col-sm-12 data-field-col">
                                                                                    <label for="data-name">Mô tả</label>
                                                                                    <textarea type="text"
                                                                                        class="form-control"
                                                                                        name="description"
                                                                                        id="description"
                                                                                        placeholder="Nhập địa chỉ"></textarea>
                                                                                </div>
                                                                                <div class="col-sm-12 data-field-col">
                                                                                    <div
                                                                                        class="custom-control custom-switch mr-2 mb-1">
                                                                                        <label for="customer_flag">Theo
                                                                                            dõi theo khách hàng</label>
                                                                                        <input type="checkbox"
                                                                                            class="custom-control-input"
                                                                                            id="customer_flag"
                                                                                            name="customer_flag">
                                                                                        <label
                                                                                            class="custom-control-label"
                                                                                            for="customer_flag"></label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-error">
                                                                                <div class="error">
                                                                                    <p class="mb-0 message">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="add-data-footer d-none justify-content-around px-3 mt-2">
                                                                        @role('admin|QS')
                                                                        <div class="add-data-btn">
                                                                            <button type="button" id="btn-submit"
                                                                                class="btn btn-primary waves-effect waves-light">Lưu</button>
                                                                        </div>
                                                                        <div class="cancel-data-btn">
                                                                            <button type="button"
                                                                                class="btn btn-outline-danger waves-effect waves-light">Huỷ</button>
                                                                        </div>
                                                                        @endrole
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                </li>
                            </ul>
                            <div>
                                <div class="tab-pane active" id="home-fill" role="tabpanel"
                                    aria-labelledby="home-tab-fill">
                                    <table class="table data-list-view tree" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%">MÃ TÀI KHOẢN</th>
                                                <th style="width: 35%">TÊN TÀI KHOẢN</th>
                                                <th style="width: 10%">TÍNH CHẤT</th>
                                                <th style="width: 15%;text-align:center">THEO DÕI THEO KHÁCH HÀNG</th>
                                                @role('admin|QS')
                                                <th style="width: 10%;text-align:center">HÀNH ĐỘNG</th>
                                                @endrole
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{getGlAccountTable()}}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="profile-fill" role="tabpanel"
                                    aria-labelledby="profile-tab-fill">
                                    <div class="col-md-12 col-lg-12">
                                        <div id="chart_div"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@push('js')
<script src="{{ mix('dashboards/js/reorder-form.js') }}"></script>
<script src="{{ mix('dashboards/js/data-table-list.js') }}"></script>
<script src="{{ mix('dashboards/js/delete_tree.js') }}"></script>
<script src="{{ mix('dashboards/js/gl_accounts.js') }}"></script>
<script src="{{ mix('dashboards/js/jquery.treegrid.js') }}"></script>
<script>
$('.select2').select2({
    placeholder: "Chọn",
    allowClear: true
});
const USER_ID = "{{Auth::user()->id}}";
const TABLE_NAME = "{{$table}}";
const INPUT_TYPE_SELECT = "{{json_encode(inputTypeSelect())}}";
const INPUT_FORMAT_PRICE = "{{json_encode(getInputFormatPrice())}}";
const URL_DELETE_TREE = '/'+TABLE_NAME+'/delete/ajax';
const USER_ROLE_NAME = "{{(Auth::user()->roles[0]->name)}}";
</script>
@endpush
@endsection