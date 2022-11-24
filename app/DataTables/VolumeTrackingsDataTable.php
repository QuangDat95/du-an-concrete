<?php

namespace App\DataTables;

use App\Models\Concrete\VolumeTracking;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
class VolumeTrackingsDataTable extends DataTable
{
    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
             ->addColumn('checkbox', function ($model) {
                return addcolumn($model);
            })->editColumn('total_price', function ($model) {
                return  number_format($model->total_price);
            })->editColumn('status',function($model){
                if($model->status == 1){
                    return 'Đã định khoản';
                }else{
                    return 'Chưa định khoản';
                };
            })->editColumn('vat_flag',function($model){
                if($model->vat_flag == 1){
                    return 'Có';
                }else{
                    return 'Không';
                };
            })->rawColumns(['checkbox']);
    }

    public function query(VolumeTracking $model)
    {
        $query = $model::with('customer:id,name','construction:id,name','organization.area:id,name')->select('volume_trackings.*');
        return $query->newQuery();
    }

    public function html()
    {
        $initComplete = "
                   this.api().columns(3).every( function () {
                    var that = this;
                     var areaId = '';
                        $('.filter-table-column').on('change', function () {
                               areaId = $(this).val();
                               if(areaId.length && areaId != 0){                                   
                                    that.search( areaId ).draw();                                   
                               }else{                          
                                 that.search('').draw();
                               }
                                $('.action-btns .actions-dropodown').hide();
                        });
                   });                
                ";
                if(auth()->user()->hasRole('admin|QS')){
                    $buttons =  [
                        [
                            'text' => "<i class='feather icon-plus'></i> Thêm",
                            'className' => "add-new-btn action-edit",
                        ],
                    ];
                }else{
                    $buttons =  [];
                }
        $params = getTableParameters($initComplete,$buttons);
        if(auth()->user()->hasRole('accountant')){
            return $this->builder()
            ->columns([
                'checkbox' => ['title'=>'<input id="select-all" type="checkbox" value="0">','sorting' => false,'class'=>'dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled filter-disable'],
                'id' => ['title' => 'Số phiếu'],
                'from_date'  => ['title'=> 'Ngày đổ'],
                'organization.area.name' => ['title' => 'Khu vực'],
                'customer.name'  => ['title'=> 'Khách hàng'],
                'construction.name'  => ['title'=> 'Công trình'],
                'total_price'  => ['title'=> 'Thành tiền'],
                'vat_flag' => ['title' => 'Xuất hóa đơn'],
                'status' => ['title' =>'Tình trạng']
                    ])->parameters($params);
        }else{
        return $this->builder()
            ->columns([
                'checkbox' => ['title'=>'<input id="select-all" type="checkbox" value="0">','sorting' => false,'class'=>'dt-checkboxes-cell dt-checkboxes-select-all sorting_disabled filter-disable'],
                'id' => ['title' => 'Số phiếu'],
                'from_date'  => ['title'=> 'Ngày đổ'],
                'organization.area.name' => ['title' => 'Khu vực'],
                'customer.name'  => ['title'=> 'Khách hàng'],
                'construction.name'  => ['title'=> 'Công trình'],
                'total_price'  => ['title'=> 'Thành tiền'],
                'vat_flag' => ['title' => 'Xuất hóa đơn']
            ])->parameters($params);
        }
    }
}