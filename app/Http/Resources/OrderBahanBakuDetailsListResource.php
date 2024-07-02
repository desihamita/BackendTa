<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\ImageManager;
use App\Manager\PriceManager;
use App\Models\Attribute;

class OrderBahanBakuDetailsListResource extends JsonResource
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
            'photo' => ImageManager::prepareImageUrl(Attribute::THUMB_IMAGE_UPLOAD_PATH, $this->photo),
            'price' => $this->price,
            'quantity' => $this->quantity,
            'sku' => $this->sku,

            'brand' => $this->brand ? $this->brand->name : null,
            'category' => $this->category ? $this->category->name : null,
            'sub_category' => $this->sub_category ? $this->sub_category->name : null,
            'supplier' => $this->supplier ? $this->supplier->name : null,
        ];
    }
}
