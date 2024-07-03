<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SalesManager;
use App\Manager\ImageManager;

class SalesManagerEditResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'status' => $this->status,
            
            'photo_preview' => ImageManager::prepareImageUrl(SalesManager::THUMB_PHOTO_UPLOAD_PATH, $this->photo),

            'address' => $this->address?->address,
            'division_id' => $this->address?->division_id,
            'district_id' => $this->address?->district_id,
            'sub_district_id' => $this->address?->sub_district_id,
            'area_id' => $this->address?->area_id,
            'landmark' => $this->address?->landmark,

            'shop_id' =>$this->shop_id,
        ];
    }
}