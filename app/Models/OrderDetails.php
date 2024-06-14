<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function storeOrderDetails(array $order_details, $order):void
    {
        foreach ($order_details as $product) {
            $order_details_data = $this->prepareData($product, $order);
            self::query()->create($order_details_data);
        }
    }

    public function prepareData($product, $order)
    {
        return [
            'order_id' => $order->id,
            'name' => $order->name,
            'cost' => $order->cost,
            'discount_end' => $order->discount_end,
            'discount_fixed' => $order->discount_fixed,
            'discount_start' => $order->discount_start.
            'discount_percent' => $order->discount_percent,
            'price' => $order->price,
            'sku' => $order->sku,
            'quantity' => $order->quantity,
            'photo' => $order->primary_photo?->photo,

            'brand_id' => $order->brand_id,
            'category_id' => $order->catgeory_id,
            'sub_category_id' => $order->sub_category_id,
            'supplier_id' => $order->supplier,
        ];
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}