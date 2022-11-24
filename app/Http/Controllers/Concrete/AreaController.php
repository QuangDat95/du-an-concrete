<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\AreasDataTable;
use App\Http\Requests\AreaRequest;
use App\Models\Concrete\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    const TITLE = "Khu vực";
    const TABLE = "areas";

    public function index(AreasDataTable $dataTable, AreaRequest $request,Area $area)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$area);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Area::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Area $area)
    {
        $oldData = $area;
        $routeSubmit = route('areas',['area'=> $area->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Area::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}