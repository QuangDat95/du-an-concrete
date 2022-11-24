<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\OrganizationTypesDataTable;
use App\Http\Requests\OrganizationTypeRequest;
use App\Models\Concrete\OrganizationType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizationTypeController extends Controller
{
    const TITLE = "Loại tổ chức";
    const TABLE = "organization_types";

    public function index(OrganizationTypesDataTable $dataTable, OrganizationTypeRequest $request,OrganizationType $organizationType)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$organizationType);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            OrganizationType::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(OrganizationType $organizationType)
    {
        $oldData = $organizationType;
        $routeSubmit = route('organization_types',['organization_type'=> $organizationType->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        OrganizationType::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}
