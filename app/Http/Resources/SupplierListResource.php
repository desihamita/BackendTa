<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\ImageManager;
use App\Models\Supplier;
use App\Http\Resources\AddressListResource;

class SupplierListResource extends JsonResource
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
            'status' => $this->status == Supplier::STATUS_ACTIVE ? Supplier::STATUS_ACTIVE_TEXT : Supplier::STATUS_INACTIVE_TEXT,
            'logo' => ImageManager::prepareImageUrl(Supplier::THUMB_IMAGE_UPLOAD_PATH, $this->logo),
            'logo_full' => ImageManager::prepareImageUrl(Supplier::IMAGE_UPLOAD_PATH, $this->logo),
            'created_by' => $this->user?->name,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not updated at',
            'address' => new AddressListResource($this->address),
        ];
    }
}