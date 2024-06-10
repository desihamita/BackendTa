<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\ImageManager;
use App\Manager\PriceManager;
use App\Models\ProductPhoto;
use App\Models\Product;
use Carbon\Carbon;
use App\Http\Resources\ProductAttributeListResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'cost' => PriceManager::CURRENCY_SYMBOL . $this->cost,
            'price' => PriceManager::CURRENCY_SYMBOL . $this->price,
            'sall_price' => PriceManager::calculate_sell_price($this->price, $this->discount_percent, $this->discount_fixed, $this->discount_start, $this->discount_end),
            'sku' => $this->sku,
            'stock' => $this->stock,
            'description' => $this->description,
            'status' => $this->status == Product::STATUS_ACTIVE ? 'Active' : 'Inactive',

            'discount_fixed' => PriceManager::CURRENCY_SYMBOL . $this->discount_fixed,
            'discount_percent' => $this->discount_percent. '%',
            'discount_start' => $this->discount_start !== null ? Carbon::Create($this->discount_start)->toDayDateTimeString() : null,
            'discount_end' => $this->discount_end !== null ? Carbon::create($this->discount_end)->toDayDateTimeString() : null,

            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not updated at',

            'brand' => $this->brand?->name,
            'category' => $this->category?->name,
            'sub_category' => $this->sub_category?->name,
            'country' => $this->country?->name,
            'supplier' =>  $this->supplier ? $this->supplier?->name . ' ' .$this->supplier?->phone : null,
            'created_by' => $this->created_by?->name,
            'updated_by' => $this->updated_by?->name,
            'primary_photo' => ImageManager::prepareImageUrl(ProductPhoto::THUMB_PHOTO_UPLOAD_PATH, $this->primary_photo?->photo ?? ''),

            'attributes' => ProductAttributeListResource::collection($this->product_attributes),
        ];
    }
}