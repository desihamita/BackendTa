<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class outboundItemDetails extends Model
{
    protected $guarded = [];

    public function storeOutboundItemDetails(array $order_details, $order) {
        foreach ($order_details as $product) {
            $order_details_data = $this->prepareData($product, $order);
            self::query()->create($order_details_data);
        }
    }

    public function prepareData($product, $order) {
        return [
            'order_id' => $order->id,
            'quantity' => $product->quantity,
            'date' => $product->date,
            'keterangan' => $product->keterangan,
        ];
    }
}