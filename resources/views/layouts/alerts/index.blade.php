@extends('layouts.master')
@section('content')
<div class="action-btns d-none">
    <div class="btn-dropdown mr-1 mb-1">
        <div class="btn-group-delete dropdown actions-dropodown ml-2">
            @role('admin|QS')
            <button type="button" class="btn btn-white px-1 py-1 dropdown-toggle waves-effect waves-light "
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
            </button>
            <div class="dropdown-menu">
                <a id="delete-params" type="button" class="dropdown-item action-delete"><i
                        class="feather icon-trash-2"></i>Xoá</a>
            </div>
            @endrole
        </div>
    </div>
</div>
</select>
<section id="data-list-view" class="data-list-view-header">
    <div class="d-none">
        <div class="custom-control filter-on-off d-inline-block custom-switch ml-2 mb-1">
            <input type="checkbox" class="custom-control-input is-filter-grid" id="customSwitch80">
            Filter:
            <label class="custom-control-label" for="customSwitch80">
                <span class="switch-text-left">On</span>
                <span class="switch-text-right">Off</span>
            </label>
        </div>
    </div>
    <div class="table-responsive">
        <div class="filters d-none">
            <tr></tr>
        </div>
        {!! $dataTable->table() !!}
    </div>
    <div class="table-edit">
        <div class="add-new-data-sidebar">
            <div class="overlay-bg"></div>
            <div class="add-new-data" style="width:95rem">
                <div class="div px-2 d-flex new-data-title justify-content-between ">
                    <div class="sidebar-arrow-left hide-data-sidebar">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                    <div class="sidebar-title">
                        <h4 class="text-center ml-1 mr-1 text-break text-uppercase fw-bold title">Sửa
                        </h4>
                    </div>
                    <div class="sidebar-more-vertical">
                        <div class="sidebar-actions">
                            <div class="d-flex flex-row-reverse">
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button type="text" class="btn waves-effect waves-light" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="data-items pb-3 data-items-unset">
                    <form method="POST" id="form-data-list" style="height:1900px;overflow-y:auto">
                        @csrf
                        <input type="hidden" id="name-old" name="name_old">
                        <div class="add-new-data-sidebar">
                            <div class="overlay-bg"></div>
                            <div class="add-new-data" style="width:95rem">
                                <div class="div px-2 d-flex new-data-title justify-content-between ">
                                    <div class="sidebar-arrow-left hide-data-sidebar">
                                        <i class="feather icon-arrow-left"></i>
                                    </div>
                                    <div class="sidebar-title">
                                        <h4 class="text-center ml-1 mr-1 text-break text-uppercase fw-bold title">
                                            Sửa</h4>
                                    </div>
                                    <div class="sidebar-more-vertical">
                                        <div class="sidebar-actions">
                                            <div class="d-flex flex-row-reverse">
                                                <div class="btn-group">
                                                    @role('admin|QS')
                                                    <div class="dropdown">
                                                        <button type="text" class="btn waves-effect waves-light"
                                                            id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            <i class="feather icon-more-vertical"></i>
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="from-edit dropdown-item button-editer"
                                                                id="button-editer"><i
                                                                    class="feather icon-edit"></i>Sửa</a>
                                                            <a class="from-edit dropdown-item button-print"
                                                                id="button-print"><i class="feather icon-edit"></i>In
                                                                phiếu</a>
                                                        </div>
                                                    </div>
                                                    @endrole
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="data-items pb-3">
                                    <div class="data-fields px-2">
                                        <div class="row">
                                            <div class="col-sm-12 data-field-col">
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Nhóm đối tượng</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select class="form-control select2" name="object_group"
                                                            id="object_group">
                                                            <option value=""></option>
                                                            <option value="1">Khách hàng</option>
                                                            <option value="2">Nhân viên</option>
                                                            <option value="3">Nhà cung cấp</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Công ty*</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select class="form-control select2" id="company_id"
                                                            name="company_id">
                                                            <option value=""></option>
                                                            {{getOrganization()}}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="get_customer_code_123">
                                            <div style="display:none">
                                                <select class="form-control select2 customer-name-select">
                                                    <option value=""></option>
                                                    {{getCustomer()}}
                                                </select>
                                                <select class="form-control select2 customer-address-select">
                                                    <option value=""></option>
                                                    {{getCustomerAddress()}}
                                                </select>
                                            </div>
                                            <div style="display:none">
                                                <select class="form-control select2 employee-name-select">
                                                    <option value=""></option>
                                                    {{getEmployee()}}
                                                </select>
                                                <select class="form-control select2 employee-address-select">
                                                    <option value=""></option>
                                                    {{getEmployeeAddress()}}
                                                </select>
                                            </div>
                                            <div style="display:none">
                                                <select class="form-control select2 supplier-name-select">
                                                    <option value=""></option>
                                                    {{getSupplier()}}
                                                </select>
                                                <select class="form-control select2 supplier-address-select">
                                                    <option value=""></option>
                                                    {{getSupplierAddress()}}
                                                </select>
                                            </div>
                                            <div class="col-sm-12 data-field-col">
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Đối tượng</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="row">
                                                            <div class="col-sm-4 empty-select">
                                                                <select class="form-control select2">
                                                                    <option value=""></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4 customer-select">
                                                                <select class="form-control select2" id="object_id1"
                                                                    name="object_id1">
                                                                    <option value=""></option>
                                                                    {{getCustomerCode()}}
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4 employee-select">
                                                                <select class="form-control select2" id="object_id2"
                                                                    name="object_id2">
                                                                    <option value=""></option>
                                                                    {{getEmployeeCode()}}
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-4 supplier-select">
                                                                <select class="form-control select2" id="object_id3"
                                                                    name="object_id3">
                                                                    <option value=""></option>
                                                                    {{getSupplierCode()}}
                                                                </select>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" id="object_name"
                                                                    name="object_name">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Khoản mục phí</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select class="form-control select2" id="transaction_type_id"
                                                            name="transaction_type_id">
                                                            <option value=""></option>
                                                            {{getTransactionType()}}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 data-field-col">
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Người nhận</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="payment_user" class="form-control">
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Ngày thu chi</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="payment_date" value="{{date('d-m-Y')}}"
                                                            class="form-control pickadate" maxlength="10"
                                                            placeholder="Chọn ngày thu chi">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 data-field-col">
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Địa chỉ</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" id="object_address" class="form-control"
                                                            disabled>
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Số phiếu</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="code" class="form-control"
                                                            maxlength="10" placeholder="Nhập số phiếu">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 data-field-col">
                                                <div class="row">
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Diễn giải</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <input type="text" name="description_payment"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-sm-1">
                                                        <label for="data-name">Người tạo phiếu</label>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <select class="form-control select2" id="created_by"
                                                            name="created_by">
                                                            <option value=""></option>
                                                            {{getEmployee()}}
                                                        </select>
                                                    </div>
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
                                    <div class="data-fields px-2" style="padding-top:20px">
                                        <div class="center" style="float:right">
                                            <button type="button" class="btn btn-primary" id="add-new-receipt">+ Thêm
                                                mới khoản tiền</button>
                                        </div>
                                        <table class="table" id="receipt-table">
                                            <thead>
                                                <tr class="receipt-title">
                                                    <th style="width:152px" class="text-center">Tài khoản nợ</th>
                                                    <th style="width:154px" class="text-center">Tài khoản có</th>
                                                    <th style="width:150px" class="text-center">Số tiền</th>
                                                    <th style="width:128px" class="text-center">CT đi kèm</th>
                                                    <th style="width:139px" class="text-center">Trạm</th>
                                                    <th style="width:138.88px" class="text-center">Số phải thu</th>
                                                    <th style="width:129.3px" class="text-center">Hạn thanh toán</th>
                                                    <th style="width:142px" class="text-center">Diễn giải</th>
                                                    <th style="width:63.92px" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="receipt-table"
                                                style="overflow-y:auto; height:350px; position:absolute">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="float-right" style="position:relative;top:-33px;width:400px">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>Tổng cộng:</h4>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="float-right" style="margin-right:25px" id="sum_payment_item">
                                                0<sup>đ</sup></h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="add-data-footer d-none justify-content-around px-3 mt-2">
                                    @role('admin|QS')
                                    <div class="add-data-btn">
                                        <button type="button" id="btn-submit"
                                            class="btn btn-primary waves-effect waves-light">Lưu</button>
                                    </div>
                                    <div class="cancel-data-btn">
                                        <button type="button" class="btn btn-outline-danger">Huỷ</button>
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
@push('js')
{!! $dataTable->scripts() !!}
<script src="{{ mix('dashboards/js/data-table-list.js') }}"></script>
<script src="{{ mix('dashboards/js/print.js') }}"></script>
<script>
const USER_ID = "{{Auth::user()->id}}";
const TABLE_NAME = "{{$table}}";
const INPUT_TYPE_SELECT = "{{json_encode(inputTypeSelect())}}";
const INPUT_FORMAT_PRICE = "{{json_encode(getInputFormatPrice())}}";
const URL_DELETE_API = "{{route($table.'.delete')}}";
const USER_ROLE_NAME = "{{(Auth::user()->roles[0]->name)}}";
</script>
@endpush
@endsection