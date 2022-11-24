<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Concrete\Controller;
use Illuminate\Http\Request;
use App\DataTables\PaymentConditionsDataTable;
use App\Http\Requests\PaymentConditionRequest;
use App\Models\Concrete\PaymentCondition;
use App\Models\Concrete\Contract;
use App\Models\Concrete\VolumeTracking;
use Illuminate\Support\Facades\DB;

class PaymentConditionController extends Controller
{
    const TITLE = "Điều kiện thanh toán";
    const TABLE = "payment_conditions";

    public function index(PaymentConditionsDataTable $dataTable, PaymentConditionRequest $request,PaymentCondition $PaymentCondition)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$PaymentCondition);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $contract_id = [];
            $contract = Contract::select('id')->where('payment_condition_id',$model->id)->get();
            foreach($contract as $value){
                $contract_id[] = $value->id;
            }
            VolumeTracking::whereIn('contract_id',$contract_id)->update(['due_date' => DB::raw('DATE_ADD(from_date, INTERVAL '.$model->date_owned.' DAY)')]);
            $message = "Sửa thành công";
        }
        else{
            PaymentCondition::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(PaymentCondition $PaymentCondition)
    {
        $oldData = $PaymentCondition;
        $routeSubmit = route('payment_conditions',['payment_condition'=> $PaymentCondition->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        PaymentCondition::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}