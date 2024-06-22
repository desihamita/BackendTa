<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Manager\ImageManager;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\SubCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\SubCategoryEditResource;
use Illuminate\Support\Facades\Schema;

class SubCategoryController extends Controller
{
    final public function index(Request $request): AnonymousResourceCollection
    {
        $categories = (new SubCategory())->getAllSubCategories($request->all());
        return SubCategoryResource::collection($categories);
    }

    final public function store(StoreSubCategoryRequest $request):  JsonResponse
    {
        $sub_category = $request->except('photo');
        $sub_category['slug'] = Str::slug($request->input('slug'));
        $sub_category['user_id'] = auth()->id();
        if($request->has('photo')){
            $sub_category['photo']= $this->processImageUpload($request->input('photo'), $sub_category['slug']);
        }

        (new SubCategory())->storeSubCategory($sub_category);
        return response()->json(['msg' => 'Category Created Successfully', 'cls' => 'success']);
    }

    final public function show(SubCategory $subCategory): SubCategoryEditResource
    {
        return new SubCategoryEditResource($subCategory);
    }

    final public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory)
    {
        $sub_category_data = $request->except('photo');
        $sub_category_data['slug'] = Str::slug($request->input('slug'));

        if($request->has('photo')){
            $sub_category_data['photo'] = $this->processImageUpload($request->input('photo'), $sub_category_data['slug'], $subCategory->photo);
        }

        $subCategory->update($sub_category_data);
        return response()->json(['msg' => 'Sub Category Updated Successfully', 'cls' => 'success']);
    }

    public function destroy(SubCategory $subCategory)
    {
        if (!empty($subCategory->photo)) {
            ImageManager::deletePhoto(SubCategory::IMAGE_UPLOAD_PATH, $subCategory->photo);
            ImageManager::deletePhoto(SubCategory::THUMB_IMAGE_UPLOAD_PATH, $subCategory->photo);
        }
        $subCategory->delete();
        return response()->json(['msg' => 'Sub Category deleted Successfully', 'cls' => 'warning']);
    }

    private function processImageUpload(string $photo, string $name, string|null $existing_photo = null): string
    {
        $file = $photo;
        $width = 800;
        $height = 800;
        $width_thumb = 150;
        $height__thumb = 150;
        $path = SubCategory::IMAGE_UPLOAD_PATH;
        $path_thumb = SubCategory::THUMB_IMAGE_UPLOAD_PATH;

        if (!empty($existing_photo)) {
            ImageManager::deletePhoto(SubCategory::IMAGE_UPLOAD_PATH, $existing_photo);
            ImageManager::deletePhoto(SubCategory::THUMB_IMAGE_UPLOAD_PATH, $existing_photo);
        }

        $photo_name = ImageManager::uploadImage($name, $width, $height, $path, $file);
        ImageManager::uploadImage($name, $width_thumb, $height__thumb, $path_thumb, $file);
        return $photo_name;
    }

    final public function get_Sub_category_list(int $category_id): JsonResponse
    {
        $Sub_categories = (new SubCategory())->getSubCategoryIdAndName($category_id);
        return response()->json($Sub_categories);
    }

    public function get_sub_category_column()
    {
        $columns = Schema::getColumnListing('sub_categories');
        return response()->json($columns);
    }
}