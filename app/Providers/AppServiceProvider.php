<?php

namespace App\Providers;
use App\Models\Rolepermission\Role;
use App\Models\Rolepermission\Permission;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Concrete\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Concrete\VolumeTracking;
use Carbon\Carbon;
use App\Models\Concrete\PaymentItem;
use App\Models\Concrete\Organization;
class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        
    }

    public function boot()
    {
    $customer_volumes = VolumeTracking::select('customer_id')->with(['customer' => function($query){
        $query->select('id','name');
    }])->groupby('customer_id')->get();
    $customer_overviews = [];
    foreach($customer_volumes as $key => $value){
        $customer_overviews[$value->customer->id] = $value->customer->name;
    }
    $classify = ['CÁ NHÂN' => 'Cá nhân','DOANH NGHIỆP' => 'Công ty', 'NỘI BỘ' => 'Nội bộ'];
    $organization = Organization::select('id','name')->where('area_id',null)->get();
    $minmaxdate = VolumeTracking::select(DB::raw("MIN(from_date) as MinDate"))->get();
    $accountants = Customer::select('accountant_name')->groupby('accountant_name')->get();
    View::share('classify',$classify);
    View::share('minmaxdate',$minmaxdate);
    View::share('organization',$organization);
    $roles = Role::all();
    View::share('roles',$roles);
    $permissions = Permission::all();
    View::share('permissions',$permissions);
    View::share('customer_overviews',$customer_overviews);
    View::share('accountants',$accountants);
    }
}