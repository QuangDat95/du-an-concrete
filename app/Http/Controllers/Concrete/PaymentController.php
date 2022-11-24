<?php
namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReceiptRequest;
use App\Models\Concrete\Payment;
use App\Models\Concrete\PaymentItem;
use App\DataTables\PaymentsDataTable;
use Illuminate\Support\Facades\DB;
class PaymentController extends Controller
{
    
    const TABLE = "payments";
    const PAYMENT_METHOD = 2;
    public function index(PaymentsDataTable $dataTable, ReceiptRequest $request, Payment $payment)
    {
        $table = self::TABLE;
        if ($request->isMethod('get')) {
            return $dataTable->render('layouts.payments.index', compact('table'));
        } else {
            return $this->store($request, $payment);
        }
    }

    public function store($request, $model)
    {
        $params = $request->all();
        if ($model->id) {
            $payment = Payment::find($model->id);
            checkObjectGroup($params,$payment,self::PAYMENT_METHOD);
            $payment->update();
            $last_id = $model->id;
            $param_array = [];
            if (isset($params['volumn_trackings_id'])) {
                foreach ($params['debit_account_id'] as $key => $value) {
                    $param_array[] = [
                        'payment_id' => $last_id,
                        'debit_account_id' => $value,
                        'credit_account_id' => 0,
                        'amount' => 0,
                        'volumn_trackings_id' => null,
                        'station_item_id' => null,
                        'description_payment_item' => null
                    ];
                }
                foreach ($params['volumn_trackings_id'] as $key => $value) {
                    
                        $param_array[$key]['volumn_trackings_id'] = $value;
                }
            } else {
                foreach ($params['debit_account_id'] as $key => $value) {
                    $param_array[] = [
                        'payment_id' => $last_id,
                        'debit_account_id' => $value,
                        'credit_account_id' => 0,
                        'amount' => 0,
                        'volumn_trackings_id' => null,
                        'station_item_id' => null,
                        'description_payment_item' => null
                    ];
                }
            }

            foreach ($params['credit_account_id'] as $key => $value) {
                $param_array[$key]['credit_account_id'] = $value;
            }

            foreach ($params['amount'] as $key => $value) {
                $param_array[$key]['amount'] = formatIntNumber($value);
            }

            foreach ($params['station_item_id'] as $key => $value) {
                $param_array[$key]['station_item_id'] = $value;
            }

            foreach ($params['description_payment_item'] as $key => $value) {
                $param_array[$key]['description_payment_item'] = $value;
            }

            $payment_items = PaymentItem::select('id')->where('payment_id', $last_id)->get()->toArray();

            if (count($payment_items) == count($param_array)) {
                foreach ($param_array as $key => $value) {
                    $payment = Payment::find($value['payment_id']);
                    UpdateObject($payment_items,$key,$value,$payment);
                }
            } elseif (count($payment_items) < count($param_array)) {
                $old_array = array_slice($param_array, 0, count($payment_items));
                $new_array = array_slice($param_array, count($payment_items));
                foreach ($old_array as $key => $value) {
                    $payment = Payment::find($value['payment_id']);
                    UpdateObject($payment_items,$key,$value,$payment);
                }
                foreach ($new_array as $key => $value) {
                    $payment = Payment::find($value['payment_id']);
                    AddObject($value,$payment);
                }
            }
            $message = "Sửa thành công";
            return response()->json(['success' => true, 'message' => $message]);
        } else {
            $payment = new Payment();
            checkObjectGroup($params,$payment,self::PAYMENT_METHOD);
            $payment->save();
            $last_id = $payment->id;
            $param_array = [];
            if (isset($params['volumn_trackings_id'])) {
                foreach ($params['debit_account_id'] as $key => $value) {
                    $param_array[] = [
                        'payment_id' => $last_id,
                        'debit_account_id' => $value,
                        'credit_account_id' => 0,
                        'amount' => 0,
                        'volumn_trackings_id' => null,
                        'station_item_id' => 0,
                        'description_payment_item' => 0
                    ];
                }
                foreach ($params['volumn_trackings_id'] as $key => $value) {
                    if ($value != null) {
                        $param_array[$key]['volumn_trackings_id'] = $value;
                    } else {
                        $param_array[$key]['volumn_trackings_id'] = null;
                    }
                }
            } 
            else 
            {
                foreach ($params['debit_account_id'] as $key => $value) {
                    $param_array[] = [
                        'payment_id' => $last_id,
                        'debit_account_id' => $value,
                        'credit_account_id' => 0,
                        'amount' => 0,
                        'volumn_trackings_id' => null,
                        'station_item_id' => null,
                        'description_payment_item' => 0
                    ];
                }
            }

            foreach ($params['credit_account_id'] as $key => $value) {
                $param_array[$key]['credit_account_id'] = $value;
            }

            foreach ($params['amount'] as $key => $value) {
                $param_array[$key]['amount'] = formatIntNumber($value);
            }

            foreach ($params['station_item_id'] as $key => $value) {
                $param_array[$key]['station_item_id'] = $value;
            }

            foreach ($params['description_payment_item'] as $key => $value) {
                $param_array[$key]['description_payment_item'] = $value;
            }

            foreach ($param_array as $value) {
                $payment = Payment::find($value['payment_id']);
                AddObject($value,$payment);
            }
            $message = "Thêm thành công";
            return response()->json(['success' => true, 'message' => $message]);
        }
    }

    public function edit(Payment $payment, PaymentItem $paymentItem)
    {
        $oldData = Payment::find($payment->id);
        $partyable_id = $oldData->partyable_id;
        $routeSubmit = route('payments', ['payment' => $payment->id]);
        return response()->json([
            'success' => true, 
            'old' => $oldData, 
            'route' => $routeSubmit,
            'html'=>view('promissories.edit',compact('oldData','paymentItem','partyable_id'))->render()
        ]);
    }

    public function destroy(Request $request)
    {
        $checkboxIds = json_decode($request->checkboxed, true);
        foreach ($checkboxIds as $value) {
            $payment_id = PaymentItem::select('id')
                ->where('payment_id', $value)
                ->get();
            foreach ($payment_id as $value) {
                PaymentItem::where('id', $value->id)->delete();
            }
        }
        Payment::whereIn('id', $checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function print(Request $request)
    {
        $id = $request->id;
        $address = $request->address;
        $payment = Payment::find($id[0]);
        $payment_item = PaymentItem::select('*')->where('payment_id',$id)->get();
        $sum = PaymentItem::select(DB::raw('SUM(amount) as sum'))->where('payment_id',$id)->get();
        $creditAccount = $payment_item[0]->creditAccount->account_code;
        return view('layouts.payments.print',compact('payment','address','payment_item','sum','creditAccount'));
    }
}