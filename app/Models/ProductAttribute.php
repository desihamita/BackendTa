<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;
    protected $guarded = [];

    final public function storeAttributeData(array $input, Product $product): void
    {
        $attribute_data = $this->prepareAttributeData($input, $product);
        foreach ($attribute_data as $attribute) {
            self::create($attribute);
        }
    }
    
    private function prepareAttributeData(array $input, Product $product): array
    {
        $attribute_data = [];
        foreach ($input as $value) {
            $data['product_id'] = $product->id;
            $data['attribute_id'] = $value['attribute_id'];
            $data['attribute_value_id'] = $value['value_id'];
            $attribute_data[] = $data;
        }
        return $attribute_data;
    }
    
}