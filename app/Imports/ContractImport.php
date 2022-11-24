<?php

namespace App\Imports;

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
use DB;
class ContractImport implements ToCollection, WithHeadingRow,WithBatchInserts,WithChunkReading
{
    public function collection(Collection $rows)
    {
        $rows = $rows->toArray();
        foreach ($rows as $row) 
        {
        $sale_user_id = 257;
         Contract::create([
                'contract_code' => $row['so_hop_dong'] ?? null,
                'customer_id' => Customer::select('id')->where('name_other','like','%'.trim($row['ma_kh']).'%')->first()->id ?? null,
                'construction_id' => $row['ma_cong_trinh'],
                'payment_condition_id' => PaymentCondition::select('id')->where('name','like','%'.trim($row['dieu_kien_thanh_toan']).'%')->first()->id,
                'contract_date' => ($row['ngay_hop_dong'] != null) ? formatDateExcel(trim($row['ngay_hop_dong'])) : null,
                'due_date' =>  ($row['ngay_nhan_hop_dong'] != null) ? formatDateExcel(trim($row['ngay_nhan_hop_dong'])) : null,
                'sale_user_id' => $sale_user_id
             ]);
        }
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function chunkSize(): int
    {
        return 50;
    }
}