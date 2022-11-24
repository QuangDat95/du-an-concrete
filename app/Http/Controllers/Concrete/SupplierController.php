<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\SuppliersDataTable;
use App\Http\Requests\SupplierRequest;
use App\Models\Concrete\Supplier;

class SupplierController extends Controller
{
    const TITLE = "Nhà cung cấp";
    const TABLE = "suppliers";

    public function index(SuppliersDataTable $dataTable, SupplierRequest $request,Supplier $supplier)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$supplier);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Supplier::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Supplier $supplier)
    {
        $oldData = $supplier;
        $routeSubmit = route('suppliers',['supplier'=> $supplier->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Supplier::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}
