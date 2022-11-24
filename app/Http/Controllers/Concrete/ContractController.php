<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\ContractDataTable;
use App\Http\Requests\ContractRequest;
use App\Models\Concrete\Contract;
use App\Models\Concrete\PaymentCondition;

class ContractController extends Controller
{
    const TITLE = "theo dõi hợp đồng";
    const TABLE = "contracts";

    public function index(ContractDataTable $dataTable, ContractRequest $request,Contract $Contract)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        $paramSelects = array(
            'payment_condition_id' => PaymentCondition::select('id','name')->get()
        );
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns','paramSelects'));
        else
            return $this->store($request,$Contract);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($params['contract_date'] != null && $params['contract_date'] != '01-01-1970'){
            $params['contract_date'] = formatDate($params['contract_date']);
        }else{
            $params['contract_date'] = null;
        }
        if($params['due_date'] != null && $params['due_date'] != '01-01-1970'){
            $params['due_date'] = formatDate($params['due_date']);
        }else{
            $params['due_date'] = null;
        }
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Contract::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Contract $Contract)
    {
        $oldData = $Contract;
        $routeSubmit = route('contracts',['contract'=> $Contract->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Contract::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}
