<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\PaymentMethodsDataTable;
use App\Http\Requests\PaymentMethodRequest;
use App\Models\Concrete\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    const TITLE = "Phương thức thanh toán";
    const TABLE = "payment_methods";

    public function index(PaymentMethodsDataTable $dataTable, PaymentMethodRequest $request,PaymentMethod $paymentMethod)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$paymentMethod);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            PaymentMethod::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $oldData = $paymentMethod;
        $routeSubmit = route('payment_methods',['payment_method'=> $paymentMethod->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        PaymentMethod::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}