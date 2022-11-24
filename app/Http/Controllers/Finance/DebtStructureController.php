<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\DebtDetail\DebtDetailRepositoryInterface;
class DebtStructureController extends Controller
{
    protected $volumeTrackingRepo;
    protected $customerRepo;
    protected $debtDetailRepo;
    public function __construct(VolumeTrackingRepositoryInterface $volumeTrackingRepo,
                                      CustomerRepositoryInterface $customerRepo, DebtDetailRepositoryInterface $debtDetailRepo)
    {
        $this->volumeTrackingRepo = $volumeTrackingRepo;
        $this->customerRepo = $customerRepo;
        $this->debtDetailRepo = $debtDetailRepo;
    }

    public function caculatorDebtOverDue($date,$customer,$customer1,$status,$accountant)
    {
        if(($customer == null || $customer1 == 0) && $status == 0 && $accountant == 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()->get();
        }else if(($customer == null || $customer1 == 0) && $status == 0 && $accountant != 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()
            ->whereHas('customer',function($query) use ($accountant){
                $query->where('accountant_name',$accountant);
            })->get();
        }else if(($customer == null || $customer1 == 0) && $status != 0 && $accountant == 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()
            ->whereHas('customer',function($query) use ($status){
                $query->where('status_id',$status);
            })->get();
        }else if(($customer == null || $customer1 == 0) && $status != 0 && $accountant != 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()
            ->whereHas('customer',function($query) use ($status, $accountant){
                $query->where('status_id',$status)->where('accountant_name',$accountant);
            })->get();
        }else if($customer1 != 0 && $status == 0 && $accountant == 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()
            ->whereIn('customer_id',$customer)->get();
        }else if($customer1 != 0 && $status == 0 && $accountant != 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()
            ->whereHas('customer',function($query) use ($accountant){
                $query->where('accountant_name',$accountant);
            })->whereIn('customer_id',$customer)->get();
        }else if($customer1 != 0 && $status != 0 && $accountant == 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()
            ->whereHas('customer',function($query) use ($status){
                $query->where('status_id',$status);
            })->whereIn('customer_id',$customer)->get();
        }else if($customer1 != 0 && $status != 0 && $accountant != 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()
            ->whereHas('customer',function($query) use ($status, $accountant){
                $query->where('status_id',$status)->where('accountant_name',$accountant);
            })->whereIn('customer_id',$customer)->get();
        }

        $listCustomers = $this->debtDetailRepo->debtOverDueByTime($customer_volumes,$date);

        return ['debtLessThan1Month' => array_sum(array_column($listCustomers,'debtLessThan1Month')),
                'debtLessThan2Month' => array_sum(array_column($listCustomers,'debtLessThan2Month')),
                'debtLessThan3Month' => array_sum(array_column($listCustomers,'debtLessThan3Month')),
                'debtLessThan4Month' => array_sum(array_column($listCustomers,'debtLessThan4Month')),
                'debtLessThan5Month' => array_sum(array_column($listCustomers,'debtLessThan5Month')),
                'debtLessThan6Month' => array_sum(array_column($listCustomers,'debtLessThan6Month')),
                       'debtOverDue' => array_sum(array_column($listCustomers,'debtOverDue'))
                ];
    }

    public function index()
    {
        $customer_volumes = $this->volumeTrackingRepo->getCustomerOtherByVolumeTracking()->get();
        $customer_details = [];
        foreach($customer_volumes as $key => $value){
            $customer_details[$value->customer->id] = $value->customer->name_other;
        }
        $accountants = $this->customerRepo->getAccountant()->get();
        $status = $this->customerRepo->getStatus()->get();
        return view('finances.debt_structure',compact('status','customer_details','accountants'));
    }
    
    public function DebtStruct()
    {
        $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()->get();
        $nows = changeDate(Carbon::now());
        $listCustomers = $this->debtDetailRepo->debtOverDueStructure($customer_volumes,$nows);
        $sum_debtIncurred = array_sum(array_column($listCustomers,'debtIncurred'));
        $sum_debtLessThan1Month = array_sum(array_column($listCustomers,'debtLessThan1Month'));
        $sum_debtLessThan2Month = array_sum(array_column($listCustomers,'debtLessThan2Month'));
        $sum_debtLessThan3Month = array_sum(array_column($listCustomers,'debtLessThan3Month'));
        $sum_debtLessThan4Month = array_sum(array_column($listCustomers,'debtLessThan4Month'));
        $sum_debtLessThan5Month = array_sum(array_column($listCustomers,'debtLessThan5Month'));
        $sum_debtLessThan6Month = array_sum(array_column($listCustomers,'debtLessThan6Month'));
        $sum_debtOverDue = array_sum(array_column($listCustomers,'debtOverDue'));

        $sum_debt_overdue = $sum_debtLessThan1Month + $sum_debtLessThan2Month + $sum_debtLessThan3Month 
        + $sum_debtLessThan4Month + $sum_debtLessThan5Month + $sum_debtLessThan6Month + + $sum_debtOverDue;
        $array_debt = [$sum_debt_overdue, $sum_debtIncurred];
        $array_debt_overdue = [$sum_debtIncurred, $sum_debtLessThan1Month, $sum_debtLessThan2Month, 
                              $sum_debtLessThan3Month, $sum_debtLessThan4Month, $sum_debtLessThan5Month,
                              $sum_debtLessThan6Month, $sum_debtOverDue];
        return response()->json(['array_debt' => $array_debt,'array_debt_overdue' => $array_debt_overdue]);
    }

    public function debtStructureTime()
    {
        $year = date("Y");
        if (($year % 4) == 0) {
            $days = 29;
        } else {
            $days = 28;
        }
        $debtMonth1 = $this->caculatorDebtOverDue($year."-01-31",0,0,0,0);
        $debtMonth2 = $this->caculatorDebtOverDue($year."-02-".$days,0,0,0,0);
        $debtMonth3 = $this->caculatorDebtOverDue($year."-03-31",0,0,0,0);
        $debtMonth4 = $this->caculatorDebtOverDue($year."-04-30",0,0,0,0);
        $debtMonth5 = $this->caculatorDebtOverDue($year."-05-31",0,0,0,0);
        $debtMonth6 = $this->caculatorDebtOverDue($year."-06-30",0,0,0,0);
        $debtMonth7 = $this->caculatorDebtOverDue($year."-07-31",0,0,0,0);
        $debtMonth8 = $this->caculatorDebtOverDue($year."-08-31",0,0,0,0);
        $debtMonth9 = $this->caculatorDebtOverDue($year."-09-30",0,0,0,0);
        $debtMonth10 = $this->caculatorDebtOverDue($year."-10-31",0,0,0,0);
        $debtMonth11 = $this->caculatorDebtOverDue($year."-11-30",0,0,0,0);
        $debtMonth12 = $this->caculatorDebtOverDue($year."-12-31",0,0,0,0);
        $debt = array_merge_recursive($debtMonth1,$debtMonth2,$debtMonth3,$debtMonth4,$debtMonth5,$debtMonth6,
                                $debtMonth7,$debtMonth8,$debtMonth9,$debtMonth10,$debtMonth11,$debtMonth12);
        return response()->json(['debtLess1Month' => $debt["debtLessThan1Month"],'debtLess2Month' => $debt["debtLessThan2Month"],
                                 'debtLess3Month' => $debt["debtLessThan3Month"],'debtLess4Month' => $debt["debtLessThan4Month"],
                                 'debtLess5Month' => $debt["debtLessThan5Month"],'debtLess6Month' => $debt["debtLessThan6Month"],
                                 'debtOverDue' => $debt["debtOverDue"]]);
    }

    public function debtStructure(Request $request)
    {
        $nows = changeDate(Carbon::now());
        $customer = $request->customer;
        if($customer[0] == 0 || $customer == null){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()->get();
        }else if($customer[0] != 0){
            $customer_volumes = $this->volumeTrackingRepo->getCustomerByVolumeTracking()->whereIn('customer_id',$customer)->get();
        }
        $listCustomers = $this->debtDetailRepo->debtOverDueStructure($customer_volumes,$nows);
        $sum_debtIncurred = array_sum(array_column($listCustomers,'debtIncurred'));
        $sum_debtLessThan1Month = array_sum(array_column($listCustomers,'debtLessThan1Month'));
        $sum_debtLessThan2Month = array_sum(array_column($listCustomers,'debtLessThan2Month'));
        $sum_debtLessThan3Month = array_sum(array_column($listCustomers,'debtLessThan3Month'));
        $sum_debtLessThan4Month = array_sum(array_column($listCustomers,'debtLessThan4Month'));
        $sum_debtLessThan5Month = array_sum(array_column($listCustomers,'debtLessThan5Month'));
        $sum_debtLessThan6Month = array_sum(array_column($listCustomers,'debtLessThan6Month'));
        $sum_debtOverDue = array_sum(array_column($listCustomers,'debtOverDue'));
        $sum_debt_overdue = $sum_debtLessThan1Month + $sum_debtLessThan2Month + $sum_debtLessThan3Month 
        + $sum_debtLessThan4Month + $sum_debtLessThan5Month + $sum_debtLessThan6Month + + $sum_debtOverDue;
        $arrayDebtRequest = [$sum_debt_overdue, $sum_debtIncurred];
        $arrayDebtOverdueRequest = [$sum_debtIncurred, $sum_debtLessThan1Month, $sum_debtLessThan2Month, $sum_debtLessThan3Month,
                                   $sum_debtLessThan4Month, $sum_debtLessThan5Month, $sum_debtLessThan6Month, $sum_debtOverDue];
        return response()->json(['arrayDebtRequest' => $arrayDebtRequest,'arrayDebtOverdueRequest' => $arrayDebtOverdueRequest]);
    }

    public function debtStructureByTime(Request $request)
    {
        $customer = $request->customer;
        $status = $request->status;
        $accountant = $request->accountant;
        $year = date("Y");
            if (($year % 4) == 0) {
                $days = 29;
            } else {
                $days = 28;
            }

        $debtMonth1 = $this->caculatorDebtOverDue($year."-01-31",$customer,$customer[0],$status,$accountant);
        $debtMonth2 = $this->caculatorDebtOverDue($year."-02-".$days,$customer,$customer[0],$status,$accountant);
        $debtMonth3 = $this->caculatorDebtOverDue($year."-03-31",$customer,$customer[0],$status,$accountant);
        $debtMonth4 = $this->caculatorDebtOverDue($year."-04-30",$customer,$customer[0],$status,$accountant);
        $debtMonth5 = $this->caculatorDebtOverDue($year."-05-31",$customer,$customer[0],$status,$accountant);
        $debtMonth6 = $this->caculatorDebtOverDue($year."-06-30",$customer,$customer[0],$status,$accountant);
        $debtMonth7 = $this->caculatorDebtOverDue($year."-07-31",$customer,$customer[0],$status,$accountant);
        $debtMonth8 = $this->caculatorDebtOverDue($year."-08-31",$customer,$customer[0],$status,$accountant);
        $debtMonth9 = $this->caculatorDebtOverDue($year."-09-30",$customer,$customer[0],$status,$accountant);
        $debtMonth10 = $this->caculatorDebtOverDue($year."-10-31",$customer,$customer[0],$status,$accountant);
        $debtMonth11 = $this->caculatorDebtOverDue($year."-11-30",$customer,$customer[0],$status,$accountant);
        $debtMonth12 = $this->caculatorDebtOverDue($year."-12-31",$customer,$customer[0],$status,$accountant);

        $overDueYears1 = $this->caculatorDebtOverDue(($year - 3)."-12-31",$customer,$customer[0],$status,$accountant);
        $overDueYears2 = $this->caculatorDebtOverDue(($year - 2)."-12-31",$customer,$customer[0],$status,$accountant);
        $overDueYears3 = $this->caculatorDebtOverDue(($year - 1)."-12-31",$customer,$customer[0],$status,$accountant);

        $months = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        $debtMonths = array_merge_recursive($debtMonth1,$debtMonth2,$debtMonth3,$debtMonth4,$debtMonth5,$debtMonth6,
                                    $debtMonth7,$debtMonth8,$debtMonth9,$debtMonth10,$debtMonth11,$debtMonth12);
       
        $precious = ['Quý I','Quý II','Quý III','Quý IV'];
        $debtPrecious = array_merge_recursive($debtMonth3,$debtMonth6,$debtMonth9,$debtMonth12);
        
        $years = [($year - 3), ($year - 2), ($year - 1), $year];
        $debtYears = array_merge_recursive($overDueYears1,$overDueYears2,$overDueYears3,$debtMonth12);

        return response()->json([
        'debtLess1Month' => $debtMonths["debtLessThan1Month"], 'debtLess2Month' => $debtMonths["debtLessThan2Month"], 
        'debtLess3Month' => $debtMonths["debtLessThan3Month"], 'debtLess4Month' => $debtMonths["debtLessThan4Month"], 
        'debtLess5Month' => $debtMonths["debtLessThan5Month"], 'debtLess6Month' => $debtMonths["debtLessThan6Month"],
        'debtOverDue' => $debtMonths["debtOverDue"], 'months' => $months,
        'debtPreciousLess1Month' => $debtPrecious["debtLessThan1Month"], 'debtPreciousLess2Month' => $debtPrecious["debtLessThan2Month"], 
        'debtPreciousLess3Month' => $debtPrecious["debtLessThan3Month"], 'debtPreciousLess4Month' => $debtPrecious["debtLessThan4Month"], 
        'debtPreciousLess5Month' => $debtPrecious["debtLessThan5Month"], 'debtPreciousLess6Month' => $debtPrecious["debtLessThan6Month"],
        'debtPreciousOverDue' => $debtPrecious["debtOverDue"], 'precious' => $precious,
        'debtYearsLess1Month' => $debtYears["debtLessThan1Month"], 'debtYearsLess2Month' => $debtYears["debtLessThan2Month"], 
        'debtYearsLess3Month' => $debtYears["debtLessThan3Month"], 'debtYearsLess4Month' => $debtYears["debtLessThan4Month"],
        'debtYearsLess5Month' => $debtYears["debtLessThan5Month"], 'debtYearsLess6Month' => $debtYears["debtLessThan6Month"], 
        'debtYearsOverDue' => $debtYears["debtOverDue"], 'years' => $years 
        ]);
    }

    public function fillterStatusSelectAccountantCustomer(Request $request)
    {
        $accountant = $request->accountant;
        $customer = $request->customer;
        if($accountant == 0 && ($customer[0] == 0 || $customer == null)){
            $status = $this->customerRepo->getStatus()->get();
        }else if($accountant != 0 && ($customer[0] == 0 || $customer == null)){
            $status = $this->customerRepo->getStatus()->where('accountant_name',$accountant)->get();
        }else if($accountant != 0 && $customer[0] != 0){
            $status = $this->customerRepo->getStatus()->whereIn('id',$customer)->where('accountant_name',$accountant)->get();
        }else if($accountant == 0 && $customer[0] != 0){
            $status = $this->customerRepo->getStatus()->whereIn('id',$customer)->get();
        }
        return view('finances.fillter.status',compact('status'));
    }

    public function fillterAccountantSelectCustomerStatus(Request $request)
    {
        $customer = $request->customer;
        $status = $request->status;
        if($status == 0 && ($customer[0] == 0 || $customer == null)){
            $accountant = $this->customerRepo->getAccountant()->get();
        }else if($status != 0 && ($customer[0] == 0 || $customer == null)){
            $accountant = $this->customerRepo->getAccountant()->where('status_id',$status)->get();
        }else if($status != 0 && $customer[0] != 0){
            $accountant = $this->customerRepo->getAccountant()->whereIn('id',$customer)->where('status_id',$status)->get();
        }else if($status == 0 && $customer[0] != 0){
            $accountant = $this->customerRepo->getAccountant()->whereIn('id',$customer)->get();
        }
        return view('finances.fillter.accountant',compact('accountant'));
    }
}