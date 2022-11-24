<section id="data-list-view" class="data-list-view-header">
    <div class="table-edit">
        <div class="add-new-data-sidebar">
            <div class="overlay-bg"></div>
            <div class="add-new-data" style="width: 50rem">
                <div class="div px-2 d-flex new-data-title justify-content-between">
                    <div class="sidebar-arrow-left hide-data-sidebar">
                        <i class="feather icon-arrow-left"></i>
                    </div>
                    <!-- @role('admin|QS') -->
                    <div class="sidebar-title">
                        <h4 class="edit-volumetracking text-uppercase fw-bold title">Sửa</h4>
                    </div>
                    <!-- @endrole -->
                    <div class="sidebar-more-vertical">
                        <div class="sidebar-actions">
                            <div class="d-flex flex-row-reverse">
                                @if($table == 'volume_trackings')
                                @role('admin|accountant')
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button type="text" class="btn waves-effect waves-light" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="from-edit dropdown-item" id="button-editer"><i
                                                    class="feather icon-edit"></i>Sửa</a>
                                        </div>
                                    </div>
                                </div>
                                @endrole
                                @else
                                @role('admin|QS')
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button type="text" class="btn waves-effect waves-light" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="from-edit dropdown-item" id="button-editer"><i
                                                    class="feather icon-edit"></i>Sửa</a>
                                        </div>
                                    </div>
                                </div>
                                @endrole
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @widget('TabComponents',['table'=>$table])
                <div class="data-items pb-3 data-items-unset">
                    <form method="post" id="form-data-list" action="{{ route($table)}}" enctype="multipart/form-data">
                        @csrf
                        <div class="data-fields mt-3 px-2">
                            <div class="row d-block">
                                <input type="hidden" name="code_old" id="code-old">
                                <input type="hidden" name="name_old" id="name-old">
                                <input type="hidden" name="account_number_old" id="account-number-old">
                                <input type="hidden" name="bank_old" id="bank-old">
                                <input type="hidden" name="area_id_primary" id="area_id_primary">
                                <input type="hidden" name="remain_price">
                                @if(tabComponemts($table))
                                <div class="col-md-12">
                                    <div style="display:none">
                                        <select id="customer-address-hidden">
                                            {{getCustomerAddress()}}
                                        </select>
                                    </div>
                                    <div class="tab-content">
                                        @foreach(tabComponemts($table) as $key => $columnNames)
                                        @if($key != 'tab3')
                                        <div class="tab-pane {{($key == 'overview') ? 'active' : ''}}" id="{{$key}}"
                                            aria-labelledby="{{$key}}-tab" role="tabpanel">
                                            <div class="row">
                                                @foreach($columns as $column)
                                                @if($column == 'is_approve' || !in_array($column,$columnNames))
                                                @continue
                                                @endif
                                                @if($column == 'station_id' && $table == 'volume_trackings')
                                                <div class="col-md-6">
                                                    <label>Khu vực*</label>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="select-area_id"
                                                            name="area_id">
                                                            <option value=""></option>
                                                            @foreach($paramSelects['area'] as $paramSelect)
                                                            <option value="{{$paramSelect['id']}}">
                                                                {{$paramSelect['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Trạm*</label>
                                                    <div class="form-group">
                                                        <select class="form-control select2" id="select-station_id"
                                                            name="station_id">
                                                            <option value=""></option>
                                                            @foreach($paramSelects['station_id'] as $paramSelect)
                                                            <option class="option-station_id"
                                                                area_id="{{$paramSelect['area_id']}}"
                                                                value="{{$paramSelect['id']}}">{{$paramSelect['name']}}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                @elseif($column == 'vat_flag' && $table == 'volume_trackings')
                                                <div class="col-md-12">
                                                    <div class="custom-control custom-switch mr-2 mb-1">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="vat_flag" name="vat_flag">
                                                        <label class="custom-control-label" for="vat_flag"></label>
                                                        <label for="vat_flag">Có lấy hóa đơn không?</label>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="{{columnCompomentInTab($column)}}">
                                                    <label>{{__($tablePrefix.$column)}}</label>
                                                    <div class="form-group {{$column}}">
                                                        @widget('Gender', ['column'=> $column])
                                                        @widget('SelectType', ['table' => $table,'column'=>
                                                        $column,'paramSelects'=>
                                                        ($paramSelects ?? '')])
                                                        @widget('InputDateType', ['table' => $table,'column'=> $column])
                                                        @widget('InputMeasurngUnitConversion',['column'=>
                                                        $column,'paramSelects'=>
                                                        ($paramSelects ?? '')])
                                                        @widget('InputImageType',['column'=> $column])
                                                        @widget('InputTextType',['table' => $table,'column'=>
                                                        $column,'columnPrefix'=>__($tablePrefix.$column)])
                                                    </div>
                                                </div>
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @else
                                        <div class="tab-pane" id="tab3" aria-labelledby="tab3-tab" role="tabpanel">
                                            <div class="row" style="margin-bottom:10px">
                                                <div class="col-sm-2" style="padding:7px 0px 0px 30px">
                                                    <label for="data-name"><b>Công ty</b></label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <select class="form-control select2" id="company_id"
                                                        name="company_id">
                                                        <option value=""></option>
                                                        {{getOrganization()}}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <input type="hidden" name="vat_rate" id="vat_rate">
                                                <table class="table">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Nội dung</th>
                                                            <th>Tài khoản nợ</th>
                                                            <th>Tài khoản có</th>
                                                            <th>Số tiền</th>
                                                            <th>Chi tiết</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td scope="row">
                                                                Doanh thu xuất bán
                                                            </td>
                                                            <td>
                                                                <select class="form-control select2"
                                                                    name="debit_account_1_id">
                                                                    <option value="">--- Root ---
                                                                    </option>
                                                                    {{getNumberDebitCreditAccount()}}
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-control select2"
                                                                    name="credit_account_1_id">
                                                                    <option value="">--- Root ---
                                                                    </option>
                                                                    {{getNumberDebitCreditAccount()}}
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" name="revenue_entry_amount"
                                                                    onkeyup='this.value = this.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");'
                                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                                    class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="description_revenue"
                                                                    class="form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td scope="row">Thuế GTGT</td>
                                                            <td><select class="form-control select2"
                                                                    name="debit_account_2_id">
                                                                    <option value="">--- Root ---
                                                                    </option>
                                                                    {{getNumberDebitCreditAccount()}}
                                                                </select></td>
                                                            <td><select class="form-control select2"
                                                                    name="credit_account_2_id">
                                                                    <option value="">--- Root ---
                                                                    </option>
                                                                    {{getNumberDebitCreditAccount()}}
                                                                </select></td>
                                                            <td>
                                                                <input type="text" name="tax_entry_amount"
                                                                    onkeyup='this.value = this.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");'
                                                                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                                    class="form-control">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="description_tax"
                                                                    class="form-control">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                </div>
                                @else
                                <div class="col-md-12">
                                    <div class="row">
                                        @foreach($columns as $column)
                                        @if($column == 'is_approve' || $column == 'is_deleted' || $column ==
                                        'remember_token' || $column == 'email_verified_at' || $column == 'image')
                                        @continue
                                        @endif
                                        <div class="{{columnCompoment($column)}}">
                                            <label>{{__($tablePrefix.$column)}}</label>
                                            <div class="form-group {{$column}}">
                                                @widget('Gender', ['column'=> $column])
                                                @widget('SelectType', ['table' => $table,'column'=>
                                                $column,'paramSelects'=>
                                                ($paramSelects ?? '')])
                                                @widget('InputDateType', ['table' => $table,'column'=> $column])
                                                @widget('InputImageType',['column'=> $column])
                                                @widget('InputTextType',['table' => $table,'column'=>
                                                $column,'columnPrefix'=>__($tablePrefix.$column)])
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                <div class="row-error">
                                    <div class="error">
                                        <p class="mb-0 message">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="import-volumetrack data-items pb-3 data-items-unset">
                    <form id="import-volume-tracking">
                        <div class="modal-body">
                            <fieldset class="form-group" style="margin-bottom: 2px">
                                <div class="custom-file">
                                    <input type="file" name="file_volumetracking" class="custom-file-input"
                                        accept=".xlsx, .xls, .csv" id="inputGroupFile01">
                                    <label class="custom-file-label" for="inputGroupFile01">Chọn tệp</label>
                                </div>
                            </fieldset>
                            <span id="reload-error">
                                @if (count($errors) > 0)
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-1">
                                        <div class="alert alert-danger alert-dismissible">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <h4><i class="icon fa fa-ban"></i> Error!</h4>
                                            @foreach($errors->all() as $error)
                                            {{ $error }} <br>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </span>
                            <span>Download tập tin mẫu <a target="_blank"
                                    href="{{asset('files/File import khối lượng mẫu.xlsx')}}">tại đây</a><br>
                                Vui lòng chọn một file có định dạng .xls hoặc .xlsx và có thứ tự các cột theo đúng tập
                                tin mẫu.<br>
                                <p style="color:red">Lưu ý*: Các trường trong file có dấu (*) là các trường không được
                                    để trống.</p>
                            </span>
                            </label>
                        </div>
                    </form>
                </div>
                @role('admin|QS|accountant')
                <div class="add-data-footer d-none justify-content-around px-3 mt-2">
                    <div class="add-data-btn">
                        <button type="button"
                            id="{{(in_array($table,tableTransaction())) ? 'action-approve-transaction' : 'btn-submit'}}"
                            class="btn btn-primary">Lưu</button>
                    </div>
                    <div class="cancel-data-btn">
                        <button type="button" class="btn btn-outline-danger">Huỷ</button>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</section>