@extends('layouts.master')
@section('content')
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
            <div class="add-new-data">
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
                <div class="data-items pb-3">
                    <form method="POST" id="form-data-list">
                        @csrf
                        <input type="hidden" id="station_old" name="station_old">
                        <div class="add-new-data-sidebar">
                            <div class="overlay-bg"></div>
                            <div class="add-new-data">
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="data-items fix-scroll-sidebar data-items-unset pb-3">
                                    <div class="data-fields px-2">
                                        <div class="row">
                                            <div class="col-sm-12 data-field-col">
                                                <label for="data-name">Chọn trạm</label>
                                                <select class="form-control select2" name="station_id" id="station_id">
                                                    <option value=""></option>
                                                    {{getStationQuantity()}}
                                                </select>
                                            </div>
                                            <div class="col-sm-12 data-field-col">
                                                <label for="data-name">Chọn công ty</label>
                                                <select class="form-control select2" name="company_id" id="company_id">
                                                    <option value=""></option>
                                                    {{getOrganization()}}
                                                </select>
                                            </div>
                                            <div class="col-sm-12 data-field-col">
                                                <div class="card bg-analytics">
                                                    <div class="card-content">
                                                        <div
                                                            class="card-header bg-primary text-white d-flex align-items-center p-1 text-bold-600">
                                                            <label for="data-name" style="color:white">Doanh thu</label>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="col-sm-12">
                                                            <label for="data-name">Tài khoản ghi nợ</label>
                                                            <select class="form-control select2" name="revenue_debit_account_id">
                                                                <option value="">--- Root ---</option>
                                                                {{getglAccountVAT()}}
                                                            </select>
                                                            </div>
                                                            <div class="col-sm-12" style="padding-top:15px">
                                                            <label for="data-name">Tài khoản ghi có</label>
                                                            <select class="form-control select2" name="revenue_credit_account_id">
                                                                <option value="">--- Root ---</option>
                                                                {{getglAccountVAT()}}
                                                            </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 data-field-col">
                                                <div class="card bg-analytics">
                                                    <div class="card-content">
                                                        <div
                                                            class="card-header bg-primary text-white d-flex align-items-center p-1 text-bold-600">
                                                            <label for="data-name" style="color:white">Thuế GTGT</label>
                                                        </div>
                                                        <div class="card-body">
                                                        <div class="col-sm-12" style="padding-top:-15px">
                                                            <label for="data-name">Thuế suất GTGT</label>
                                                            <select class="form-control select2" name="vat_rate">
                                                                <option value="">--- Root ---</option>
                                                                <option value="0">0%</option>
                                                                <option value="5">5%</option>
                                                                <option value="8">8%</option>
                                                                <option value="10">10%</option>
                                                            </select>
                                                            </div>
                                                            <div class="col-sm-12" style="padding-top:15px">
                                                            <label for="data-name">Tài khoản ghi nợ</label>
                                                            <select class="form-control select2" name="tax_debit_account_id">
                                                                <option value="">--- Root ---</option>
                                                                {{getglAccountVAT()}}
                                                            </select>
                                                            </div>
                                                            <div class="col-sm-12" style="padding-top:15px">
                                                            <label for="data-name">Tài khoản ghi có</label>
                                                            <select class="form-control select2" name="tax_credit_account_id">
                                                                <option value="">--- Root ---</option>
                                                                {{getglAccountVAT()}}
                                                            </select>
                                                            </div>
                                                        </div>
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
                                </div>
                                <div class="add-data-footer d-none justify-content-around px-3 mt-2">
                                    <div class="add-data-btn">
                                        <button type="button" id="btn-submit"
                                            class="btn btn-primary waves-effect waves-light">Lưu</button>
                                    </div>
                                    <div class="cancel-data-btn">
                                        <button type="button"
                                            class="btn btn-outline-danger waves-effect waves-light">Huỷ</button>
                                    </div>
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
<script src="{{ mix('dashboards/js/transaction_entries.js') }}"></script>
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