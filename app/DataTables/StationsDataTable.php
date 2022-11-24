<?php

namespace App\DataTables;

use App\Models\Concrete\Organization;
use Yajra\DataTables\Services\DataTable;

class StationsDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('checkbox', function ($model) {
                return addcolumn($model);
            })->editColumn('parent_id', function ($model) {
                $name = $model->select('name')->where('id',$model->parent_id)->get();
                return $name[0]->name;
            })->rawColumns(['checkbox']);
    }

    public function query(Organization $model)
    {
        $query = $model->with('area:id,name')->where('area_id','!=',null);
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
                'name'  => ['title'=> 'Tên trạm'],
                'area.name'  => ['title'=> 'Khu vực'],
                'parent_id' => ['title' => 'Công ty quản lý']
            ])->parameters($params);
    }
}
