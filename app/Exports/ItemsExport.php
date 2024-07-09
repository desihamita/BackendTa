<?php

namespace App\Exports;

use App\Models\OutboundItems;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ItemsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return OutboundItems::with('sales_manager')
            ->select('id', 'quantity', 'date', 'keterangan', 'sales_manager_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kauntitas',
            'Tanggal',
            'Keterangan',
            'Karyawan',
        ];
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        return [
            'No.' => $index,
            'Kuantitas' => $item->quantity,
            'Tanggal' => $item->date,
            'Keterangan' => $item->keterangan,
            'Karyawan' => optional($item->sales_manager)->name,
        ];
    }
}