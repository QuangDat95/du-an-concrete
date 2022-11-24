<?php

namespace App\Http\Controllers\Concrete;

use Illuminate\Http\Request;
use App\Models\Concrete\Area;
use App\Models\Concrete\Slump;
use App\Models\Survey\Employee;
use App\Models\Concrete\Station;
use App\Models\Concrete\Vehicle;
use App\Models\Concrete\Contract;
use App\Models\Concrete\Customer;
use App\Models\Concrete\Sampleage;
use Illuminate\Support\Facades\DB;
use App\Models\Concrete\PaymentItem;
use App\Models\Concrete\Construction;
use App\Models\Concrete\Organization;
use App\Models\Concrete\ConcreteGrade;
use App\Models\Concrete\VolumeTracking;
use App\Models\Concrete\TransactionEntry;
use App\DataTables\VolumeTrackingsDataTable;
use App\Http\Requests\VolumeTrackingRequest;
use Illuminate\Support\Arr;
class VolumeTrackingController extends Controller
{
    const TITLE = "Theo dõi khối lượng";
    const TABLE = "volume_trackings";
    const ORG_TYPE_STATION = 2;
    public function index(VolumeTrackingsDataTable $dataTable, VolumeTrackingRequest $request, VolumeTracking $volumeTracking)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        $paramSelects = [
            'area' => Area::select('id', 'name')->get(),
            'station_id' => Station::select('id', 'name', 'area_id', 'organization_type_id')
                ->where('organization_type_id', self::ORG_TYPE_STATION)
                ->get(),
            'concrete_grade_id' => ConcreteGrade::select('id', 'name')->get(),
            'sampleage_id' => Sampleage::select('id', 'name')->get(),
            'slump_id' => Slump::select('id', 'name')->get(),
            'vehicle_id' => Vehicle::select('id', 'name')->get(),
            'contract_id' => Contract::select('id', 'contract_code', 'customer_id', 'construction_id')->get(),
        ];
        if ($request->isMethod('get')) {
            return $dataTable->render('layouts.tables.index', compact('title', 'table', 'columns', 'paramSelects'));
        } else {
            return $this->store($request, $volumeTracking);
        }
    }

    public function store($request, $model)
    {
        $params = $request->all();
        $params['area_id'] = $params['area_id_primary'];

        if ($params['from_date'] != null && $params['from_date'] != '01-01-1970') {
            $params['from_date'] = formatDate($params['from_date']);
        } else {
            $params['from_date'] = null;
        }

        if ($params['received_date'] != null && $params['received_date'] != '01-01-1970') {
            $params['received_date'] = formatDate($params['received_date']);
        } else {
            $params['received_date'] = null;
        }

        if ($params['due_date'] != null && $params['due_date'] != '01-01-1970') {
            $params['due_date'] = formatDate($params['due_date']);
        } else {
            $params['due_date'] = null;
        }

        if ($params['vat_date'] != null && $params['vat_date'] != '01-01-1970') {
            $params['vat_date'] = formatDate($params['vat_date']);
        } else {
            $params['vat_date'] = null;
        }

        foreach (getInputFormatPrice() as $nameFormat) {
            $number = str_replace(",", "", $params[$nameFormat]);
            if (!$number) {
                $number = 0;
            }
            $params[$nameFormat] = $number;
        }

        if ($model->id) {
            if (isset($params['vat_flag']) && $params['vat_flag'] == 1) {
                $params['vat_flag'] = 1;
            } else {
                $params['vat_flag'] = 0;
            }
            $amount = PaymentItem::select(DB::raw('SUM(amount) as sum'))
                ->where('volumn_trackings_id', $model->id)
                ->get();
            if ($amount[0]->sum != null) {
                $sum_amount = $amount[0]->sum;
            } else {
                $sum_amount = 0;
            }
            $transaction = TransactionEntry::select('revenue_debit_account_id', 'revenue_credit_account_id', 'company_id', 'vat_rate', 'tax_debit_account_id', 'tax_credit_account_id')
                ->where('station_id', $params['station_id'])
                ->get();
            $volume = VolumeTracking::find($model->id);
            $volume->remain_price = $volume->total_price - $sum_amount;
            $total_price = (int) str_replace(',', '', $params['total_price']);
            if ($total_price != $volume->total_price) {
                $volume->debit_account_1_id = $transaction[0]->revenue_debit_account_id;
                $volume->credit_account_1_id = $transaction[0]->revenue_credit_account_id;
                $volume->debit_account_2_id = $transaction[0]->tax_debit_account_id;
                $volume->credit_account_2_id = $transaction[0]->tax_credit_account_id;
                $volume->vat_rate = $transaction[0]->vat_rate;
                $volume->company_id = $transaction[0]->company_id;
                $volume->revenue_entry_amount = str_replace(',', '', $params['total_price']) / (1 + $params['vat_rate'] / 100);
                $volume->tax_entry_amount = (str_replace(',', '', $params['total_price']) / (1 + $params['vat_rate'] / 100)) * ($params['vat_rate'] / 100);
            } else {
                $volume->debit_account_1_id = $params['debit_account_1_id'];
                $volume->credit_account_1_id = $params['credit_account_1_id'];
                $volume->vat_rate = $params['vat_rate'];
                $volume->company_id = $params['company_id'];
                $volume->debit_account_2_id = $params['debit_account_2_id'];
                $volume->credit_account_2_id = $params['credit_account_2_id'];
                $volume->revenue_entry_amount = $params['revenue_entry_amount'];
                $volume->tax_entry_amount = $params['tax_entry_amount'];
            }
            if ($params['revenue_entry_amount'] == '') {
                $volume->status = 0;
            } else {
                $volume->status = 1;
            }
            $model->update($params);
            $volume->update();
            $message = "Sửa thành công";
        } else {
            if (!isset($params['contract_id'])) {
                $params['due_date'] = $params['from_date'];
            } else {
                $contract = Contract::find($params['contract_id']);
                $owned_date = $contract->paymentCondition->date_owned;
                $params['due_date'] = addDate(formatDate($params['from_date']), $owned_date);
            }
            
            if (isset($params['vat_flag']) && $params['vat_flag'] == 1) {
                $params['vat_flag'] = 1;
            } else {
                $params['vat_flag'] = 0;
            }
            $transaction = TransactionEntry::select('revenue_debit_account_id', 'revenue_credit_account_id', 'company_id', 'vat_rate', 'tax_debit_account_id', 'tax_credit_account_id')
                ->where('station_id', $params['station_id'])
                ->get();
            if (count($transaction) > 0) {
                $params['debit_account_1_id'] = $transaction[0]->revenue_debit_account_id;
                $params['credit_account_1_id'] = $transaction[0]->revenue_credit_account_id;
                $params['debit_account_2_id'] = $transaction[0]->tax_debit_account_id;
                $params['credit_account_2_id'] = $transaction[0]->tax_credit_account_id;
                $params['vat_rate'] = $transaction[0]->vat_rate;
                $params['company_id'] = $transaction[0]->company_id;
                $params['revenue_entry_amount'] = $params['total_price'] / (1 + $params['vat_rate'] / 100);
                $params['tax_entry_amount'] = ($params['total_price'] / (1 + $params['vat_rate'] / 100)) * ($params['vat_rate'] / 100);
                $params['status'] = 1;
            } else {
                $params['debit_account_1_id'] = null;
                $params['credit_account_1_id'] = null;
                $params['vat_rate'] = null;
                $params['company_id'] = null;
                $params['debit_account_2_id'] = null;
                $params['credit_account_2_id'] = null;
                $params['revenue_entry_amount'] = null;
                $params['tax_entry_amount'] = null;
                $params['status'] = 0;
            }
            $params['remain_price'] = $params['total_price'];
            VolumeTracking::create($params);
            $message = "Thêm thành công";
        }
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function edit(VolumeTracking $volumeTracking)
    {
        $oldData = $volumeTracking;
        $oldData['pumping_time_begin'] = formatTime($volumeTracking->pumping_time_begin);
        $oldData['pumping_time_finish'] = formatTime($volumeTracking->pumping_time_finish);
        $oldData['area_id'] = $volumeTracking->station->area->id;
        $routeSubmit = route('volume_trackings', ['volume_tracking' => $volumeTracking->id]);
        return response()->json([
            'success' => true,
            'old' => $oldData,
            'customer_id' => $oldData->customer_id,
            'construction_id' => $oldData->construction_id,
            'route' => $routeSubmit
        ]);
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed, true);
        VolumeTracking::whereIn('id', $checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function changeContractcode(Request $request)
    {
        $customer_id = $request->customer_id;
        $construction_id = $request->construction_id;
        if ($customer_id != null && $construction_id != null) {
            $contract_codes = Contract::select('id', 'contract_code', 'customer_id', 'construction_id')
                ->where('customer_id', $customer_id)
                ->where('construction_id', $construction_id)
                ->get();
        } else {
            $contract_codes = Contract::select('id', 'contract_code', 'customer_id', 'construction_id')->get();
        }
        return view('layouts.ajax.contract_code', ['contract_codes' => $contract_codes]);
    }

    public function dueDate(Request $request)
    {
        $id = $request->id;
        $from_date = $request->from_date;
        if ($id == null) {
            return $from_date;
        } else {
            $contract = Contract::find($id);
            $owned_date = $contract->paymentCondition->date_owned;
            return addDate($from_date, $owned_date);
        }
    }

    public function loadContract()
    {
        $nodes = Contract::select('id', 'customer_id', 'construction_id', 'contract_code')->get();
        $traverse = function ($Contracts) use (&$traverse) {
            echo PHP_EOL . "<option value=''>--root--</option>";
            foreach ($Contracts as $Contract) {
                echo PHP_EOL . "<option value='" . $Contract->id . "' customer-id='" . $Contract->customer_id . "' construction-id='" . $Contract->construction_id . "'> " . $Contract->contract_code . "</option>";
            }
        };
        return $traverse($nodes);
    }

    public function loadCustomer()
    {
        $nodes = Customer::select('id', 'name')->get();
        $traverse = function ($Customers) use (&$traverse) {
            echo PHP_EOL . "<option value=''>--root--</option>";
            foreach ($Customers as $Customer) {
                echo PHP_EOL . "<option value='" . $Customer->id . "'> " . $Customer->name . "</option>";
            }
        };
        return $traverse($nodes);
    }

    public function loadConstruction()
    {
        $nodes = Construction::select('id', 'name')->get();
        $traverse = function ($Constructions) use (&$traverse) {
            echo PHP_EOL . "<option value=''>--root--</option>";
            foreach ($Constructions as $Construction) {
                echo PHP_EOL . "<option value='" . $Construction->id . "'> " . $Construction->name . "</option>";
            }
        };
        return $traverse($nodes);
    }
}
