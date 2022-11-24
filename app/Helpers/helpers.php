<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Survey\Survey;
use App\Models\Concrete\Organization;
use App\Models\Concrete\Station;
use App\Models\Concrete\Area;
use App\Models\Concrete\Construction;
use App\Models\Concrete\GlAccount;
use App\Models\Concrete\TransactionType;
use App\Models\Concrete\PaymentMethod;
use App\Models\Concrete\VolumeTracking;
use App\Models\Survey\Employee;
use App\Models\Concrete\Supplier;
use App\Models\Concrete\Customer;
use App\Models\Concrete\PaymentItem;
use App\Models\Concrete\TransactionEntry;

function saveImageDirectory($image)
{
    $directory = config('default.directory.images.inventory');
    $path = $image->move($directory, $image->getClientOriginalName());
    return $path->getPathName();
}

function getBase64Encode($code)
{
    $key = config('default.keybase64');
    return base64_encode($key . $code);
}

function getBase64Decode($code)
{
    $key = config('default.keybase64');
    return str_replace($key, '', base64_decode($code));
}

function getColumnDataBase($table, $diff = [])
{
    $columns = Schema::getColumnListing($table);
    $diffDefault = ['id', 'created_at', 'updated_at'];
    $diffColumns = array_merge($diffDefault, $diff);
    return array_diff($columns, $diffColumns);
}

function checkExistValidate($errors)
{
    $isError = '';
    if ($errors->any()) {
        $isError = 1;
    }
    return $isError;
}

function getLableSelect($column, $value)
{
    switch ($column) {
        case 'gender':
            $value == 1 ? 'Nam' : 'Nữ';
            break;
        default:
            null;
    }
}

function changeDate($date)
{
    return date('Y-m-d', strtotime(str_replace("/", "-", $date)));
}

function formatDate($date)
{
    return date("Y-m-d", strtotime($date));
}

function formatTime($time)
{
    return ($time != '') ? date_format(date_create($time), "H:i") : '';
}

function addDate($date, $number)
{
    if ($number >= 0) {
        return date('d-m-Y', strtotime($date . ' + ' . $number . ' days'));
    } else {
        return date('d-m-Y', strtotime($date . ' - ' . abs($number) . ' days'));
    }
}

function formatDateSurvey($date)
{
    return date("Y-m-d", strtotime(str_replace('/', '-', $date)));
}

function getNextId($table)
{
    $statement = DB::select("show table status like '$table'");
    return $statement[0]->Auto_increment;
}

function getCodeNextId($table, $title)
{
    preg_match_all('/(?<=\s|^)[a-z]/i', $title, $matches);
    $words = '';
    foreach ($matches[0] as $matche) {
        $words .= $matche;
    }
    $nextId = str_pad(getNextId($table), 5, '0', STR_PAD_LEFT);
    return trim($words . $nextId);
}

function inputTypeDate()
{
    return [];
}

function inputTypeSelect()
{
    $columnSelects = [
        'created_by',
        'transaction_type_id',
        'from_pour_date',
        'pour_date',
        'organization_id',
        'contract_id',
        'user_id',
        'customer_id',
        'construction_id',
        'concrete_grade_id',
        'sampleage_id',
        'slump_id',
        'vehicle_id',
        'type_id',
        'status_id',
        'payment_condition_id',
        'revenue_debit_account_id',
        'revenue_credit_account_id',
        'vat_rate',
        'tax_debit_account_id',
        'tax_credit_account_id',
        'debit_account_1_id',
        'credit_account_1_id',
        'debit_account_2_id',
        'credit_account_2_id',
        'station_id',
        'company_id',
        'payment_method_id',
        'debit_account_id',
        'credit_account_id',
        'parent_id'
    ];
    return $columnSelects;
}

function checkInputTypeSelect($column)
{
    $columnSelects = inputTypeSelect();
    return in_array($column, $columnSelects);
}

function getTypeColumn($table, $column)
{
    try {
        return DB::getSchemaBuilder()->getColumnType($table, $column);
    } catch (Exception $e) {
        $isFieldDate = in_array($column, inputTypeDate());
        if ($isFieldDate) {
            return 'date';
        }
    }
}

function tablePrefixName($table)
{
    return $table . '.';
}

function addcolumn($model)
{
    return "<input value='{$model->id}' class='select-param' type='checkbox'>";
}

function getTableParameters($initComplete = null, $buttons = null)
{
    return [
        'aaSorting' => [],
        'info' => false,
        'paging' => true,
        'orderCellsTop' => true,
        'search' => ['regex' => true, 'smart' => true],
        'columnDefs' => [['targets' => 0, 'className' => 'dt-checkboxes-cell', 'width' => '8%', 'orderable' => false]],
        'dom' => '<"top actions-sticky"<"actions action-btns"B><"action-filters"lf>><"clear">rt<"bottom"<"actions">p>',
        'oLanguage' => ['sLengthMenu' => '_MENU_', 'sSearch' => ''],
        'aLengthMenu' => [[6, 10, 12, 25, 50, 100], [6, 10, 12, 25, 50, 100]],
        'pageLength' => 6,
        'buttons' => $buttons,
        'deferRender' => true,
        'scrollCollapse' => true,
        'scroller' => true,
        'initComplete' => "function () {
               {$initComplete}
               var api = this.api();
               
            // For each column
            api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                    var cell = $('.filters th').eq(
                        $(api.column(colIdx).header()).index()
                    );
                   
                    if($(cell).hasClass('filter-disable')){ 
                          $(cell).html('');
                    }else{
                        var option = '';
                        api.column(colIdx).data().unique().sort().each( function ( d, j ) {
                        if(TABLE_NAME == 'receipts' || TABLE_NAME == 'payments' || TABLE_NAME == 'debits'){
                            var value = d;
                            if(colIdx == 1){
                                var dateAr = value.split('-');
                                var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0].slice();
                                option += '<option value=\"'+newDate+'\">'+d+'</option>';
                            }else if(colIdx == 5){
                                var valueNumber = value.replaceAll(',', '')
                                option += '<option value=\"'+valueNumber+'\">'+d+'</option>';                              
                            }else if(colIdx == 7){
                                if(d == 'Đã hoàn tất'){
                                    var valueId = 1;
                                }else if(d == 'Chưa hoàn tất'){
                                    var valueId = 0;
                                }
                                option += '<option value=\"' + valueId + '\">'+d+'</option>';                              
                            }else{
                             option += '<option value=\"'+d+'\">'+d+'</option>';
                            }
                        }else if(TABLE_NAME == 'volume_trackings'){
                            var value = d;
                            if(colIdx == 2){
                                var dateAr = value.split('-');
                                var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0].slice();
                                option += '<option value=\"'+newDate+'\">'+d+'</option>';                              
                            }else if(colIdx == 6){
                                var valueNumber = value.replaceAll(',', '')
                                option += '<option value=\"'+valueNumber+'\">'+d+'</option>';                              
                            }else if(colIdx == 7){
                                if(d == 'Có'){
                                    var valueId = 1;
                                }else if(d == 'Không'){
                                    var valueId = 0;
                                }
                                option += '<option value=\"' + valueId + '\">'+d+'</option>';
                            }else if(colIdx == 8){
                                if(d == 'Đã định khoản'){
                                    var valueId = 1;
                                }else if(d == 'Chưa định khoản'){
                                    var valueId = 0;
                                }
                                option += '<option value=\"' + valueId + '\">'+d+'</option>';
                            }else{
                             option += '<option value=\"'+d+'\">'+d+'</option>';
                            }
                        }else{
                            option += '<option value=\"'+d+'\">'+d+'</option>';
                        }
                    });   
                        $(cell).html('<div class=\"form-group\"><select class=\"select2 form-control\" multiple=\"multiple\">'+option+'</select></div>');
                    }
                    $(
                        'select',
                        $('.filters th').eq($(api.column(colIdx).header()).index())
                    )
                        .off('change')
                        .on('change', function (e) {
                            var search = [];
                            var value = $(this).val();                                
                            e.stopPropagation();
                            e.preventDefault()
                            search = $(this).val().join('|');
                            api.column(colIdx).search(search,true, false).draw();
                        });   
                });  

                $('.select2:not(#select-pour_date)').select2({
                    placeholder: \"Chọn\",
                    allowClear: true,
                    dropdownAutoWidth: true,
                    width: '100%'
                });
                $('#select-pour_date').select2({
                    placeholder: \"Chọn ngày*\",
                    allowClear: true,
                    dropdownAutoWidth: true,
                    width: '100%'
                });

                $('.select2-filter-table').select2({
                placeholder: \"Chọn\",
                dropdownAutoWidth: true
                });
            }",
    ];
}

function getFormShowDetail($table)
{
    return in_array($table, tableShowDetail());
}

function isJson($string)
{
    json_decode($string);
    return json_last_error() == JSON_ERROR_NONE;
}

function tabComponemts($table)
{
    $tabs = [];
    switch ($table) {
        case "volume_trackings":
            $tabs['overview'] = [
                'station_id',
                'user_id',
                'from_date',
                'contract_id',
                'sale_user_id',
                'customer_id',
                'construction_id',
                'concrete_grade_id',
                'sampleage_id',
                'slump_id',
                'vehicle_id',
                'pumping_time_begin',
                'pumping_time_finish',
                'engineer_id',
                'pumping_time',
                'article',
                'due_date',
            ];
            $tabs['tab1'] = [
                'actual_weight',
                'minus_volume',
                'sending_volume',
                'payment_volume',
                'concreate_price',
                'sendprice_concreate',
                'additive_price',
                'sendprice_addditive',
                'pump_price',
                'sendprice_pump',
                'shipping_surcharge',
                'pump_surcharge',
                'introduce',
                'tip',
                'received_date',
                'total_price',
                'comment',
            ];
            $tabs['tab2'] = [
                'vat_flag', 
                'vat_company', 
                'vat_address', 
                'tax_number', 
                'serial_number', 
                'vat_number', 
                'vat_date', 
                'vat_amount'
            ];
            $tabs['tab3'] = [
                'debit_account_1_id', 
                'credit_account_1_id', 
                'revenue_entry_amount', 
                'description_revenue', 
                'vat_rate', 
                'debit_account_2_id', 
                'credit_account_2_id', 
                'tax_entry_amount', 
                'description_tax'
            ];
            break;
        default:
            return $tabs;
    }
    return $tabs;
}

function columnCompomentInTab($column)
{
    $colMd12 = ['customer_id', 'construction_id', 'comment', 'payment_condition_id', 'article', 'vat_flag', 'vat_company'];
    $colMd4 = ['type_id', 'status_id', 'accountant_name'];
    if (in_array($column, $colMd12)) {
        return 'col-md-12';
    } elseif (in_array($column, $colMd4)) {
        return 'col-md-4';
    } else {
        return 'col-md-6';
    }
}

function columnCompoment($column)
{
    $colMd6 = ['phone_director', 'phone_accountant', 'phone_cht', 'phone_qs', 'contract_date', 'due_date', 'from_pour_date', 'pour_date'];
    $colMd4 = ['type_id', 'status_id', 'accountant_name'];

    if (in_array($column, $colMd4)) {
        return 'col-md-4';
    } elseif (in_array($column, $colMd6)) {
        return 'col-md-6';
    } else {
        return 'col-md-12';
    }
}

function getInputFormatPrice()
{
    return [
        'concreate_price',
        'total_price',
        'additive_price',
        'payment_volume',
        'minus_volume',
        'sending_volume',
        'pump_price',
        'shipping_surcharge',
        'tip',
        'sendprice_addditive',
        'sendprice_concreate',
        'sendprice_pump',
        'actual_weight',
        'pump_surcharge',
        'revenue_entry_amount',
        'tax_entry_amount',
    ];
}

function fieldCustomerStaus()
{
    return [['id' => 'Đang hoạt động', 'name' => 'Đang hoạt động'], ['id' => 'Không hoạt động', 'name' => 'Không hoạt động'], ['id' => 'Không phát sinh', 'name' => 'Không phát sinh'], ['id' => 'Khởi kiện', 'name' => 'Khởi kiện']];
}

function fieldCustomerType()
{
    return [['id' => 'CÁ NHÂN', 'name' => 'Cá nhân'], ['id' => 'DOANH NGHIỆP', 'name' => 'Công ty'], ['id' => 'NỘI BỘ', 'name' => 'Nội bộ']];
}

function formatFloatNumber($number)
{
    return round((float) str_replace(',', '', $number), 1) ? round((float) str_replace(',', '', $number), 1) : 0;
}

function formatIntNumber($number)
{
    return (int) str_replace(',', '', $number) ? (int) str_replace(',', '', $number) : 0;
}

function formatDateExcel($date)
{
    return ($date != null) ? date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date), "Y-m-d") : null;
}

function surveyByPermission()
{
    $surveys = [];
    switch (true) {
        case Auth::user()->hasRole('QS'):
            return Survey::orderBy('name')->get(['id', 'name', 'image']);
            break;
        case Auth::user()->hasRole('admin'):
            return Survey::orderBy('name')->get(['id', 'name', 'image']);
            break;
        case Auth::user()->hasAllPermissions(['Phiếu Khảo Sát Chất Lượng Dịch Vụ Số 1', 'Phiếu Khảo Sát Chất Lượng Dịch Vụ Số 2']):
            return Survey::orderBy('name')->get(['id', 'name', 'image']);
            break;
        case Auth::user()->hasPermissionTo('Phiếu Khảo Sát Chất Lượng Dịch Vụ Số 1'):
            return Survey::where('sort', '1')->get(['id', 'name', 'image']);
            break;
        case Auth::user()->hasPermissionTo('Phiếu Khảo Sát Chất Lượng Dịch Vụ Số 2'):
            return Survey::where('sort', '2')->get(['id', 'name', 'image']);
            break;
        default:
            return $surveys;
            break;
    }
}

function getOrganization()
{
    $nodes = Organization::where('organization_type_id', 1)
        ->get()
        ->toTree();
    $traverse = function ($organizations, $prefix = '') use (&$traverse) {
        // echo PHP_EOL . "<option  value=''></option>";
        foreach ($organizations as $organization) {
            echo PHP_EOL . "<option  value='" . $organization->id . "'>" . $prefix . " " . $organization->name . "</option>";
            $traverse($organization->children, $prefix . '-');
        }
    };
    return $traverse($nodes);
}

function getTransactionType()
{
    $nodes = TransactionType::get();
    $traverse = function ($transaction_types) use (&$traverse) {
        // echo PHP_EOL . "<option value=''></option>";
        foreach ($transaction_types as $transaction_type) {
            echo PHP_EOL . "<option value='" . $transaction_type->id . "'> " . $transaction_type->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getNumberDebitCreditAccount()
{
    $id = [];
    $gl_account = GlAccount::select('id')->get();
    foreach ($gl_account as $value) {
        $id[] = $value->id;
    }
    $id2 = [];
    $gl_account1 = GlAccount::where('level', '=', '2')
        ->select('parent_id')
        ->get();
    foreach ($gl_account1 as $value) {
        $id1[] = $value->parent_id;
    }
    $array_id = array_diff($id, $id1);
    $nodes = GlAccount::whereIn('id', $array_id)->get();
    $traverse = function ($gl_accounts) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($gl_accounts as $gl_account) {
            echo PHP_EOL . "<option value='" . $gl_account->id . "'>" . $gl_account->account_code . "</option>";
        }
    };
    return $traverse($nodes);
}

function getDebitCreditAccount()
{
    $id = [];
    $gl_account = GlAccount::select('id')
        ->get()
        ->toTree();
    foreach ($gl_account as $value) {
        $id[] = $value->id;
    }
    $id1 = [];
    $gl_account1 = GlAccount::where('level', '=', 2)
        ->select('parent_id')
        ->get();
    foreach ($gl_account1 as $value) {
        $id1[] = $value->parent_id;
    }
    $array_id = array_diff($id, $id1);
    $nodes = GlAccount::whereIn('id', $array_id)->get();
    $traverse = function ($gl_accounts) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($gl_accounts as $gl_account) {
            echo PHP_EOL . "<option value='" . $gl_account->id . "'>" . $gl_account->account_code . " - " . $gl_account->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getNumberEditDebitCreditAccount($debit_id)
{
    $id = [];
    $gl_account = GlAccount::select('id')->get();
    foreach ($gl_account as $value) {
        $id[] = $value->id;
    }
    $id2 = [];
    $gl_account1 = GlAccount::where('level', '=', '2')
        ->select('parent_id')
        ->get();
    foreach ($gl_account1 as $value) {
        $id1[] = $value->parent_id;
    }
    $array_id = array_diff($id, $id1);
    $nodes = GlAccount::whereIn('id', $array_id)->get();
    $traverse = function ($gl_accounts, $debit_id) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($gl_accounts as $gl_account) {
            $selected = $gl_account->id == $debit_id ? "selected" : "";
            echo PHP_EOL . "<option value='" . $gl_account->id . "' " . $selected . ">" . $gl_account->account_code . "</option>";
        }
    };
    return $traverse($nodes, $debit_id);
}

function getglAccount()
{
    $nodes = GlAccount::get()->toTree();
    $traverse = function ($glAccounts, $prefix = '') use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($glAccounts as $glAccount) {
            echo PHP_EOL . "<option level='" . $glAccount->level . "'  value='" . $glAccount->id . "'>" . $prefix . " " . $glAccount->name . "</option>";
            $traverse($glAccount->children, $prefix . '-');
        }
    };
    return $traverse($nodes);
}

function getglAccountVAT()
{
    $id = [];
    $gl_account = GlAccount::select('id')->get();
    foreach ($gl_account as $value) {
        $id[] = $value->id;
    }
    $id2 = [];
    $gl_account1 = GlAccount::where('level', '=', '2')
        ->select('parent_id')
        ->get();
    foreach ($gl_account1 as $value) {
        $id1[] = $value->parent_id;
    }
    $array_id = array_diff($id, $id1);
    $nodes = GlAccount::whereIn('id', $array_id)->get();
    $traverse = function ($gl_accounts) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($gl_accounts as $gl_account) {
            echo PHP_EOL . "<option value='" . $gl_account->id . "'>" . $gl_account->account_code . "</option>";
        }
    };
    return $traverse($nodes);
}

function tableTransaction()
{
    $params = [];
    return $params;
}

function getArea()
{
    $nodes = Area::get();
    $traverse = function ($areas) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($areas as $area) {
            echo PHP_EOL . "<option value='" . $area->id . "'>" . $area->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getConstruction()
{
    $nodes = Construction::select('id', 'name')->get();
    $traverse = function ($constructions) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($constructions as $construction) {
            echo PHP_EOL . "<option value='" . $construction->id . "'>" . $construction->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getConstructions()
{
    return Construction::select('id', 'name')->get();
}

function getCustomer()
{
    $nodes = Customer::select('id', 'name')->get();
    $traverse = function ($customers) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($customers as $customer) {
            echo PHP_EOL . "<option value='" . $customer->id . "'>" . $customer->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getConstructionFromId($id)
{
    $nodes = Construction::select('id', 'name')->get();
    $traverse = function ($constructions, $id) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($constructions as $construction) {
            $selected = $construction->id == $id ? 'selected' : '';
            echo PHP_EOL . "<option value='" . $construction->id . "' " . $selected . ">" . $construction->name . "</option>";
        }
    };
    return $traverse($nodes, $id);
}

function getCustomerFromId($id)
{
    $nodes = Customer::select('id', 'name')->get();
    $traverse = function ($customers, $id) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($customers as $customer) {
            $selected = $customer->id == $id ? 'selected' : '';
            echo PHP_EOL . "<option value='" . $customer->id . "' " . $selected . ">" . $customer->name . "</option>";
        }
    };
    return $traverse($nodes, $id);
}

function getEmployee()
{
    $nodes = Employee::select('id', 'hovaten')->get();
    $traverse = function ($employees) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($employees as $employee) {
            echo PHP_EOL . "<option value='" . $employee->id . "'>" . $employee->hovaten . "</option>";
        }
    };
    return $traverse($nodes);
}

function getSupplier()
{
    $nodes = Supplier::select('id', 'name')->get();
    $traverse = function ($suppliers) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($suppliers as $supplier) {
            echo PHP_EOL . "<option value='" . $supplier->id . "'>" . $supplier->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getCustomerAddress()
{
    $nodes = Customer::select('id', 'address')->get();
    $traverse = function ($customers) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($customers as $customer) {
            echo PHP_EOL . "<option value='" . $customer->id . "'>" . $customer->address . "</option>";
        }
    };
    return $traverse($nodes);
}

function getEmployeeAddress()
{
    $nodes = Employee::select('id')->get();
    $traverse = function ($employees) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($employees as $employee) {
            echo PHP_EOL . "<option value='" . $employee->id . "'></option>";
        }
    };
    return $traverse($nodes);
}

function getSupplierAddress()
{
    $nodes = Supplier::select('id', 'address')->get();
    $traverse = function ($suppliers) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($suppliers as $supplier) {
            echo PHP_EOL . "<option value='" . $supplier->id . "'>" . $supplier->address . "</option>";
        }
    };
    return $traverse($nodes);
}

function getEmployeeCode()
{
    $nodes = Employee::select('id', 'manhanvien','hovaten')->get();
    $traverse = function ($employees) use (&$traverse) {
        echo PHP_EOL . "<option object-group='2' value=''></option>";
        foreach ($employees as $employee) {
            echo PHP_EOL . "<option object-group='2' object-name='" . $employee->hovaten . "' object-address='' value='" . $employee->id . "'>" . $employee->manhanvien . " - " . $employee->hovaten . "</option>";
        }
    };
    return $traverse($nodes);
}

function getSupplierCode()
{
    $nodes = Supplier::select('id', 'code','name','address')->get();
    $traverse = function ($suppliers) use (&$traverse) {
        echo PHP_EOL . "<option object-group='3' value=''></option>";
        foreach ($suppliers as $supplier) {
            echo PHP_EOL . "<option object-group='3' object-name='" . $supplier->name . "' object-address='".$supplier->address."' value='" . $supplier->id . "'>" . $supplier->code . " - " . $supplier->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getCustomerCode()
{
    $nodes = Customer::select('id', 'name_other','name','address')->get();
    $traverse = function ($customers) use (&$traverse) {
        echo PHP_EOL . "<option object-group='1' value=''></option>";
        foreach ($customers as $customer) {
            echo PHP_EOL . "<option object-group='1' object-name='" . $customer->name . "' object-address='".$customer->address."' value='" . $customer->id . "'>" . $customer->name_other." - ".$customer->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getGlAccountTable()
{
    $nodes = GlAccount::get()->toTree();
    $traverse = function ($glAccounts) use (&$traverse) {
        foreach ($glAccounts as $glAccount) {
            if ($glAccount->parent_id == null) {
                if (auth()->user()->hasRole('admin|QS')) {
                    echo PHP_EOL."<tr class='level-0 treegrid-".$glAccount->id."'><input type='hidden' class='select-param' value='".$glAccount->id."'><td><a class='select-param' data-id='".$glAccount->id."'>".$glAccount->account_code."</a></td><td>".$glAccount->name."</td><td>".config('default.nature_id.'.$glAccount->nature_id)."</td><td class='text-center'>".config('default.customer_flag.'.$glAccount->customer_flag)."</td><td class='text-center'><a class='delete-param' data-id='".$glAccount->id."'><i class='fa fa-trash-o'></i></a></td></tr>";
                } else {
                    echo PHP_EOL."<tr class='level-0 treegrid-".$glAccount->id."'><input type='hidden' class='select-param' value='".$glAccount->id."'><td><a class='select-param' data-id='".$glAccount->id."'>".$glAccount->account_code."</a></td><td>".$glAccount->name."</td><td>".config('default.nature_id.'.$glAccount->nature_id)."</td><td class='text-center'>".config('default.customer_flag.'.$glAccount->customer_flag)."</td></tr>";
                }
            } else {
                if (auth()->user()->hasRole('admin|QS')) {
                    echo PHP_EOL."<tr class='treegrid-".$glAccount->id." treegrid-parent-".$glAccount->parent_id."' treegrid-parent='".$glAccount->id."' parent='".$glAccount->parent_id."'><input type='hidden' class='select-param' value='".$glAccount->id."'><td><a class='select-param' data-id='".$glAccount->id."'>".$glAccount->account_code."</a></td><td>".$glAccount->name."</td><td>".config('default.nature_id.' . $glAccount->nature_id)."</td><td class='text-center'>".config('default.customer_flag.'.$glAccount->customer_flag)."</td><td class='text-center'><a class='delete-param' data-id='".$glAccount->id."'><i class='fa fa-trash-o'></i></a></td></tr>";
                } else {
                    echo PHP_EOL."<tr class='treegrid-".$glAccount->id." treegrid-parent-".$glAccount->parent_id."' treegrid-parent='".$glAccount->id."' parent='".$glAccount->parent_id."'><input type='hidden' class='select-param' value='".$glAccount->id."'><td><a class='select-param' data-id='".$glAccount->id."'>".$glAccount->account_code."</a></td><td>".$glAccount->name."</td><td>".config('default.nature_id.'.$glAccount->nature_id)."</td><td class='text-center'>".config('default.customer_flag.'.$glAccount->customer_flag)."</td></tr>";
                }
            }
            $traverse($glAccount->children);
        }
    };
    return $traverse($nodes);
}

function getOrganizationTable()
{
    $nodes = Organization::where('organization_type_id', 1)
        ->get()
        ->toTree();
    $traverse = function ($organizations) use (&$traverse) {
        foreach ($organizations as $organization) {
            if ($organization->parent_id == null) {
                if (auth()->user()->hasRole('admin|QS')) {
                    echo PHP_EOL . "<tr class='level-0 treegrid-" . $organization->id . "'><input type='hidden' class='select-param' value='" . $organization->id . "'><td><a class='select-param' data-id='" . $organization->id . "'>" . $organization->name . "</a></td><td>" . $organization->tax_number . "</td><td>" . $organization->email . "</td><td class='text-center'><a class='delete-param' data-id='" . $organization->id . "'><i class='fa fa-trash-o'></i></a></td></tr>";
                } else {
                    echo PHP_EOL . "<tr class='level-0 treegrid-" . $organization->id . "'><input type='hidden' class='select-param' value='" . $organization->id . "'><td><a class='select-param' data-id='" . $organization->id . "'>" . $organization->name . "</a></td><td>" . $organization->tax_number . "</td><td>" . $organization->email . "</td></tr>";
                }
            } else {
                if (auth()->user()->hasRole('admin|QS')) {
                    echo PHP_EOL."<tr class='treegrid-" . $organization->id . " treegrid-parent-" . $organization->parent_id . "' treegrid-parent='" . $organization->id . "' parent='" . $organization->parent_id . "'><input type='hidden' class='select-param' value='" . $organization->id . "'><td><a class='select-param' data-id='" . $organization->id . "'>" . $organization->name . "</a></td><td>" . $organization->tax_number . "</td><td>" . $organization->email . "</td><td class='text-center'><a class='delete-param' data-id='" . $organization->id . "'><i class='fa fa-trash-o'></i></a></td></tr>";
                } else {
                    echo PHP_EOL."<tr class='treegrid-".$organization->id." treegrid-parent-".$organization->parent_id."' treegrid-parent='".$organization->id."' parent='".$organization->parent_id."'><input type='hidden' class='select-param' value='".$organization->id."'><td><a class='select-param' data-id='".$organization->id."'>".$organization->name."</a></td><td>".$organization->tax_number."</td><td>".$organization->email."</td></tr>";
                }
            }
            $traverse($organization->children);
        }
    };
    return $traverse($nodes);
}

function getPaymentMethod()
{
    $nodes = PaymentMethod::select('id', 'name')->get();
    $traverse = function ($paymentmethods) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($paymentmethods as $paymentmethod) {
            echo PHP_EOL . "<option value='" . $paymentmethod->id . "'>" . $paymentmethod->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getOptionDebt($ids)
{
    $id = [];
    $gl_account = GlAccount::select('id')->get();
    foreach ($gl_account as $value) {
        $id[] = $value->id;
    }
    $id2 = [];
    $gl_account1 = GlAccount::where('level', '=', '2')
        ->select('parent_id')
        ->get();
    foreach ($gl_account1 as $value) {
        $id1[] = $value->parent_id;
    }
    $array_id = array_diff($id, $id1);
    $nodes = GlAccount::whereIn('id', $array_id)
        ->where('nature_id', 1)
        ->orwhere('nature_id', 3)
        ->get();
    $traverse = function ($gl_accounts) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($gl_accounts as $gl_account) {
            if ($ids == $gl_account->id) {
                echo PHP_EOL . "<option value='" . $gl_account->id . "' selected>" . $gl_account->account_code . " - " . $gl_account->name . "</option>";
            } else {
                echo PHP_EOL . "<option value='" . $gl_account->id . "'>" . $gl_account->account_code . " - " . $gl_account->name . "</option>";
            }
        }
    };
    return $traverse($nodes);
}

function getOptionCredit($ids)
{
    $id = [];
    $gl_account = GlAccount::select('id')->get();
    foreach ($gl_account as $value) {
        $id[] = $value->id;
    }
    $id2 = [];
    $gl_account1 = GlAccount::where('level', '=', '2')
        ->select('parent_id')
        ->get();
    foreach ($gl_account1 as $value) {
        $id1[] = $value->parent_id;
    }
    $array_id = array_diff($id, $id1);
    $nodes = GlAccount::whereIn('id', $array_id)
        ->where('nature_id', 2)
        ->orwhere('nature_id', 3)
        ->get();
    $traverse = function ($gl_accounts) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($gl_accounts as $gl_account) {
            if ($ids == $gl_account->id) {
                echo PHP_EOL . "<option value='" . $gl_account->id . "' selected>" . $gl_account->account_code . " - " . $gl_account->name . "</option>";
            } else {
                echo PHP_EOL . "<option value='" . $gl_account->id . "'>" . $gl_account->account_code . " - " . $gl_account->name . "</option>";
            }
        }
    };
    return $traverse($nodes);
}

function getMenuDebt()
{
    $nodes = Organization::where('organization_type_id', 1)
        ->get()
        ->toTree();
    $traverse = function ($organizations) use (&$traverse) {
        foreach ($organizations as $organization) {
            echo PHP_EOL .
                '<li><a href="/receipt/' .
                $organization->id .
                '"><i class="fa fa-building-o"></i><span class="menu-item">' .
                $organization->name .
                '</span></a>
            <ul class="menu-content">
                
            </ul>
        </li>';
            $traverse($organization->children);
        }
    };
    return $traverse($nodes);
}

function getVolumeTracking()
{
    $nodes = VolumeTracking::get();
    $traverse = function ($volumetrackings) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($volumetrackings as $volumetracking) {
            echo PHP_EOL . "<option value='" . $volumetracking->id . "'>" . $volumetracking->id . "</option>";
        }
    };
    return $traverse($nodes);
}

function getEditVolumeTracking($id_volume,$customer_id)
{
    if($customer_id != ''){
        $nodes = VolumeTracking::select('id')->where('customer_id',$customer_id)->get();
    }else{
        $nodes = VolumeTracking::select('id')->get();
    }
    $traverse = function ($volumetrackings, $id_volume) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($volumetrackings as $volumetracking) {
            $selected = $volumetracking->id == $id_volume ? "selected" : "";
            echo PHP_EOL . "<option value='" . $volumetracking->id . "'" . $selected . ">" . $volumetracking->id . "</option>";
        }
    };
    return $traverse($nodes, $id_volume);
}

function checkVolumeId($id)
{
    $receipts = PaymentItem::select('volumn_trackings_id')
        ->where('payment_id', $id)
        ->get()
        ->toArray();
    $array_volume = [];
    foreach ($receipts as $value) {
        $array_volume[] = $value['volumn_trackings_id'];
    }
    if (in_array(null, $array_volume)) {
        return 0;
    } else {
        return 1;
    }
}

function getStation()
{
    $nodes = Station::select('id', 'name')
        ->where('organization_type_id', 2)
        ->get();
    $traverse = function ($stations) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($stations as $station) {
            echo PHP_EOL . "<option value='" . $station->id . "'>" . $station->name . "</option>";
        }
    };
    return $traverse($nodes);
}

function getStationQuantity()
{
    $station_ids = Organization::select('id')
        ->where('organization_type_id', 2)
        ->get();
    $station_id = TransactionEntry::select('station_id')->get();
    $array1 = [];
    $array2 = [];
    foreach ($station_ids as $value) {
        $array1[] = $value->id;
    }
    foreach ($station_id as $value) {
        $array2[] = $value->station_id;
    }
    $array_new = array_diff($array1, $array2);
    $array_volumes = [];
    foreach ($array_new as $value) {
        $array_volumes[] = Station::select('id', 'name')
            ->where('id', $value)
            ->get();
    }
    $traverse = function ($stations) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($stations as $station) {
            echo PHP_EOL . "<option value='" . $station[0]->id . "'>" . $station[0]->name . "</option>";
        }
    };
    return $traverse($array_volumes);
}

function getEditStation($id)
{
    $nodes = Station::select('id', 'name')
        ->where('organization_type_id', 2)
        ->get();
    $traverse = function ($stations, $id) use (&$traverse) {
        echo PHP_EOL . "<option  value=''></option>";
        foreach ($stations as $station) {
            $selected = $station->id == $id ? "selected" : "";
            echo PHP_EOL . "<option value='" . $station->id . "' " . $selected . ">" . $station->name . "</option>";
        }
    };
    return $traverse($nodes, $id);
}

function checkObjectGroup($params, $object, $id)
{
    switch ($params['object_group']) {
        case 1:
            $customer_id = $params['object_id1'];
            $customer = Customer::find($customer_id);
            $object->partyable_name = $customer->name;
            $object->partyable()->associate($customer);
            break;
        case 2:
            $employee_id = $params['object_id2'];
            $employee = Employee::find($employee_id);
            $object->partyable_name = $employee->hovaten;
            $object->partyable()->associate($employee);
            break;
        case 3:
            $supplier_id = $params['object_id3'];
            $supplier = Supplier::find($supplier_id);
            $object->partyable_name = $supplier->name;
            $object->partyable()->associate($supplier);
            break;
    }
    $object->payment_method_id = $id;
    if ($params['payment_date'] != null && $params['payment_date'] != '01-01-1970') {
        $object->payment_date = formatDate($params['payment_date']);
    } else {
        $object->payment_date = null;
    }
    $object->payment_user = $params['payment_user'];
    $object->amount = 0;
    $object->company_id = $params['company_id'];
    $object->description = $params['description_payment'];
    $object->created_by = $params['created_by'];
    $object->transaction_type_id = $params['transaction_type_id'];
    $object->code = $params['code'];
}

function UpdateObject($payment_items, $key, $value, $object)
{
    $payment_item_id = $payment_items[$key]['id'];
    $payment_item = PaymentItem::find($payment_item_id);
    $payment_item->payment_id = $value['payment_id'];
    $payment_item->volumn_trackings_id = $value['volumn_trackings_id'];
    $payment_item->debit_account_id = $value['debit_account_id'];
    $payment_item->credit_account_id = $value['credit_account_id'];
    $payment_item->description = $value['description_payment_item'];
    $payment_item->amount = formatIntNumber($value['amount']);
    $payment_item->update();
    if ($value['volumn_trackings_id'] != null) {
        $amount_volume = PaymentItem::selectRaw("SUM(amount) as sum")
            ->where('volumn_trackings_id', $value['volumn_trackings_id'])
            ->get();
        $sum_amount_volume = $amount_volume[0]->sum;
        $volume = VolumeTracking::find($value['volumn_trackings_id']);
        if($volume->total_price > $sum_amount_volume){
            $volume->payment_status = 0;
        }else if($volume->total_price == $sum_amount_volume){
            $volume->payment_status = 1;
        }
        $volume->remain_price = $volume->total_price - $sum_amount_volume;
        $volume->update();
    }
    $amount_payment = PaymentItem::selectRaw("SUM(amount) as sum_payment")
        ->where('payment_id', $value['payment_id'])
        ->get();
    $object->amount = $amount_payment[0]->sum_payment;
    $object->status = checkVolumeId($value['payment_id']);
    $object->update();
}

function AddObject($value, $object)
{
    $payment_item = new PaymentItem();
    $payment_item->payment_id = $value['payment_id'];
    $payment_item->volumn_trackings_id = $value['volumn_trackings_id'];
    $payment_item->debit_account_id = $value['debit_account_id'];
    $payment_item->credit_account_id = $value['credit_account_id'];
    $payment_item->description = $value['description_payment_item'];
    $payment_item->amount = formatIntNumber($value['amount']);
    $payment_item->save();
    if ($value['volumn_trackings_id'] != null) {
        $amount = PaymentItem::selectRaw("SUM(amount) as sum")
            ->where('volumn_trackings_id', $value['volumn_trackings_id'])
            ->get();
        $sum_amount = $amount[0]->sum;
        $volume = VolumeTracking::find($value['volumn_trackings_id']);
        $volume->remain_price = $volume->total_price - $sum_amount;
        if($volume->total_price > $sum_amount){
            $volume->payment_status = 0;
        }else if($volume->total_price == $sum_amount){
            $volume->payment_status = 1;
        }
        $volume->update();
    }
    $amount_payment = PaymentItem::selectRaw("SUM(amount) as sum_payment")
        ->where('payment_id', $value['payment_id'])
        ->get();
    $object->amount = $amount_payment[0]->sum_payment;
    $object->status = checkVolumeId($value['payment_id']);
    $object->update();
}

function convert_number_to_words($number)
{
    $hyphen = ' ';
    $conjunction = ' ';
    $separator = ' ';
    $negative = 'âm ';
    $decimal = ' phẩy ';
    $dictionary = [
        0 => 'không',
        1 => 'một',
        2 => 'hai',
        3 => 'ba',
        4 => 'bốn',
        5 => 'năm',
        6 => 'sáu',
        7 => 'bảy',
        8 => 'tám',
        9 => 'chín',
        10 => 'mười',
        11 => 'mười một',
        12 => 'mười hai',
        13 => 'mười ba',
        14 => 'mười bốn',
        15 => 'mười năm',
        16 => 'mười sáu',
        17 => 'mười bảy',
        18 => 'mười tám',
        19 => 'mười chín',
        20 => 'hai mươi',
        30 => 'ba mươi',
        40 => 'bốn mươi',
        50 => 'năm mươi',
        60 => 'sáu mươi',
        70 => 'bảy mươi',
        80 => 'tám mươi',
        90 => 'chín mươi',
        100 => 'trăm',
        1000 => 'nghìn',
        1000000 => 'triệu',
        1000000000 => 'tỷ',
        1000000000000 => 'nghìn tỷ',
        1000000000000000 => 'nghìn triệu triệu',
        1000000000000000000 => 'tỷ tỷ',
    ];

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        trigger_error('convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX, E_USER_WARNING);
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = [];
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,$company)
{
        $paymentItems = PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment'=>function($query){
            $query->select('id','payment_date');
        }])->whereHas('payment',function($query) use ($date1,$date2,$customer,$company){
            if ($customer == 0 && $company == 0){
            $query->whereBetween('payment_date',[$date1,$date2]);
        }else if($customer != 0 && $company == 0){
            $query->whereBetween('payment_date',[$date1,$date2])->where('partyable_type','App\Models\Concrete\Customer')->whereIn('partyable_id',$customer);
        }else if($customer != 0 && $company != 0){
            $query->whereBetween('payment_date',[$date1,$date2])->where('partyable_type','App\Models\Concrete\Customer')->whereIn('partyable_id',$customer)->whereIn('company_id',$company);
        }else if($customer == 0 && $company != 0){
            $query->whereBetween('payment_date',[$date1,$date2])->whereIn('company_id',$company);
        }
        })->whereHas('creditAccount',function($query){
            $query->where('account_code','like','131%');
        })->groupBy('payment_id','credit_account_id')->get(['payment_id','credit_account_id'])->sortBy("payment.payment_date")->groupBy('payment.payment_date');
        if ($customer == 0 && $company == 0){
        $creditGroupDates = getDebitCreditVolumeByDate($date1,$date2,0,0)['creditGroupDate'];
        $debitGroupDates = VolumeTracking::selectRaw('SUM(total_price) as sum,from_date')->whereBetween('from_date',[$date1,$date2])->groupby('from_date')->orderBy('from_date','asc')->pluck('sum','from_date')->toArray();
        }else if($customer != 0 && $company == 0){
            $creditGroupDates = getDebitCreditVolumeByDate($date1,$date2,$customer,0)['creditGroupDate'];
            $debitGroupDates = VolumeTracking::selectRaw('SUM(total_price) as sum,from_date')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->groupby('from_date')->orderBy('from_date','asc')->pluck('sum','from_date')->toArray();
        }else if($customer != 0 && $company != 0){
            $creditGroupDates = getDebitCreditVolumeByDate($date1,$date2,$customer,$company)['creditGroupDate'];
            $debitGroupDates = VolumeTracking::selectRaw('SUM(total_price) as sum,from_date')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->groupby('from_date')->orderBy('from_date','asc')->pluck('sum','from_date')->toArray();
        }else if($customer == 0 && $company != 0){
            $creditGroupDates = getDebitCreditVolumeByDate($date1,$date2,0,$company)['creditGroupDate'];
            $debitGroupDates = VolumeTracking::selectRaw('SUM(total_price) as sum,from_date')->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->groupby('from_date')->orderBy('from_date','asc')->pluck('sum','from_date')->toArray();
        }
        $arrayDate = getDatesFromRange($date1,$date2);
        $arrCredit = [];
        foreach($arrayDate as $value){
            $arrCredit[$value] = 0;
        }
        foreach($arrCredit as $key => $value){
            foreach($paymentItems as $key1 => $value1){
                if($key == date('d-m-Y', strtotime($key1))){
                    $arrCredit[$key] += array_sum(array_column($value1->toArray(), 'amount'));
                }
            }
        }

        foreach($arrCredit as $key => $value){
            foreach($creditGroupDates as $key1 => $value1){
                if($key == $key1){
                    $arrCredit[$key] += $value1;
                }
            }
        }
        foreach($arrCredit as $key => $value){
            if($value == 0){
                unset($arrCredit[$key]);
            }
        }
        $date_credit = array_keys($arrCredit);
        $sum_credit = array_values($arrCredit);
        $credit_sums = array_sum($sum_credit);

        $creditsGroupMonth = [];
        foreach($arrCredit as $date => $value){
            $creditsGroupMonth[date('m-Y', strtotime($date))]=( $creditsGroupMonth[date('m-Y', strtotime($date))] ?? 0 ) + $value;
        }

        $month_credit = array_keys($creditsGroupMonth);
        $month_sum_credit = array_values($creditsGroupMonth);
        $debitDates = [];
        foreach($debitGroupDates as $key => $value){
            $debitDates[date('d-m-Y',strtotime($key))] = ( $debitDates[date('m-Y', strtotime($key))] ?? 0 ) + $value;
        }
        $date_debit = array_keys($debitDates);
        $sum_debit = array_values($debitDates);
        $debit_sums = array_sum($sum_debit);

        $debitsGroupMonth = [];
        foreach($debitGroupDates as $key => $value){
            $debitsGroupMonth[date('m-Y', strtotime($key))] = ( $debitsGroupMonth[date('m-Y', strtotime($key))] ?? 0 ) + $value;
        }
        $month_debit = array_keys($debitsGroupMonth);
        $month_sum_debit = array_values($debitsGroupMonth);
    return [
    'date_debit' => $date_debit,
    'sum_debit' => $sum_debit,
    'debit_sums' => $debit_sums,
    'date_credit' => $date_credit, 
    'sum_credit' => $sum_credit, 
    'credit_sums' => $credit_sums,
    'month_debit' => $month_debit, 
    'month_sum_debit' => $month_sum_debit,
    'month_credit' => $month_credit, 
    'month_sum_credit' => $month_sum_credit
    ];
}

function getDebitCreditVolumeByDate($date1,$date2,$customer,$company){
       if($customer == 0 && $company == 0){
        $volumeTrackings = VolumeTracking::selectRaw("(CASE 
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') AND `credit_account_2_id` IN 
        (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount + revenue_entry_amount
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN revenue_entry_amount
        WHEN `credit_account_2_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount
        ELSE 0
        END) AS credit,from_date")->whereBetween('from_date',[$date1,$date2])->orderby('from_date','asc')->pluck('credit','from_date');
    }else if($customer != 0 && $company == 0){
        $volumeTrackings = VolumeTracking::selectRaw("(CASE 
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') AND `credit_account_2_id` IN 
        (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount + revenue_entry_amount
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN revenue_entry_amount
        WHEN `credit_account_2_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount
        ELSE 0
        END) AS credit,from_date")->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->orderby('from_date','asc')->pluck('credit','from_date');
    }else if($customer != 0 && $company != 0){
        $volumeTrackings = VolumeTracking::selectRaw("(CASE 
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') AND `credit_account_2_id` IN 
        (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount + revenue_entry_amount
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN revenue_entry_amount
        WHEN `credit_account_2_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount
        ELSE 0
        END) AS credit,from_date")->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->orderby('from_date','asc')->pluck('credit','from_date');
    }else if($customer == 0 && $company != 0){
        $volumeTrackings = VolumeTracking::selectRaw("(CASE 
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') AND `credit_account_2_id` IN 
        (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount + revenue_entry_amount
        WHEN `credit_account_1_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN revenue_entry_amount
        WHEN `credit_account_2_id` IN (SELECT id FROM `gl_accounts` WHERE `account_code` LIKE '131%') THEN tax_entry_amount
        ELSE 0
        END) AS credit,from_date")->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->orderby('from_date','asc')->pluck('credit','from_date');
    }
    $creditGroup = [];
    foreach($volumeTrackings->toArray() as $key => $value){
        $creditGroup[$key]=( $creditGroup[$key] ?? 0 ) + $value;
    }
    return ['creditGroupDate' => $creditGroup];
}

function getDatesFromRange($start, $end, $format = 'd-m-Y') {
    $array = array();
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
    foreach($period as $date) {                 
        $array[] = $date->format($format); 
    }
    return $array;
}

function turnOverStation($customer,$company,$employee,$station,$date1,$date2){
    if($customer[0] == 0 && $company[0] == 0 && $employee == 0 && $station == 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->groupBy('station_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee == 0 && $station == 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->groupBy('station_id')->get();
    }else if($customer[0] == 0 && $company[0] != 0 && $employee == 0 && $station == 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->groupBy('station_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee == 0 && $station == 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->groupBy('station_id')->get();
    }else if($customer[0] == 0 && $company[0] == 0 && $employee != 0 && $station == 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->groupBy('station_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee != 0 && $station == 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('sale_user_id','=',$employee)->groupBy('station_id')->get();
    }else if($customer[0] == 0 && $company[0] != 0 && $employee != 0 && $station == 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->whereIn('company_id',$company)->groupBy('station_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee != 0 && $station == 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->whereIn('company_id',$company)->whereIn('customer_id',$customer)->groupBy('station_id')->get();
    }else if($customer[0] == 0 && $company[0] == 0 && $employee == 0 && $station != 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('station_id',$station)->groupBy('station_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee == 0 && $station != 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('station_id',$station)->groupBy('station_id')->get();
    }else if($customer[0] == 0 && $company[0] != 0 && $employee == 0 && $station != 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('station_id',$station)->groupBy('station_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee == 0 && $station != 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('station_id',$station)->groupBy('station_id')->get();
    }else if($customer[0] == 0 && $company[0] == 0 && $employee != 0 && $station != 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('station_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee != 0 && $station != 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('station_id')->get();
    }else if($customer[0] == 0 && $company[0] != 0 && $employee != 0 && $station != 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('station_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee != 0 && $station != 0){
        $concreteStations = VolumeTracking::selectRaw('SUM(total_price) as sum, station_id')->with(['organization' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('station_id')->get();
    }
    return $concreteStations;
}

function turnOverEmployee($customer,$company,$employee,$station,$date1,$date2)
{
    if($customer [0] == 0 && $company[0] == 0 && $employee == 0 && $station == 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->groupBy('sale_user_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee == 0 && $station != 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->where('station_id',$station)->groupBy('sale_user_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee != 0 && $station == 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->groupBy('sale_user_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee != 0 && $station != 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->where('station_id',$station)->where('sale_user_id','=',$employee)->groupBy('sale_user_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee == 0 && $station == 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->groupBy('sale_user_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee == 0 && $station != 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('station_id',$station)->groupBy('sale_user_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee != 0 && $station == 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->groupBy('sale_user_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee != 0 && $station != 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('sale_user_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee == 0 && $station == 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->groupBy('sale_user_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee == 0 && $station != 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('station_id',$station)->groupBy('sale_user_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee != 0 && $station == 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('sale_user_id','=',$employee)->groupBy('sale_user_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee != 0 && $station != 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('sale_user_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee == 0 && $station == 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->groupBy('sale_user_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee == 0 && $station != 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('station_id',$station)->groupBy('sale_user_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee != 0 && $station == 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('sale_user_id',$employee)->groupBy('sale_user_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee != 0 && $station != 0){
        $concreteEmployees = VolumeTracking::selectRaw('SUM(total_price) as sum, sale_user_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('sale_user_id',$employee)->where('station_id',$station)->groupBy('sale_user_id')->get();
    }
    return $concreteEmployees;
}

function turnOverCustomer($customer,$company,$employee,$station,$date1,$date2)
{
    if($customer [0] == 0 && $company[0] == 0 && $employee == 0 && $station == 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->groupBy('customer_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee == 0 && $station != 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('station_id',$station)->groupBy('customer_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee != 0 && $station == 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->groupBy('customer_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee != 0 && $station != 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('customer_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee == 0 && $station == 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->groupBy('customer_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee == 0 && $station != 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('station_id',$station)->groupBy('customer_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee != 0 && $station == 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->groupBy('customer_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee != 0 && $station != 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('customer_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee == 0 && $station == 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->groupBy('customer_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee == 0 && $station != 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('station_id',$station)->groupBy('customer_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee != 0 && $station == 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('sale_user_id','=',$employee)->groupBy('customer_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee != 0 && $station != 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('customer_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee == 0 && $station == 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->groupBy('customer_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee == 0 && $station != 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('station_id',$station)->groupBy('customer_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee != 0 && $station == 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->groupBy('customer_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee != 0 && $station != 0){
        $concreteCustomers = VolumeTracking::selectRaw('SUM(total_price) as sum, customer_id')->with(['customer' => function($query){
            $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('customer_id')->get();
    }
    return $concreteCustomers;
}

function turnOverConstruction($customer,$company,$employee,$station,$date1,$date2)
{
    if($customer [0] == 0 && $company[0] == 0 && $employee == 0 && $station == 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->groupBy('construction_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee == 0 && $station != 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('station_id',$station)->groupBy('construction_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee != 0 && $station == 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->groupBy('construction_id')->get();
    }else if($customer [0] == 0 && $company[0] == 0 && $employee != 0 && $station != 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->where('sale_user_id','=',$employee)->where('station_id',$station)->groupBy('construction_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee == 0 && $station == 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->groupBy('construction_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee == 0 && $station != 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('station_id',$station)->groupBy('construction_id')->get();
    }else if($customer [0] == 0 && $company[0] != 0 && $employee != 0 && $station == 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->groupBy('construction_id')->get();
    }else if($customer == 0 && $company[0] != 0 && $employee != 0 && $station != 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->where('station_id',$station)->where('sale_user_id','=',$employee)->groupBy('construction_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee == 0 && $station == 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->groupBy('construction_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee == 0 && $station != 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('station_id',$station)->groupBy('construction_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee != 0 && $station == 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('sale_user_id','=',$employee)->groupBy('construction_id')->get();
    }else if($customer[0] != 0 && $company[0] == 0 && $employee != 0 && $station != 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->where('station_id',$station)->where('sale_user_id','=',$employee)->groupBy('construction_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee == 0 && $station == 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->groupBy('construction_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee == 0 && $station != 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('station_id',$station)->groupBy('construction_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee != 0 && $station == 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('sale_user_id','=',$employee)->groupBy('construction_id')->get();
    }else if($customer[0] != 0 && $company[0] != 0 && $employee != 0 && $station != 0){
        $concreteConstructions = VolumeTracking::selectRaw('SUM(total_price) as sum, construction_id')->with(['construction' => function($query){
                $query->select('id','name');
        }])->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->where('station_id',$station)->where('sale_user_id','=',$employee)->groupBy('construction_id')->get();
    }
    return $concreteConstructions;
}

function customerOverview()
{
    $nodes = VolumeTracking::select('customer_id')->with(['customer' => function($query){
        $query->select('id','name');
    }])->get();
    $traverse = function ($volumetrackings) use (&$traverse) {
        echo PHP_EOL . "<option value='0'>Tất cả</option>";
        foreach ($volumetrackings as $volumetracking) {
            echo PHP_EOL . "<option value='" . $volumetracking->customer->id . "'>" . $volumetracking->customer->name . "</option>";
        }
    };
    return $traverse($nodes);
}