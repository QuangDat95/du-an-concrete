<?php

namespace App\Http\Controllers\Survey;

use Illuminate\Http\Request;
use App\Models\Rolepermission\Role;
use App\Models\Rolepermission\Permission;
use App\Models\Survey\Employee;
class AdminController extends Controller
{
    public function show()
    {
        $users = Employee::with('roles','permissions')->select(['id','hovaten','email','phongban'])->where('id','!=','257')->orderBy('hovaten')->get();    
        $roles = Role::select(['id','name'])->where('name','!=','admin')->get(); 
        $permissions = Permission::select(['id','name'])->get();
        return view('pages.form_admin',['users'=>$users,'roles'=>$roles,'permissions'=>$permissions]);
    }
    
    public function setRole(Request $request)
    {
        $roleValue = $request->input('role_value');
        $arrUser = $request->input('arr_users');
        $check = 0;
        foreach($arrUser as $userId){
            $user = Employee::findOrFail($userId);
            if($user->hasRole('admin')){
                $user->syncRoles($roleValue,'admin');
                $check = 1;
            }
            else{
                $user->syncRoles($roleValue); 
            }            
        }
        echo $check;
    }
    public function setPermission(Request $request)
    {
        $arrPermissionValues = $request->input('arrPermissionValues');
        $arrUser = $request->input('arr_users');
        $arrPermissions = [];
        $check = 0;
        foreach($arrPermissionValues as $permissionValue){
            if($permissionValue=='clear-all-permission'){
                foreach($arrUser as $userId){
                    $user = Employee::findOrFail($userId);
                    $user->syncPermissions([]);
                }
                $check = 1;
                break;
            }
            else{
                array_push($arrPermissions,$permissionValue);
            }
            
        }
        if($check==0){
            foreach($arrUser as $userId){
                $user = Employee::findOrFail($userId);
                $user->syncPermissions($arrPermissions);
            }
        }
    }
}
