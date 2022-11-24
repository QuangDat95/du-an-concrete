<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Requests\TransactionTypeRequest;
use App\DataTables\TransactionTypesDataTable;
use App\Models\Concrete\TransactionType;
use App\Models\Concrete\PaymentMethod;
use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    const TABLE = "transaction_types";
    const TITLE = "Khoản mục chi phí";
    public function index(TransactionTypeRequest $request,TransactionType $transactionType,TransactionTypesDataTable $dataTable)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        $paramSelects = [
            'payment_method_id' => PaymentMethod::select('id', 'name')->get()
        ];
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns','paramSelects'));
        else
            return $this->store($request,$transactionType);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            TransactionType::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(TransactionType $transactionType)
    {
        $oldData = $transactionType;
        $routeSubmit = route('transaction_types',['transaction_type'=> $transactionType->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        TransactionType::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}
