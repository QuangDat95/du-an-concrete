<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\SampleagesDataTable;
use App\Http\Requests\SampleageRequest;
use App\Models\Concrete\Sampleage;
use Illuminate\Http\Request;

class SampleageController extends Controller
{
    const TITLE = "Tuổi mẫu";
    const TABLE = "sampleages";

    public function index(SampleagesDataTable $dataTable, SampleageRequest $request,Sampleage $sampleage)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$sampleage);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Sampleage::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Sampleage $sampleage)
    {
        $oldData = $sampleage;
        $routeSubmit = route('sampleages',['sampleage'=> $sampleage->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Sampleage::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}