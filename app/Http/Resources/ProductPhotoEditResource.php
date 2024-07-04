<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Manager\ImageManager;
use App\Models\ProductPhoto;

class ProductPhotoEditResource extends JsonResource
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
            'photo_preview' => ImageManager::prepareImageUrl(ProductPhoto::THUMB_IMAGE_UPLOAD_PATH, $this->photo)
            // 'photo' => ImageManager::prepareImageUrl(ProductPhoto::THUMB_PHOTO_UPLOAD_PATH, $this->photo),
            // 'photo_original' => ImageManager::prepareImageUrl(ProductPhoto::PHOTO_UPLOAD_PATH, $this->photo),
        ];
    }
}