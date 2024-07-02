<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Manager\PriceManager;

class OrderBahanBakuDetails extends Model
{
    use HasFactory;

    protected $table = 'order_ingredient_details';
    protected $guarded = [];

    public function storeOrderDetails(array $orderBahanBaku_details, $orderBahanBaku) {
        foreach ($orderBahanBaku_details as $attribute) {
            $orderBahanBaku_details = $this->prepareData($attribute, $orderBahanBaku);
            self::query()->create($orderBahanBaku_details);
        }
    }

    public function prepareData($attribute, $orderBahanBaku) {
        $sale_price_data = PriceManager::calculate_sell_price_bahan_baku($attribute->price);

        return [
            'order_ingredients_id' => $orderBahanBaku->id,
            'name' => $attribute->name,
            'price' => $attribute->price,
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