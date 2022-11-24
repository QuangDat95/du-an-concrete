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
        //lấy tổng phát sinh có đầu kỳ của KH
        $paymentItems = $this->paymentItemRepo->getPaymentItemClassify($date1,null,$id);
            $SumAriseIncurred = 0;
            foreach ($paymentItems as $key => $value) {
                $SumAriseIncurred = array_sum(array_column($value, 'amount'));
            }
        //lấy tổng phát sinh đầu kỳ theo volume
        $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->whereIn('customer_id',$id)->pluck('sum','customer_id')->toArray();
        $volumeTrackings = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereIn('customer_id',$id)->pluck('credit','customer_id')->toArray();
            //lấy tổng phát sinh có đầu kỳ của KH theo volumetracking
            foreach($volumeTrackings as $value){
                $SumAriseIncurred += $value;
            }
            //phát sinh nợ đầu kỳ theo volume
            $SumDebtIncurred = 0;
            foreach($debitGroupCustomer as $key => $value){
                $SumDebtIncurred += $value;
            }
        //lấy tổng phát sinh có trong kỳ của KH
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemClassify($date1,$date2,$id);
            $Generate = 0;
            foreach ($paymentItems1 as $key => $value) {
                    $Generate = array_sum(array_column($value, 'amount'));
            }
        //lấy tổng phát sinh trong kỳ theo volume
        $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$id)->pluck('sum','customer_id')->toArray();
        $volumeTrackings1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereIn('customer_id',$id)->pluck('credit','customer_id')->toArray();
            //lấy tổng phát sinh có trong kỳ của KH theo volumetracking
            foreach($volumeTrackings1 as $value){
                $Generate += $value;
            }
            //phát sinh nợ trong kỳ theo volume
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
        //TÍNH CÒN LẠI
        $customers = $this->customerRepo->getCustomer()->get();
        // //tạo mảng ID khách hàng
        foreach ($customers as $value) {
            $arrayCustomer[] = $value->id;
        }
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'name' => $value->name,
                'SumAriseIncurred' => 0,// tổng PS có đầu kỳ
                'SumDebtIncurred' => 0,// tổng PS nợ đầu kỳ
                'outstandingBalanceBeginning' => 0,//dư nợ đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ
                'DebtIncurred' => 0,//PS nợ trong kỳ (Doanh số)
                'Generate' => 0,//PS có trong kỳ(Đã thu)
                'receivableEndTerm' => 0, //phải thu cuối kỳ
                'remain_receive' => 0
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của KH theo paymentitem
        $paymentItems = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
        foreach($paymentItems as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
        //lấy tổng phát sinh đầu kỳ theo volume
        $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->pluck('credit','customer_id')->toArray();
        //lấy tổng phát sinh có đầu kỳ của KH theo volumetracking
        foreach($creditGroupCustomer as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] += $value;
                    }
                }
        }
        //lấy tổng phát sinh nợ đầu kỳ của KH theo volume
        $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
        foreach($debitGroupCustomer as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumDebtIncurred'] = $value;
                    }
                }
        }
        //lấy tổng phát sinh có trong kỳ của KH theo paymentitem
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
        foreach($paymentItems1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
        //lấy tổng phát sinh trong kỳ của KH
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
           //phát sinh có trong kỳ theo volume
        foreach($creditGroupCustomer1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] += $value;
                    }
                }
            }
        //phát sinh nợ trong kỳ theo volume
        $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','customer_id')->toArray();
        foreach($debitGroupCustomer1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['DebtIncurred'] = $value;
                    }
                }
            }
        //tính theo nhân viên------------------------------------------------------------------
        $employees = Employee::select('id','hovaten')->orderby('id','asc')->get();

        foreach($employees as $value){
            $arrayEmployee[] = $value->id;
        }
        foreach ($employees as $key => $value) {
            $listEmployees[] = [
                'ID' => $value->id,
                'SumAriseIncurred' => 0,// tổng PS có đầu kỳ
                'SumDebtIncurred' => 0,// tổng PS nợ đầu kỳ
                'outstandingBalanceBeginning' => 0,//dư nợ đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ
                'DebtIncurred' => 0,//PS nợ trong kỳ (Doanh số)
                'Generate' => 0,//PS có trong kỳ(Đã thu)
                'remain_receive' => 0
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của nhân viên
        $paymentItems2 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,null,null);
        foreach($paymentItems2 as $key => $value){
            foreach($listEmployees as $key1 => $value1){
                if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                    $listEmployees[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
        }
        //lấy tổng phát sinh có trong kỳ của nhân viên
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
        //tính theo nhà cung cấp--------------------------------------------------------------
        $suppliers = Supplier::select('id','name')->orderby('id','asc')->get();

        foreach($suppliers as $value){
            $arraySupplier[] = $value->id;
        }
        foreach ($suppliers as $key => $value) {
            $listSuppliers[] = [
                'ID' => $value->id,
                'SumAriseIncurred' => 0,// tổng PS có đầu kỳ
                'SumDebtIncurred' => 0,// tổng PS nợ đầu kỳ == 0
                'outstandingBalanceBeginning' => 0,//dư nợ đầu kỳ = Phải thu đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ = KH trả trước đầu kỳ
                'DebtIncurred' => 0,//PS nợ trong kỳ (Doanh số)
                'Generate' => 0,//PS có trong kỳ(Đã thu)
                'remain_receive' => 0
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của nhà cung cấp
        $paymentItems4 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,null,null);
        foreach($paymentItems4 as $key => $value){
            foreach($listSuppliers as $key1 => $value1){
                if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                    $listSuppliers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
        }
        //lấy tổng phát sinh có trong kỳ của nhà cung cấp
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
         //tính khách hàng trả trước cuối kỳ đang hoạt động---------------------------------------------------
         $customerIsActive = $this->customerRepo->getCustomerByStatus('Đang hoạt động');
         
         $idIsActive = [];
         foreach ($customerIsActive as $value) {
             $idIsActive[] = $value->id;
         }
         $receivableEndPeriodActive = $this->customerClassify($idIsActive,$date1,$date2);
         //tính khách hàng trả trước cuối kỳ khởi kiện----------------------------------------------------------
         $customerSue = $this->customerRepo->getCustomerByStatus('Khởi kiện');
          $idSue = [];
         foreach ($customerSue as $value) {
              $idSue[] = $value->id;
         }
         $receivableEndPeriodSue = $this->customerClassify($idSue,$date1,$date2);
         //tính khách hàng trả trước cuối kỳ không phát sinh----------------------------------------------------------
         $customerUnArise = $this->customerRepo->getCustomerByStatus('Không phát sinh');
         $idUnArise = [];
         foreach ($customerUnArise as $value) {
             $idUnArise[] = $value->id;
         }
         $receivableEndPeriodUnArise = $this->customerClassify($idUnArise,$date1,$date2);
         //tính khách hàng trả trước cuối kỳ không hoạt động----------------------------------------------------------
         $customerInActive = $this->customerRepo->getCustomerByStatus('Không hoạt động');
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

         //tỷ lệ thu nợ trong kỳ theo kế toán
         $ariseThereAccountant = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
         $accountant_values = [];
         foreach($ariseThereAccountant as $key => $value){
            foreach($value as $value1){
               if($this->customerRepo->find($key)->accountant_name == null){
                   $accountant_values['Không xác định'] = ( $accountant_values['Không xác định'] ?? 0 ) + $value1['amount'];
               }else{
                   $accountant_values[$this->customerRepo->find($key)->accountant_name]=( $accountant_values[$this->customerRepo->find($key)->accountant_name] ?? 0 ) + $value1['amount'];
               }
            };
        };
        $accountant = array_keys($accountant_values);
        $debtAccountant = array_values($accountant_values);
        //số lượng khách hàng theo kế toán
        $getAccountants = $this->customerRepo->getAccountant()->get();
        $number_accountant = [];
        $customer_accountant = [];
        foreach ($getAccountants as $value) {
                $customer_accountant[] = ($value->accountant_name != null) ? $value->accountant_name : 'Không xác định';
        }

        foreach ($customer_accountant as $value) {
                $customer_accountants[] = ($value == 'Không xác định') ? $this->customerRepo->getNumberByStatus()->where('accountant_name', null)->get() 
                : $this->customerRepo->getNumberByStatus()->where('accountant_name', $value)->get();
        }

        $customerAccountantActive = [0, 0, 0, 0, 0, 0];
        $customerAccountantSue = [0, 0, 0, 0, 0, 0];
        $customerAccountantUnActive = [0, 0, 0, 0, 0, 0];
        $customerAccountantUnArise = [0, 0, 0, 0, 0, 0];

        foreach ($customer_accountants as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($value1->status_id == 'Đang hoạt động') {
                    $customerAccountantActive[$key] = $value1->numberCustomer;
                }
            }
        }

        foreach ($customer_accountants as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($value1->TinhTrang == 'Khởi kiện') {
                    $customerAccountantSue[$key] = $value1->numberCustomer;
                }
            }
        }

        foreach ($customer_accountants as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($value1->TinhTrang == 'Không hoạt động') {
                    $customerAccountantUnActive[$key] = $value1->numberCustomer;
                }
            }
        }

        foreach ($customer_accountants as $key => $value) {
            foreach ($value as $key1 => $value1) {
                if ($value1->TinhTrang == 'Không phát sinh') {
                    $customerAccountantUnArise[$key] = $value1->numberCustomer;
                }
            }
        }
        ////////////bieu do no phai thu con lai theo thunoketoan
        $arrayRemainAccountant = [];
        $ratioDebtReceivable = [];
        foreach ($customer_accountant as $value_accountant) {
             //lấy tổng phát sinh có đầu kỳ của KH theo kế toán
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
          //lấy tổng phát sinh đầu kỳ theo volume
          $debitGroupCustomer = ($value_accountant == 'Không xác định') ? $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereHas('customer',function($query){
            $query->where('accountant_name',null);
        })->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray() : $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereHas('customer',function($query) use ($value_accountant){
            $query->where('accountant_name',$value_accountant);
          })->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
          $volumeTrackings = ($value_accountant == 'Không xác định') ? $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereHas('customer',function($query){
            $query->where('accountant_name',null);
          })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray()
          : $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereHas('customer',function($query) use ($value_accountant){
            $query->where('accountant_name',$value_accountant);
          })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray();
          //lấy tổng phát sinh có đầu kỳ của KH theo volumetracking
             foreach($volumeTrackings as $value){
                 $SumAriseIncurred += $value;
             }
         //phát sinh nợ đầu kỳ theo volume
         $SumDebtIncurred = 0;
         foreach($debitGroupCustomer as $key => $value){
             $SumDebtIncurred += $value;
         }
        //lấy tổng phát sinh có trong kỳ của KH
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
            //lấy tổng phát sinh trong kỳ theo volume
            $debitGroupCustomer1 = ($value_accountant == 'Không xác định') ? $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query){
                $query->where('accountant_name',null);
              })->pluck('sum','customer_id')->toArray()
              : $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query) use ($value_accountant){
                $query->where('accountant_name',$value_accountant);
              })->pluck('sum','customer_id')->toArray();
            $volumeTrackings1 = ($value_accountant == 'Không xác định') ? $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query){
                $query->where('accountant_name',null);
              })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray()
            : $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query) use ($value_accountant){
                $query->where('accountant_name',$value_accountant);
              })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray();
            //lấy tổng phát sinh có trong kỳ của KH theo volumetracking
               foreach($volumeTrackings1 as $value){
                   $Generate += $value;
               }
           //phát sinh nợ trong kỳ theo volume
           $DebtIncurred = 0;
           foreach($debitGroupCustomer1 as $key => $value){
               $DebtIncurred += $value;
           }
         $debtCreditBeginning = $this->caculatorDebtCredit($SumDebtIncurred, $SumAriseIncurred);
         $outstandingBalanceBeginning = $debtCreditBeginning['outstandingBalanceBeginning'];
         $balanceBeginning = $debtCreditBeginning['balanceBeginning'];
         $receivedCustomerEndTerm = $this->caculatorDetail($outstandingBalanceBeginning, $balanceBeginning, $DebtIncurred, $Generate);
            //lấy giá trị còn lại ở bảng tổng quan
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
        //TÍNH CÒN LẠI
        $customers = $this->customerRepo->getCustomer()->get();
        // //tạo mảng ID khách hàng
        foreach ($customers as $value) {
            $arrayCustomer[] = $value->id;
        }
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'name' => $value->name,
                'SumAriseIncurred' => 0,// tổng PS có đầu kỳ
                'SumDebtIncurred' => 0,// tổng PS nợ đầu kỳ
                'outstandingBalanceBeginning' => 0,//dư nợ đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ
                'DebtIncurred' => 0,//PS nợ trong kỳ (Doanh số)
                'Generate' => 0,//PS có trong kỳ(Đã thu)
                'receivableEndTerm' => 0, //phải thu cuối kỳ
                'remain_receive' => 0
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của KH theo paymentitem
        $paymentItems = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
        foreach($paymentItems as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
        //lấy tổng phát sinh đầu kỳ theo volume
        $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->pluck('credit','customer_id')->toArray();

        //lấy tổng phát sinh có đầu kỳ của KH theo volumetracking
        foreach($creditGroupCustomer as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] += $value;
                    }
                }
        }
        //lấy tổng phát sinh nợ đầu kỳ của KH theo volume
        $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
        foreach($debitGroupCustomer as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumDebtIncurred'] = $value;
                    }
                }
        }
        //lấy tổng phát sinh có trong kỳ của KH theo paymentitem
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
        foreach($paymentItems1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
        //lấy tổng phát sinh trong kỳ của KH
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
           //phát sinh có trong kỳ theo volume
        foreach($creditGroupCustomer1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] += $value;
                    }
                }
            }
        //phát sinh nợ trong kỳ theo volume
        $debitGroupCustomer1 = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->pluck('sum','customer_id')->toArray();
        foreach($debitGroupCustomer1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['DebtIncurred'] = $value;
                    }
                }
            }
        //tính theo nhân viên------------------------------------------------------------------
        $employees = Employee::select('id','hovaten')->orderby('id','asc')->get();

        foreach($employees as $value){
            $arrayEmployee[] = $value->id;
        }
        foreach ($employees as $key => $value) {
            $listEmployees[] = [
                'ID' => $value->id,
                'SumAriseIncurred' => 0,// tổng PS có đầu kỳ
                'SumDebtIncurred' => 0,// tổng PS nợ đầu kỳ
                'outstandingBalanceBeginning' => 0,//dư nợ đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ
                'DebtIncurred' => 0,//PS nợ trong kỳ (Doanh số)
                'Generate' => 0,//PS có trong kỳ(Đã thu)
                'remain_receive' => 0
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của nhân viên
        $paymentItems2 = $this->paymentItemRepo->getPaymentItemByEmployee($date1,null,null);
        foreach($paymentItems2 as $key => $value){
            foreach($listEmployees as $key1 => $value1){
                if (in_array($key, $arrayEmployee) && $value1['ID'] == $key) {
                    $listEmployees[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
        }
        //lấy tổng phát sinh có trong kỳ của nhân viên
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
        //tính theo nhà cung cấp--------------------------------------------------------------
        $suppliers = Supplier::select('id','name')->orderby('id','asc')->get();

        foreach($suppliers as $value){
            $arraySupplier[] = $value->id;
        }
        foreach ($suppliers as $key => $value) {
            $listSuppliers[] = [
                'ID' => $value->id,
                'SumAriseIncurred' => 0,// tổng PS có đầu kỳ
                'SumDebtIncurred' => 0,// tổng PS nợ đầu kỳ == 0
                'outstandingBalanceBeginning' => 0,//dư nợ đầu kỳ = Phải thu đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ = KH trả trước đầu kỳ
                'DebtIncurred' => 0,//PS nợ trong kỳ (Doanh số)
                'Generate' => 0,//PS có trong kỳ(Đã thu)
                'remain_receive' => 0
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của nhà cung cấp
        $paymentItems4 = $this->paymentItemRepo->getPaymentItemBySupplier($date1,null,null);
        foreach($paymentItems4 as $key => $value){
            foreach($listSuppliers as $key1 => $value1){
                if (in_array($key, $arraySupplier) && $value1['ID'] == $key) {
                    $listSuppliers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
        }
        //lấy tổng phát sinh có trong kỳ của nhà cung cấp
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
        //tính khách hàng trả trước cuối kỳ đang hoạt động---------------------------------------------------
        $customerIsActive = $this->customerRepo->getCustomerByStatus('Đang hoạt động');
        $idIsActive = [];
        foreach ($customerIsActive as $value) {
            $idIsActive[] = $value->id;
        }
        $receivableEndPeriodActive = $this->customerClassify($idIsActive,$date1,$date2);
        //tính khách hàng trả trước cuối kỳ khởi kiện----------------------------------------------------------
        $customerSue = $this->customerRepo->getCustomerByStatus('Khởi kiện');
         $idSue = [];
        foreach ($customerSue as $value) {
             $idSue[] = $value->id;
        }
        $receivableEndPeriodSue = $this->customerClassify($idSue,$date1,$date2);
        //tính khách hàng trả trước cuối kỳ không phát sinh----------------------------------------------------------
        $customerUnArise = $this->customerRepo->getCustomerByStatus('Không phát sinh');
        $idUnArise = [];
        foreach ($customerUnArise as $value) {
            $idUnArise[] = $value->id;
        }
        $receivableEndPeriodUnArise = $this->customerClassify($idUnArise,$date1,$date2);
        //tính khách hàng trả trước cuối kỳ không hoạt động----------------------------------------------------------
        $customerInActive = $this->customerRepo->getCustomerByStatus('Không hoạt động');
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
        //TÍNH CÒN LẠI
        $customers = $this->customerRepo->getCustomer()->get();
        // //tạo mảng ID khách hàng
        foreach ($customers as $value) {
            $arrayCustomer[] = $value->id;
        }
        foreach ($customers as $key => $value) {
            $listCustomers[] = [
                'ID' => $value->id,
                'name' => $value->name,
                'SumAriseIncurred' => 0,// tổng PS có đầu kỳ
                'SumDebtIncurred' => 0,// tổng PS nợ đầu kỳ
                'outstandingBalanceBeginning' => 0,//dư nợ đầu kỳ
                'balanceBeginning' => 0, //dư có đầu kỳ
                'DebtIncurred' => 0,//PS nợ trong kỳ (Doanh số)
                'Generate' => 0,//PS có trong kỳ(Đã thu)
                'receivableEndTerm' => 0, //phải thu cuối kỳ
                'remain_receive' => 0
            ];
        }
        //lấy tổng phát sinh có đầu kỳ của KH theo paymentitem
        $paymentItems = $this->paymentItemRepo->getPaymentItemByCustomer($date1,null,null);
        foreach($paymentItems as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
        //lấy tổng phát sinh đầu kỳ theo volume
        $creditGroupCustomer = $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->pluck('credit','customer_id')->toArray();

        //lấy tổng phát sinh có đầu kỳ của KH theo volumetracking
        foreach($creditGroupCustomer as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumAriseIncurred'] += $value;
                    }
                }
        }
        //lấy tổng phát sinh nợ đầu kỳ của KH theo volume
        $debitGroupCustomer = $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
        foreach($debitGroupCustomer as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['SumDebtIncurred'] = $value;
                    }
                }
        }
        //lấy tổng phát sinh có trong kỳ của KH theo paymentitem
        $paymentItems1 = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
        foreach($paymentItems1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] = array_sum(array_column($value, 'amount'));
                    }
                }
            }
        //lấy tổng phát sinh trong kỳ của KH
        $creditGroupCustomer1 = $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->pluck('credit','customer_id')->toArray();
           //phát sinh có trong kỳ theo volume
        foreach($creditGroupCustomer1 as $key => $value){
            foreach($listCustomers as $key1 => $value1){
                if (in_array($key, $arrayCustomer) && $value1['ID'] == $key) {
                    $listCustomers[$key1]['Generate'] += $value;
                    }
                }
            }
        //phát sinh nợ trong kỳ theo volume
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
         //tỷ lệ thu nợ trong kỳ theo kế toán
         $ariseThereAccountant = $this->paymentItemRepo->getPaymentItemByCustomer($date1,$date2,null);
         $accountant_values = [];
         foreach($ariseThereAccountant as $key => $value){
             foreach($value as $value1){
                if($this->customerRepo->find($key)->accountant_name == null){
                    $accountant_values['Không xác định'] = ( $accountant_values['Không xác định'] ?? 0 ) + $value1['amount'];
                }else{
                    $accountant_values[$this->customerRepo->find($key)->accountant_name]=( $accountant_values[$this->customerRepo->find($key)->accountant_name] ?? 0 ) + $value1['amount'];
                }
             };
         };
         arsort($accountant_values);
        if($accountants == 'Không xác định'){
            $arr_accountant = ['Không xác định'];
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
        //số lượng khách hàng theo kế toán
        if($accountants == 0){
            $getAccountants = $this->customerRepo->getAccountant()->get();
        }else if($accountants == 'Không xác định'){
            $getAccountants = $this->customerRepo->getAccountant()->where('accountant_name',null)->get();
        }else if($accountants != 0){
            $getAccountants = $this->customerRepo->getAccountant()->where('accountant_name',$accountants)->get();
        }
         $number_accountant = [];
         $customer_accountant = [];
         foreach ($getAccountants as $value) {
                 $customer_accountant[] = ($value->accountant_name != null) ? $value->accountant_name : 'Không xác định';
         }
 
         foreach ($customer_accountant as $value) {
                 $customer_accountants[] = ($value == 'Không xác định') ? $this->customerRepo->getNumberByStatus()->where('accountant_name', null)->get()
                 : $this->customerRepo->getNumberByStatus()->where('accountant_name', $value)->get();
         }
 
         $customerAccountantActive = [0, 0, 0, 0, 0, 0];
         $customerAccountantSue = [0, 0, 0, 0, 0, 0];
         $customerAccountantUnActive = [0, 0, 0, 0, 0, 0];
         $customerAccountantUnArise = [0, 0, 0, 0, 0, 0];
 
         foreach ($customer_accountants as $key => $value) {
             foreach ($value as $key1 => $value1) {
                 if ($value1->status_id == 'Đang hoạt động') {
                     $customerAccountantActive[$key] = $value1->numberCustomer;
                 }
             }
         }
 
         foreach ($customer_accountants as $key => $value) {
             foreach ($value as $key1 => $value1) {
                 if ($value1->TinhTrang == 'Khởi kiện') {
                     $customerAccountantSue[$key] = $value1->numberCustomer;
                 }
             }
         }
 
         foreach ($customer_accountants as $key => $value) {
             foreach ($value as $key1 => $value1) {
                 if ($value1->TinhTrang == 'Không hoạt động') {
                     $customerAccountantUnActive[$key] = $value1->numberCustomer;
                 }
             }
         }
 
         foreach ($customer_accountants as $key => $value) {
             foreach ($value as $key1 => $value1) {
                 if ($value1->TinhTrang == 'Không phát sinh') {
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
            $customer_accountant[] = ($value->accountant_name != null) ? $value->accountant_name : 'Không xác định';
        }
        ////////////bieu do no phai thu con lai theo thunoketoan
        $arrayRemainAccountant = [];
        $ratioDebtReceivable = [];
        foreach ($customer_accountant as $value_accountant) {
             //lấy tổng phát sinh có đầu kỳ của KH theo kế toán
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
          //lấy tổng phát sinh đầu kỳ theo volume
          $debitGroupCustomer = ($value_accountant == 'Không xác định') ? $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereHas('customer',function($query){
            $query->where('accountant_name',null);
          })->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray()
          : $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereHas('customer',function($query) use ($value_accountant){
            $query->where('accountant_name',$value_accountant);
          })->where('from_date','<',$date1)->pluck('sum','customer_id')->toArray();
          $volumeTrackings = ($value_accountant == 'Không xác định') ? $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereHas('customer',function($query){
            $query->where('accountant_name',null);
          })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray()
          : $this->volumeTrackingRepo->getCreditVolume()->where('from_date','<',$date1)->whereHas('customer',function($query) use ($value_accountant){
            $query->where('accountant_name',$value_accountant);
          })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray();
          //lấy tổng phát sinh có đầu kỳ của KH theo volumetracking
             foreach($volumeTrackings as $value){
                 $SumAriseIncurred += $value;
             }
         //phát sinh nợ đầu kỳ theo volume
         $SumDebtIncurred = 0;
         foreach($debitGroupCustomer as $key => $value){
             $SumDebtIncurred += $value;
         }
        //lấy tổng phát sinh có trong kỳ của KH
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
            //lấy tổng phát sinh trong kỳ theo volume
            $debitGroupCustomer1 = ($value_accountant == 'Không xác định') ? $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query){
                $query->where('accountant_name',null);
              })->pluck('sum','customer_id')->toArray()
              : $this->volumeTrackingRepo->sumTotalPriceGroupObject('customer_id')->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query) use ($value_accountant){
                $query->where('accountant_name',$value_accountant);
              })->pluck('sum','customer_id')->toArray();
            $volumeTrackings1 = ($value_accountant == 'Không xác định') ? $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query){
                $query->where('accountant_name',null);
              })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray()
            : $this->volumeTrackingRepo->getCreditVolume()->whereBetween('from_date',[$date1,$date2])->whereHas('customer',function($query) use ($value_accountant){
                $query->where('accountant_name',$value_accountant);
              })->orderby('customer_id','asc')->pluck('credit','customer_id')->toArray();
            //lấy tổng phát sinh có trong kỳ của KH theo volumetracking
               foreach($volumeTrackings1 as $value){
                   $Generate += $value;
               }
           //phát sinh nợ trong kỳ theo volume
           $DebtIncurred = 0;
           foreach($debitGroupCustomer1 as $key => $value){
               $DebtIncurred += $value;
           }
         $debtCreditBeginning = $this->caculatorDebtCredit($SumDebtIncurred, $SumAriseIncurred);
         $outstandingBalanceBeginning = $debtCreditBeginning['outstandingBalanceBeginning'];
         $balanceBeginning = $debtCreditBeginning['balanceBeginning'];
         $receivedCustomerEndTerm = $this->caculatorDetail($outstandingBalanceBeginning, $balanceBeginning, $DebtIncurred, $Generate);
            //lấy giá trị còn lại ở bảng tổng quan
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
        }else if($accountants == 'Không xác định'){
            $nameAccountant = ['Không xác định'];
            $remain = [$arrayRemainAccountant['Không xác định']];
        }else if($accountants != 'Không xác định' && $accountants != 0){
            $nameAccountant = [$accountants];
            $remain = [$arrayRemainAccountant[$accountants]];
        }
        arsort($ratioDebtReceivable);
        if($accountants == 0){
            $nameAccountantRatio = array_keys($ratioDebtReceivable);
            $ratioDebtCollects = array_values($ratioDebtReceivable);
        }else if($accountants == 'Không xác định'){
            $nameAccountantRatio = ['Không xác định'];
            $ratioDebtCollects = [$ratioDebtReceivable['Không xác định']];
        }else if($accountants != 'Không xác định' && $accountants != 0){
            $nameAccountantRatio = [$accountants];
            $ratioDebtCollects = [$ratioDebtReceivable[$accountants]];
        }
        return response()->json(['data' => [$nameAccountantRatio,$ratioDebtCollects,$nameAccountant,$remain]]);
    }
}
