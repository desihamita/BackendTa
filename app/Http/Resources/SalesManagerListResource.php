<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AddressListResource;
use App\Manager\ImageManager;
use App\Models\SalesManager;

class SalesManagerListResource extends JsonResource
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
            'status' => $this->status == SalesManager::STATUS_ACTIVE ? SalesManager::STATUS_ACTIVE_TEXT : SalesManager::STATUS_INACTIVE_TEXT,

            'photo' => ImageManager::prepareImageUrl(SalesManager::THUMB_PHOTO_UPLOAD_PATH, $this->photo),
            'photo_full' => ImageManager::prepareImageUrl(SalesManager::PHOTO_UPLOAD_PATH, $this->photo),

            'created_by' => $this->user?->name,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : 'Not updated at',
            
            'address' => new AddressListResource($this->address),
            'shop' =>$this->shop?->name,
        ];
    }
}