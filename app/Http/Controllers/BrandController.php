<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Support\Str;
use App\Manager\ImageManager;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use App\Http\Resources\BrandListResource;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BrandEditResource;
use Illuminate\Support\Facades\Schema;

class BrandController extends Controller
{
    final public function index(Request $request): AnonymousResourceCollection
    {
        $brands = (new Brand())->getAllbrands($request->all());
        return BrandListResource::collection($brands);
    }

    final public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = $request->except('logo');
        $brand['slug'] = Str::slug($request->input('slug'));
        $brand['user_id'] = auth()->id();

        if($request->has('logo')){
            $brand['logo']= $this->processImageUpload($request->input('logo'), $brand['slug']);
        }

        (new Brand())->storeBrand($brand);
        return response()->json(['msg' => 'Berhasil Menambahkan Data Merek', 'cls' => 'success']);
    }

    final public function show(Brand $brand): BrandEditResource
    {
        return new BrandEditResource($brand);
    }

    final public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $brand_data = $request->except('logo');
        $brand_data['slug'] = Str::slug($request->input('slug'));

        if($request->has('logo')){
            $brand_data['logo'] = $this->processImageUpload($request->input('logo'), $brand_data['slug'], $brand->logo);
        }

        $brand->update($brand_data);
        return response()->json(['msg' => 'Berhasil Mengubah Data Merek', 'cls' => 'success']);
    }

    final public function destroy(Brand $brand): JsonResponse
    {
        if(!empty($brand->logo)) {
            ImageManager::deletePhoto(Brand::IMAGE_UPLOAD_PATH, $brand->logo);
            ImageManager::deletePhoto(Brand::THUMB_IMAGE_UPLOAD_PATH, $brand->logo);
        }

        $brand->delete();
        return response()->json(['msg' => 'Berhasil Menghapus Data Merek', 'cls' => 'warning']);
    }

    private function processImageUpload(string $photo, string $name, string|null $existing_photo = null): string
    {
        $file = $photo;
        $width = 800;
        $height = 800;
        $width_thumb = 150;
        $height__thumb = 150;
        $path = Brand::IMAGE_UPLOAD_PATH;
        $path_thumb = Brand::THUMB_IMAGE_UPLOAD_PATH;

        if (!empty($existing_photo)) {
            ImageManager::deletePhoto(Brand::IMAGE_UPLOAD_PATH, $existing_photo);
            ImageManager::deletePhoto(Brand::THUMB_IMAGE_UPLOAD_PATH, $existing_photo);
        }

        $photo_name = ImageManager::uploadImage($name, $width, $height, $path, $file);
        ImageManager::uploadImage($name, $width_thumb, $height__thumb, $path_thumb, $file);
        return $photo_name;
    }

    final public function get_brand_list(): JsonResponse
    {
        $brands = (new Brand())->getBrandIdAndName();
        return response()->json($brands);
    }

    public function get_brand_column()
    {
        $columns = Schema::getColumnListing('brands');
        return response()->json($columns);
    }
}