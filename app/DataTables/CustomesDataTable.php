<?php

namespace App\DataTables;

use App\Models\Concrete\Customer;
use Yajra\DataTables\Services\DataTable;

class CustomesDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('checkbox', function ($model) {
                return addcolumn($model);
            })->rawColumns(['checkbox']);
    }

    public function query(Customer $model)
    {
        return $model->newQuery();
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
                'name'  => ['title'=> 'Tên khách hàng'],
                'address' => ['title'=> 'Địa chỉ'],
            ])->parameters($params);
    }
}