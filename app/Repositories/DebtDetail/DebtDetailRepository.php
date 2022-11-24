<?php
namespace App\Repositories\DebtDetail;

use App\Repositories\Repository;
use App\Models\Concrete\PaymentItem;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;
use App\Repositories\PaymentItem\PaymentItemRepositoryInterface;
class DebtDetailRepository extends Repository implements DebtDetailRepositoryInterface
{
    
    protected $volumeTrackingRepo;
    protected $paymentItemRepo;

    public function __construct(VolumeTrackingRepositoryInterface $volumeTrackingRepo,PaymentItemRepositoryInterface $paymentItemRepo)
    {
        $this->volumeTrackingRepo = $volumeTrackingRepo;
        $this->paymentItemRepo = $paymentItemRepo;
    }

    public function getModel()
    {
        return;
    }

    public function debtOverDueDetail($customer_volumes,$nows)
    {
        $listCustomers = [];
        foreach($customer_volumes as $value){
            $listCustomers[] = [
                'id' => $value->customer_id,
                'name' => $value->customer->name,
                'sumDebt' => 0,
                'debtIncurred' => 0,
                'debtLessThan1Month' => 0,
                'debtLessThan2Month' => 0,
                'debtLessThan3Month' => 0,
                'debtLessThan4Month' => 0,
                'debtLessThan5Month' => 0,
                'debtLessThan6Month' => 0,
                'debtOverDue' => 0
            ];
        }
        //ps nợ còn lại theo volume
        $DebitVolumes = $this->volumeTrackingRepo->getDebitVolume()->pluck('debit','customer_id');
         //tính còn lại tổng nợ
        foreach($listCustomers as $key => $value){
            foreach($DebitVolumes as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['sumDebt'] = $value1;
                }
            }
        }
        
        //ps có theo paymentItem
        $CreditPaymentItems = $this->paymentItemRepo->getPayment();
        $CreditPaymentItems_arr = [];
        foreach($CreditPaymentItems as $value){
            $CreditPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        //tính còn lại bằng cách - psco theo payment
        foreach($listCustomers as $key => $value){
            foreach($CreditPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['sumDebt'] -= $value1;
                }
            }
        }
        //ps có trong hạn theo volume định khoản
        $CreditIncurredVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("0 >= DATEDIFF('".$nows."',due_date) AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
        //còn lại = ps có trong hạn
        foreach($listCustomers as $key => $value){
            foreach($CreditIncurredVolumes as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtIncurred'] = $value1;
                }
            }
        }
        //psco theo paymentitem
        $CreditIncurredPaymentItems = $this->paymentItemRepo->getPaymentItemIncurred($nows);
        $CreditIncurredPaymentItems_arr = [];
        foreach($CreditIncurredPaymentItems as $value){
            $CreditIncurredPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditIncurredPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        //còn lại bằng nợ trừ psco theo paymentitem
        foreach($listCustomers as $key => $value){
            foreach($CreditIncurredPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtIncurred'] -= $value1;
                }
            }
        }

        $CreditLessThan1MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("0 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 30
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan1MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan1Month'] = $value1;
                    }
                }
            }
        $CreditLessThan1MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,0,30);
        $CreditLessThan1MonthPaymentItems_arr = [];
        foreach($CreditLessThan1MonthPaymentItems as $value){
            $CreditLessThan1MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan1MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan1MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan1Month'] -= $value1;
                }
            }
        }

        $CreditLessThan2MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("30 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 60
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan2MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan2Month'] = $value1;
                    }
                }
            }
        $CreditLessThan2MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,30,60);
        $CreditLessThan2MonthPaymentItems_arr = [];
        foreach($CreditLessThan2MonthPaymentItems as $value){
            $CreditLessThan2MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan2MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan2MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan2Month'] -= $value1;
                }
            }
        }

        $CreditLessThan3MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("60 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 90
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan3MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan3Month'] = $value1;
                    }
                }
            }
        $CreditLessThan3MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,60,90);
        $CreditLessThan3MonthPaymentItems_arr = [];
        foreach($CreditLessThan3MonthPaymentItems as $value){
            $CreditLessThan3MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan3MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan3MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan3Month'] -= $value1;
                }
            }
        }

        $CreditLessThan4MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("90 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 120
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan4MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan4Month'] = $value1;
                    }
                }
            }
        $CreditLessThan4MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,90,120);
        $CreditLessThan4MonthPaymentItems_arr = [];
        foreach($CreditLessThan4MonthPaymentItems as $value){
            $CreditLessThan4MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan4MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan4MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan4Month'] -= $value1;
                }
            }
        }

        $CreditLessThan5MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("120 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 150
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan5MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan5Month'] = $value1;
                    }
                }
            }
        $CreditLessThan5MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,120,150);
        $CreditLessThan5MonthPaymentItems_arr = [];
        foreach($CreditLessThan5MonthPaymentItems as $value){
            $CreditLessThan5MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan5MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan5MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan5Month'] -= $value1;
                }
            }
        }

        $CreditLessThan6MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("150 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 180
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan6MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan6Month'] = $value1;
                    }
                }
            }
        $CreditLessThan6MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,150,180);
        $CreditLessThan6MonthPaymentItems_arr = [];
        foreach($CreditLessThan6MonthPaymentItems as $value){
            $CreditLessThan6MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan6MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan6MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan6Month'] -= $value1;
                }
            }
        }

        $CreditOverDueVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("DATEDIFF('".$nows."',due_date) > 180 AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditOverDueVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtOverDue'] = $value1;
                    }
                }
            }
        $CreditOverDuePaymentItems = $this->paymentItemRepo->getPaymentItemOverdue($nows);
        $CreditOverDuePaymentItems_arr = [];
        foreach($CreditOverDuePaymentItems as $value){
            $CreditOverDuePaymentItems_arr[$value['payment']['partyable_id']] = ($CreditOverDuePaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditOverDuePaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtOverDue'] -= $value1;
                }
            }
        }
        return $listCustomers;
    }

    public function debtOverDueStructure($customer_volumes,$nows)
    {
        $listCustomers = [];
        foreach($customer_volumes as $value){
            $listCustomers[] = [
                'id' => $value->customer_id,
                'debtIncurred' => 0,
                'debtLessThan1Month' => 0,
                'debtLessThan2Month' => 0,
                'debtLessThan3Month' => 0,
                'debtLessThan4Month' => 0,
                'debtLessThan5Month' => 0,
                'debtLessThan6Month' => 0,
                'debtOverDue' => 0
            ];
        }
        //ps có trong hạn theo volume định khoản
        $CreditIncurredVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("0 >= DATEDIFF('".$nows."',due_date) AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
        //còn lại = ps có trong hạn
        foreach($listCustomers as $key => $value){
            foreach($CreditIncurredVolumes as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtIncurred'] = $value1;
                }
            }
        }
        //psco theo paymentitem
        $CreditIncurredPaymentItems = $this->paymentItemRepo->getPaymentItemIncurred($nows);
        $CreditIncurredPaymentItems_arr = [];
        foreach($CreditIncurredPaymentItems as $value){
            $CreditIncurredPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditIncurredPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        //còn lại bằng nợ trừ psco theo paymentitem
        foreach($listCustomers as $key => $value){
            foreach($CreditIncurredPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtIncurred'] -= $value1;
                }
            }
        }

        $CreditLessThan1MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("0 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 30
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan1MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan1Month'] = $value1;
                    }
                }
            }
        $CreditLessThan1MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,0,30);
        $CreditLessThan1MonthPaymentItems_arr = [];
        foreach($CreditLessThan1MonthPaymentItems as $value){
            $CreditLessThan1MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan1MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan1MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan1Month'] -= $value1;
                }
            }
        }

        $CreditLessThan2MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("30 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 60
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan2MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan2Month'] = $value1;
                    }
                }
            }
        $CreditLessThan2MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,30,60);
        $CreditLessThan2MonthPaymentItems_arr = [];
        foreach($CreditLessThan2MonthPaymentItems as $value){
            $CreditLessThan2MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan2MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan2MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan2Month'] -= $value1;
                }
            }
        }

        $CreditLessThan3MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("60 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 90
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan3MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan3Month'] = $value1;
                    }
                }
            }
        $CreditLessThan3MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,60,90);
        $CreditLessThan3MonthPaymentItems_arr = [];
        foreach($CreditLessThan3MonthPaymentItems as $value){
            $CreditLessThan3MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan3MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan3MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan3Month'] -= $value1;
                }
            }
        }

        $CreditLessThan4MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("90 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 120
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan4MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan4Month'] = $value1;
                    }
                }
            }
        $CreditLessThan4MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,90,120);
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan4MonthPaymentItems as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan4Month'] -= $value1;
                }
            }
        }

        $CreditLessThan5MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("120 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 150
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan5MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan5Month'] = $value1;
                    }
                }
            }
        $CreditLessThan5MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,120,150);
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan5MonthPaymentItems as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan5Month'] -= $value1;
                }
            }
        }

        $CreditLessThan6MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("150 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 180
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan6MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan6Month'] = $value1;
                    }
                }
            }
        $CreditLessThan6MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,150,180);
        $CreditLessThan6MonthPaymentItems_arr = [];
        foreach($CreditLessThan6MonthPaymentItems as $value){
            $CreditLessThan6MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan6MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan6MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan6Month'] -= $value1;
                }
            }
        }

        $CreditOverDueVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("DATEDIFF('".$nows."',due_date) > 180 AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditOverDueVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtOverDue'] = $value1;
                    }
                }
            }
        $CreditOverDuePaymentItems = $this->paymentItemRepo->getPaymentItemOverdue($nows);
        $CreditOverDuePaymentItems_arr = [];
        foreach($CreditOverDuePaymentItems as $value){
            $CreditOverDuePaymentItems_arr[$value['payment']['partyable_id']] = ($CreditOverDuePaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditOverDuePaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtOverDue'] -= $value1;
                }
            }
        }
        return $listCustomers;
    }

    public function debtOverDueByTime($customer_volumes,$nows)
    {
        $listCustomers = [];
        foreach($customer_volumes as $value){
            $listCustomers[] = [
                'id' => $value->customer_id,
                'debtLessThan1Month' => 0,
                'debtLessThan2Month' => 0,
                'debtLessThan3Month' => 0,
                'debtLessThan4Month' => 0,
                'debtLessThan5Month' => 0,
                'debtLessThan6Month' => 0,
                'debtOverDue' => 0
            ];
        }

        $CreditLessThan1MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("0 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 30
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan1MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan1Month'] = $value1;
                    }
                }
            }
        $CreditLessThan1MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,0,30);
        $CreditLessThan1MonthPaymentItems_arr = [];
        foreach($CreditLessThan1MonthPaymentItems as $value){
            $CreditLessThan1MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan1MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan1MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan1Month'] -= $value1;
                }
            }
        }

        $CreditLessThan2MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("30 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 60
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan2MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan2Month'] = $value1;
                    }
                }
            }
        $CreditLessThan2MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,30,60);
        $CreditLessThan2MonthPaymentItems_arr = [];
        foreach($CreditLessThan2MonthPaymentItems as $value){
            $CreditLessThan2MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan2MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan2MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan2Month'] -= $value1;
                }
            }
        }

        $CreditLessThan3MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("60 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 90
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan3MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan3Month'] = $value1;
                    }
                }
            }
        $CreditLessThan3MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,60,90);
        $CreditLessThan3MonthPaymentItems_arr = [];
        foreach($CreditLessThan3MonthPaymentItems as $value){
            $CreditLessThan3MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan3MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan3MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan3Month'] -= $value1;
                }
            }
        }

        $CreditLessThan4MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("90 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 120
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan4MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan4Month'] = $value1;
                    }
                }
            }
        $CreditLessThan4MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,90,120);
        $CreditLessThan4MonthPaymentItems_arr = [];
        foreach($CreditLessThan4MonthPaymentItems as $value){
            $CreditLessThan4MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan4MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan4MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan4Month'] -= $value1;
                }
            }
        }

        $CreditLessThan5MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("120 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 150
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan5MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan5Month'] = $value1;
                    }
                }
            }
        $CreditLessThan5MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,120,150);
        $CreditLessThan5MonthPaymentItems_arr = [];
        foreach($CreditLessThan5MonthPaymentItems as $value){
            $CreditLessThan5MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan5MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan5MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan5Month'] -= $value1;
                }
            }
        }

        $CreditLessThan6MonthVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("150 < DATEDIFF('".$nows."',due_date) AND DATEDIFF('".$nows."',due_date) <= 180
            AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditLessThan6MonthVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtLessThan6Month'] = $value1;
                    }
                }
            }
        $CreditLessThan6MonthPaymentItems = $this->paymentItemRepo->getPaymentItemLessMonth($nows,150,180);
        $CreditLessThan6MonthPaymentItems_arr = [];
        foreach($CreditLessThan6MonthPaymentItems as $value){
            $CreditLessThan6MonthPaymentItems_arr[$value['payment']['partyable_id']] = ($CreditLessThan6MonthPaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditLessThan6MonthPaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtLessThan6Month'] -= $value1;
                }
            }
        }

        $CreditOverDueVolumes = $this->volumeTrackingRepo->getDebitVolume()->whereRaw("DATEDIFF('".$nows."',due_date) > 180 AND (payment_status IS NULL OR payment_status = 0)")->pluck('debit','customer_id');
            foreach($listCustomers as $key => $value){
                foreach($CreditOverDueVolumes as $key1 => $value1){
                    if($value['id'] == $key1){
                        $listCustomers[$key]['debtOverDue'] = $value1;
                    }
                }
            }
        $CreditOverDuePaymentItems = $this->paymentItemRepo->getPaymentItemOverdue($nows);
        $CreditOverDuePaymentItems_arr = [];
        foreach($CreditOverDuePaymentItems as $value){
            $CreditOverDuePaymentItems_arr[$value['payment']['partyable_id']] = ($CreditOverDuePaymentItems_arr[$value['payment']['partyable_id']] ?? 0) + $value['amount'];
        }
        foreach($listCustomers as $key => $value){
            foreach($CreditOverDuePaymentItems_arr as $key1 => $value1){
                if($value['id'] == $key1){
                    $listCustomers[$key]['debtOverDue'] -= $value1;
                }
            }
        }
        return $listCustomers;
    }
}