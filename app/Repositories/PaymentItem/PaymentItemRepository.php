<?php
namespace App\Repositories\PaymentItem;

use App\Repositories\Repository;
use App\Models\Concrete\PaymentItem;
class PaymentItemRepository extends Repository implements PaymentItemRepositoryInterface
{
    //lấy model tương ứng
    public function getModel()
    {
        return PaymentItem::class;
    }

    public function getPayment()
    {
        return PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment'=>function($query){
            $query->select('id','payment_date','partyable_id');
        }])->whereHas('payment',function($query){
           $query->where('partyable_type','App\Models\Concrete\Customer');
       })->whereHas('creditAccount',function($query){
            $query->where('account_code','like','131%');
        })->groupBy('payment_id','credit_account_id')->get(['payment_id','credit_account_id'])->toArray();
    }

    public function getPaymentItemIncurred($nows)
    {
        return PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
            ->with(['payment'=>function($query){
                $query->select('id','payment_date','partyable_id');
            }])->whereHas('payment',function($query){
               $query->where('partyable_type','App\Models\Concrete\Customer');
           })->whereHas('creditAccount',function($query){
                $query->where('account_code','like','131%');
            })->whereHas('volumetracking',function($query) use ($nows){
               $query->whereRaw("0 > DATEDIFF('".$nows."',due_date) AND (payment_status IS NULL OR payment_status = 0)");
           })->groupBy('payment_id','credit_account_id')->get(['payment_id','credit_account_id'])->toArray();
    }

    public function getPaymentItemLessMonth($nows,$number1,$number2)
    {
        return PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment'=>function($query){
            $query->select('id','payment_date','partyable_id');
        }])->whereHas('payment',function($query){
           $query->where('partyable_type','App\Models\Concrete\Customer');
       })->whereHas('creditAccount',function($query){
            $query->where('account_code','like','131%');
        })->whereHas('volumetracking',function($query) use ($nows,$number1,$number2){
           $query->whereRaw("(".$number1." < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= ".$number2.") AND (payment_status IS NULL OR payment_status = 0)");
       })->groupBy('payment_id','credit_account_id')->get(['payment_id','credit_account_id'])->toArray();
    }

    public function getPaymentItemOverdue($nows)
    {
        return PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment'=>function($query){
            $query->select('id','payment_date','partyable_id');
        }])->whereHas('payment',function($query){
           $query->where('partyable_type','App\Models\Concrete\Customer');
       })->whereHas('creditAccount',function($query){
            $query->where('account_code','like','131%');
        })->whereHas('volumetracking',function($query) use ($nows){
           $query->whereRaw("DATEDIFF('".$nows."',due_date) > 180 AND (payment_status IS NULL OR payment_status = 0)");
       })->groupBy('payment_id','credit_account_id')->get(['payment_id','credit_account_id'])->toArray();
    }

    public function getPaymentItemByCustomer($date1,$date2 = null,$company = null)
    {
        return $this->model->selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment' => function($query){
        $query->select('id','partyable_id');
        }])->whereHas('payment',function($query) use ($date1,$date2,$company){
            if($date2 != null && $company == null){
                $query->whereBetween('payment_date',[$date1,$date2])->where('partyable_type','App\Models\Concrete\Customer');
            }else if($date2 == null && $company == null){
                $query->where('payment_date','<',$date1)->where('partyable_type','App\Models\Concrete\Customer');
            }else if($date2 != null && $company != null){
                $query->whereBetween('payment_date',[$date1,$date2])->whereIn('company_id',$company)->where('partyable_type','App\Models\Concrete\Customer');
            }else if($date2 == null && $company != null){
                $query->where('payment_date','<',$date1)->whereIn('company_id',$company)->where('partyable_type','App\Models\Concrete\Customer');
            }
           })->whereHas('creditAccount',function($query){
               $query->where('account_code','like','131%');
           })->groupBy('payment_id','credit_account_id')
           ->get(['payment_id','credit_account_id'])->groupBy('payment.partyable_id')->toArray();
    }

    public function getPaymentItemByEmployee($date1,$date2 = null,$company = null)
    {
        return PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment' => function($query){
            $query->select('id','partyable_id');
        }])->whereHas('payment',function($query) use ($date1,$date2,$company){
            if($date2 == null && $company == null){
                $query->where('payment_date','<',$date1)->where('partyable_type','App\Models\Survey\Employee');
            }else if($date2 != null && $company == null){
                $query->whereBetween('payment_date',[$date1,$date2])->where('partyable_type','App\Models\Survey\Employee');
            }else if($date2 == null && $company != null){
                $query->where('payment_date','<',$date1)->whereIn('company_id',$company)->where('partyable_type','App\Models\Survey\Employee');
            }else if($date2 != null && $company != null){
                $query->whereBetween('payment_date',[$date1,$date2])->whereIn('company_id',$company)->where('partyable_type','App\Models\Survey\Employee');
            }
        })->whereHas('creditAccount',function($query){
            $query->where('account_code','like','131%');
        })->groupBy('payment_id','credit_account_id')
        ->get(['payment_id','credit_account_id'])->sortBy("payment.partyable_id")->groupBy('payment.partyable_id')->toArray();
    }

    public function getPaymentItemBySupplier($date1,$date2 = null,$company = null)
    {
        return PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment' => function($query){
            $query->select('id','partyable_id');
        }])->whereHas('payment',function($query) use ($date1,$date2,$company){
            if($date2 == null && $company == null){
                $query->where('payment_date','<',$date1)->where('partyable_type','App\Models\Concrete\Supplier');
            }else if($date2 != null && $company == null){
                $query->whereBetween('payment_date',[$date1,$date2])->where('partyable_type','App\Models\Concrete\Supplier');
            }else if($date2 == null && $company != null){
                $query->where('payment_date','<',$date1)->whereIn('company_id',$company)->where('partyable_type','App\Models\Concrete\Supplier');
            }else if($date2 != null && $company != null){
                $query->whereBetween('payment_date',[$date1,$date2])->whereIn('company_id',$company)->where('partyable_type','App\Models\Concrete\Supplier');
            }
        })->whereHas('creditAccount',function($query){
            $query->where('account_code','like','131%');
        })->groupBy('payment_id','credit_account_id')
        ->get(['payment_id','credit_account_id'])->sortBy("payment.partyable_id")->groupBy('payment.partyable_id')->toArray();
    }

    public function getPaymentItemClassify($date1,$date2,$id)
    {
        return PaymentItem::selectRaw('payment_id,credit_account_id, sum(amount) as amount')
        ->with(['payment' => function($query){
            $query->select('id','partyable_id');
        }])->whereHas('payment',function($query) use ($date1,$date2,$id){
            if($date1 != null && $date2 == null && $id != null){
                $query->where('payment_date','<',$date1)->where('partyable_type','App\Models\Concrete\Customer')->whereIn('partyable_id',$id);
            }else if($date1 != null && $date2 != null && $id != null){
                $query->whereBetween('payment_date',[$date1,$date2])->where('partyable_type','App\Models\Concrete\Customer')->whereIn('partyable_id',$id);
            }
        })->whereHas('creditAccount',function($query){
            $query->where('account_code','like','131%');
        })->groupBy('payment_id','credit_account_id')
        ->get(['payment_id','credit_account_id'])->sortBy("payment.partyable_id")->groupBy('payment.partyable_id')->toArray();
    }
}