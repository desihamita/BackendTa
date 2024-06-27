<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\PriceManager;
use App\Manager\ImageManager;
use App\Models\Attribute;

class attributeEditResource extends JsonResource
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
            'price' => $this->price,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'description' => $this->description,
            'status' => $this->status,
            'photo_preview' => ImageManager::prepareImageUrl(Attribute::THUMB_IMAGE_UPLOAD_PATH, $this->photo),

            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not updated at',

            'created_by' => $this->created_by?->name,
            'updated_by' => $this->updated_by?->name,

            'category_id' => $this->category_id,
            'sub_category_id' => $this->sub_category_id,
            'brand_id' => $this->brand_id,
            'supplier_id' => $this->supplier_id,
        ];
    }
}