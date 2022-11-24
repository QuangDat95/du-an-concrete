<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class LoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check())
        {
            // echo strcmp(Auth::user()->tinhtrang,'Thôi việc');exit;
            if(strcmp(Auth::user()->tinhtrang,'Đang làm việc')==17&&Auth::user()->verified_at==""){
                return redirect()->route('users.setPassword');
            }
            else if(strcmp(Auth::user()->tinhtrang,'Thôi việc')==21){
                Auth::logout();
                return redirect()->route('auth.show')->with(['message'=>'Không thể đăng nhập bằng email này']);
            }
            else{
                return $next($request);
            }
        }
        else
        {
            return redirect()->route('auth.show')->with('message','You must login first');
        }
        
    }
}
