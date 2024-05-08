<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Manager\ImageUploadManager;
use App\Http\Resources\CategoryListResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = (new Category())->getAllCategories();
        return CategoryListResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = $request->except('photo');
        $category['slug'] = Str::slug($request->input('slug'));
        $category['user_id'] = auth()->id();
        if($request->has('photo')){
            $file = $request->input('photo');
            $width = 800;
            $height = 800;
            $width_thumb = 150;
            $height__thumb = 150;
            $name = Str::slug($request->input('slug'));
            $path = Category::IMAGE_UPLOAD_PATH;
            $path_thumb = Category::THUMB_IMAGE_UPLOAD_PATH;
            $category['photo'] = ImageManager::uploadImage($name, $width, $height, $path, $file);
            ImageManager::uploadImage($name, $width_thumb, $height__thumb, $path_thumb, $file);
        }
        (new Category())->storeCategory($category);
        return response()->json(['msg' => 'Category Created Successfully', 'cls' => 'success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}