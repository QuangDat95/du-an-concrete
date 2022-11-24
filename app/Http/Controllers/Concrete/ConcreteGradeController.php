<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\ConcreteGradesDataTable;
use App\Http\Requests\ConcreteGradeRequest;
use App\Models\Concrete\ConcreteGrade;
use Illuminate\Http\Request;

class ConcreteGradeController extends Controller
{
    const TITLE = "Mác bê tông";
    const TABLE = "concrete_grades";

    public function index(ConcreteGradesDataTable $dataTable, ConcreteGradeRequest $request,ConcreteGrade $concreteGrade)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$concreteGrade);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            ConcreteGrade::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(ConcreteGrade $concreteGrade)
    {
        $oldData = $concreteGrade;
        $routeSubmit = route('concrete_grades',['concrete_grade'=> $concreteGrade->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        ConcreteGrade::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('error' => true,'message'=>$message));
    }
}