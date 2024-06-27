<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\ImageManager;
use App\Manager\PriceManager;
use App\Models\Attribute;

class AttributeListResource extends JsonResource
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
            'price' => PriceManager::CURRENCY_SYMBOL . number_format($this->price),
            'sku' => $this->sku,
            'stock' => $this->stock,
            'description' => $this->description,
            'status' => $this->status === 1 ? 'Active' : 'Inactive',
            'photo' => ImageManager::prepareImageUrl(Attribute::THUMB_IMAGE_UPLOAD_PATH, $this->photo),

            'category' => $this->category?->name,
            'sub_category' => $this->sub_category?->name,
            'brand' => $this->brand?->name,
            'supplier' => $this->supplier?->name,
            'created_by' => $this->created_by?->name,
            'updated_by' => $this->updated_by?->name,

            'original_status' => $this->status,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not updated at',
            'value' => ValueListResource::collection($this->value),
        ];

   }
}