<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Concrete\TransactionEntry;
use App\Models\Concrete\VolumeTracking;
use App\Models\Concrete\Station;
use App\DataTables\TransactionEntryDataTable;
use App\Http\Requests\TransactionEntryRequest;
class TransactionEntryController extends Controller
{
    const TABLE = "transaction_entries";
    public function index(TransactionEntryDataTable $dataTable, TransactionEntryRequest $request,TransactionEntry $transactionEntry)
    {
        $table = self::TABLE;
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.transaction_entries.index',compact('table'));
        else
            return $this->store($request,$transactionEntry);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->station_id){
            $transaction = TransactionEntry::find($model->station_id);
            $station_id = $model->station_id;
            $volume = VolumeTracking::select('id','debit_account_1_id','credit_account_1_id','company_id','vat_rate','debit_account_2_id','credit_account_2_id')->where('station_id',$station_id)->get();
            if(count($volume) > 0){
                foreach($volume as $value){
                    if($transaction->revenue_debit_account_id == $value->debit_account_1_id 
                    && $transaction->revenue_credit_account_id == $value->credit_account_1_id
                    && $transaction->company_id == $value->company_id
                    && $transaction->tax_debit_account_id == $value->debit_account_2_id
                    && $transaction->tax_credit_account_id == $value->credit_account_2_id){
                        $volume1 = VolumeTracking::find($value->id);
                        $volume1->debit_account_1_id = $params['revenue_debit_account_id'];
                        $volume1->credit_account_1_id = $params['revenue_credit_account_id'];
                        $volume1->debit_account_2_id = $params['tax_debit_account_id'];
                        $volume1->credit_account_2_id = $params['tax_credit_account_id'];
                        $volume1->vat_rate = $params['vat_rate'];
                        $volume1->company_id = $params['company_id'];
                        $volume1->revenue_entry_amount = $volume1->total_price / (1 + $volume1->vat_rate / 100);
                        $volume1->tax_entry_amount = ($volume1->total_price / (1 + $volume1->vat_rate / 100)) * ($volume1->vat_rate / 100);
                        $volume1->status = 1;
                        $volume1->update();
                    }
                }
            }
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            $station_id = $params['station_id'];
            $volume = VolumeTracking::select('id','debit_account_1_id','credit_account_1_id','company_id','vat_rate','debit_account_2_id','credit_account_2_id')->where('station_id',$station_id)->get();
            if(count($volume) > 0){
                foreach($volume as $value){
                        $volume1 = VolumeTracking::find($value->id);
                        $volume1->debit_account_1_id = $params['revenue_debit_account_id'];
                        $volume1->credit_account_1_id = $params['revenue_credit_account_id'];
                        $volume1->debit_account_2_id = $params['tax_debit_account_id'];
                        $volume1->credit_account_2_id = $params['tax_credit_account_id'];
                        $volume1->vat_rate = $params['vat_rate'];
                        $volume1->company_id = $params['company_id'];
                        $volume1->revenue_entry_amount = $volume1->total_price / (1 + $volume1->vat_rate / 100);
                        $volume1->tax_entry_amount = ($volume1->total_price / (1 + $volume1->vat_rate / 100)) * ($volume1->vat_rate / 100);
                        $volume1->status = 1;
                        $volume1->update();
                }
            }
            TransactionEntry::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(TransactionEntry $transactionEntry){
        $oldData = $transactionEntry;
        $routeSubmit = route('transaction_entries',['transaction_entry' => $transactionEntry->station_id]);
        return response()->json(array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        TransactionEntry::whereIn('station_id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function loadStationEdit(Request $request){
        $id = $request->id;
        $station_id = TransactionEntry::select('station_id')->get();
        $stationId = [];
        foreach($station_id as $value){
            $stationId[] = $value->station_id;
        }
        if (($key = array_search($id, $stationId)) !== false) {
            unset($stationId[$key]);
        }
        $nodes = Station::select('id','name')->where('organization_type_id',2)->whereNotIn('id',$stationId)->get();
        $traverse = function ($stations) use (&$traverse) {
            echo PHP_EOL . "<option  value=''></option>";
            foreach ($stations as $station) {
                echo PHP_EOL . "<option value='" . $station->id . "'>" . $station->name . "</option>";
            }
        };
        return $traverse($nodes);
    }

    public function loadStation(){
        $station_ids = Station::select('id')->where('organization_type_id',2)->get();
        $station_id = TransactionEntry::select('station_id')->get();
        $array1 = [];
        $array2 = [];
        foreach($station_ids as $value){
            $array1[] = $value->id;
        }
        foreach($station_id as $value){
            $array2[] = $value->station_id;
        }
        $array_new = array_diff($array1,$array2);
        $array_volumes = [];
        foreach($array_new as $value){
            $array_volumes[] = Station::select('id','name')->where('id',$value)->get();
        } 
        $traverse = function ($stations) use (&$traverse) {
            echo PHP_EOL . "<option  value=''></option>";
            foreach ($stations as $station) {
                echo PHP_EOL . "<option value='" . $station[0]->id . "'>" . $station[0]->name . "</option>";
            }
        };
        return $traverse($array_volumes);
    }
}
