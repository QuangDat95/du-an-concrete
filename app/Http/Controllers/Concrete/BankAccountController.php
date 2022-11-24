<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\BankAccountsDataTable;
use App\Http\Requests\BankAccountRequest;
use App\Models\Concrete\BankAccount;
class BankAccountController extends Controller
{
    const TITLE = "Tài khoản ngân hàng";
    const TABLE = "bank_accounts";

    public function index(BankAccountsDataTable $dataTable, BankAccountRequest $request,BankAccount $bankAccount)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$bankAccount);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            BankAccount::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(BankAccount $bankAccount)
    {
        $oldData = $bankAccount;
        $routeSubmit = route('bank_accounts',['bank_account'=> $bankAccount->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        BankAccount::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}
