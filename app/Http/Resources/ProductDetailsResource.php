<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductPhotoListResource;
use App\Manager\PriceManager;
use App\Manager\ImageManager;
use App\Models\ProductPhoto;
use App\Models\Product;
use Carbon\Carbon;
use App\Utility\Date;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $price_manager = PriceManager::calculate_sell_price($this->price, $this->discount_percent, $this->discount_fixed, $this->discount_start, $this->discount_end);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'cost' => PriceManager::CURRENCY_SYMBOL . $this->cost,
            'profit' => $price_manager['price'] - $this->cost,
            'profit_precentage' => number_format(((($price_manager['price'] - $this->cost) / $price_manager['price'])*100), 0),
            'price' => PriceManager::CURRENCY_SYMBOL . number_format($this->price),
            'original_price' => $this->price,
            'sell_price' => $price_manager,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'description' => $this->description,
            'status' => $this->status == Product::STATUS_ACTIVE ? 'Active' : 'Inactive',

            'discount_fixed' => PriceManager::CURRENCY_SYMBOL . $this->discount_fixed,
            'discount_percent' => $this->discount_percent. '%',
            'discount_start' => $this->discount_start !== null ? Carbon::Create($this->discount_start)->toDayDateTimeString() : null,
            'discount_end' => $this->discount_end !== null ? Carbon::create($this->discount_end)->toDayDateTimeString() : null,
            'discount_remaining_days' => Date::calculate_discount_remaining_days($this->discount_end),

            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not updated at',

            'created_by' => $this->created_by?->name,
            'updated_by' => $this->updated_by?->name,

            'category' => $this->category?->name,
            'sub_category' => $this->sub_category?->name,
            'primary_photo' => ImageManager::prepareImageUrl(ProductPhoto::THUMB_PHOTO_UPLOAD_PATH, $this->primary_photo?->photo ?? ''),

            'attributes' => ProductAttributeListResource::collection($this->product_attributes),
            'photos' => ProductPhotoListResource::collection($this->photos),
        ];
    }
}