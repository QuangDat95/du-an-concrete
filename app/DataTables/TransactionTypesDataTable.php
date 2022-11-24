<?php

namespace App\DataTables;

use App\Models\Concrete\TransactionType;
use Yajra\DataTables\Services\DataTable;

class TransactionTypesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
        ->eloquent($query)
        ->addColumn('checkbox', function ($model) {
            return addcolumn($model);
        })->rawColumns(['checkbox']);
    }

    public function query(TransactionType $model)
    {
        $query = $model->with(['debitaccount:id,account_code','creditaccount:id,account_code']);
        return $query->newQuery();
    }

    public function html()
    {
        $initComplete = '';
        if(auth()->user()->hasRole('admin|QS')){
            $buttons =  [
                [
                    'text' => "<i class='feather icon-plus'></i> Thêm",
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
                'name'  => ['title'=> 'Tên khoản mục'],
                'debitaccount.account_code' => ['title'=> 'Tài khoản ghi nợ'],
                'creditaccount.account_code' => ['title'=> 'Tài khoản ghi có'],
            ])->parameters($params);
    }
}
