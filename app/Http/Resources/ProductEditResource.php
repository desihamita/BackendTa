<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductPhotoEditResource;
use App\Manager\PriceManager;
use App\Manager\ImageManager;
use App\Models\ProductPhoto;
use App\Models\Product;
use Carbon\Carbon;
use App\Utility\Date;

class ProductEditResource extends JsonResource
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
            'cost' => $this->cost,
            'price' => $this->price,
            'original_price' => $this->price,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'description' => $this->description,
            'status' => $this->status,

            'discount_fixed' => $this->discount_fixed,
            'discount_percent' => $this->discount_percent,
            'discount_start' => $this->discount_start,
            'discount_end' => $this->discount_end,

            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not updated at',

            'created_by' => $this->created_by?->name,
            'updated_by' => $this->updated_by?->name,

            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,

            'photo_preview' => ImageManager::prepareImageUrl(Product::THUMB_PHOTO_UPLOAD_PATH, $this->photo),

        ];

   }
}