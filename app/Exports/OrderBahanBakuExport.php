<?php

namespace App\Exports;

use App\Models\OrderBahanaBaku;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderBahanBakuExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return OrderBahanaBaku::with('supplier')
            ->select('id', 'order_number', 'supplier_id', 'quantity', 'total', 'created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Order Number',
            'Supplier',
            'Quantity',
            'Total',
            'Created At',
        ];
    }

    public function map($order): array
    {
        static $index = 0;
        $index++;

        return [
            'No.' => $index,
            'Order Number' => $order->order_number,
            'Supplier' => optional($order->supplier)->name,
            'Quantity' => $order->quantity,
            'Total' => $order->total,
            'Created At' => $order->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
