<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\SlumpsDataTable;
use App\Http\Requests\SlumpRequest;
use App\Models\Concrete\Slump;
use Illuminate\Http\Request;

class SlumpController extends Controller
{
    const TITLE = "Độ sụt";
    const TABLE = "slumps";

    public function index(SlumpsDataTable $dataTable, SlumpRequest $request,Slump $slump)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$slump);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Slump::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Slump $slump)
    {
        $oldData = $slump;
        $routeSubmit = route('slumps',['slump'=> $slump->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Slump::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}
