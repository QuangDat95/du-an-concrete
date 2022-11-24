<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\ConstructionsDataTable;
use App\Http\Requests\ConstructionRequest;
use App\Models\Concrete\Construction;
use Illuminate\Http\Request;

class ConstructionController extends Controller
{
    const TITLE = "Công trình";
    const TABLE = "constructions";

    public function index(ConstructionsDataTable $dataTable, ConstructionRequest $request,Construction $construction)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$construction);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Construction::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Construction $construction)
    {
        $oldData = $construction;
        $routeSubmit = route('constructions',['construction'=> $construction->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Construction::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}