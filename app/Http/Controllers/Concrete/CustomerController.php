<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\CustomesDataTable;
use App\Http\Requests\CustomerRequest;
use App\Models\Concrete\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    const TITLE = "Khác hàng";
    const TABLE = "customers";

    public function index(CustomesDataTable $dataTable, CustomerRequest $request,Customer $customer)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        $paramSelects = array(
            'type_id' => fieldCustomerType(),
            'status_id' => fieldCustomerStaus()
        );
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns','paramSelects'));
        else
            return $this->store($request,$customer);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Customer::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Customer $customer)
    {
        $oldData = $customer;
        $routeSubmit = route('customers',['customer'=> $customer->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Customer::whereIn('id',$checkboxIds)->update(['is_deleted' => 1]);
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}