<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Manager\ImageManager;
use App\Http\Resources\CategoryListResource;
use App\Http\Resources\CategoryEditResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;


class CategoryController extends Controller
{
    final public function index(Request $request): AnonymousResourceCollection
    {
        $categories = (new Category())->getAllCategories($request->all());
        return CategoryListResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $request->except('photo');
        $category['slug'] = Str::slug($request->input('slug'));
        $category['user_id'] = auth()->id();

        if($request->has('photo')){
            $category['photo']= $this->processImageUpload($request->input('photo'), $category['slug']);
        }

        (new Category())->storeCategory($category);
        
        return response()->json(['msg' => 'Category Created Successfully', 'cls' => 'success']);
    }

    final public function show(Category $category): CategoryEditResource
    {
        return new CategoryEditResource($category);
    }

    final public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category_data = $request->except('photo');
        $category_data['slug'] = Str::slug($request->input('slug'));

        if($request->has('photo')){
            $category_data['photo'] = $this->processImageUpload($request->input('photo'), $category_data['slug'], $category->photo);
        }

        $category->update($category_data);
        return response()->json(['msg' => 'Category Updated Successfully', 'cls' => 'success']);
    }

    final public function destroy(Category $category): JsonResponse
    {
        if (!empty($category->photo)) {
            ImageManager::deletePhoto(Category::IMAGE_UPLOAD_PATH, $category->photo);
            ImageManager::deletePhoto(Category::THUMB_IMAGE_UPLOAD_PATH, $category->photo);
        }
        $category->delete();
        return response()->json(['msg' => 'Category deleted Successfully', 'cls' => 'warning']);
    }

    final public function get_category_list(): JsonResponse
    {
        $categories = (new Category())->getCategoryIdAndName();
        return response()->json($categories);
    }

    private function processImageUpload(string $photo, string $name, string|null $existing_photo = null): string
    {
        $file = $photo;
        $width = 800;
        $height = 800;
        $width_thumb = 150;
        $height__thumb = 150;
        $path = Category::IMAGE_UPLOAD_PATH;
        $path_thumb = Category::THUMB_IMAGE_UPLOAD_PATH;

        if (!empty($existing_photo)) {
            ImageManager::deletePhoto(Category::IMAGE_UPLOAD_PATH, $existing_photo);
            ImageManager::deletePhoto(Category::THUMB_IMAGE_UPLOAD_PATH, $existing_photo);
        }

        $photo_name = ImageManager::uploadImage($name, $width, $height, $path, $file);
        ImageManager::uploadImage($name, $width_thumb, $height__thumb, $path_thumb, $file);
        return $photo_name;
    }

    public function get_category_column()
    {
        $columns = Schema::getColumnListing('categories');
        return response()->json($columns);
    }
}
