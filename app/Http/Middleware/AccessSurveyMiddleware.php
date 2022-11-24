<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Survey\Survey;
use Illuminate\Support\Facades\URL;
class AccessSurveyMiddleware
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
        $surveyId = last(request()->segments());
        $surveys = Survey::get(['id','sort']);
        $permissionName = $this->getPermissionName($surveyId,$surveys);
        if(Auth::check()){
            // if(Auth::user()->hasRole('admin')){
            //     return redirect()->route('error.show');
            // }
            if(Auth::user()->hasRole('QS')){
                return $next($request);
            }
            else if(Auth::user()->hasRole('admin')){
                return $next($request);
            }
            else if(Auth::user()->hasPermissionTo($permissionName)){
                return $next($request);
            }
            else{
                return redirect()->route('error.show');
            }
        }
        else{
            return redirect()->route('auth.show')->with('message','You must login first');
        }
    }
    public function getPermissionName($surveyId,$surveys)
    {
        foreach($surveys as $survey){
            if($survey->id==$surveyId){
                $sort = $survey->sort;
            }
        }
        if($sort == 1){
            return $permissionName = 'Phiếu Khảo Sát Chất Lượng Dịch Vụ Số 1';
        }
        else{
            return $permissionName = 'Phiếu Khảo Sát Chất Lượng Dịch Vụ Số 2';
        }
    }
}
