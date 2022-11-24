<?php

namespace App\Imports;

use App\Models\Concrete\VolumeTracking;
use App\Models\Concrete\Area;
use App\Models\Concrete\Customer;
use App\Models\Concrete\Construction;
use App\Models\Concrete\ConcreteGrade;
use App\Models\Concrete\Sampleage;
use App\Models\Concrete\Slump;
use App\Models\Concrete\Vehicle;
use App\Models\Concrete\SaleUser;
use App\Models\Concrete\Engineer;
use App\Models\Concrete\PaymentCondition;
use App\Models\Survey\Employee;
use App\Models\Concrete\Organization;
use App\Models\Concrete\Contract;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;

class VolumeTrackingImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    const OR_TYPE_STATION = 2;
    public function __construct()
    {
        $this->Contracts = Contract::all(['id','contract_code'])->pluck(value: 'id', key: 'contract_code');
        $this->Constructions = Construction::all(['id','name'])->pluck(value: 'id', key: 'name');
        $this->Stations = Organization::where('organization_type_id',self::OR_TYPE_STATION)->get(['id','name'])->pluck(value:'id',key:'name');
        $this->Users = Employee::all(['id','hovaten'])->pluck(value:'id', key:'hovaten');
        $this->ConcreteGrades = ConcreteGrade::all(['id','name'])->pluck(value:'id',key: 'name');
        $this->SampleAges = Sampleage::all(['id','name'])->pluck(value:'id',key: 'name');
        $this->Slumps = Slump::all(['id','name'])->pluck(value:'id',key: 'name');
        $this->Vehicles = Vehicle::all(['id','name'])->pluck(value:'id',key: 'name');
    }

    
    function paymentVolume($actual_weight, $sending_volume, $minus_volume){
        return $actual_weight + $sending_volume - $minus_volume;
    }

    function totalPrice($payment_volume, $concreate_price, $additive_price, $pump_price, $pump_surcharge, $shipping_surcharge){
        if ($pump_price > 500000) {
            $total_price = $payment_volume * ($concreate_price + $additive_price) + $pump_price + $pump_surcharge;
        } else {
            $total_price = $payment_volume * ($concreate_price + $additive_price + $pump_price) + $shipping_surcharge + $pump_surcharge;
        }
        return $total_price;
    }

    function getdueDate($code_contract,$date){
        if($code_contract != null){
            $contract_date = Contract::with('paymentCondition:id,date_owned')->find($this->Contracts[$code_contract]);
            $date_number = $contract_date->paymentCondition->date_owned;
            $due_date = formatDate(addDate(formatDateExcel($date),$date_number));
        }else{
            $due_date = formatDateExcel($date);
        }
        return $due_date;
    }
    
    public function collection(Collection $rows)
    {
        set_time_limit(0);
        DB::disableQueryLog();
        $rows = $rows->toArray();
        foreach ($rows as $row) 
        {
        $pump_surcharge = 0;
        $company_id = null;
        if(strtolower($row['ngay_nhan']) == 'gốc'){
            $nhan_ngay = ($row['ngay'] != null) ? formatDateExcel($row['ngay']) : null;
        }else{
            $nhan_ngay = formatDateExcel($row['ngay_nhan']);
        }
        $payment_volume = $this->paymentVolume(formatFloatNumber($row['kl_thuc_te']),formatFloatNumber($row['kl_gui']), formatFloatNumber($row['kl_bi_tru']));
         VolumeTracking::create([
                'contract_id' => $this->Contracts[trim($row['ma_hd'])] ?? null,
                'station_id' => $this->Stations[$row['ten_tram']],
                'company_id' => $company_id,
                'user_id' => $this->Users[trim($row['qs'])],
                'from_date' => formatDateExcel($row['ngay']),
                'due_date' => $this->getdueDate($row['ma_hd'],$row['ngay']),
                'customer_id' =>  $row['id_khach_hang'],
                'construction_id' => $this->Constructions[trim($row['ten_ct'])],
                'concrete_grade_id' => $this->ConcreteGrades[trim($row['mac'])] ?? null,
                'sampleage_id' => $this->SampleAges[$row['tuoi_mau']] ?? null,
                'slump_id' => $this->Slumps[trim($row['do_sut'])] ?? null,
                'vehicle_id' => $this->Vehicles[trim($row['pt_do'])] ?? null,
                'pumping_time_begin' => (trim($row['tg_bd_bom']) != null) ? trim($row['tg_bd_bom']) : null,
                'pumping_time_finish' => (trim($row['tg_ket_thu_bom']) != null) ? trim($row['tg_ket_thu_bom']) : null,
                'engineer_id' => $row['kt_cong_trinh'] ?? null,
                'sale_user_id' => $row['phu_trach_bh'] ?? null,
                'article' => $row['hang_muc'] ?? null,
                'actual_weight' =>  formatFloatNumber($row['kl_thuc_te']),
                'minus_volume' => formatFloatNumber($row['kl_bi_tru']),
                'sending_volume' => formatFloatNumber($row['kl_gui']),
                'payment_volume' => $payment_volume,
                'concreate_price' => formatIntNumber($row['don_gia_be_tong']),
                'sendprice_concreate' => formatIntNumber($row['gui_gia_be_tong']),
                'additive_price' => formatIntNumber($row['don_gia_phu_gia']),
                'sendprice_addditive' => formatIntNumber($row['gui_gia_phu_gia']),
                'pump_price' => formatIntNumber($row['don_gia_bom']),
                'sendprice_pump' => formatIntNumber($row['gui_gia_bom']),
                'shipping_surcharge' => formatIntNumber($row['phu_phi_van_chuyen']),
                'pump_surcharge' => $pump_surcharge,
                'introduce' => $row['gioi_thieu'],
                'tip' => formatIntNumber($row['hoa_hong']),
                'received_date' => $nhan_ngay,
                'total_price' => $this->totalPrice($payment_volume, formatIntNumber($row['don_gia_be_tong']), formatIntNumber($row['don_gia_phu_gia']), formatIntNumber($row['don_gia_bom']), $pump_surcharge, formatIntNumber($row['phu_phi_van_chuyen'])),
                'comment' => $row['ghi_chu']
             ]);
        }
    }

    public function batchSize(): int
    {
        return 600;
    }

    public function chunkSize(): int
    {
        return 600;
    }
}