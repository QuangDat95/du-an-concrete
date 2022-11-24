<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\OrganizationRequest;
use App\Models\Concrete\Organization;

class OrganizationController extends Controller
{
    const TABLE = "organizations";
    public function index(OrganizationRequest $request,Organization $organization)
    {
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return view('layouts.organizations.index',compact('table'));
        else
            return $this->store($request,$organization);
    }

    public function store($request,$model)
    {
        $params = $request->all();
        $params['organization_type_id'] = 1;
        $params['area_id'] = null;
        if($model->id){
            $model->update($params);
            $message = "Sửa thành công";
        }
        else{
            Organization::create($params);
            $message = "Thêm thành công";
        }
        return response()->json( array('success' => true,'message'=>$message,'organization' => $model->toArray()));
    }

    public function edit(Organization $Organization)
    {
        $oldData = $Organization;
        $routeSubmit = route('organizations',['organization'=> $Organization->id]);
        return response()->json( array('success' => true,'old' => $oldData,'route'=>$routeSubmit));
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        Organization::where('organization_type_id',1)->get()->toTree();
        $tree = Organization::descendantsAndSelf($id);
        Organization::whereIn('id',$tree->pluck('id'))->delete();
        Organization::where('id',$id)->delete();
        $message = 'Xóa thành công';
        return response()->json( array('success' => true,'message' => $message));
    }

    public function loadcompany()
    {
        $nodes = Organization::where('organization_type_id',1)->get()->toTree();
        $traverse = function ($organizations, $prefix = '') use (&$traverse) {
        echo   PHP_EOL."<option  value=''></option>";
        foreach ($organizations as $organization) {
             echo   PHP_EOL."<option  value='".$organization->id."'>".$prefix." ".$organization->name."</option>";
             $traverse($organization->children, $prefix.'-');
        }
    };
    return $traverse($nodes);
    }
}