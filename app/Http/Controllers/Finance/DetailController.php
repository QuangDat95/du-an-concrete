<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Survey\Employee;
use App\Models\Concrete\Supplier;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\PaymentItem\PaymentItemRepositoryInterface;

class DetailController extends Controller
{
    protected $volumeTrackingRepo;
    protected $customerRepo;
    protected $paymentItemRepo;

    public function __construct(VolumeTrackingRepositoryInterface $volumeTrackingRepo,
    CustomerRepositoryInterface $customerRepo,PaymentItemRepositoryInterface $paymentItemRepo)
    {
        $this->volumeTrackingRepo = $volumeTrackingRepo;
        $this->customerRepo = $customerRepo;
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
        $types = fieldCustomerType();
        $customer_status = $this->customerRepo->getStatus()->get();
        return view('finances.detail',compact('types','customer_status'));
    }

    public function detail()
    {
        $date1 = changeDate(Carbon::now()->startOfMonth());
        $date2 = changeDate(Carbon::now());
        $customers = $this->customerRepo->getCustomer()->get();
        $arrayCustomer = [];
        foreach($customers as $value){
            $arrayCustomer[] = $value->id;
        }
        $listCustomers = [];
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'customerName' => $value->name,
                'SumAriseIncurred' => 0, // tổng PS có đầu kỳ
                'SumDebtIncurred' => 0, // tổng PS nợ đầu kỳ
                'outstandingBalanceBeginning' => 0, //dư nợ đầu kỳ = Phải thu đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ = KH trả trước đầu kỳ
                'DebtIncurred' => 0, //PS nợ trong kỳ (Doanh số)
                'Generate' => 0, //PS có trong kỳ(Đã thu)
                'receivableEndTerm' => 0, //phải thu cuối kỳ
                'CustomerPrepayEndTerm' => 0, //KH trả trước cuối kỳ
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của KH
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
        foreach ($paymentItems1 as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                }
            }
        }
        $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->pluck('credit','customer_id')->toArray();
        //lấy tổng phát sinh có đầu kỳ của KH theo volumetracking
        foreach ($creditGroupCustomer as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] += $value;
                }
            }
        }
        //lấy tổng phát sinh đầu kỳ theo volume
        $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
        //lấy tổng phát sinh nợ đầu kỳ của KH theo volume
        foreach ($debitGroupCustomer as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumDebtIncurred'] = $value;
                }
            }
        }
        //lấy tổng phát sinh có trong kỳ của KH
        $paymentItems2 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
        foreach ($paymentItems2 as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                }
            }
        }
        //lấy tổng phát sinh trong kỳ của KH
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
        //phát sinh có trong kỳ theo volume
        foreach ($creditGroupCustomer1 as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] += $value;
                }
            }
        }
        //phát sinh nợ trong kỳ theo volume
        $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','customer_id')->toArray();
        foreach ($debitGroupCustomer1 as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['DebtIncurred'] = $value;
                }
            }
        }

        foreach ($listCustomers as $key => $value) {
            if ($value['ID'] == $arrayCustomer[$key]) {
                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'], $value['SumAriseIncurred']);
                $listCustomers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                $listCustomers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'], $debtCreditBeginning['balanceBeginning'], $listCustomers[$key]['DebtIncurred'], $listCustomers[$key]['Generate']);
                $listCustomers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                $listCustomers[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
            }
        }
        //tính theo nhân viên------------------------------------------------------------------
        $employees = Employee::select('id', 'hovaten')->orderby('id', 'asc')->get();

        foreach ($employees as $value) {
            $arrayEmployee[] = $value->id;
        }
        foreach ($employees as $key => $value) {
            $listEmployees[] = [
                'ID' => $value->id,
                'customerName' => $value->hovaten,
                'SumAriseIncurred' => 0, // tổng PS có đầu kỳ
                'SumDebtIncurred' => 0, // tổng PS nợ đầu kỳ == 0
                'outstandingBalanceBeginning' => 0, //dư nợ đầu kỳ = Phải thu đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ = KH trả trước đầu kỳ
                'DebtIncurred' => 0, //PS nợ trong kỳ (Doanh số)
                'Generate' => 0, //PS có trong kỳ(Đã thu)
                'receivableEndTerm' => 0, //phải thu cuối kỳ
                'CustomerPrepayEndTerm' => 0, //KH trả trước cuối kỳ
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của nhân viên
        $paymentItems3 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,null,null);
        foreach ($paymentItems3 as $key => $value) {
            foreach ($listEmployees as $key1 => $value1) {
                if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                    $listEmployees[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                }
            }
        }
        //lấy tổng phát sinh có trong kỳ của nhân viên
        $paymentItems4 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,$date2,null);
        foreach ($paymentItems4 as $key => $value) {
            foreach ($listEmployees as $key1 => $value1) {
                if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                    $listEmployees[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                }
            }
        }
        foreach ($listEmployees as $key => $value) {
            if ($value['ID'] == $arrayEmployee[$key]) {
                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'], $value['SumAriseIncurred']);
                $listEmployees[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                $listEmployees[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'], $debtCreditBeginning['balanceBeginning'], $listEmployees[$key]['DebtIncurred'], $listEmployees[$key]['Generate']);
                $listEmployees[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                $listEmployees[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
            }
        }
        //tính theo nhà cung cấp--------------------------------------------------------------
        $suppliers = Supplier::select('id', 'name')->orderby('id', 'asc')->get();

        foreach ($suppliers as $value) {
            $arraySupplier[] = $value->id;
        }
        foreach ($suppliers as $key => $value) {
            $listSuppliers[] = [
                'ID' => $value->id,
                'customerName' => $value->name,
                'SumAriseIncurred' => 0, // tổng PS có đầu kỳ
                'SumDebtIncurred' => 0, // tổng PS nợ đầu kỳ == 0
                'outstandingBalanceBeginning' => 0, //dư nợ đầu kỳ = Phải thu đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ = KH trả trước đầu kỳ
                'DebtIncurred' => 0, //PS nợ trong kỳ (Doanh số)
                'Generate' => 0, //PS có trong kỳ(Đã thu)
                'receivableEndTerm' => 0, //phải thu cuối kỳ
                'CustomerPrepayEndTerm' => 0, //KH trả trước cuối kỳ
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của nhà cung cấp
        $paymentItems5 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,null,null);
        foreach ($paymentItems5 as $key => $value) {
            foreach ($listSuppliers as $key1 => $value1) {
                if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                    $listSuppliers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                }
            }
        }
        //lấy tổng phát sinh có trong kỳ của nhà cung cấp
        $paymentItems6 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,$date2,null);
        foreach ($paymentItems6 as $key => $value) {
            foreach ($listSuppliers as $key1 => $value1) {
                if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                    $listSuppliers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                }
            }
        }
        foreach ($listSuppliers as $key => $value) {
            if ($value['ID'] == $arraySupplier[$key]) {
                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'], $value['SumAriseIncurred']);
                $listSuppliers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                $listSuppliers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'], $debtCreditBeginning['balanceBeginning'], $listSuppliers[$key]['DebtIncurred'], $listSuppliers[$key]['Generate']);
                $listSuppliers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                $listSuppliers[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
            }
        }

        $listCustomerNews = [];
        foreach ($listCustomers as $key => $value) {
            if ($value['ID'] == $arrayCustomer[$key]) {
                if ($value['outstandingBalanceBeginning'] != 0 || $value['balanceBeginning'] != 0 || $value['DebtIncurred'] != 0 || $value['Generate'] || $value['receivableEndTerm'] != 0 || $value['CustomerPrepayEndTerm']) {
                    $listCustomerNews[] = [
                        'ID' => $listCustomers[$key]['ID'],
                        'customerName' => $listCustomers[$key]['customerName'],
                        'outstandingBalanceBeginning' => $listCustomers[$key]['outstandingBalanceBeginning'],
                        'balanceBeginning' => $listCustomers[$key]['balanceBeginning'],
                        'DebtIncurred' => $listCustomers[$key]['DebtIncurred'],
                        'Generate' => $listCustomers[$key]['Generate'],
                        'receivableEndTerm' => $listCustomers[$key]['receivableEndTerm'],
                        'CustomerPrepayEndTerm' => $listCustomers[$key]['CustomerPrepayEndTerm'],
                    ];
                }
            }
        }

        $listEmployeeNews = [];
        foreach ($listEmployees as $key => $value) {
            if ($value['ID'] == $arrayEmployee[$key]) {
                if ($value['outstandingBalanceBeginning'] != 0 || $value['balanceBeginning'] != 0 || $value['DebtIncurred'] != 0 || $value['Generate'] || $value['receivableEndTerm'] != 0 || $value['CustomerPrepayEndTerm']) {
                    $listEmployeeNews[] = [
                        'ID' => $listEmployees[$key]['ID'],
                        'customerName' => $listEmployees[$key]['customerName'],
                        'outstandingBalanceBeginning' => $listEmployees[$key]['outstandingBalanceBeginning'],
                        'balanceBeginning' => $listEmployees[$key]['balanceBeginning'],
                        'DebtIncurred' => $listEmployees[$key]['DebtIncurred'],
                        'Generate' => $listEmployees[$key]['Generate'],
                        'receivableEndTerm' => $listEmployees[$key]['receivableEndTerm'],
                        'CustomerPrepayEndTerm' => $listEmployees[$key]['CustomerPrepayEndTerm'],
                    ];
                }
            }
        }

        $listSuplierNews = [];
        foreach ($listSuppliers as $key => $value) {
            if ($value['ID'] == $arraySupplier[$key]) {
                if ($value['outstandingBalanceBeginning'] != 0 || $value['balanceBeginning'] != 0 || $value['DebtIncurred'] != 0 || $value['Generate'] || $value['receivableEndTerm'] != 0 || $value['CustomerPrepayEndTerm']) {
                    $listSuplierNews[] = [
                        'ID' => $listSuppliers[$key]['ID'],
                        'customerName' => $listSuppliers[$key]['customerName'],
                        'outstandingBalanceBeginning' => $listSuppliers[$key]['outstandingBalanceBeginning'],
                        'balanceBeginning' => $listSuppliers[$key]['balanceBeginning'],
                        'DebtIncurred' => $listSuppliers[$key]['DebtIncurred'],
                        'Generate' => $listSuppliers[$key]['Generate'],
                        'receivableEndTerm' => $listSuppliers[$key]['receivableEndTerm'],
                        'CustomerPrepayEndTerm' => $listSuppliers[$key]['CustomerPrepayEndTerm'],
                    ];
                }
            }
        }
        $dataTableDetails = array_merge($listCustomerNews, $listEmployeeNews, $listSuplierNews);
        if (request()->ajax()) {
            return datatables()
                ->of($dataTableDetails)
                ->editColumn('outstandingBalanceBeginning', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail['outstandingBalanceBeginning']));
                })
                ->editColumn('balanceBeginning', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["balanceBeginning"]));
                })
                ->editColumn('DebtIncurred', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["DebtIncurred"]));
                })
                ->editColumn('Generate', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["Generate"]));
                })
                ->editColumn('receivableEndTerm', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["receivableEndTerm"]));
                })
                ->editColumn('CustomerPrepayEndTerm', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["CustomerPrepayEndTerm"]));
                })
                ->make(true);
        }
    }

    public function detailRequest(Request $request)
    {
        $customer = $request->customer;
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        $status = $request->status;
        $classify = $request->classify;
        $company = $request->company;
        if (($customer[0] == 0 && $status == 0 && $classify == 0 && $company[0] == 0) || ($customer[0] == 0 && $status == 0 && $classify == 0 && $company[0] != 0)) {
            $customers = $this->customerRepo->getCustomer()->get();
        }else if(($customer[0] != 0 && $status == 0 && $classify == 0 && $company[0] == 0) || ($customer[0] != 0 && $status == 0 && $classify == 0 && $company[0] != 0)){
            $customers = $this->customerRepo->getCustomer()->whereIn('id',$customer)->get();
        }else if(($customer[0] != 0 && $status != 0 && $classify == 0 && $company[0] == 0) || ($customer[0] != 0 && $status != 0 && $classify == 0 && $company[0] != 0)){
            $customers = $this->customerRepo->getCustomer()->whereIn('id',$customer)->where('status_id','=',$status)->get();
        }else if(($customer[0] != 0 && $status != 0 && $classify != 0 && $company[0] == 0) || ($customer[0] != 0 && $status != 0 && $classify != 0 && $company[0] != 0)){
            $customers = $this->customerRepo->getCustomer()->whereIn('id',$customer)->where('status_id','=',$status)->where('type_id','=',$classify)->get();
        }else if(($customer[0] != 0 && $status == 0 && $classify != 0 && $company[0] == 0) || ($customer[0] != 0 && $status == 0 && $classify != 0 && $company[0] != 0)){
            $customers = $this->customerRepo->getCustomer()->whereIn('id',$customer)->where('type_id',$classify)->get();
        }else if(($customer[0] == 0 && $status != 0 && $classify != 0 && $company[0] == 0) || ($customer[0] == 0 && $status != 0 && $classify != 0 && $company[0] != 0)){
            $customers = $this->customerRepo->getCustomer()->where('status_id',$status)->where('type_id',$classify)->get();
        }else if(($customer[0] == 0 && $status != 0 && $classify == 0 && $company[0] == 0) || ($customer[0] == 0 && $status != 0 && $classify == 0 && $company[0] != 0)){
            $customers = $this->customerRepo->getCustomer()->where('status_id',$status)->get();
        }else if(($customer[0] == 0 && $status == 0 && $classify != 0 && $company[0] == 0) || ($customer[0] == 0 && $status == 0 && $classify != 0 && $company[0] != 0)){
            $customers = $this->customerRepo->getCustomer()->where('type_id',$classify)->get();
        }

         //tạo mảng ID khách hàng
            foreach ($customers as $value) {
                $arrayCustomer[] = $value->id;
            }
            foreach ($customers as $key => $value) {
                $listCustomers[] = [
                    'ID' => $value->id,
                    'customerName' => $value->name,
                    'SumAriseIncurred' => 0, // tổng PS có đầu kỳ
                    'SumDebtIncurred' => 0, // tổng PS nợ đầu kỳ
                    'outstandingBalanceBeginning' => 0, //dư nợ đầu kỳ = Phải thu đầu kỳ
                    'balanceBeginning' => 0, //dư có đầu kỳ = KH trả trước đầu kỳ
                    'DebtIncurred' => 0, //PS nợ trong kỳ (Doanh số)
                    'Generate' => 0, //PS có trong kỳ(Đã thu)
                    'receivableEndTerm' => 0, //phải thu cuối kỳ
                    'CustomerPrepayEndTerm' => 0, //KH trả trước cuối kỳ
                ];
            }
            //lấy tổng phát sinh có đầu kỳ của KH
            if($company[0] == 0){
                $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
            }else if($company[0] != 0){
                $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,$company);
            }
            foreach ($paymentItems1 as $key => $value) {
                foreach ($listCustomers as $key1 => $value1) {
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
            //lấy tổng phát sinh đầu kỳ theo volume
            if ($customer[0] == 0 && $company[0] == 0) {
                $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
                $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->pluck('credit','customer_id')->toArray();
            }else if($customer[0] != 0 && $company[0] == 0){
                $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->whereIn('customer_id',$customer)->pluck('sum','customer_id')->toArray();
                $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereIn('customer_id',$customer)->pluck('credit','customer_id')->toArray();
            }else if($customer[0] != 0 && $company[0] != 0){
                $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->whereIn('customer_id',$customer)->whereIn('company_id',$company)->pluck('sum','customer_id')->toArray();
                $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereIn('company_id',$company)->whereIn('customer_id',$customer)->pluck('credit','customer_id')->toArray();
            }else if($customer[0] == 0 && $company[0] != 0){
                $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->whereIn('company_id',$company)->pluck('sum','customer_id')->toArray();
                $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereIn('company_id',$company)->pluck('credit','customer_id')->toArray();
            }
            //lấy tổng phát sinh có đầu kỳ của KH theo volumetracking
            foreach ($creditGroupCustomer as $key => $value) {
                foreach ($listCustomers as $key1 => $value1) {
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['SumAriseIncurred'] += $value;
                    }
                }
            }
            //lấy tổng phát sinh nợ đầu kỳ của KH theo volume
            foreach ($debitGroupCustomer as $key => $value) {
                foreach ($listCustomers as $key1 => $value1) {
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['SumDebtIncurred'] = $value;
                    }
                }
            }
             //lấy tổng phát sinh có trong kỳ của KH
            if ($company[0] == 0) {
                $paymentItems2 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
            }else if($company[0] != 0){
                $paymentItems2 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,$company);
            }
            foreach ($paymentItems2 as $key => $value) {
                foreach ($listCustomers as $key1 => $value1) {
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
            //lấy tổng phát sinh trong kỳ của KH
            if ($customer[0] == 0 && $company[0] == 0) {
                $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->where('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
                $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','customer_id')->toArray();
            }else if($customer[0] != 0 && $company[0] == 0){
                $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->where('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->pluck('credit','customer_id')->toArray();
                $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->pluck('sum','customer_id')->toArray();
            }else if($customer[0] != 0 && $company[0] != 0){
                $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->where('from_date',[$date1,$date2])->whereIn('company_id',$company)->whereIn('customer_id',$customer)->pluck('credit','customer_id')->toArray();
                $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$customer)->whereIn('company_id',$company)->pluck('sum','customer_id')->toArray();
            }else if($customer[0] == 0 && $company[0] != 0){
                $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->where('from_date',[$date1,$date2])->whereIn('company_id',$company)->pluck('credit','customer_id')->toArray();
                $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id') ->whereBetween('from_date',[$date1,$date2])->whereIn('company_id',$company)->pluck('sum','customer_id')->toArray();
            }
            //phát sinh có trong kỳ theo volume
            foreach ($creditGroupCustomer1 as $key => $value) {
                foreach ($listCustomers as $key1 => $value1) {
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['Generate'] += $value;
                    }
                }
            }
            //phát sinh nợ trong kỳ theo volume
            foreach ($debitGroupCustomer1 as $key => $value) {
                foreach ($listCustomers as $key1 => $value1) {
                    if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                        $listCustomers[$key1]['DebtIncurred'] = $value;
                    }
                }
            }
            if (($customer[0] == 0 && $status == 0 && $classify == 0 && $company[0] == 0) || ($customer[0] == 0 && $status == 0 && $classify == 0 && $company[0] != 0)) {
                foreach ($listCustomers as $key => $value) {
                            if ($value['ID'] == $arrayCustomer[$key]) {
                                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'], $value['SumAriseIncurred']);
                                $listCustomers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                                $listCustomers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'],$debtCreditBeginning['balanceBeginning'],$listCustomers[$key]['DebtIncurred'],$listCustomers[$key]['Generate']);
                                $listCustomers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                                $listCustomers[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
                            }
                        }
                        //tính theo nhân viên------------------------------------------------------------------
                        $employees = Employee::select('id', 'hovaten')
                            ->orderby('id', 'asc')
                            ->get();
            
                        foreach ($employees as $value) {
                            $arrayEmployee[] = $value->id;
                        }
                        foreach ($employees as $key => $value) {
                            $listEmployees[] = [
                                'ID' => $value->id,
                                'customerName' => $value->hovaten,
                                'SumAriseIncurred' => 0, // tổng PS có đầu kỳ
                                'SumDebtIncurred' => 0, // tổng PS nợ đầu kỳ == 0
                                'outstandingBalanceBeginning' => 0, //dư nợ đầu kỳ = Phải thu đầu kỳ
                                'balanceBeginning' => 0, //dư có đầu kỳ = KH trả trước đầu kỳ
                                'DebtIncurred' => 0, //PS nợ trong kỳ (Doanh số)
                                'Generate' => 0, //PS có trong kỳ(Đã thu)
                                'receivableEndTerm' => 0, //phải thu cuối kỳ
                                'CustomerPrepayEndTerm' => 0, //KH trả trước cuối kỳ
                            ];
                        }
                        //lấy tổng phát sinh có đầu kỳ của nhân viên
                        $paymentItems3 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,null,null);
                        foreach ($paymentItems3 as $key => $value) {
                            foreach ($listEmployees as $key1 => $value1) {
                                if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                                    $listEmployees[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                                }
                            }
                        }
                        //lấy tổng phát sinh có trong kỳ của nhân viên
                        $paymentItems4 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,$date2,null);
                        foreach ($paymentItems4 as $key => $value) {
                            foreach ($listEmployees as $key1 => $value1) {
                                if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                                    $listEmployees[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                                }
                            }
                        }
                        foreach ($listEmployees as $key => $value) {
                            if ($value['ID'] == $arrayEmployee[$key]) {
                                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'], $value['SumAriseIncurred']);
                                $listEmployees[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                                $listEmployees[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'], $debtCreditBeginning['balanceBeginning'], $listEmployees[$key]['DebtIncurred'], $listEmployees[$key]['Generate']);
                                $listEmployees[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                                $listEmployees[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
                            }
                        }
                        //tính theo nhà cung cấp--------------------------------------------------------------
                        $suppliers = Supplier::select('id', 'name')
                            ->orderby('id', 'asc')
                            ->get();
            
                        foreach ($suppliers as $value) {
                            $arraySupplier[] = $value->id;
                        }
                        foreach ($suppliers as $key => $value) {
                            $listSuppliers[] = [
                                'ID' => $value->id,
                                'customerName' => $value->name,
                                'SumAriseIncurred' => 0, // tổng PS có đầu kỳ
                                'SumDebtIncurred' => 0, // tổng PS nợ đầu kỳ == 0
                                'outstandingBalanceBeginning' => 0, //dư nợ đầu kỳ = Phải thu đầu kỳ
                                'balanceBeginning' => 0, //dư có đầu kỳ = KH trả trước đầu kỳ
                                'DebtIncurred' => 0, //PS nợ trong kỳ (Doanh số)
                                'Generate' => 0, //PS có trong kỳ(Đã thu)
                                'receivableEndTerm' => 0, //phải thu cuối kỳ
                                'CustomerPrepayEndTerm' => 0, //KH trả trước cuối kỳ
                            ];
                        }
                        //lấy tổng phát sinh có đầu kỳ của nhà cung cấp
                        $paymentItems5 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,null,null);
                        foreach ($paymentItems5 as $key => $value) {
                            foreach ($listSuppliers as $key1 => $value1) {
                                if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                                    $listSuppliers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                                }
                            }
                        }
                        //lấy tổng phát sinh có trong kỳ của nhà cung cấp
                        $paymentItems6 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,$date2,null);
                        foreach ($paymentItems6 as $key => $value) {
                            foreach ($listSuppliers as $key1 => $value1) {
                                if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                                    $listSuppliers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                                }
                            }
                        }
                        foreach ($listSuppliers as $key => $value) {
                            if ($value['ID'] == $arraySupplier[$key]) {
                                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'], $value['SumAriseIncurred']);
                                $listSuppliers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                                $listSuppliers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'], $debtCreditBeginning['balanceBeginning'], $listSuppliers[$key]['DebtIncurred'], $listSuppliers[$key]['Generate']);
                                $listSuppliers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                                $listSuppliers[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
                            }
                        }
            
                        $listCustomerNews = [];
                        foreach ($listCustomers as $key => $value) {
                            if ($value['ID'] == $arrayCustomer[$key]) {
                                if ($value['outstandingBalanceBeginning'] != 0 || $value['balanceBeginning'] != 0 || $value['DebtIncurred'] != 0 || $value['Generate'] || $value['receivableEndTerm'] != 0 || $value['CustomerPrepayEndTerm']) {
                                    $listCustomerNews[] = [
                                        'ID' => $listCustomers[$key]['ID'],
                                        'customerName' => $listCustomers[$key]['customerName'],
                                        'outstandingBalanceBeginning' => $listCustomers[$key]['outstandingBalanceBeginning'],
                                        'balanceBeginning' => $listCustomers[$key]['balanceBeginning'],
                                        'DebtIncurred' => $listCustomers[$key]['DebtIncurred'],
                                        'Generate' => $listCustomers[$key]['Generate'],
                                        'receivableEndTerm' => $listCustomers[$key]['receivableEndTerm'],
                                        'CustomerPrepayEndTerm' => $listCustomers[$key]['CustomerPrepayEndTerm'],
                                    ];
                                }
                            }
                        }
            
                        $listEmployeeNews = [];
                        foreach ($listEmployees as $key => $value) {
                            if ($value['ID'] == $arrayEmployee[$key]) {
                                if ($value['outstandingBalanceBeginning'] != 0 || $value['balanceBeginning'] != 0 || $value['DebtIncurred'] != 0 || $value['Generate'] || $value['receivableEndTerm'] != 0 || $value['CustomerPrepayEndTerm']) {
                                    $listEmployeeNews[] = [
                                        'ID' => $listEmployees[$key]['ID'],
                                        'customerName' => $listEmployees[$key]['customerName'],
                                        'outstandingBalanceBeginning' => $listEmployees[$key]['outstandingBalanceBeginning'],
                                        'balanceBeginning' => $listEmployees[$key]['balanceBeginning'],
                                        'DebtIncurred' => $listEmployees[$key]['DebtIncurred'],
                                        'Generate' => $listEmployees[$key]['Generate'],
                                        'receivableEndTerm' => $listEmployees[$key]['receivableEndTerm'],
                                        'CustomerPrepayEndTerm' => $listEmployees[$key]['CustomerPrepayEndTerm'],
                                    ];
                                }
                            }
                        }
            
                        $listSuplierNews = [];
                        foreach ($listSuppliers as $key => $value) {
                            if ($value['ID'] == $arraySupplier[$key]) {
                                if ($value['outstandingBalanceBeginning'] != 0 || $value['balanceBeginning'] != 0 || $value['DebtIncurred'] != 0 || $value['Generate'] || $value['receivableEndTerm'] != 0 || $value['CustomerPrepayEndTerm']) {
                                    $listSuplierNews[] = [
                                        'ID' => $listSuppliers[$key]['ID'],
                                        'customerName' => $listSuppliers[$key]['customerName'],
                                        'outstandingBalanceBeginning' => $listSuppliers[$key]['outstandingBalanceBeginning'],
                                        'balanceBeginning' => $listSuppliers[$key]['balanceBeginning'],
                                        'DebtIncurred' => $listSuppliers[$key]['DebtIncurred'],
                                        'Generate' => $listSuppliers[$key]['Generate'],
                                        'receivableEndTerm' => $listSuppliers[$key]['receivableEndTerm'],
                                        'CustomerPrepayEndTerm' => $listSuppliers[$key]['CustomerPrepayEndTerm'],
                                    ];
                                }
                            }
                        }
                        $dataTableDetails = array_merge($listCustomerNews, $listEmployeeNews, $listSuplierNews);
            }else if(($customer[0] != 0 && $status == 0 && $classify == 0 && $company[0] == 0)
                  || ($customer[0] != 0 && $status != 0 && $classify == 0 && $company[0] == 0)
                  || ($customer[0] != 0 && $status != 0 && $classify != 0 && $company[0] == 0)
                  || ($customer[0] != 0 && $status != 0 && $classify != 0 && $company[0] != 0)
                  || ($customer[0] != 0 && $status != 0 && $classify == 0 && $company[0] != 0)
                  || ($customer[0] != 0 && $status == 0 && $classify == 0 && $company[0] != 0)
                  || ($customer[0] != 0 && $status == 0 && $classify != 0 && $company[0] == 0)
                  || ($customer[0] != 0 && $status == 0 && $classify != 0 && $company[0] != 0)
                  || ($customer[0] == 0 && $status != 0 && $classify != 0 && $company[0] != 0)
                  || ($customer[0] == 0 && $status != 0 && $classify == 0 && $company[0] != 0)
                  || ($customer[0] == 0 && $status == 0 && $classify != 0 && $company[0] != 0)
                  || ($customer[0] == 0 && $status != 0 && $classify == 0 && $company[0] == 0)
                  || ($customer[0] == 0 && $status != 0 && $classify != 0 && $company[0] == 0)
                  || ($customer[0] == 0 && $status == 0 && $classify != 0 && $company[0] == 0)){
                foreach ($listCustomers as $key => $value) {
                    if ($value['ID'] == $arrayCustomer[$key]) {
                        $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'], $value['SumAriseIncurred']);
                        $listCustomers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                        $listCustomers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                        $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'], $debtCreditBeginning['balanceBeginning'], $listCustomers[$key]['DebtIncurred'], $listCustomers[$key]['Generate']);
                        $listCustomers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                        $listCustomers[$key]['CustomerPrepayEndTerm'] = $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
                    }
                }
                $dataTableDetails = [];
                foreach ($listCustomers as $key => $value) {
                    if ($value['ID'] == $arrayCustomer[$key]) {
                        if ($value['outstandingBalanceBeginning'] != 0 || $value['balanceBeginning'] != 0 || $value['DebtIncurred'] != 0 || $value['Generate'] || $value['receivableEndTerm'] != 0 || $value['CustomerPrepayEndTerm']) {
                            $dataTableDetails[] = [
                                'ID' => $listCustomers[$key]['ID'],
                                'customerName' => $listCustomers[$key]['customerName'],
                                'outstandingBalanceBeginning' => $listCustomers[$key]['outstandingBalanceBeginning'],
                                'balanceBeginning' => $listCustomers[$key]['balanceBeginning'],
                                'DebtIncurred' => $listCustomers[$key]['DebtIncurred'],
                                'Generate' => $listCustomers[$key]['Generate'],
                                'receivableEndTerm' => $listCustomers[$key]['receivableEndTerm'],
                                'CustomerPrepayEndTerm' => $listCustomers[$key]['CustomerPrepayEndTerm'],
                            ];
                        }
                    }
                }
            }
        if (request()->ajax()) {
            return datatables()->of($dataTableDetails)
                ->editColumn('outstandingBalanceBeginning', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail['outstandingBalanceBeginning']));
                })
                ->editColumn('balanceBeginning', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["balanceBeginning"]));
                })
                ->editColumn('DebtIncurred', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["DebtIncurred"]));
                })
                ->editColumn('Generate', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["Generate"]));
                })
                ->editColumn('receivableEndTerm', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["receivableEndTerm"]));
                })
                ->editColumn('CustomerPrepayEndTerm', function ($dataTableDetail) {
                    return number_format(round($dataTableDetail["CustomerPrepayEndTerm"]));
                })->make(true);
        }
    }

    public function fillterStatusSelectCustomer(Request $request)
    {
        $customer = $request->customer;
        if ($customer != null && $customer[0] != 0) {
            $status = $this->customerRepo->getStatus()->whereIn('id', $customer)->where('status_id','!=',null)->get();
        } else if ($customer == null || $customer[0] == 0) {
            $status = $this->customerRepo->getStatus()->where('status_id','!=',null)->get();
        }
        return view('finances.fillter.status', compact('status'));
    }

    public function fillterClassifySelectCustomer(Request $request)
    {
        $customer = $request->customer;
        if ($customer != null && $customer[0] != 0) {
            $classify = $this->customerRepo->getType()->whereIn('id', $customer)->get();
        } else if ($customer[0] == 0) {
            $classify = $this->customerRepo->getType()->get();
        }
        return view('finances.fillter.classify', compact('classify'));
    }
}
