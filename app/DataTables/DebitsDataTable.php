<?php

namespace App\DataTables;

use App\Models\Concrete\Debit;
use Yajra\DataTables\Services\DataTable;

class DebitsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
        ->eloquent($query)
        ->addColumn('checkbox', function ($model) {
            return addcolumn($model);
        })
        ->addColumn('partyable_code', function ($model) {
            if($model->partyable_type == 'App\Models\Concrete\Customer'){
                return $model->customer->name_other;
            }else if($model->partyable_type == 'App\Models\Concrete\Supplier'){
                return $model->supplier->code;
            }else if($model->partyable_type == 'App\Models\Survey\Employee'){
                return $model->employee->manhanvien;
            }
        })->editColumn('amount',function($model){
            return  number_format($model->amount);
        })->editColumn('status',function($model){
            if($model->status == 1){
                return 'Đã hoàn tất';
            }else{
                return 'Chưa hoàn tất';
            };
        })->rawColumns(['checkbox']);
    }

    public function query(Debit $model)
    {
        $query = $model->with('transaction_type:id,name')->where('payment_method_id',3);
        return $query->newQuery();
    }

    public function html()
    {
        $initComplete = '';
        if(auth()->user()->hasRole('admin|QS')){
            $buttons =  [
                [
                    'text' => "<i class='feather icon-plus'></i> Thêm báo nợ",
                    'className' => "add-new-btn action-edit"
                ]
            ];
        }else{
            $buttons =  [];
        }
        $params = getTableParameters($initComplete,$buttons);
        
        return $this->builder()
            ->columns([
                        'checkbox' => ['title'=>'<input id="select-all" type="checkbox" value="0">','sorting' => false,'class'=>'dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled filter-disable'],
                        'payment_date'  => ['title'=> 'Ngày lập'],
                        'code' => ['title' => 'Số phiếu'],
                        'partyable_code' => ['title' => 'Mã đối tượng'],
                        'partyable_name' => ['title' => 'Tên đối tượng'],
                        'amount' => ['title' => 'Số tiền'],
                        'transaction_type.name' => ['title' => 'Khoản mục chi phí'],
                        'status' => ['title' => 'Tình trạng']
                    ])->parameters($params);
    }
}