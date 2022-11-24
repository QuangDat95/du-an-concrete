<?php
namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReceiptRequest;
use App\Models\Concrete\Debit;
use App\Models\Concrete\PaymentItem;
use App\DataTables\DebitsDataTable;
use Illuminate\Support\Facades\DB;
class DebitController extends Controller
{
    
    const TABLE = "debits";
    const PAYMENT_METHOD = 3;
    public function index(DebitsDataTable $dataTable, ReceiptRequest $request, Debit $debit)
    {
        $table = self::TABLE;
        if ($request->isMethod('get')) {
            return $dataTable->render('layouts.debits.index', compact('table'));
        } else {
            return $this->store($request, $debit);
        }
    }

    public function store($request, $model)
    {
        $params = $request->all();
        if ($model->id) {
            $debit = Debit::find($model->id);
            checkObjectGroup($params,$debit,self::PAYMENT_METHOD);
            $debit->update();
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
                    $debit = Debit::find($value['payment_id']);
                    UpdateObject($payment_items,$key,$value,$debit);
                }
            } elseif (count($payment_items) < count($param_array)) {
                $old_array = array_slice($param_array, 0, count($payment_items));
                $new_array = array_slice($param_array, count($payment_items));
                foreach ($old_array as $key => $value) {
                    $debit = Debit::find($value['payment_id']);
                    UpdateObject($payment_items,$key,$value,$debit);
                }
                foreach ($new_array as $key => $value) {
                    $debit = Debit::find($value['payment_id']);
                    AddObject($value,$debit);
                }
            }
            $message = "Sửa thành công";
            return response()->json(['success' => true, 'message' => $message]);
        } else {
            $debit = new Debit();
            checkObjectGroup($params,$debit,self::PAYMENT_METHOD);
            $debit->save();
            $last_id = $debit->id;
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
                $debit = Debit::find($value['payment_id']);
                AddObject($value,$debit);
            }
            $message = "Thêm thành công";
            return response()->json(['success' => true, 'message' => $message]);
        }
    }

    public function edit(Debit $debit, PaymentItem $paymentItem)
    {
        $oldData = Debit::find($debit->id);
        $partyable_id = $oldData->partyable_id;
        $routeSubmit = route('debits', ['debit' => $debit->id]);
        return response()->json([
            'success' => true, 
            'old' => $oldData,
            'customer_id' => $partyable_id, 
            'route' => $routeSubmit,
            'html'=>view('promissories.edit',compact('oldData','paymentItem','partyable_id'))->render()]);
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
        Debit::whereIn('id', $checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function print(Request $request)
    {
        $id = $request->id;
        $address = $request->address;
        $debit = Debit::find($id[0]);
        $payment_item = PaymentItem::select('*')->where('payment_id',$id)->get();
        $sum = PaymentItem::select(DB::raw('SUM(amount) as sum'))->where('payment_id',$id)->get();
        $creditAccount = $payment_item[0]->creditAccount->account_code;
        return view('layouts.debits.print',compact('debit','address','payment_item','sum','creditAccount'));
    }
}