<?php

namespace App\Http\Controllers;

use App\Models\ProductPhoto;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPhotoRequest;
use App\Http\Requests\UpdateProductPhotoRequest;
use App\Manager\ImageManager;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;

class ProductPhotoController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {

    }

    final public function store(StoreProductPhotoRequest $request, int $id): JsonResponse
    {
        if ($request->has('photos')) {
            $product = (new Product())->getProductById($id);
            if ($product) {
                foreach ($request->photos as $photo) {
                $name = Str::slug($product->slug . '-' . Carbon::now()->toDayDateTimeString().'-'.random_int(10000, 99999));
                    $photo_data['product_id'] = $id;
                    $photo_data['is_primary'] = $photo['is_primary'];

                    $photo_data['product_id'] = $id;
                    $photo_data['photo'] = $photo['photo'];
                    $photo_data['is_primary'] = $photo['is_primary'];
                    $photo_data['photo'] = ImageManager::processImageUpload(
                        $photo['photo'],
                        $name,
                        ProductPhoto::PHOTO_UPLOAD_PATH,
                        ProductPhoto::THUMB_PHOTO_UPLOAD_PATH,
                        ProductPhoto::PHOTO_WIDTH,
                        ProductPhoto::PHOTO_HEIGHT,
                        ProductPhoto::PHOTO_THUMB_WIDTH,
                        ProductPhoto::PHOTO_THUMB_HEIGHT,
                    );
                    (new ProductPhoto())->storeProductPhoto($photo_data);
                }
            }
        }
        return response()->json(['msg' => 'Product Photo Created Successfully', 'cls' => 'success']);
    }

    public function show(ProductPhoto $productPhoto)
    {
        //
    }

    public function edit(ProductPhoto $productPhoto)
    {
        //
    }

    public function update(UpdateProductPhotoRequest $request, ProductPhoto $productPhoto)
    {
        //
    }

    public function destroy(ProductPhoto $productPhoto)
    {
        //
    }
}