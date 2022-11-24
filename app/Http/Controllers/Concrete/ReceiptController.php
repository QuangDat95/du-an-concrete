<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReceiptRequest;
use App\Models\Concrete\Receipt;
use App\Models\Concrete\TransactionType;
use App\Models\Concrete\PaymentItem;
use App\Models\Concrete\VolumeTracking;
use App\DataTables\ReceiptsDataTable;
use Illuminate\Support\Facades\DB;
class ReceiptController extends Controller
{
    const TABLE = "receipts";
    const PAYMENT_METHOD = 1;
    public function __construct()
    {
        $this->debitAccount = TransactionType::all(['debit_account_id','id'])->pluck('debit_account_id','id');
        $this->creditAccount = TransactionType::all(['credit_account_id','id'])->pluck('credit_account_id','id');
    }

    public function index(ReceiptsDataTable $dataTable, ReceiptRequest $request, Receipt $receipt)
    {
        $table = self::TABLE;
        if ($request->isMethod('get')) {
            return $dataTable->render('layouts.receipts.index', compact('table'));
        } else {
            return $this->store($request, $receipt);
        }
    }

    public function store($request, $model)
    {
        $params = $request->all();
        if ($model->id) {
            $receipt = Receipt::find($model->id);
            checkObjectGroup($params,$receipt,self::PAYMENT_METHOD);
            $receipt->update();
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

            foreach ($params['description_payment_item'] as $key => $value) {
                $param_array[$key]['description_payment_item'] = $value;
            }

            $payment_items = PaymentItem::select('id')->where('payment_id', $last_id)->get()->toArray();

            if (count($payment_items) == count($param_array)) {
                foreach ($param_array as $key => $value) {
                    $receipt = Receipt::find($value['payment_id']);
                    UpdateObject($payment_items,$key,$value,$receipt);
                }
            } elseif (count($payment_items) < count($param_array)) {
                $old_array = array_slice($param_array, 0, count($payment_items));
                $new_array = array_slice($param_array, count($payment_items));
                foreach ($old_array as $key => $value) {
                    $receipt = Receipt::find($value['payment_id']);
                    UpdateObject($payment_items,$key,$value,$receipt);
                }
                foreach ($new_array as $value) {
                    $receipt = Receipt::find($value['payment_id']);
                    AddObject($value,$receipt);
                }
            } 
            $message = "Sửa thành công";
            return response()->json(['success' => true, 'message' => $message]);
        } else {
            $receipt = new Receipt();
            checkObjectGroup($params,$receipt,self::PAYMENT_METHOD);
            $receipt->save();
            $last_id = $receipt->id;
            $param_array = [];
            if (isset($params['volumn_trackings_id'])) {
                foreach ($params['debit_account_id'] as $key => $value) {
                    $param_array[] = [
                        'payment_id' => $last_id,
                        'debit_account_id' => $value,
                        'credit_account_id' => 0,
                        'amount' => 0,
                        'volumn_trackings_id' => null,
                        'description_payment_item' => null
                    ];
                }
                foreach ($params['volumn_trackings_id'] as $key => $value) {
                    if ($value != null) {
                        $param_array[$key]['volumn_trackings_id'] = $value;
                    } else {
                        $param_array[$key]['volumn_trackings_id'] = null;
                    }
                }
            } else {
                foreach ($params['debit_account_id'] as $key => $value) {
                    $param_array[] = [
                        'payment_id' => $last_id,
                        'debit_account_id' => $value,
                        'credit_account_id' => 0,
                        'amount' => 0,
                        'volumn_trackings_id' => null,
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

            foreach ($params['description_payment_item'] as $key => $value) {
                $param_array[$key]['description_payment_item'] = $value;
            }

            foreach ($param_array as $value) {
                $receipt = Receipt::find($value['payment_id']);
                AddObject($value,$receipt);
            }
            $message = "Thêm thành công";
            return response()->json(['success' => true, 'message' => $message]);
        }
    }

    public function edit(Receipt $receipt, PaymentItem $paymentItem)
    {
        $oldData = Receipt::find($receipt->id);
        $oldData['payment_date'] = date_format(date_create($oldData['payment_date']),"d-m-Y");
        if($oldData->partyable_type == 'App\Models\Concrete\Customer'){
            $partyable_id = $oldData->partyable_id;   
        }else{
            $partyable_id = '';
        }
        $routeSubmit = route('receipts', ['receipt' => $receipt->id]);
        return response()->json(
        array(
            'success' => true,
            'old' => $oldData,
            'customer_id' => $partyable_id,
            'route' => $routeSubmit,
            'html'=>view('promissories.edit',compact('oldData','paymentItem','partyable_id'))->render()
            ));
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
        Receipt::whereIn('id', $checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function addReceiptTable(Request $request)
    {
        $id = $request->id;
        if($id != null){
            $debit_id = $this->debitAccount[$id];
            $credit_id = $this->creditAccount[$id];
            return response()->json(
                array(
                    'debit_id' => $debit_id,
                    'credit_id' => $credit_id,
                    'html'=>view('promissories.add')->render()
                    ));
        }else{
            return response()->json(
                array(
                    'html'=>view('promissories.add')->render()
                    ));
        }
    }

    public function getVolumeValue(Request $request)
    {
        $id = $request->id;
        $payment_id = $request->payment_id;
        $amount_input = str_replace(',', '', $request->amount_input);
        if ($id != null){
            $volume = VolumeTracking::where('id', $id)->get(['station_id', 'total_price', 'due_date'])->first();
            if($payment_id != null){
                $number_remain = PaymentItem::where('volumn_trackings_id',$id)->where('payment_id','<=',$payment_id)->sum('amount');
            }else{
                $number_remain = PaymentItem::where('volumn_trackings_id',$id)->sum('amount');
            }
            $remain = $volume->total_price - $number_remain;
            $remain_price = (int)$amount_input - $remain;
            return [number_format($volume->total_price - $number_remain), $volume->due_date, $remain_price, $volume->station_id];
        } else {
            return ['', '', '', ''];
        }
    }

    public function getVolumeId(Request $request)
    {
        $id = $request->id;
        $volumeId = VolumeTracking::select('id')->where('customer_id', $id)->get();
        return view('layouts.ajax.volume_id', compact('volumeId'));
    }

    public function print(Request $request)
    {
        $id = $request->id;
        $address = $request->address;
        $receipt = Receipt::find($id[0]);
        $payment_item = PaymentItem::select('*')->where('payment_id',$id)->get();
        $sum = PaymentItem::select(DB::raw('SUM(amount) as sum'))->where('payment_id',$id)->get();
        $creditAccount = $payment_item[0]->creditAccount->account_code;
        return view('layouts.receipts.print',compact('receipt','address','payment_item','sum','creditAccount'));
    }
}