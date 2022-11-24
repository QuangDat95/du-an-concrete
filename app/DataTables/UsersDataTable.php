<?php

namespace App\DataTables;

use App\Models\Survey\Employee;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('checkbox', function ($model) {
                $permissionIds = $model->permissions->pluck('id');
                $permissionIds = json_encode($permissionIds);
                $roleId = $model->roles->pluck('id');
                $roleId = json_encode($roleId);
                return
                    "<input value='{$model->id}' permission='{$permissionIds}' role='{$roleId}' class='select-param' type='checkbox'>";
            })->addColumn('role', function ($user) {
                $roleName = implode(' | ',$user->roles->pluck('name')->all());
                return $roleName;
            })->addColumn('permission', function ($user) {
                $permissionName="";
                if(count($user->getAllPermissions())>0){
                    $listPermissions = $user->getAllPermissions();
                    $permissionName = "";
                    foreach($listPermissions as $permission){
                        $permissionName .= $permission['name']."<br>";
                    }
                }
                return $permissionName;
            })->rawColumns(['checkbox','permission']);
    }

    public function query(Employee $model)
    {
        return $model->where('id','!=',257)->where('email','!=',null);
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
                'checkbox' => ['sorting' => false, 'class'=>'dt-checkboxes-cell filter-disable dt-checkboxes-select-all sorting_disabled','title'=>'<input id="select-all" type="checkbox" value="0">'],
                'hovaten' => ['title' => 'Tên'],
                'email',
                'phongban' => ['title' => 'Phòng ban'],
                'role' => ['title' => 'Vai trò'],
                'permission' => ['title' => 'Sự cho phép']
            ])->parameters($params);
    }
}
