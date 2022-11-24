<?php

namespace App\Http\Controllers\Finance;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Concrete\Customer;
use App\Http\Controllers\Controller;
use App\Repositories\DebtDetail\DebtDetailRepositoryInterface;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;

class OverdueController extends Controller
{
    protected $volumeTrackingRepo;
    protected $debtDetailRepo;
    public function __construct(VolumeTrackingRepositoryInterface $volumeTrackingRepo,DebtDetailRepositoryInterface $debtDetailRepo)
    {
        $this->volumeTrackingRepo = $volumeTrackingRepo;
        $this->debtDetailRepo = $debtDetailRepo;
    }

    public function index()
    {
        $customer_volumes = $this->volumeTrackingRepo->getCustomerOtherByVolumeTracking()->get();
        $customer_details = [];
        foreach($customer_volumes as $key => $value){
            $customer_details[$value->customer->id] = $value->customer->name_other;
        }
        return view('finances.overdue',compact('customer_details'));
    }

    public function customerDeltails()
    {
         //khách hàng theo volume
         $nows = changeDate(Carbon::now());
         $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()->get();
         $listCustomers = $this->debtDetailRepo->debtOverDueDetail($customer_volumes,$nows);
        if(request()->ajax()){
            return datatables()->of($listCustomers)->editColumn('sumDebt', function ($listCustomer) {
                return number_format(round($listCustomer['sumDebt']));
            })->editColumn('debtIncurred', function ($listCustomer) {
                return number_format(round($listCustomer["debtIncurred"]));
            })->editColumn('debtLessThan1Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan1Month"]));
            })->editColumn('debtLessThan2Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan2Month"]));
            })->editColumn('debtLessThan3Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan3Month"]));
            })->editColumn('debtLessThan4Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan4Month"]));
            })->editColumn('debtLessThan5Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan5Month"]));
            })->editColumn('debtLessThan6Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan6Month"]));
            })->editColumn('debtOverDue', function ($listCustomer) {
                return number_format(round($listCustomer["debtOverDue"]));
            })->make(true);
        };
    }

    public function customerOverDues(Request $request)
    {
        $nows = changeDate(Carbon::now());
        $customer = $request->customer;
        if($customer[0] == 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()->get();
        }else if($customer[0] != 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()->whereIn('customer_id',$customer)->get();
        }
        $listCustomers = $this->debtDetailRepo->debtOverDueDetail($customer_volumes,$nows);
        if(request()->ajax()){
            return datatables()->of($listCustomers)->editColumn('sumDebt', function ($listCustomer) {
                return number_format(round($listCustomer['sumDebt']));
            })->editColumn('debtIncurred', function ($listCustomer) {
                return number_format(round($listCustomer["debtIncurred"]));
            })->editColumn('debtLessThan1Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan1Month"]));
            })->editColumn('debtLessThan2Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan2Month"]));
            })->editColumn('debtLessThan3Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan3Month"]));
            })->editColumn('debtLessThan4Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan4Month"]));
            })->editColumn('debtLessThan5Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan5Month"]));
            })->editColumn('debtLessThan6Month', function ($listCustomer) {
                return number_format(round($listCustomer["debtLessThan6Month"]));
            })->editColumn('debtOverDue', function ($listCustomer) {
                return number_format(round($listCustomer["debtOverDue"]));
            })->make(true);
        };
    }
}
