<?php

namespace App\Manager;
use Intervention\Image\Facades\Image;

class ImageUploadManager {

    final public static function uploadImage(string $name, int $width, int $height, string $path, string $file):string
    {
        $image_file_name = $name.'.webp';
        Image::make($file)->fit($width, $height)->save(public_path($path).$image_file_name, 50, 'webp');
        return $image_file_name;
    }

    final public static function deletePhoto($path, $img): void
    {
        $path = public_path($path) . $img;
        if ($img !== '' && file_exists($path)) {
            unlink($path);
        }
    }
}