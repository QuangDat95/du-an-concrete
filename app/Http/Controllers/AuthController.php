<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\View;
use App\Models\Survey\Employee;
use App\Models\Concrete\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credential = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $remember_me  = ( !empty( $request->remember_me ) )? TRUE : FALSE;
        if(Auth::attempt($credential)){
            $employee = Employee::where(["email" => $credential['email']])->first();
            Auth::login($employee, $remember_me);
            if(Auth::check()){
                return redirect('/');
            }
        }
        // if(Auth::attempt($credential)){
        //     $employee = User::where(["email" => $credential['email']])->first();
        //     Auth::login($employee, $remember_me);
        //     if(Auth::check()){
        //         return redirect('/');
        //     }
        // }
        return redirect()->back()->with(['message'=>'Sai email hoặc mật khẩu']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.show');
    }
}