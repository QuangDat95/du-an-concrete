<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Concrete\Customer;
use App\Models\Concrete\Supplier;
use App\Models\Survey\Employee;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;
use App\Repositories\PaymentItem\PaymentItemRepositoryInterface;
class OverviewController extends Controller
{
    protected $volumeTrackingRepo;
    protected $paymentItemRepo;

    public function __construct(VolumeTrackingRepositoryInterface $volumeTrackingRepo,
    PaymentItemRepositoryInterface $paymentItemRepo)
    {
        $this->volumeTrackingRepo = $volumeTrackingRepo;
        $this->paymentItemRepo = $paymentItemRepo;
    }

    function caculatorDebtCredit($SumDebtIncurred, $SumAriseIncurred)
    {
        $debtIncurred = 0;
        $creditIncurred = 0;
        if(($SumDebtIncurred - $SumAriseIncurred)>=0){
            $debtIncurred = $SumDebtIncurred - $SumAriseIncurred;
            $creditIncurred = 0;
        }else if(($SumDebtIncurred - $SumAriseIncurred) < 0){
            $debtIncurred = 0;
            $creditIncurred = -($SumDebtIncurred - $SumAriseIncurred);
        }
        return ['outstandingBalanceBeginning' => $debtIncurred, 'balanceBeginning' => $creditIncurred];
    }

    function caculatorDetail($outstandingBalanceBeginning, $balanceBeginning, $DebtIncurred, $Generate)
    {
        $receivableEndTerm = 0;
        $CustomerPrepayEndTerm = 0;
        if(($outstandingBalanceBeginning - $balanceBeginning + $DebtIncurred - $Generate) >= 0){
            $receivableEndTerm = $outstandingBalanceBeginning - $balanceBeginning + $DebtIncurred - $Generate;
        }else if(($outstandingBalanceBeginning - $balanceBeginning + $DebtIncurred - $Generate) < 0){
            $CustomerPrepayEndTerm = -($outstandingBalanceBeginning - $balanceBeginning + $DebtIncurred - $Generate);
        }
        return ['receivableEndTerm' => $receivableEndTerm, 'CustomerPrepayEndTerm' => $CustomerPrepayEndTerm];
    }

    public function index()
    {
        return view('finances.overview');
    }

    public function overviews()
    {
        $date1 = changeDate(Carbon::now()->startOfMonth());
        $date2 = changeDate(Carbon::now());
        //T??NH C??N L???I
        $customers = Customer::select('id')->get();
        // //t???o m???ng ID kh??ch h??ng
        foreach ($customers as $value) {
            $arrayCustomer[] = $value->id;
        }
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k???
                'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k???
                'balanceBeginning' => 0, //d?? c?? ?????u k???
                'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                'Generate' => 0,//PS c?? trong k???(???? thu)
                'remain_receive' => 0
            ];
        }
        //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo paymentitem
        $paymentItems = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
        foreach($paymentItems as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
        //l???y t???ng ph??t sinh ?????u k??? theo volume
        $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->pluck('credit','customer_id')->toArray();
        //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo volumetracking
        foreach($creditGroupCustomer as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] += $value;
                    }
                }
        }
        //l???y t???ng ph??t sinh n??? ?????u k??? c???a KH theo volume
        $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
        foreach($debitGroupCustomer as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumDebtIncurred'] = $value;
                    }
                }
        }
        //l???y t???ng ph??t sinh c?? trong k??? c???a KH theo paymentitem
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
        foreach($paymentItems1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
        //l???y t???ng ph??t sinh trong k??? c???a KH
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->where('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
           //ph??t sinh c?? trong k??? theo volume
        foreach($creditGroupCustomer1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] += $value;
                    }
                }
            }
        //ph??t sinh n??? trong k??? theo volume
        $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','customer_id')->toArray();
        foreach($debitGroupCustomer1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['DebtIncurred'] = $value;
                    }
                }
            }
        //t??nh theo nh??n vi??n------------------------------------------------------------------
        $employees = Employee::select('id','hovaten')->orderby('id','asc')->get();

        foreach($employees as $value){
            $arrayEmployee[] = $value->id;
        }
        foreach ($employees as $key => $value) {
            $listEmployees[] = [
                'ID' => $value->id,
                'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k???
                'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k???
                'balanceBeginning' => 0, //d?? c?? ?????u k???
                'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                'Generate' => 0,//PS c?? trong k???(???? thu)
                'remain_receive' => 0
            ];
        }
        //l???y t???ng ph??t sinh c?? ?????u k??? c???a nh??n vi??n
        $paymentItems2 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,null,null);
        foreach($paymentItems2 as $key => $value){
            foreach($listEmployees as $key1 => $value1){
                if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                    $listEmployees[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
        }
        //l???y t???ng ph??t sinh c?? trong k??? c???a nh??n vi??n
        $paymentItems3 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,$date2,null);
        foreach($paymentItems3 as $key => $value){
            foreach($listEmployees as $key1 => $value1){
                if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                    $listEmployees[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                    }
                }
        }
        foreach ($listEmployees as $key => $value) {
            if ($value['ID'] == $arrayEmployee[$key]) {
                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                $listEmployees[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                $listEmployees[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'],$debtCreditBeginning['balanceBeginning'],$listEmployees[$key]['DebtIncurred'],$listEmployees[$key]['Generate']);
                $listEmployees[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                $listEmployees[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
            }
        }
        //t??nh theo nh?? cung c???p--------------------------------------------------------------
        $suppliers = Supplier::select('id','name')->orderby('id','asc')->get();

        foreach($suppliers as $value){
            $arraySupplier[] = $value->id;
        }
        foreach ($suppliers as $key => $value) {
            $listSuppliers[] = [
                'ID' => $value->id,
                'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k??? == 0
                'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k??? = Ph???i thu ?????u k???
                'balanceBeginning' => 0, //d?? c?? ?????u k??? = KH tr??? tr?????c ?????u k???
                'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                'Generate' => 0,//PS c?? trong k???(???? thu)
                'remain_receive' => 0
            ];
        }
        //l???y t???ng ph??t sinh c?? ?????u k??? c???a nh?? cung c???p
        $paymentItems4 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,null,null);
        foreach($paymentItems4 as $key => $value){
            foreach($listSuppliers as $key1 => $value1){
                if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                    $listSuppliers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
        }
        //l???y t???ng ph??t sinh c?? trong k??? c???a nh?? cung c???p
        $paymentItems5 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,$date2,null);
        foreach($paymentItems5 as $key => $value){
            foreach($listSuppliers as $key1 => $value1){
                if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                    $listSuppliers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                    }
                }
        }
        foreach ($listSuppliers as $key => $value) {
            if ($value['ID'] == $arraySupplier[$key]) {
                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                $listSuppliers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                $listSuppliers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'],$debtCreditBeginning['balanceBeginning'],$listSuppliers[$key]['DebtIncurred'],$listSuppliers[$key]['Generate']);
                $listSuppliers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                $listSuppliers[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
            }
        }
        
        foreach ($listCustomers as $key => $value) {
        if ($value['ID'] == $arrayCustomer[$key]) {
            $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
            $listCustomers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
            $listCustomers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
            $listCustomers[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listCustomers[$key]['DebtIncurred']) 
            - ($debtCreditBeginning['balanceBeginning'] + $listCustomers[$key]['Generate']);
            }
        }

        foreach ($listEmployees as $key => $value) {
            if ($value['ID'] == $arrayEmployee[$key]) {
                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                $listEmployees[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                $listEmployees[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                $listEmployees[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listEmployees[$key]['DebtIncurred']) 
                - ($debtCreditBeginning['balanceBeginning'] + $listEmployees[$key]['Generate']);
                }
            }
        foreach ($listSuppliers as $key => $value) {
            if ($value['ID'] == $arraySupplier[$key]) {
                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                $listSuppliers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                $listSuppliers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                $listSuppliers[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listSuppliers[$key]['DebtIncurred']) 
                - ($debtCreditBeginning['balanceBeginning'] + $listSuppliers[$key]['Generate']);
                }
            }
        $customerEmpoyeeSuppliers = array_merge($listCustomers,$listEmployees,$listSuppliers); 
        $remain_receive = array_sum(array_column($customerEmpoyeeSuppliers, 'remain_receive'));
         //ph??t sinh c??
         $date_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,0)['date_credit'];
         $sum_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,0)['sum_credit'];
         $credit_sums = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,0)['credit_sums'];
         //ph??t sinh n???
         $date_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['date_debit'];
         $sum_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['sum_debit'];
         $debit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['debit_sums'];
        return response()->json(['date_credit' => $date_credit,'sum_credit' => $sum_credit,'date_debit' => $date_debit,
        'sum_debit' => $sum_debit,'credit_sums' => $credit_sums,'debit_sums' => $debit_sums,'remain_receive' => $remain_receive]);
    }

    public function overviewRequest(Request $request)
    {
        $customer = $request->customer;
        $company = $request->company;
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        //T??NH C??N L???I
        if (($customer[0] != 0 && $company[0] == 0) || ($customer[0] != 0 && $company[0] != 0)) {
            $customers = Customer::select('id')->whereIn('id', $customer)->get();
        }else if(($customer[0] == 0 && $company[0] != 0) || ($customer[0] == 0 && $company[0] == 0)){
            $customers = Customer::select('id')->get();
        }
         // //t???o m???ng ID kh??ch h??ng
         foreach ($customers as $value) {
            $arrayCustomer[] = $value->id;
        }
            $listCustomers = [];
            foreach ($customers as $key => $value) {
                $listCustomers[] = [
                    'ID' => $value->id,
                    'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                    'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k???
                    'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k???
                    'balanceBeginning' => 0, //d?? c?? ?????u k???
                    'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                    'Generate' => 0,//PS c?? trong k???(???? thu)
                    'remain_receive' => 0
                ];
            }
            //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo paymentitem
            if (($customer[0] != 0 && $company[0] == 0) || ($customer[0] == 0 && $company[0] == 0)) {
                $paymentItems = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
            }else if(($customer[0] != 0 && $company[0] != 0) || ($customer[0] == 0 && $company[0] != 0)){
                $paymentItems = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,$company);
            }
            foreach($paymentItems as $key => $value){
                foreach($listCustomers as $key1 => $value1){
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                        }
                    }
                }
                //l???y t???ng ph??t sinh ?????u k??? theo volume
            if ($customer[0] != 0 && $company[0] == 0) {
                $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereIn('customer_id',$customer)->pluck('credit','customer_id')->toArray();
                $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->whereIn('customer_id',$customer)->pluck('sum','customer_id')->toArray();
            }else if($customer[0] != 0 && $company[0] != 0){
                $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereIn('company_id',$company)->whereIn('customer_id',$customer)->pluck('credit','customer_id')->toArray();
                $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->whereIn('customer_id',$customer)->whereIn('company_id',$company)->pluck('sum','customer_id')->toArray();
            }else if($customer[0] == 0 && $company[0] != 0){
                $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereIn('company_id',$company)->pluck('credit','customer_id')->toArray();
                $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->whereIn('company_id',$company)->pluck('sum','customer_id')->toArray();
            }else if($customer[0] == 0 && $company[0] == 0){
                $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->pluck('credit','customer_id')->toArray();
                $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
            }
             //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo volumetracking
             foreach($creditGroupCustomer as $key => $value){
                foreach($listCustomers as $key1 => $value1){
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['SumAriseIncurred'] += $value;
                        }
                    }
            }
            //l???y t???ng ph??t sinh n??? ?????u k??? c???a KH theo volume
            foreach($debitGroupCustomer as $key => $value){
                foreach($listCustomers as $key1 => $value1){
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['SumDebtIncurred'] = $value;
                        }
                    }
            }
            //l???y t???ng ph??t sinh c?? trong k??? c???a KH
            if (($customer[0] != 0 && $company[0] == 0) || ($customer[0] == 0 && $company[0] == 0)) {
                $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
            }else if(($customer[0] != 0 && $company[0] != 0) || ($customer[0] == 0 && $company[0] != 0)){
                $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,$company);
            }
            foreach($paymentItems1 as $key => $value){
                foreach($listCustomers as $key1 => $value1){
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                        }
                    }
                }
            //l???y t???ng ph??t sinh trong k??? c???a KH
            if ($customer[0] != 0 && $company[0] == 0) {
            $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->pluck('credit','customer_id')->toArray();
            $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->pluck('sum','customer_id')->toArray();
            }else if($customer[0] != 0 && $company[0] != 0){
            $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->whereIn('customer_id',$customer)->pluck('credit','customer_id')->toArray();
            $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->pluck('sum','customer_id')->toArray();
            }else if($customer[0] == 0 && $company[0] != 0){
            $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->pluck('credit','customer_id')->toArray();
            $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->pluck('sum','customer_id')->toArray();
            }else if($customer[0] == 0 && $company[0] == 0){
            $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
            $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','customer_id')->toArray();
            }
             //ph??t sinh c?? trong k??? theo volume
             foreach($creditGroupCustomer1 as $key => $value){
                foreach($listCustomers as $key1 => $value1){
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['Generate'] += $value;
                        }
                    }
                }
            //ph??t sinh n??? trong k??? theo volume
            foreach($debitGroupCustomer1 as $key => $value){
                foreach($listCustomers as $key1 => $value1){
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['DebtIncurred'] = $value;
                        }
                    }
                }
                foreach ($listCustomers as $key => $value) {
                    if ($value['ID'] == $arrayCustomer[$key]) {
                        $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                        $listCustomers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                        $listCustomers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                        $listCustomers[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listCustomers[$key]['DebtIncurred']) 
                        - ($debtCreditBeginning['balanceBeginning'] + $listCustomers[$key]['Generate']);
                        }
                    }
                if ($customer[0] != 0 && $company[0] == 0) {
                        //ph??t sinh c?? ng??y
                        $date_credit = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, 0)['date_credit'];
                        $sum_credit = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, 0)['sum_credit'];
                        $credit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, 0)['credit_sums'];
                        //ph??t sinh n??? ng??y
                        $date_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, 0)['date_debit'];
                        $sum_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, 0)['sum_debit'];
                        $debit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, 0)['debit_sums'];
                        //PS n??? th??ng
                        $month_debit = getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,0)['month_debit'];
                        $month_sum_debit = getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,0)['month_sum_debit'];
                        //PS c?? th??ng
                        $month_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,0)['month_credit'];
                        $month_sum_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,0)['month_sum_credit'];
                        //c??n l???i
                        $remain_receive = array_sum(array_column($listCustomers, 'remain_receive'));
                }else if($customer[0] != 0 && $company[0] != 0){
                    //ph??t sinh c??
                    $date_credit = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, $company)['date_credit'];
                    $sum_credit = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, $company)['sum_credit'];
                    $credit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, $company)['credit_sums'];
                    //ph??t sinh n???
                    $date_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, $company)['date_debit'];
                    $sum_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, $company)['sum_debit'];
                    $debit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, $customer, $company)['debit_sums'];
                    //ps n??? th??ng
                    $month_debit = getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,$company)['month_debit'];
                    $month_sum_debit = getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,$company)['month_sum_debit'];
                    //ps c?? th??ng
                    $month_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,$company)['month_credit'];
                    $month_sum_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,$customer,$company)['month_sum_credit'];
                    //c??n l???i
                    $remain_receive = array_sum(array_column($listCustomers, 'remain_receive'));
                }else if(($customer[0] == 0 && $company[0] != 0) || ($customer[0] == 0 && $company[0] == 0)){
                    $listEmployees = [];
                    $listSuppliers = [];
                    //t??nh theo nh??n vi??n------------------------------------------------------------------
                    $employees = Employee::select('id','hovaten')->orderby('id','asc')->get();
                    foreach($employees as $value){
                        $arrayEmployee[] = $value->id;
                    }
                    foreach ($employees as $key => $value) {
                        $listEmployees[] = [
                            'ID' => $value->id,
                            'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                            'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k???
                            'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k???
                            'balanceBeginning' => 0, //d?? c?? ?????u k???
                            'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                            'Generate' => 0,//PS c?? trong k???(???? thu)
                            'remain_receive' => 0
                        ];
                    }
                    //l???y t???ng ph??t sinh c?? ?????u k??? c???a nh??n vi??n
                    if($customer[0] == 0 && $company[0] != 0){
                        $paymentItems2 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,null,$company);
                    }else if($customer[0] == 0 && $company[0] == 0){
                        $paymentItems2 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,null,null);
                    }
                    foreach($paymentItems2 as $key => $value){
                        foreach($listEmployees as $key1 => $value1){
                            if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                                $listEmployees[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                                }
                            }
                    }
                    //l???y t???ng ph??t sinh c?? trong k??? c???a nh??n vi??n
                    if($customer[0] == 0 && $company[0] != 0){
                        $paymentItems3 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,$date2,$company);
                    }else if($customer[0] == 0 && $company[0] == 0){
                        $paymentItems3 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,$date2,null);
                    }
                    foreach($paymentItems3 as $key => $value){
                        foreach($listEmployees as $key1 => $value1){
                            if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                                $listEmployees[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                                }
                            }
                    }
                    foreach ($listEmployees as $key => $value) {
                        if ($value['ID'] == $arrayEmployee[$key]) {
                            $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                            $listEmployees[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                            $listEmployees[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                            $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'],$debtCreditBeginning['balanceBeginning'],$listEmployees[$key]['DebtIncurred'],$listEmployees[$key]['Generate']);
                            $listEmployees[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                            $listEmployees[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
                        }
                    }
                    //t??nh theo nh?? cung c???p--------------------------------------------------------------
                    $suppliers = Supplier::select('id','name')->orderby('id','asc')->get();

                    foreach($suppliers as $value){
                        $arraySupplier[] = $value->id;
                    }
                    foreach ($suppliers as $key => $value) {
                        $listSuppliers[] = [
                            'ID' => $value->id,
                            'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                            'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k??? == 0
                            'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k??? = Ph???i thu ?????u k???
                            'balanceBeginning' => 0, //d?? c?? ?????u k??? = KH tr??? tr?????c ?????u k???
                            'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                            'Generate' => 0,//PS c?? trong k???(???? thu)
                            'remain_receive' => 0
                        ];
                    }
                    //l???y t???ng ph??t sinh c?? ?????u k??? c???a nh?? cung c???p
                    if($customer[0] == 0 && $company[0] != 0){
                        $paymentItems4 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,null,$company);
                    }else if($customer[0] == 0 && $company[0] == 0){
                        $paymentItems4 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,null,null);
                    }
                    foreach($paymentItems4 as $key => $value){
                        foreach($listSuppliers as $key1 => $value1){
                            if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                                $listSuppliers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                                }
                            }
                    }
                    //l???y t???ng ph??t sinh c?? trong k??? c???a nh?? cung c???p
                    if($customer[0] == 0 && $company[0] != 0){
                        $paymentItems5 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,$date2,$company);
                    }else if($customer[0] == 0 && $company[0] == 0){
                        $paymentItems5 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,$date2,null);
                    }
                    foreach($paymentItems5 as $key => $value){
                        foreach($listSuppliers as $key1 => $value1){
                            if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                                $listSuppliers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                                }
                            }
                    }
                    foreach ($listSuppliers as $key => $value) {
                        if ($value['ID'] == $arraySupplier[$key]) {
                            $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                            $listSuppliers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                            $listSuppliers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                            $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'],$debtCreditBeginning['balanceBeginning'],$listSuppliers[$key]['DebtIncurred'],$listSuppliers[$key]['Generate']);
                            $listSuppliers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                            $listSuppliers[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
                        }
                    }
                
                    foreach ($listEmployees as $key => $value) {
                        if ($value['ID'] == $arrayEmployee[$key]) {
                            $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                            $listEmployees[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                            $listEmployees[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                            $listEmployees[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listEmployees[$key]['DebtIncurred']) 
                            - ($debtCreditBeginning['balanceBeginning'] + $listEmployees[$key]['Generate']);
                            }
                        }
                    foreach ($listSuppliers as $key => $value) {
                        if ($value['ID'] == $arraySupplier[$key]) {
                            $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                            $listSuppliers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                            $listSuppliers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                            $listSuppliers[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listSuppliers[$key]['DebtIncurred']) 
                            - ($debtCreditBeginning['balanceBeginning'] + $listSuppliers[$key]['Generate']);
                            }
                        }
                        $arrayOverviews = array_merge($listCustomers,$listEmployees,$listSuppliers);
                        if($customer[0] == 0 && $company[0] != 0){
                        //ph??t sinh c??
                        $date_credit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, $company)['date_credit'];
                        $sum_credit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, $company)['sum_credit'];
                        $credit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, $company)['credit_sums'];
                        //ph??t sinh n???
                        $date_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, $company)['date_debit'];
                        $sum_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, $company)['sum_debit'];
                        $debit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, $company)['debit_sums'];
                        //ps n??? th??ng
                        $month_debit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,$company)['month_debit'];
                        $month_sum_debit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,$company)['month_sum_debit'];
                        //ps c?? th??ng
                        $month_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,$company)['month_credit'];
                        $month_sum_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,$company)['month_sum_credit'];
                        }else if($customer[0] == 0 && $company[0] == 0){
                        //ph??t sinh c??
                        $date_credit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['date_credit'];
                        $sum_credit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['sum_credit'];
                        $credit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['credit_sums'];
                        //ph??t sinh n???
                        $date_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['date_debit'];
                        $sum_debit = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['sum_debit'];
                        $debit_sums = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['debit_sums'];
                        //ps n??? th??ng
                        $month_debit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,0)['month_debit'];
                        $month_sum_debit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,0)['month_sum_debit'];
                        //ps c?? th??ng
                        $month_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,0)['month_credit'];
                        $month_sum_credit = getDateSumCreditDebitByCustomerCompany($date1,$date2,0,0)['month_sum_credit'];
                        }
                        //c??n l???i
                        $remain_receive = array_sum(array_column($arrayOverviews, 'remain_receive'));
                }
        return response()->json(['date_credit' =>$date_credit,'sum_credit' => $sum_credit,'credit_sums' => $credit_sums,'date_debit' => $date_debit,
                                'sum_debit' => $sum_debit,'debit_sums' => $debit_sums,'remain_receive' => $remain_receive,'month_debit' => $month_debit,
                                'month_sum_debit' => $month_sum_debit,'month_credit' => $month_credit,'month_sum_credit' => $month_sum_credit]);
    }
}