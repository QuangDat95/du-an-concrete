<?php

namespace App\Http\Controllers\Concrete;
use Illuminate\Http\Request;
use App\Models\Concrete\Area;
use App\Exports\CustomerExport;
use App\Imports\ContractImport;
use App\Exports\ConstructionExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VolumeTrackingImport;
use App\Models\Concrete\VolumeTracking;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\Facades\FastExcel;

class ExportImportController extends Controller
{
   public function customerexport(Request $request){
    return Excel::download(new CustomerExport($request->all()), 'customers.xlsx');
   }

   public function constructionexport(Request $request){
    return Excel::download(new ConstructionExport($request->all()), 'constructions.xlsx');
   }

   public function volumeTrackingImport(Request $request) 
   {
       Excel::import(new VolumeTrackingImport, $request->file('file_volumetracking'));
    //    Excel::import(new ContractImport, $request->file('file_volumetracking'));
       $message = "Import thành công";
       return response()->json( array('success' => true,'message'=>$message));
   }

   public function volumetrackingexport(Request $request) {
    $filterTable = $request->filter_table;
    if($filterTable){
        $area = Area::where('name',$filterTable)->first();
        $origanzationIds = $area->origanzations->pluck('id');
        $volumeTrackings = VolumeTracking::with(['customer','contract','construction','concreteGrade','sampleage','slump','vehicle','organization','station','getQS'])->whereIn('station_id',$origanzationIds)->get();
    }else{
        $volumeTrackings = VolumeTracking::with(['customer','contract','construction','concreteGrade','sampleage','slump','vehicle','organization','station','getQS'])->get();
    }

    function usersGenerator($volumeTrackings)
    {
        foreach ($volumeTrackings as $volumeTracking) {
            yield $volumeTracking;
        }
    }
    
        return FastExcel::data(usersGenerator($volumeTrackings))->download('volume_trackings.xlsx', function ($volumeTracking) {
            return [
            'ID' => $volumeTracking->id,
            'Phân loại' => config('default.classify.'.$volumeTracking->customer->type_id),
            'Ngày' => $volumeTracking->from_date ?? '',
            'Mã hợp đồng' => $volumeTracking->contract->contract_code ?? '',
            'Tên rút gọn' => $volumeTracking->customer->name_other,
            'Tên Khách hàng' => $volumeTracking->customer->name,
            'Tên Công trình' => $volumeTracking->construction->name,
            'Hạng mục' => $volumeTracking->article ?? '',
            'Mác' => $volumeTracking->concreteGrade->name ?? '',
            'Tuổi mẫu' => $volumeTracking->sampleage->name ?? '',
            'Độ sụt' => $volumeTracking->slump->name ?? '',
            'PT đổ' => $volumeTracking->vehicle->name ?? '',
            'Thời gian bắt đầu bơm' => ($volumeTracking->pumping_time_begin != null) ? date('H:i',strtotime($volumeTracking->pumping_time_begin)) : '',
            'Thời gian kết thúc bơm' => ($volumeTracking->pumping_time_finish != null) ? date('H:i',strtotime($volumeTracking->pumping_time_finish)) : '',
            'KL thực tế' => number_format($volumeTracking->actual_weight,2),
            'KL gửi' => number_format($volumeTracking->sending_volume,2),
            'KL bị trừ' => number_format($volumeTracking->minus_volume,2),
            'KL thanh toán' => number_format($volumeTracking->payment_volume,2),
            'Đơn giá bê tông' => number_format($volumeTracking->concreate_price),
            'Đơn giá phụ gia' => number_format($volumeTracking->additive_price),
            'Đơn giá bơm' => number_format($volumeTracking->pump_price),
            'Phụ phí vận chuyển' => number_format($volumeTracking->shipping_surcharge) ?? '',
            'Phụ phí dời bơm' => number_format($volumeTracking->pump_surcharge) ?? '',
            'Thành tiền' => number_format($volumeTracking->total_price),
            'Khu vực' => $volumeTracking->organization->area->name,
            'Trạm' => $volumeTracking->station->name,
            'Kỹ thuật công trình' => $volumeTracking->engineer_id ?? '',
            'Ngày nhận' => $volumeTracking->received_date ?? '',
            'QS' => $volumeTracking->getQS->hovaten,
            'Phụ trách BH' => $volumeTracking->sale_user_id ?? '',
            'Giới thiệu' => $volumeTracking->introduce ?? '',
            'Hoa hồng' => $volumeTracking->tip ?? '',
            'Gửi giá bê tông' => number_format($volumeTracking->sendprice_concreate) ?? '',
            'Gửi giá bơm' => number_format($volumeTracking->sendprice_pump) ?? '',
            'Gửi giá phụ gia' => number_format($volumeTracking->sendprice_addditive) ?? '',
            'Ghi chú' => $volumeTracking->keep ?? ''
            ];
        });
    }
}