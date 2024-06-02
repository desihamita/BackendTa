<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSpecifications extends Model
{
    use HasFactory;
    protected $guarded = [];

    final public function storeProductSpecification(array $input, Product $product): void
    {
        $specification_data = $this->prepareSpecificationData($input, $product);
        foreach ($specification_data as $specification) {
            self::create($specification);
        }
    }
    
    private function prepareSpecificationData(array $input, Product $product): array
    {
        $specification_data = [];
        foreach ($input as $value) {
            $data['product_id'] = $product->id;
            $data['name'] = $value['name'];
            $data['value'] = $value['value'];
            $specification_data[] = $data;
        }
        return $specification_data;
    }
}