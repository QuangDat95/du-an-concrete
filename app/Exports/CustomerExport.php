<?php

namespace App\Exports;
use App\Models\Concrete\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromQuery, WithMapping, WithHeadings
{
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function map($customer): array
    {
        if($customer->id)
            return [
                $customer->id,
                $customer->name,
                $customer->name_other,
                $customer->address,
                $customer->phone_director,
                $customer->phone_accountant,
                $customer->phone_qs,
                $customer->phone_cht,
                $customer->type_id,
                $customer->status_id,
                $customer->accountant_name,
                $customer->address_office
            ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên khách hàng',
            'Tên rút gọn',
            'Địa chỉ',
            'Số điện thoại GĐ/CT',
            'Số điện thoại kế toán',
            'Số điện thoại QS',
            'Số điện thoại CHT',
            'Phân loại',
            'Tình trạng',
            'Kế toán phụ trách',
            'Địa chỉ văn phòng giao dịch'
        ];
    }

    public function query()
    {
            return Customer::query();
    }
}