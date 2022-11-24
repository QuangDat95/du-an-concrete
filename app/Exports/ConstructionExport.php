<?php

namespace App\Exports;
use App\Models\Concrete\Construction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ConstructionExport implements FromQuery, WithMapping, WithHeadings
{
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function map($construction): array
    {
        if($construction->id)
            return [
                $construction->id,
                $construction->name,
                $construction->address
            ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên công trình',
            'Địa chỉ'
        ];
    }

    public function query()
    {
            return Construction::query();
    }
}
