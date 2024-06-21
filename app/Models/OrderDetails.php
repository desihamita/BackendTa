<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Manager\ImageManager;
use App\Manager\PriceManager;

class OrderDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function storeOrderDetails(array $order_details, $order) {
        foreach ($order_details as $product) {
            $order_details_data = $this->prepareData($product, $order);
            self::query()->create($order_details_data);
        }
    }

    public function prepareData($product, $order) {
        return [
            'order_id' => $order->id,
            'name' => $product->name,
            'cost' => $product->cost,
            'discount_end' => $product->discount_end,
            'discount_fixed' => $product->discount_fixed,
            'discount_start' => $product->discount_start,
            'discount_percent' => $product->discount_percent,
            'price' => $product->price,
            'sale_price' => PriceManager::calculate_sell_price($product->price, $product->discount_percent, $product->discount_fixed, $product->discount_start, $product->discount_end)['price'],
            'sku' => $product->sku,
            'quantity' => $product->quantity,
            'photo' => $product->primary_photo?->photo,
            'category_id' => $product->category_id,
            'sub_category_id' => $product->sub_category_id,
        ];
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function sub_category() {
        return $this->belongsTo(SubCategory::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }
}