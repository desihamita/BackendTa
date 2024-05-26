<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\ImageManager;
use App\Models\Supplier;
use App\Http\Resources\AddressListResource;

class SupplierEditResource extends JsonResource
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
            'details' => $this->details,
            'status' => $this->status,
            'display_logo' => ImageManager::prepareImageUrl(Supplier::THUMB_IMAGE_UPLOAD_PATH, $this->logo),

            'address' => $this->address?->address,
            'division_id' => $this->address?->division_id,
            'district_id' => $this->address?->district_id,
            'sub_district_id' => $this->address?->sub_district_id,
            'area_id' => $this->address?->area_id,
            'landmark' => $this->address?->landmark,
        ];
    }
}