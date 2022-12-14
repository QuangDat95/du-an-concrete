<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Concrete\Customer;
use App\Models\Concrete\PaymentItem;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\PaymentItem\PaymentItemRepositoryInterface;
use Carbon\Carbon;

class DetailDebtCollectionController extends Controller
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
        $accountants = $this->customerRepo->getAccountant()->get();
        return view('finances.detail_debt_collection',compact('accountants'));
    }

    public function moneyCollect()
    {
        $date1 = changeDate(Carbon::now()->startOfMonth());
        $date2 = changeDate(Carbon::now());
        //------------------chi ti???t thu n??? theo s??? ti???n thu ???????c------------------------------------------//
        $accountants = $this->customerRepo->getAccountant()->get();
        foreach ($accountants as $value) {
            if($value->accountant_name == null){
                $proceedsaccountants[] = [
                        'nameAccountant' => 'Kh??ng x??c ?????nh',
                        'nameCustomer' => [],
                        'ariseThere' => 0
                ];
            }else{
                $proceedsaccountants[] = [
                    'nameAccountant' => $value->accountant_name,
                    'nameCustomer' => [],
                    'ariseThere' => 0
                ];
            }
        }
        foreach ($proceedsaccountants as $key => $value) {
            $proceedsaccountants[$key]['nameCustomer'] = $this->getaccountant($value['nameAccountant'],$date1,$date2);
        }
        
        $proceedsAccountant = [];
        foreach ($proceedsaccountants as $value) {
            if ($value['nameCustomer'] != []) {
                foreach ($value['nameCustomer'] as $key1 => $value1) {
                    $proceedsAccountant[] = [
                        'nameAccountant' => $value['nameAccountant'],
                        'nameCustomer' => $key1,
                        'ariseThere' => $value1
                    ];
                }
            } else {
                $proceedsAccountant[] = [
                    'nameAccountant' => $value['nameAccountant'],
                    'nameCustomer' => [],
                    'ariseThere' => 0
                ];
            }
        }
        if (request()->ajax()) {
            return datatables()
                ->of($proceedsAccountant)
                ->editColumn('ariseThere', function ($value) {
                    return (int) $value['ariseThere'];
                })
                ->make(true);
        }
    }

    public function moneyCollectRequest(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        $accountant = $request->accountant;
        //------------------chi ti???t thu n??? theo s??? ti???n thu ???????c------------------------------------------//
        if($accountant == 'Kh??ng x??c ?????nh'){
            $accountants = $this->customerRepo->getAccountant()->where('accountant_name',null)->get();
        }else if($accountant == 0){
            $accountants = $this->customerRepo->getAccountant()->get();
        }else if($accountant != 0){
            $accountants = $this->customerRepo->getAccountant()->where('accountant_name',$accountant)->get();
        }
        foreach ($accountants as $value) {
            if($value->accountant_name == null){
                $proceedsaccountants[] = [
                        'nameAccountant' => 'Kh??ng x??c ?????nh',
                        'nameCustomer' => [],
                        'ariseThere' => 0
                ];
            }else{
                $proceedsaccountants[] = [
                    'nameAccountant' => $value->accountant_name,
                    'nameCustomer' => [],
                    'ariseThere' => 0
                ];
            }
        }
        foreach ($proceedsaccountants as $key => $value) {
            $proceedsaccountants[$key]['nameCustomer'] = $this->getaccountant($value['nameAccountant'],$date1,$date2);
        }
        
        $proceedsAccountant = [];
        foreach ($proceedsaccountants as $value) {
            if ($value['nameCustomer'] != []) {
                foreach ($value['nameCustomer'] as $key1 => $value1) {
                    $proceedsAccountant[] = [
                        'nameAccountant' => $value['nameAccountant'],
                        'nameCustomer' => $key1,
                        'ariseThere' => $value1
                    ];
                }
            } else {
                $proceedsAccountant[] = [
                    'nameAccountant' => $value['nameAccountant'],
                    'nameCustomer' => [],
                    'ariseThere' => 0
                ];
            }
        }
        if (request()->ajax()) {
            return datatables()
                ->of($proceedsAccountant)
                ->editColumn('ariseThere', function ($value) {
                    return (int) $value['ariseThere'];
                })
                ->make(true);
        }
    }

    function getaccountant($accountant,$date1,$date2)
    {
        $getaccountant = PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment' => function($query){
        $query->select('id','partyable_id');
        }])->whereHas('payment',function($query) use ($date1,$date2){
                    $query->whereBetween('payment_date',[$date1,$date2])->where('partyable_type','App\Models\Concrete\Customer');
            })->whereHas('creditAccount',function($query){
                $query->where('account_code','like','131%');
            })->groupBy('payment_id','credit_account_id')
            ->get(['payment_id','credit_account_id'])->sortBy("payment.partyable_id")->groupBy('payment.partyable_id')->toArray();
        $arrayObtainAccountant = [];
        if($getaccountant != []){
            foreach ($getaccountant as $key => $value) {
                if(Customer::find($key)->accountant_name == $accountant && $accountant != 'Kh??ng x??c ?????nh'){
                    foreach($value as $key1 => $value1){
                        $arrayObtainAccountant[] = [
                            'nameCustomer' => Customer::find($value1['payment']['partyable_id'])->name,
                            'ariseThere' => $value1['amount']
                        ];
                    }
                }else if(Customer::find($key)->accountant_name == null && $accountant == 'Kh??ng x??c ?????nh'){
                    foreach($value as $key1 => $value1){
                        $arrayObtainAccountant[] = [
                            'nameCustomer' => Customer::find($value1['payment']['partyable_id'])->name,
                            'ariseThere' => $value1['amount']
                        ];
                    }
                }
            } 
        }else{
            $arrayObtainAccountant[] = [
                'nameCustomer' => [],
                'ariseThere' => 0
            ];
        }
        // dd($arrayObtainAccountant);
        $arrayObtainAccountants = [];
        foreach($arrayObtainAccountant as $key => $value){
            if($value['nameCustomer'] != []){
                $arrayObtainAccountants[$value['nameCustomer']] = ($arrayObtainAccountants[$value['nameCustomer']] ?? 0) + $value['ariseThere'];
            }else{
                $arrayObtainAccountants[null] = 0;
            }
        }
        if($accountant == 'Kh??ng x??c ?????nh'){
            $volumeTrackings = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])
            ->with(['customer:id,name'])->whereHas('customer',function($query) use ($accountant){
                $query->where('accountant_name',null);
            })->get()->toArray();
        }else if($accountant != 0){
            $volumeTrackings = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])
            ->with(['customer:id,name'])->whereHas('customer',function($query) use ($accountant){
                $query->where('accountant_name',$accountant);
            })->get()->toArray();
        }else if($accountant == 0){
            $volumeTrackings = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])
            ->with(['customer:id,name'])->get()->toArray();
        }
        
            $customers = [];
            foreach($volumeTrackings as $value){
                $customers[$value['customer']['name']] = ($customers[$value['customer_id']] ?? 0) + $value['credit'];
            }
            foreach ($customers as $key => $value) {
                foreach($arrayObtainAccountants as $key1 => $value1){
                    if($key1 == $key && $key1 != []){
                        $arrayObtainAccountants[$key1] += $value;
                    }
                }
            }
        return $arrayObtainAccountants;
    }

    public function accountReceivable()
    {
        $date1 = changeDate(Carbon::now()->startOfMonth());
        $date2 = changeDate(Carbon::now());
        //-------------------chi ti???t thu n??? theo n??? ph???i thu-----------------------------------------------------------//
        $accountants = $this->customerRepo->getAccountant()->get();
        $arrayAccountReceivableAccountant = [];
        foreach ($accountants as $value) {
            if($value->accountant_name != null){
                $arrayAccountReceivableAccountant[] = [
                    'nameAccountant' => $value->accountant_name,
                    'customerAccountant' => 0,
                    'sum' => 0,
                ];
            }else{
                $arrayAccountReceivableAccountant[] = [
                    'nameAccountant' => 'Kh??ng x??c ?????nh',
                    'customerAccountant' => 0,
                    'sum' => 0,
                ];
            }
        }

        foreach ($arrayAccountReceivableAccountant as $key => $value) {
            $arrayAccountReceivableAccountant[$key]['customerAccountant'] = $this->accountReceivableAccountant($value['nameAccountant'],$date1,$date2);
        }

        $arrayDebtReceivableAccountant = [];
        foreach ($arrayAccountReceivableAccountant as $value) {
            if ($value['customerAccountant'] != []) {
                foreach ($value['customerAccountant'] as $value1) {
                    $arrayDebtReceivableAccountant[] = [
                        'nameAccountant' => $value['nameAccountant'],
                        'nameCustomer' => $value1['nameCustomer'],
                        'receivableEndPeriod' => $value1['receivableEndPeriod'],
                    ];
                }
            } else {
                $arrayDebtReceivableAccountant[] = [
                    'nameAccountant' => $value['nameAccountant'],
                    'nameCustomer' => [],
                    'receivableEndPeriod' => "0",
                ];
            }
        }
        if (request()->ajax()) {
            return datatables()
                ->of($arrayDebtReceivableAccountant)
                ->editColumn('receivableEndPeriod', function ($value) {
                    return (int) $value['receivableEndPeriod'];
                })
                ->make(true);
        }
    }

    public function accountReceivableRequest(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        $accountant = $request->accountant;
        //-------------------chi ti???t thu n??? theo n??? ph???i thu-----------------------------------------------------------//
        if($accountant == 'Kh??ng x??c ?????nh'){
            $accountants = $this->customerRepo->getAccountant()->where('accountant_name',null)->get();
        }else if($accountant == 0){
            $accountants = $this->customerRepo->getAccountant()->get();
        }else if($accountant != 0){
            $accountants = $this->customerRepo->getAccountant()->where('accountant_name',$accountant)->get();
        }
        $arrayAccountReceivableAccountant = [];
        foreach ($accountants as $value) {
            if($value->accountant_name != null){
                $arrayAccountReceivableAccountant[] = [
                    'nameAccountant' => $value->accountant_name,
                    'customerAccountant' => 0,
                    'sum' => 0,
                ];
            }else{
                $arrayAccountReceivableAccountant[] = [
                    'nameAccountant' => 'Kh??ng x??c ?????nh',
                    'customerAccountant' => 0,
                    'sum' => 0,
                ];
            }
        }

        foreach ($arrayAccountReceivableAccountant as $key => $value) {
            $arrayAccountReceivableAccountant[$key]['customerAccountant'] = $this->accountReceivableAccountant($value['nameAccountant'],$date1,$date2);
        }

        $arrayDebtReceivableAccountant = [];
        foreach ($arrayAccountReceivableAccountant as $value) {
            if ($value['customerAccountant'] != []) {
                foreach ($value['customerAccountant'] as $value1) {
                    $arrayDebtReceivableAccountant[] = [
                        'nameAccountant' => $value['nameAccountant'],
                        'nameCustomer' => $value1['nameCustomer'],
                        'receivableEndPeriod' => $value1['receivableEndPeriod'],
                    ];
                }
            } else {
                $arrayDebtReceivableAccountant[] = [
                    'nameAccountant' => $value['nameAccountant'],
                    'nameCustomer' => [],
                    'receivableEndPeriod' => "0",
                ];
            }
        }
        if (request()->ajax()) {
            return datatables()
                ->of($arrayDebtReceivableAccountant)
                ->editColumn('receivableEndPeriod', function ($value) {
                    return (int) $value['receivableEndPeriod'];
                })
                ->make(true);
        }
    }
    
    function accountReceivableAccountant($accountant,$date1,$date2)
    {
        if($accountant == 'Kh??ng x??c ?????nh'){
            $customers = $this->customerRepo->orderName()->where('accountant_name',null)->get();
        }else if($accountant == 0){
            $customers = $this->customerRepo->orderName()->get();
        }else if($accountant != 0){
            $customers = $this->customerRepo->orderName()->where('accountant_name', $accountant)->get();
        }
        // //t???o m???ng ID kh??ch h??ng
        foreach ($customers as $value) {
        $arrayCustomer[] = $value->id;
        }
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'customerName' => $value->name,
                'SumAriseIncurred' => 0, // t???ng PS c?? ?????u k???
                'SumDebtIncurred' => 0, // t???ng PS n??? ?????u k???
                'outstandingBalanceBeginning' => 0, //d?? n??? ?????u k??? = Ph???i thu ?????u k???
                'balanceBeginning' => 0, //d?? c?? ?????u k??? = KH tr??? tr?????c ?????u k???
                'DebtIncurred' => 0, //PS n??? trong k??? (Doanh s???)
                'Generate' => 0, //PS c?? trong k???(???? thu)
                'receivableEndTerm' => 0, //ph???i thu cu???i k???
                'CustomerPrepayEndTerm' => 0, //KH tr??? tr?????c cu???i k???
            ];
        }
        //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);

        foreach ($paymentItems1 as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                }
            }
        }
        //l???y t???ng ph??t sinh ?????u k??? theo volume
        $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
        $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->pluck('credit','customer_id')->toArray();
        //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo volumetracking
        foreach ($creditGroupCustomer as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] += $value;
                }
            }
        }
        //l???y t???ng ph??t sinh n??? ?????u k??? c???a KH theo volume
        foreach ($debitGroupCustomer as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumDebtIncurred'] = $value;
                }
            }
        }
        //l???y t???ng ph??t sinh c?? trong k??? c???a KH
        $paymentItems2 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
        foreach ($paymentItems2 as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                }
            }
        }
        //l???y t???ng ph??t sinh trong k??? c???a KH
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->where('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
        $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','customer_id')->toArray();
        //ph??t sinh c?? trong k??? theo volume
        foreach ($creditGroupCustomer1 as $key => $value) {
            foreach ($listCustomers as $key1 => $value1) {
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] += $value;
                }
            }
        }
        //ph??t sinh n??? trong k??? theo volume
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
        //l???y t??n kh??ch h??ng, n??? ph???i thu
        foreach ($listCustomers as $value) {
            if ($value['outstandingBalanceBeginning'] + $value['DebtIncurred'] - $value['Generate'] > 0) {
                $nameCustomerReiceivable[] = [
                    'nameCustomer' => $value['customerName'],
                    'receivableEndPeriod' => $value['outstandingBalanceBeginning'] + $value['DebtIncurred'] - $value['Generate'],
                ];
            }
        }
        return $nameCustomerReiceivable;
    }
}
