<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\PriceManager;
use App\Manager\ImageManager;
use App\Models\Attribute;

class attributeDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => number_format($this->price),
            'sku' => $this->sku,
            'stock' => $this->stock,
            'description' => $this->description,
            'status' => $this->status,
            'photo' => $this->photo ? ImageManager::prepareImageUrl(Attribute::THUMB_IMAGE_UPLOAD_PATH, $this->photo) : null,

            'created_at' => $this->created_at ? $this->created_at->toDayDateTimeString() : 'Unknown',
            'updated_at' => $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not updated',

            'created_by' => $this->created_by?->name,
            'updated_by' => $this->updated_by?->name,

            'category' => $this->category?->name,
            'sub_category' => $this->sub_category?->name,
            'brand' => $this->brand?->name,
            'supplier' => $this->supplier?->name,
        ];
    }
}