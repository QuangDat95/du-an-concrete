<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\GlAccountRequest;
use App\Models\Concrete\GlAccount;

class GlAccountController extends Controller
{
    const TABLE = "gl_accounts";
    public function index(GlAccountRequest $request,GlAccount $glAccount)
    {
        $table = self::TABLE;
        if ($request->isMethod('get'))
            return view('layouts.gl_accounts.index',compact('table'));
        else
            return $this->store($request,$glAccount);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if(!isset($params['parent_id'])){
            $params['level'] = 1;
        }else if($params['parent_id'] != null){
            $result = GlAccount::withDepth()->find($params['parent_id'])->depth;
            $params['level'] = $result + 2;
        }
        if(isset($params['customer_flag'])){
            $params['customer_flag'] = 1;
        }else{
            $params['customer_flag'] = 0;
        }
        $params['created_by'] = auth()->user()->id;
        if($model->id){
            if($params['level'] <= 4){
                $model->update($params);
                $message = "Sửa thành công";
            return response()->json( array('success' => true,'message'=>$message));
            }else{
            return 1;
            }
        }
        else{
            if($params['level'] <= 4){
            GlAccount::create($params);
            $message = "Thêm thành công";
            return response()->json( array('success' => true,'message'=>$message));
            }else{
            return 1;
            }
        }
    }

    public function edit(GlAccount $glAccount)
    {
        $oldData = $glAccount;
        $routeSubmit = route('gl_accounts',['gl_account'=> $glAccount->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        GlAccount::get()->toTree();
        $tree = GlAccount::descendantsAndSelf($id);
        GlAccount::whereIn('id',$tree->pluck('id'))->delete();
        GlAccount::where('id',$id)->delete();
        $message = "Xóa thành công";
        return response()->json( array('success' => true,'message' => $message));
    }

    public function loadglaccount()
    {
        $nodes = GlAccount::get()->toTree();
        $traverse = function ($GlAccounts,$prefix = '') use (&$traverse) {
            echo PHP_EOL."<option  value=''>--root--</option>";
            foreach ($GlAccounts as $GlAccount) {
                 echo PHP_EOL."<option value='".$GlAccount->id."'>".$prefix." ".$GlAccount->name."</option>";
                 $traverse($GlAccount->children, $prefix.'-');
            }
        };
        return $traverse($nodes);
    }
}