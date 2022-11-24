<?php

namespace App\DataTables;

use App\Models\Concrete\TransactionEntry;
use Yajra\DataTables\Services\DataTable;

class TransactionEntryDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
             ->addColumn('checkbox', function ($model) {
                return "<input value='{$model->station_id}' class='select-param' type='checkbox'>";
            })->rawColumns(['checkbox']);
    }

    public function query(TransactionEntry $model)
    {
        $query = $model->with('station:id,name','revenuedebitaccount:id,account_code','revenuecreditaccount:id,account_code','taxdebitaccount:id,account_code','taxcreditaccount:id,account_code');
        return $query->newQuery();
    }

    public function html()
    {
        $initComplete = '';
        if(auth()->user()->hasRole('admin|QS')){
            $buttons =  [
                [
                    'text' => "<i class='feather icon-plus'></i> Thêm định khoản",
                    'className' => "add-new-btn action-edit"
                ]
            ];
        }else{
            $buttons =  [];
        }
        $params = getTableParameters($initComplete,$buttons);
            return $this->builder()
            ->columns([
                        'checkbox' => [
                            'title'=>'<input id="select-all" type="checkbox" value="0">',
                            'sorting' => false,
                            'class'=>'dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled filter-disable'],
                        'station.name'  => ['title'=> 'Trạm'],
                        'revenuedebitaccount.account_code' => ['title' => 'Tài khoản ghi nợ doanh thu'],
                        'revenuecreditaccount.account_code' => ['title' => 'Tài khoản ghi có doanh thu'],
                        'vat_rate' => ['title' => 'Thuế suất (%)'],
                        'taxdebitaccount.account_code' => ['title' => 'Tài khoản ghi nợ thuế suất'],
                        'taxcreditaccount.account_code' => ['title' => 'Tài khoản ghi có thuế suất']
                    ])->parameters($params);
    }
}