<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\VehiclesDataTable;
use App\Http\Requests\VehicleRequest;
use App\Models\Concrete\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    const TITLE = "Phuơng tiện đổ";
    const TABLE = "vehicles";

    public function index(VehiclesDataTable $dataTable, VehicleRequest $request,Vehicle $vehicle)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request,$vehicle);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Vehicle::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Vehicle $vehicle)
    {
        $oldData = $vehicle;
        $routeSubmit = route('vehicles',['vehicle'=> $vehicle->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Vehicle::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}