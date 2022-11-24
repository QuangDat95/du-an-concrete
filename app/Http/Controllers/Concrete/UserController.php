<?php

namespace App\Http\Controllers\Concrete;
use App\Http\Requests\PasswordRequest;
use Illuminate\Http\Request;
use App\Models\Concrete\User;
use App\Models\Survey\Employee;
use Illuminate\Support\Facades\Hash;
use App\Models\Rolepermission\Role;
use App\Models\Rolepermission\Permission;
use Illuminate\Support\Facades\Auth;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\Storage;
class UserController extends Controller
{
    const TITLE = "Người dùng";
    const TABLE = "users";

    public function index(UsersDataTable $usersDataTable,Request $request,User $user)
    {
        $title = self::TITLE;
        $table = self::TABLE;
        $columns = getColumnDataBase($table);
        if ($request->isMethod('get'))
            return $usersDataTable->render('layouts.tables.index',compact('title','table','columns'));
        else
            return $this->store($request);
    }

    public function profile(User $user)
    {
        $roles = Role::with('permissions')->get();
        $permissions =  Permission::all();
        return view('users.edit',compact('user','roles','permissions'));
    }

    public function changeImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user = User::find(Auth::user()->id);
        if ($request->hasFile('file')) {
            $currentFile = $user->image;
            if ($currentFile) {
                Storage::delete('/public/' . $currentFile);
            }
            $file = $request->file;
            $path = $file->store('image', 'public');
            $user->image = $path;
        }
        $user->save();
        return response()->json('Image uploaded successfully');
    }
    
    public function password(PasswordRequest $request,User $user)
    {
        $user->update(['password' => Hash::make($request->get('password'))]);
        return back()->withStatus(__('Password successfully updated.'));
    }

    public function insertRole(Request $request)//cập nhật vai trò
    {
        $id = $request->id;
        foreach($id as $value){
            $user = Employee::find($value);
            $roles = [];
            $roles[] = $request->name_role;
            $user->syncRoles($roles);
        }
        $message = 'Cập nhật vai trò thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }

    public function insertPermission(Request $request)//cập nhật quyền
    {
        $id = $request->id;
        foreach($id as $value){
            $user = Employee::find($value);
            $permissions = $request->permission;
            $user->syncPermissions($permissions);
        }
        $message = 'Cập nhật quyền thành công';
        return response()->json( array('success' => true,'message'=>$message));
    }
}