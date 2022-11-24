<?php

namespace App\DataTables;

use App\Models\Concrete\Contract;
use Yajra\DataTables\Services\DataTable;

class ContractDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
        ->eloquent($query)
        ->addColumn('checkbox', function ($model) {
            return addcolumn($model);
        })->rawColumns(['checkbox']);
    }

    public function query(Contract $model)
    {
        $query = $model::with('customer:id,name');
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
            'contract_code'  => ['title'=> 'Mã hợp đồng'],
            'customer.name' => ['title' => 'Tên khách hàng'],
            'contract_date' => ['title' => 'Ngày hợp đồng'],
            'due_date' => ['title' => 'Ngày nhận hợp đồng']
        ])->parameters($params);
    }
}
