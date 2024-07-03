<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::with('sales_managers')
            ->select('id', 'order_number', 'sales_manager_id', 'discount', 'quantity', 'total', 'sub_total', 'created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Order Number',
            'Karyawan',
            'Discount',
            'Quantity',
            'Total',
            'Sub Total',
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
            'Karyawan' => optional($order->sales_manager)->name,
            'Discount' => $order->discount,
            'Quantity' => $order->quantity,
            'Total' => $order->total,
            'Sub Total' => $order->sub_total,
            'Created At' => $order->created_at->format('Y-m-d'),
        ];
    }
}