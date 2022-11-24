<?php

namespace App\Http\Controllers\Concrete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ReceiptRequest;
use App\Models\Concrete\Alert;
use App\Models\Concrete\PaymentItem;
use App\DataTables\AlertsDataTable;
use Illuminate\Support\Facades\DB;
class AlertController extends Controller
{
    
    const TABLE = "alerts";
    const PAYMENT_METHOD = 4;
    public function index(AlertsDataTable $dataTable, ReceiptRequest $request, Alert $alert)
    {
        $table = self::TABLE;
        if ($request->isMethod('get')) {
            return $dataTable->render('layouts.alerts.index', compact('table'));
        } else {
            return $this->store($request, $alert);
        }
    }

    public function store($request, $model)
    {
        $params = $request->all();
        if ($model->id) {
            $alert = Alert::find($model->id);
            checkObjectGroup($params,$alert,self::PAYMENT_METHOD);
            $alert->update();
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
                    $alert = Alert::find($value['payment_id']);
                    UpdateObject($payment_items,$key,$value,$alert);
                }
                $message = "Sửa thành công";
            } elseif (count($payment_items) < count($param_array)) {
                $old_array = array_slice($param_array, 0, count($payment_items));
                $new_array = array_slice($param_array, count($payment_items));
                foreach ($old_array as $key => $value) {
                    $alert = Alert::find($value['payment_id']);
                    UpdateObject($payment_items,$key,$value,$alert);
                }
                foreach ($new_array as $key => $value) {
                    $alert = Alert::find($value['payment_id']);
                    AddObject($value,$alert);
                }
                $message = "Sửa thành công";
            }
            return response()->json(['success' => true, 'message' => $message]);
        } else {
            $alert = new Alert();
            checkObjectGroup($params,$alert,self::PAYMENT_METHOD);
            $alert->save();
            $last_id = $alert->id;
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
                $alert = Alert::find($value['payment_id']);
                AddObject($value,$alert);
            }
            $message = "Thêm thành công";
            return response()->json(['success' => true, 'message' => $message]);
        }
    }

    public function edit(Alert $alert,PaymentItem $paymentItem)
    {
        $oldData = Alert::find($alert->id);
        $partyable_id = $oldData->partyable_id;
        $routeSubmit = route('alerts', ['alert' => $alert->id]);
        return response()->json([
            'success' => true, 
            'old' => $oldData,
            'customer_id' => $partyable_id,
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
        Alert::whereIn('id', $checkboxIds)->delete();
        $message = 'Đã xoá thành công';
        return response()->json(['success' => true, 'message' => $message]);
    }

    public function print(Request $request)
    {
        $id = $request->id;
        $address = $request->address;
        $alert = Alert::find($id[0]);
        $payment_item = PaymentItem::select('*')->where('payment_id',$id)->get();
        $sum = PaymentItem::select(DB::raw('SUM(amount) as sum'))->where('payment_id',$id)->get();
        $creditAccount = $payment_item[0]->creditAccount->account_code;
        return view('layouts.alerts.print',compact('alert','address','payment_item','sum','creditAccount'));
    }
}