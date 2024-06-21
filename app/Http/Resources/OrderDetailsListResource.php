<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\ImageManager;
use App\Manager\PriceManager;
use App\Models\ProductPhoto;
use Carbon\Carbon;

class OrderDetailsListResource extends JsonResource
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
            'photo'=> ImageManager::prepareImageUrl(ProductPhoto::THUMB_PHOTO_UPLOAD_PATH, $this->photo),
            'cost' => PriceManager::CURRENCY_SYMBOL . $this->cost,
            'price' => PriceManager::CURRENCY_SYMBOL . number_format($this->price),
            'sell_price' => PriceManager::calculate_sell_price($this->price, $this->discount_percent, $this->discount_fixed, $this->discount_start, $this->discount_end),
            'quantity' => $this->quantity,
            'sku' => $this->sku,

            'brand' => $this->brand_id?->name,
            'category' => $this->category?->name,
            'sub_category' => $this->sub_category?->name,
            'supplier' => $this->supplier?->name,

            'discount_fixed' => $this->discount_fixed,
            'discount_percent' => $this->discount_percent,
            'discount_start' => $this->discount_start ? Carbon::create($this->discount_start)->toDayDateTimeString() : '',
            'discount_end' => $this->discount_end ? Carbon::create($this->discount_end)->toDayDateTimeString() : '',
        ];
    }
}
