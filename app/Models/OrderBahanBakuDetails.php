<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Manager\PriceManager;

class OrderBahanBakuDetails extends Model
{
    use HasFactory;

    protected $table = 'order_bahan_baku_details';
    protected $guarded = [];

    public function storeOrderDetails(array $order_bahan_baku_details, $orderBahanBaku) {
        foreach ($order_bahan_baku_details as $attribute) {
            $order_bahan_baku_details_data = $this->prepareData($attribute, $orderBahanBaku);
            self::query()->create($order_bahan_baku_details_data);
        }
    }

    public function prepareData($attribute, $orderBahanBaku) {
        $sale_price_data = PriceManager::calculate_sell_price_bahan_baku($attribute->price);

        return [
            'orderBahanBaku_id' => $orderBahanBaku->id,
            'name' => $attribute->name,
            'price' => $attribute->price,
            'sale_price' => $sale_price_data['price'],
            'sku' => $attribute->sku,
            'quantity' => $attribute->quantity,
            'photo' => $attribute->photo,
            'category_id' => $attribute->category_id,
            'sub_category_id' => $attribute->sub_category_id,
            'brand_id' => $attribute->brand_id,
            'supplier_id' => $attribute->supplier_id,
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
