<?php

namespace App\Http\Controllers\Survey;

use Illuminate\Http\Request;
use App\Models\Survey\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
class UserController extends Controller
{
    //
    public function edit($user,Request $request)
    {
        if($request->isMethod('GET'))
        {
            return $this->getEdit($user);
        }
        else if($request->isMethod('POST'))
        {
            return $this->postEdit($user,$request);
        }
    }

    public function getEdit($user)
    {
        $user = Employee::where('id',$user)->get(['id','hovaten','email']);
        return view('surveys.pages.profile',['user'=>$user]);
    }
    public function postEdit($user,Request $request)
    {
        Employee::findOrFail($user)->update($request->all());
        return back()->with('message','Update profile successfull');
    }
    public function passwordChange(Request $request)
    {
        $userPassword = Auth::user()->password;
        if(Hash::check($request->input('old_password'),$userPassword))
        {
            $user = Employee::findOrFail(Auth::user()->id)->update([
                'password' => bcrypt($request->input('new_password'))
            ]);
            echo 1;
        }
        else
        {
            echo 2;
        }
    }
    public function setPassword(Request $request)
    {
        if($request->isMethod('get')){
            return $this->getMethodSetPassword();
        }
        else{
            return $this->postMethodSetPassword($request);
        }
    }
    public function getMethodSetPassword()
    {
        return view('surveys.pages.set_password');
    }
    public function postMethodSetPassword(Request $request)
    {
        $oldPassword = Auth::user()->password;
        if(Hash::check($request->input('newPassword'),$oldPassword)){
            return false;
        }
        else{
            $employee = Employee::findOrFail(Auth::user()->id);
            $employee->update([
                'password'=> Hash::make($request->input('newPassword')),
                'verified_at'=>date('Y-m-d H:i:s'),
            ]);
            return true;
        }
    }
    public function uploadImage(Request $request)
    {
        if($request->hasFile('image')){
            $oldImageName = Auth::user()->image;
            $oldImagePath = public_path('/surveys/images/user_images/'.$oldImageName);
            if(File::exists($oldImagePath)){
                File::delete($oldImagePath);
            }
            $image = $request->file('image');
            $fileExtension = $image->getClientOriginalExtension();
            $fileName = Str::random(32).'.'.$fileExtension;
            $request->file('image')->move(public_path('/surveys/images/user_images'),$fileName);
            $employee = Employee::findOrFail(Auth::user()->id);
            $employee->update([
                'image' => $fileName
            ]);
            echo $fileName;
        }
    }
}
