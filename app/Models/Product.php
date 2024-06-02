<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    final public function storeProduct(array $input, int $auth_id): mixed
    {
        return self::create($this->prepareData($input, $auth_id));
    }

    private function prepareData(array $input, int $auth_id): array
    {
        return [
            'brand_id' => $input['brand_id'] ?? '',
            'category_id' => $input['category_id'] ?? '',
            'sub_category_id' => $input['sub_category_id'] ?? '',
            'supplier_id' => $input['supplier_id'] ?? '',
            'country_id' => $input['country_id'] ?? '',
            'created_by_id' => $auth_id,
            'updated_by_id' => $auth_id,

            'name' => $input['name'] ?? '',
            'slug' => $input['slug'] ?? '',
            'status' => $input['status'] ?? '',
            'cost' => $input['cost'] ?? '',
            'price' => $input['price'] ?? '',
            'discount_end' => $input['discount_end'] ?? '',
            'discount_fixed' => $input['discount_fixed'] ?? '',
            'discount_percent' => $input['discount_percent'] ?? '',
            'discount_start' => $input['discount_start'] ?? '',
            'stock' => $input['stock'] ?? '',
            'sku' => $input['sku'] ?? '',
            'description' => $input['description'] ?? '',
        ];
    }
}