<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CustomerDetailsResource;
use App\Http\Resources\PaymentMethodDetailsResource;
use App\Http\Resources\SalesManagerListResource;
use App\Http\Resources\OrderDetailsListResource;
use App\Models\Order;

class OrderDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payment_status = 'Unpaid';

        if($this->payment_status == Order::PAYMENT_STATUS_PAID) {
            $payment_status = 'Paid';
        } else if($this->payment_status == Order::PAYMENT_STATUS_PARTIALLY_PAID) {
            $payment_status = 'Partially Paid';
        }

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'discount' => number_format($this->discount),
            'paid_amount' => $this->paid_amount,
            'due_amount' => $this->due_amount,
            'quantity' => $this->quantity,
            'sub_total' => number_format($this->sub_total),
            'total' => $this->total,

            'customer' => new CustomerDetailsResource($this->customer),
            'payment_method' => new PaymentMethodDetailsResource($this->payment_method),
            'sales_manager' => new SalesManagerListResource($this->sales_manager),
            'shop' => new ShopListResource($this->shop),
            'order_details' => OrderDetailsListResource::collection($this->order_details),
            'transactions' => TransactionListResource::collection($this->transactions),

            'order_status' => $this->order_status,
            'order_status_string' => $this->order_status === Order::STATUS_COMPLETED ? 'Completed' : 'Pending',
            'payment_status' => $payment_status,

            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not Updated',
        ];
    }
}
