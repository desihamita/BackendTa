<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\OrderBahanaBaku;

class OrderBahanBakuListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payment_status = 'Unpaid';

        if($this->payment_status == OrderBahanaBaku::PAYMENT_STATUS_PAID) {
            $payment_status = 'Paid';
        } else if($this->payment_status == OrderBahanaBaku::PAYMENT_STATUS_PARTIALLY_PAID) {
            $payment_status = 'Partially Paid';
        }

        return [
            'id' => $this->id,

            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : '',

            'supplier_name' => $this->supplier?->name,
            'supplier_phone' => $this->supplier?->phone,
            'supplier_email' => $this->supplier?->email,

            'order_number' => $this->order_number,
            'order_status' => $this->order_status,
            'order_status_string' => $this->order_status === OrderBahanaBaku::STATUS_COMPLETED ? 'Completed' : 'Pending',

            'payment_method' => $this->payment_method?->name,
            'payment_status' => $this->payment_status,

            'sales_manager' => $this->sales_manager?->name,
            'shop' => $this->shop?->name,

            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,
            'quantity' => $this->quantity,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
        ];
    }
}