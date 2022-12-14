<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\PaymentItem\PaymentItemRepositoryInterface;
use App\Models\Concrete\Supplier;
use App\Models\Survey\Employee;
use Carbon\Carbon;
class DebtCollectionController extends Controller
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

    function customerClassify($id,$date1,$date2)
    {
        //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH
        $paymentItems = $this->paymentItemRepo->getPaymentItemClassify($date1,null,$id);
            $SumAriseIncurred = 0;
            foreach ($paymentItems as $key => $value) {
                $SumAriseIncurred = array_sum(array_column($value, 'amount'));
            }
        //l???y t???ng ph??t sinh ?????u k??? theo volume
        $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->whereIn('customer_id',$id)->pluck('sum','customer_id')->toArray();
        $volumeTrackings = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereIn('customer_id',$id)->pluck('credit','customer_id')->toArray();
            //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo volumetracking
            foreach($volumeTrackings as $value){
                $SumAriseIncurred += $value;
            }
            //ph??t sinh n??? ?????u k??? theo volume
            $SumDebtIncurred = 0;
            foreach($debitGroupCustomer as $key => $value){
                $SumDebtIncurred += $value;
            }
        //l???y t???ng ph??t sinh c?? trong k??? c???a KH
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemClassify($date1,$date2,$id);
            $Generate = 0;
            foreach ($paymentItems1 as $key => $value) {
                    $Generate = array_sum(array_column($value, 'amount'));
            }
        //l???y t???ng ph??t sinh trong k??? theo volume
        $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$id)->pluck('sum','customer_id')->toArray();
        $volumeTrackings1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$id)->pluck('credit','customer_id')->toArray();
            //l???y t???ng ph??t sinh c?? trong k??? c???a KH theo volumetracking
            foreach($volumeTrackings1 as $value){
                $Generate += $value;
            }
            //ph??t sinh n??? trong k??? theo volume
            $DebtIncurred = 0;
            foreach($debitGroupCustomer1 as $key => $value){
                $DebtIncurred += $value;
            }
        $debtCreditBeginning = $this->caculatorDebtCredit($SumDebtIncurred, $SumAriseIncurred);
        $outstandingBalanceBeginning = $debtCreditBeginning['outstandingBalanceBeginning'];
        $balanceBeginning = $debtCreditBeginning['balanceBeginning'];
        $receivedCustomerEndTerm = $this->caculatorDetail($outstandingBalanceBeginning, $balanceBeginning, $DebtIncurred, $Generate);
        return $receivedCustomerEndTerm['receivableEndTerm'];
    }

    public function index()
    {
        return view('finances.debt_collection');
    }

    public function debtCollection()
    {
        $date1 = changeDate(Carbon::now()->startOfMonth());
        $date2 = changeDate(Carbon::now());
        //T??NH C??N L???I
        $customers = $this->customerRepo->getCustomer()->get();
        // //t???o m???ng ID kh??ch h??ng
        foreach ($customers as $value) {
            $arrayCustomer[] = $value->id;
        }
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'name' => $value->name,
                'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k???
                'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k???
                'balanceBeginning' => 0, //d?? c?? ?????u k???
                'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                'Generate' => 0,//PS c?? trong k???(???? thu)
                'receivableEndTerm' => 0, //ph???i thu cu???i k???
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
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
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
            $listCustomers[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listCustomers[$key]['DebtIncurred']) - ($debtCreditBeginning['balanceBeginning'] + $listCustomers[$key]['Generate']);
            $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'],$debtCreditBeginning['balanceBeginning'],$listCustomers[$key]['DebtIncurred'],$listCustomers[$key]['Generate']);
                $listCustomers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
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
        $credit_sum = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['credit_sums'];
        $receivable_in_period = [$remain_receive,$credit_sum];
         //t??nh kh??ch h??ng tr??? tr?????c cu???i k??? ??ang ho???t ?????ng---------------------------------------------------
         $customerIsActive = $this->customerRepo->getCustomerByStatus('??ang ho???t ?????ng');
         
         $idIsActive = [];
         foreach ($customerIsActive as $value) {
             $idIsActive[] = $value->id;
         }
         $receivableEndPeriodActive = $this->customerClassify($idIsActive,$date1,$date2);
         //t??nh kh??ch h??ng tr??? tr?????c cu???i k??? kh???i ki???n----------------------------------------------------------
         $customerSue = $this->customerRepo->getCustomerByStatus('Kh???i ki???n');
          $idSue = [];
         foreach ($customerSue as $value) {
              $idSue[] = $value->id;
         }
         $receivableEndPeriodSue = $this->customerClassify($idSue,$date1,$date2);
         //t??nh kh??ch h??ng tr??? tr?????c cu???i k??? kh??ng ph??t sinh----------------------------------------------------------
         $customerUnArise = $this->customerRepo->getCustomerByStatus('Kh??ng ph??t sinh');
         $idUnArise = [];
         foreach ($customerUnArise as $value) {
             $idUnArise[] = $value->id;
         }
         $receivableEndPeriodUnArise = $this->customerClassify($idUnArise,$date1,$date2);
         //t??nh kh??ch h??ng tr??? tr?????c cu???i k??? kh??ng ho???t ?????ng----------------------------------------------------------
         $customerInActive = $this->customerRepo->getCustomerByStatus('Kh??ng ho???t ?????ng');
         $idInActive = [];
         foreach ($customerInActive as $value) {
             $idInActive[] = $value->id;
         }
         $receivableEndPeriodInactive = $this->customerClassify($idInActive,$date1,$date2);
         $array_receivableEndPeriod = [$receivableEndPeriodActive,$receivableEndPeriodSue,$receivableEndPeriodUnArise,$receivableEndPeriodInactive];

         $debtCollectEndPeriod = [];
         foreach ($listCustomers as $value) {
             if ($value['receivableEndTerm'] > 0) {
                 $debtCollectEndPeriod[$value['name']] = $value['receivableEndTerm'];
             }
         }
        arsort($debtCollectEndPeriod);
        $nameCustomer = array_keys($debtCollectEndPeriod);
        $debtCollectEndPeriod = array_values($debtCollectEndPeriod);
        $nameCustomer10 = array_slice($nameCustomer,0,10);
        $debtCollectEndPeriod10 = array_slice($debtCollectEndPeriod,0,10);

         //t??? l??? thu n??? trong k??? theo k??? to??n
         $ariseThereAccountant = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
         $accountant_values = [];
         foreach($ariseThereAccountant as $key => $value){
            foreach($value as $value1){
               if($this->customerRepo->find($key)->accountant_name == null){
                   $accountant_values['Kh??ng x??c ?????nh'] = ( $accountant_values['Kh??ng x??c ?????nh'] ?? 0 ) + $value1['amount'];
               }else{
                   $accountant_values[$this->customerRepo->find($key)->accountant_name]=( $accountant_values[$this->customerRepo->find($key)->accountant_name] ?? 0 ) + $value1['amount'];
               }
            };
        };
        $accountant = array_keys($accountant_values);
        $debtAccountant = array_values($accountant_values);
        //s??? l?????ng kh??ch h??ng theo k??? to??n
        $getAccountants = $this->customerRepo->getAccountant()->get();
        $number_accountant = [];
        $customer_accountant = [];
        foreach ($getAccountants as $value) {
                $customer_accountant[] = ($value->accountant_name != null) ? $value->accountant_name : 'Kh??ng x??c ?????nh';
        }

        foreach ($customer_accountant as $value) {
                $customer_accountants[] = ($value == 'Kh??ng x??c ?????nh') ? $this->customerRepo->getNumberByStatus()->where('accountant_name', null)->get() 
                : $this->customerRepo->getNumberByStatus()->where('accountant_name', $value)->get();
        }

        $customerAccountantActive = [0, 0, 0, 0, 0, 0];
        $customerAccountantSue = [0, 0, 0, 0, 0, 0];
        $customerAccountantUnActive = [0, 0, 0, 0, 0, 0];
        $customerAccountantUnArise = [0, 0, 0, 0, 0, 0];

        foreach ($customer_accountants as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($value1->status_id == '??ang ho???t ?????ng') {
                    $customerAccountantActive[$key] = $value1->numberCustomer;
                }
            }
        }

        foreach ($customer_accountants as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($value1->TinhTrang == 'Kh???i ki???n') {
                    $customerAccountantSue[$key] = $value1->numberCustomer;
                }
            }
        }

        foreach ($customer_accountants as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($value1->TinhTrang == 'Kh??ng ho???t ?????ng') {
                    $customerAccountantUnActive[$key] = $value1->numberCustomer;
                }
            }
        }

        foreach ($customer_accountants as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($value1->TinhTrang == 'Kh??ng ph??t sinh') {
                    $customerAccountantUnArise[$key] = $value1->numberCustomer;
                }
            }
        }
        ////////////bieu do no phai thu con lai theo thunoketoan
        $arrayRemainAccountant = [];
        $ratioDebtReceivable = [];
        foreach ($customer_accountant as $value_accountant) {
             //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo k??? to??n
         $paymentItems = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
         $SumAriseIncurred = 0;
          foreach ($paymentItems as $key => $value) {
            foreach($value as $value1){
                if($this->customerRepo->find($key)->accountant_name == null){
                    $SumAriseIncurred += $value1['amount'];
                }else if($this->customerRepo->find($key)->accountant_name == $value_accountant){
                    $SumAriseIncurred += $value1['amount'];
                }
            }
          }
          //l???y t???ng ph??t sinh ?????u k??? theo volume
          $debitGroupCustomer = ($value_accountant == 'Kh??ng x??c ?????nh') ? $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereHas('customer',function($query){
            $query->where('accountant_name',null);
        })->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray() : $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereHas('customer',function($query) use ($value_accountant){
            $query->where('accountant_name',$value_accountant);
          })->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
          $volumeTrackings = ($value_accountant == 'Kh??ng x??c ?????nh') ? $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereHas('customer',function($query){
            $query->where('accountant_name',null);
          })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray()
          : $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereHas('customer',function($query) use ($value_accountant){
            $query->where('accountant_name',$value_accountant);
          })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray();
          //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo volumetracking
             foreach($volumeTrackings as $value){
                 $SumAriseIncurred += $value;
             }
         //ph??t sinh n??? ?????u k??? theo volume
         $SumDebtIncurred = 0;
         foreach($debitGroupCustomer as $key => $value){
             $SumDebtIncurred += $value;
         }
        //l???y t???ng ph??t sinh c?? trong k??? c???a KH
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
           $Generate = 0;
           foreach ($paymentItems1 as $key => $value) {
            foreach($value as $value1){
                if($this->customerRepo->find($key)->accountant_name == null){
                    $Generate += $value1['amount'];
                }else if($this->customerRepo->find($key)->accountant_name == $value_accountant){
                    $Generate += $value1['amount'];
                }
            }
          }
            //l???y t???ng ph??t sinh trong k??? theo volume
            $debitGroupCustomer1 = ($value_accountant == 'Kh??ng x??c ?????nh') ? $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query){
                $query->where('accountant_name',null);
              })->pluck('sum','customer_id')->toArray()
              : $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query) use ($value_accountant){
                $query->where('accountant_name',$value_accountant);
              })->pluck('sum','customer_id')->toArray();
            $volumeTrackings1 = ($value_accountant == 'Kh??ng x??c ?????nh') ? $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query){
                $query->where('accountant_name',null);
              })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray()
            : $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query) use ($value_accountant){
                $query->where('accountant_name',$value_accountant);
              })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray();
            //l???y t???ng ph??t sinh c?? trong k??? c???a KH theo volumetracking
               foreach($volumeTrackings1 as $value){
                   $Generate += $value;
               }
           //ph??t sinh n??? trong k??? theo volume
           $DebtIncurred = 0;
           foreach($debitGroupCustomer1 as $key => $value){
               $DebtIncurred += $value;
           }
         $debtCreditBeginning = $this->caculatorDebtCredit($SumDebtIncurred, $SumAriseIncurred);
         $outstandingBalanceBeginning = $debtCreditBeginning['outstandingBalanceBeginning'];
         $balanceBeginning = $debtCreditBeginning['balanceBeginning'];
         $receivedCustomerEndTerm = $this->caculatorDetail($outstandingBalanceBeginning, $balanceBeginning, $DebtIncurred, $Generate);
            //l???y gi?? tr??? c??n l???i ??? b???ng t???ng quan
            $remain = $receivedCustomerEndTerm['receivableEndTerm'] - $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
            $arrayRemainAccountant[] = [
                'accountant' => $value_accountant,
                'remain' => $remain
            ];
            
            $debtCollected = 0;
            $totalDebtRatio = 0;
            if ($Generate != null) {
                $debtCollected = $Generate;
            } elseif ($Generate == null) {
                $debtCollected = 0;
            }

            $totalDebtRatio = $outstandingBalanceBeginning + $DebtIncurred;
            if ($totalDebtRatio > 0) {
                $ratioDebtReceivable[] = [
                    'accountant' => $value_accountant,
                    'ratio' => ($debtCollected * 100) / $totalDebtRatio,
                ];
            }
        }
        $arrayAccountant1 = array_column($arrayRemainAccountant, 'remain');
        array_multisort($arrayAccountant1, SORT_DESC, $arrayRemainAccountant);
        $nameAccountant = [];
        $remain = [];
        foreach ($arrayRemainAccountant as $value) {
            if ($value['remain'] > 0) {
                $nameAccountant[] = $value['accountant'];
                $remain[] = $value['remain'];
            } elseif ($value['remain'] <= 0) {
                $nameAccountant[] = $value['accountant'];
                $remain[] = 0;
            }
        }
        $arrayRatioDebtCollect = array_column($ratioDebtReceivable, 'ratio');
        array_multisort($arrayRatioDebtCollect, SORT_DESC, $ratioDebtReceivable);
        $nameAccountantRatio = [];
        $ratioDebtCollects = [];
        foreach ($ratioDebtReceivable as $value) {
            $nameAccountantRatio[] = $value['accountant'];
            $ratioDebtCollects[] = round($value['ratio'], 2);
        }
            return response()->json(['receivable_in_period' => $receivable_in_period,'array_receivableEndPeriod' => $array_receivableEndPeriod,
            'nameCustomer10' => $nameCustomer10,'debtCollectEndPeriod10' => $debtCollectEndPeriod10,'accountant' => $accountant,
            'debtAccountant' => $debtAccountant,'customerAccountantActive' => $customerAccountantActive,'customerAccountantSue' => $customerAccountantSue,
            'customerAccountantUnActive' => $customerAccountantUnActive, 'customerAccountantUnArise' => $customerAccountantUnArise,
            'customer_accountant' => $customer_accountant,'nameAccountantRatio' => $nameAccountantRatio,
            'ratioDebtCollects' => $ratioDebtCollects,'nameAccountant' => $nameAccountant, 'remain' => $remain]);
    }

    public function ReceivableInPeriod(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        //T??NH C??N L???I
        $customers = $this->customerRepo->getCustomer()->get();
        // //t???o m???ng ID kh??ch h??ng
        foreach ($customers as $value) {
            $arrayCustomer[] = $value->id;
        }
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'name' => $value->name,
                'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k???
                'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k???
                'balanceBeginning' => 0, //d?? c?? ?????u k???
                'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                'Generate' => 0,//PS c?? trong k???(???? thu)
                'receivableEndTerm' => 0, //ph???i thu cu???i k???
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
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
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
            $listCustomers[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listCustomers[$key]['DebtIncurred']) - ($debtCreditBeginning['balanceBeginning'] + $listCustomers[$key]['Generate']);
            $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'],$debtCreditBeginning['balanceBeginning'],$listCustomers[$key]['DebtIncurred'],$listCustomers[$key]['Generate']);
                $listCustomers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
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
        $credit_sum = getDateSumCreditDebitByCustomerCompany($date1, $date2, 0, 0)['credit_sums'];
        $receivable_in_period = [$remain_receive,$credit_sum];
        return response()->json(['data' => $receivable_in_period]);
    }

    public function debtCollectionEndPeriod(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        //t??nh kh??ch h??ng tr??? tr?????c cu???i k??? ??ang ho???t ?????ng---------------------------------------------------
        $customerIsActive = $this->customerRepo->getCustomerByStatus('??ang ho???t ?????ng');
        $idIsActive = [];
        foreach ($customerIsActive as $value) {
            $idIsActive[] = $value->id;
        }
        $receivableEndPeriodActive = $this->customerClassify($idIsActive,$date1,$date2);
        //t??nh kh??ch h??ng tr??? tr?????c cu???i k??? kh???i ki???n----------------------------------------------------------
        $customerSue = $this->customerRepo->getCustomerByStatus('Kh???i ki???n');
         $idSue = [];
        foreach ($customerSue as $value) {
             $idSue[] = $value->id;
        }
        $receivableEndPeriodSue = $this->customerClassify($idSue,$date1,$date2);
        //t??nh kh??ch h??ng tr??? tr?????c cu???i k??? kh??ng ph??t sinh----------------------------------------------------------
        $customerUnArise = $this->customerRepo->getCustomerByStatus('Kh??ng ph??t sinh');
        $idUnArise = [];
        foreach ($customerUnArise as $value) {
            $idUnArise[] = $value->id;
        }
        $receivableEndPeriodUnArise = $this->customerClassify($idUnArise,$date1,$date2);
        //t??nh kh??ch h??ng tr??? tr?????c cu???i k??? kh??ng ho???t ?????ng----------------------------------------------------------
        $customerInActive = $this->customerRepo->getCustomerByStatus('Kh??ng ho???t ?????ng');
        $idInActive = [];
        foreach ($customerInActive as $value) {
            $idInActive[] = $value->id;
        }
        $receivableEndPeriodInactive = $this->customerClassify($idInActive,$date1,$date2);
        return response()->json(['data' => [$receivableEndPeriodActive,$receivableEndPeriodSue,$receivableEndPeriodUnArise,$receivableEndPeriodInactive]]);
    }

    public function debtEndPeriodCustomer(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        //T??NH C??N L???I
        $customers = $this->customerRepo->getCustomer()->get();
        // //t???o m???ng ID kh??ch h??ng
        foreach ($customers as $value) {
            $arrayCustomer[] = $value->id;
        }
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'name' => $value->name,
                'SumAriseIncurred' => 0,// t???ng PS c?? ?????u k???
                'SumDebtIncurred' => 0,// t???ng PS n??? ?????u k???
                'outstandingBalanceBeginning' => 0,//d?? n??? ?????u k???
                'balanceBeginning' => 0, //d?? c?? ?????u k???
                'DebtIncurred' => 0,//PS n??? trong k??? (Doanh s???)
                'Generate' => 0,//PS c?? trong k???(???? thu)
                'receivableEndTerm' => 0, //ph???i thu cu???i k???
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
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
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
        $debtCollectEndPeriod = [];
        foreach ($listCustomers as $value) {
            if ($value['receivableEndTerm'] > 0) {
                $debtCollectEndPeriod[] = [
                    'name' => $value['name'],
                    'receivableEndTerm' => $value['receivableEndTerm'],
                ];
            }
        }
        foreach ($listCustomers as $key => $value) {
            if ($value['ID'] == $arrayCustomer[$key]) {
                $debtCreditBeginning = $this->caculatorDebtCredit($value['SumDebtIncurred'],$value['SumAriseIncurred']);
                $listCustomers[$key]['outstandingBalanceBeginning'] = $debtCreditBeginning['outstandingBalanceBeginning'];
                $listCustomers[$key]['balanceBeginning'] = $debtCreditBeginning['balanceBeginning'];
                $listCustomers[$key]['remain_receive'] = ($debtCreditBeginning['outstandingBalanceBeginning'] + $listCustomers[$key]['DebtIncurred']) - ($debtCreditBeginning['balanceBeginning'] + $listCustomers[$key]['Generate']);
                $receivedCustomerEndTerm = $this->caculatorDetail($debtCreditBeginning['outstandingBalanceBeginning'],$debtCreditBeginning['balanceBeginning'],$listCustomers[$key]['DebtIncurred'],$listCustomers[$key]['Generate']);
                    $listCustomers[$key]['receivableEndTerm'] = $receivedCustomerEndTerm['receivableEndTerm'];
                }
            }
            $debtCollectEndPeriod = [];
            foreach ($listCustomers as $value) {
                if ($value['receivableEndTerm'] > 0) {
                    $debtCollectEndPeriod[$value['name']] = $value['receivableEndTerm'];
                }
            }
            arsort($debtCollectEndPeriod);
            $nameCustomer = array_keys($debtCollectEndPeriod);
            $debtCollectEndPeriod = array_values($debtCollectEndPeriod);
            $nameCustomer10 = array_slice($nameCustomer,0,10);
            $debtCollectEndPeriod10 = array_slice($debtCollectEndPeriod,0,10);
            $nameCustomer20 = array_slice($nameCustomer,0,20);
            $debtCollectEndPeriod20 = array_slice($debtCollectEndPeriod,0,20);
            $nameCustomer30 = array_slice($nameCustomer,0,30);
            $debtCollectEndPeriod30 = array_slice($debtCollectEndPeriod,0,30);
            $nameCustomer40 = array_slice($nameCustomer,0,40);
            $debtCollectEndPeriod40 = array_slice($debtCollectEndPeriod,0,40);
        return response()->json(['data' => [
                                            $nameCustomer10,$debtCollectEndPeriod10,
                                            $nameCustomer20,$debtCollectEndPeriod20,
                                            $nameCustomer30,$debtCollectEndPeriod30,
                                            $nameCustomer40,$debtCollectEndPeriod40
                                            ]]);
    }
    public function ratioDebtCollectAccountant(Request $request)
    {
        $accountants = $request->accountant;
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
         //t??? l??? thu n??? trong k??? theo k??? to??n
         $ariseThereAccountant = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
         $accountant_values = [];
         foreach($ariseThereAccountant as $key => $value){
             foreach($value as $value1){
                if($this->customerRepo->find($key)->accountant_name == null){
                    $accountant_values['Kh??ng x??c ?????nh'] = ( $accountant_values['Kh??ng x??c ?????nh'] ?? 0 ) + $value1['amount'];
                }else{
                    $accountant_values[$this->customerRepo->find($key)->accountant_name]=( $accountant_values[$this->customerRepo->find($key)->accountant_name] ?? 0 ) + $value1['amount'];
                }
             };
         };
         arsort($accountant_values);
        if($accountants == 'Kh??ng x??c ?????nh'){
            $arr_accountant = ['Kh??ng x??c ?????nh'];
            $arr_debtAccountant = [$accountant_values[$accountants]];
        }else if($accountants != 0){
            $arr_accountant = [$accountants];
            $arr_debtAccountant = [$accountant_values[$accountants]];
        }else if($accountants == 0){
            $arr_accountant = array_keys($accountant_values);
            $arr_debtAccountant = array_values($accountant_values);
        }
        return response()->json(['data' => [$arr_accountant,$arr_debtAccountant]]);
    }

    public function numberCustomer(Request $request)
    {
        $accountants = $request->accountant;
        //s??? l?????ng kh??ch h??ng theo k??? to??n
        if($accountants == 0){
            $getAccountants = $this->customerRepo->getAccountant()->get();
        }else if($accountants == 'Kh??ng x??c ?????nh'){
            $getAccountants = $this->customerRepo->getAccountant()->where('accountant_name',null)->get();
        }else if($accountants != 0){
            $getAccountants = $this->customerRepo->getAccountant()->where('accountant_name',$accountants)->get();
        }
         $number_accountant = [];
         $customer_accountant = [];
         foreach ($getAccountants as $value) {
                 $customer_accountant[] = ($value->accountant_name != null) ? $value->accountant_name : 'Kh??ng x??c ?????nh';
         }
 
         foreach ($customer_accountant as $value) {
                 $customer_accountants[] = ($value == 'Kh??ng x??c ?????nh') ? $this->customerRepo->getNumberByStatus()->where('accountant_name', null)->get()
                 : $this->customerRepo->getNumberByStatus()->where('accountant_name', $value)->get();
         }
 
         $customerAccountantActive = [0, 0, 0, 0, 0, 0];
         $customerAccountantSue = [0, 0, 0, 0, 0, 0];
         $customerAccountantUnActive = [0, 0, 0, 0, 0, 0];
         $customerAccountantUnArise = [0, 0, 0, 0, 0, 0];
 
         foreach ($customer_accountants as $key => $value) {
             foreach ($value as $key1 => $value1) {
                 if ($value1->status_id == '??ang ho???t ?????ng') {
                     $customerAccountantActive[$key] = $value1->numberCustomer;
                 }
             }
         }
 
         foreach ($customer_accountants as $key => $value) {
             foreach ($value as $key1 => $value1) {
                 if ($value1->TinhTrang == 'Kh???i ki???n') {
                     $customerAccountantSue[$key] = $value1->numberCustomer;
                 }
             }
         }
 
         foreach ($customer_accountants as $key => $value) {
             foreach ($value as $key1 => $value1) {
                 if ($value1->TinhTrang == 'Kh??ng ho???t ?????ng') {
                     $customerAccountantUnActive[$key] = $value1->numberCustomer;
                 }
             }
         }
 
         foreach ($customer_accountants as $key => $value) {
             foreach ($value as $key1 => $value1) {
                 if ($value1->TinhTrang == 'Kh??ng ph??t sinh') {
                     $customerAccountantUnArise[$key] = $value1->numberCustomer;
                 }
             }
         }
         return response()->json(['data' =>[$customer_accountant,$customerAccountantActive,$customerAccountantSue,$customerAccountantUnActive,$customerAccountantUnArise]]);
    }

    public function ratioDebtPeriodAccountant(Request $request)
    {
        $date1 = changeDate($request->date1);
        $date2 = changeDate($request->date2);
        $accountants = $request->accountant;
            $getAccountants = $this->customerRepo->getAccountant()->get();
        $customer_accountant = [];
        foreach ($getAccountants as $value) {
            $customer_accountant[] = ($value->accountant_name != null) ? $value->accountant_name : 'Kh??ng x??c ?????nh';
        }
        ////////////bieu do no phai thu con lai theo thunoketoan
        $arrayRemainAccountant = [];
        $ratioDebtReceivable = [];
        foreach ($customer_accountant as $value_accountant) {
             //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo k??? to??n
         $paymentItems = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
         $SumAriseIncurred = 0;
          foreach ($paymentItems as $key => $value) {
            foreach($value as $value1){
                if($this->customerRepo->find($key)->accountant_name == null){
                    $SumAriseIncurred += $value1['amount'];
                }else if($this->customerRepo->find($key)->accountant_name == $value_accountant){
                    $SumAriseIncurred += $value1['amount'];
                }
            }
          }
          //l???y t???ng ph??t sinh ?????u k??? theo volume
          $debitGroupCustomer = ($value_accountant == 'Kh??ng x??c ?????nh') ? $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereHas('customer',function($query){
            $query->where('accountant_name',null);
          })->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray()
          : $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereHas('customer',function($query) use ($value_accountant){
            $query->where('accountant_name',$value_accountant);
          })->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
          $volumeTrackings = ($value_accountant == 'Kh??ng x??c ?????nh') ? $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereHas('customer',function($query){
            $query->where('accountant_name',null);
          })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray()
          : $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereHas('customer',function($query) use ($value_accountant){
            $query->where('accountant_name',$value_accountant);
          })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray();
          //l???y t???ng ph??t sinh c?? ?????u k??? c???a KH theo volumetracking
             foreach($volumeTrackings as $value){
                 $SumAriseIncurred += $value;
             }
         //ph??t sinh n??? ?????u k??? theo volume
         $SumDebtIncurred = 0;
         foreach($debitGroupCustomer as $key => $value){
             $SumDebtIncurred += $value;
         }
        //l???y t???ng ph??t sinh c?? trong k??? c???a KH
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
           $Generate = 0;
           foreach ($paymentItems1 as $key => $value) {
            foreach($value as $value1){
                if($this->customerRepo->find($key)->accountant_name == null){
                    $Generate += $value1['amount'];
                }else if($this->customerRepo->find($key)->accountant_name == $value_accountant){
                    $Generate += $value1['amount'];
                }
            }
          }
            //l???y t???ng ph??t sinh trong k??? theo volume
            $debitGroupCustomer1 = ($value_accountant == 'Kh??ng x??c ?????nh') ? $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query){
                $query->where('accountant_name',null);
              })->pluck('sum','customer_id')->toArray()
              : $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query) use ($value_accountant){
                $query->where('accountant_name',$value_accountant);
              })->pluck('sum','customer_id')->toArray();
            $volumeTrackings1 = ($value_accountant == 'Kh??ng x??c ?????nh') ? $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query){
                $query->where('accountant_name',null);
              })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray()
            : $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query) use ($value_accountant){
                $query->where('accountant_name',$value_accountant);
              })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray();
            //l???y t???ng ph??t sinh c?? trong k??? c???a KH theo volumetracking
               foreach($volumeTrackings1 as $value){
                   $Generate += $value;
               }
           //ph??t sinh n??? trong k??? theo volume
           $DebtIncurred = 0;
           foreach($debitGroupCustomer1 as $key => $value){
               $DebtIncurred += $value;
           }
         $debtCreditBeginning = $this->caculatorDebtCredit($SumDebtIncurred, $SumAriseIncurred);
         $outstandingBalanceBeginning = $debtCreditBeginning['outstandingBalanceBeginning'];
         $balanceBeginning = $debtCreditBeginning['balanceBeginning'];
         $receivedCustomerEndTerm = $this->caculatorDetail($outstandingBalanceBeginning, $balanceBeginning, $DebtIncurred, $Generate);
            //l???y gi?? tr??? c??n l???i ??? b???ng t???ng quan
            $remain = $receivedCustomerEndTerm['receivableEndTerm'] - $receivedCustomerEndTerm['CustomerPrepayEndTerm'];
            $arrayRemainAccountant[$value_accountant] = $remain;
            $debtCollected = 0;
            $totalDebtRatio = 0;
            if ($Generate != null) {
                $debtCollected = $Generate;
            } elseif ($Generate == null) {
                $debtCollected = 0;
            }

            $totalDebtRatio = $outstandingBalanceBeginning + $DebtIncurred;
            if ($totalDebtRatio > 0) {
                $ratioDebtReceivable[$value_accountant] = round(($debtCollected * 100) / $totalDebtRatio,2);
            }
        }
        arsort($arrayRemainAccountant);
        if($accountants == 0){
            $nameAccountant = array_keys($arrayRemainAccountant);
            $remain = array_values($arrayRemainAccountant);
        }else if($accountants == 'Kh??ng x??c ?????nh'){
            $nameAccountant = ['Kh??ng x??c ?????nh'];
            $remain = [$arrayRemainAccountant['Kh??ng x??c ?????nh']];
        }else if($accountants != 'Kh??ng x??c ?????nh' && $accountants != 0){
            $nameAccountant = [$accountants];
            $remain = [$arrayRemainAccountant[$accountants]];
        }
        arsort($ratioDebtReceivable);
        if($accountants == 0){
            $nameAccountantRatio = array_keys($ratioDebtReceivable);
            $ratioDebtCollects = array_values($ratioDebtReceivable);
        }else if($accountants == 'Kh??ng x??c ?????nh'){
            $nameAccountantRatio = ['Kh??ng x??c ?????nh'];
            $ratioDebtCollects = [$ratioDebtReceivable['Kh??ng x??c ?????nh']];
        }else if($accountants != 'Kh??ng x??c ?????nh' && $accountants != 0){
            $nameAccountantRatio = [$accountants];
            $ratioDebtCollects = [$ratioDebtReceivable[$accountants]];
        }
        return response()->json(['data' => [$nameAccountantRatio,$ratioDebtCollects,$nameAccountant,$remain]]);
    }
}
