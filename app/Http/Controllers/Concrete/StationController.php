<?php

namespace App\Http\Controllers\Concrete;

use App\DataTables\StationsDataTable;
use App\Http\Requests\StationRequest;
use App\Models\Concrete\Station;

use Illuminate\Http\Request;

class StationController extends Controller
{
    const TABLE = "stations";
    public function index(StationsDataTable $dataTable, StationRequest $request,Station $station)
    {
        $table = self::TABLE;
        if ($request->isMethod('get'))
            return $dataTable->render('layouts.stations.index',compact('table'));
        else
            return $this->store($request,$station);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        $params['address'] = null; 
        $params['tax_number'] = null; 
        $params['email'] = null;
        $params['organization_type_id'] = 2;
        $params['_lft'] = null;
        $params['_rgt'] = null;
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Station::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function edit(Station $station)
    {
        $oldData = $station;
        $routeSubmit = route('stations',['station'=> $oldData->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed,true);
        Station::whereIn('id',$checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}