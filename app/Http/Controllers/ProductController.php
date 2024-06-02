<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductAttribute;
use App\Models\ProductSPecifications;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = (new Product())->storeProduct($request->all(), auth()->id());

            if($request->has('attributes')){
                (new ProductAttribute())->storeAttributeData($request->input('attributes'), $product);
            }

            if($request->has('specifications')){
                (new ProductSpecifications())->storeProductSpecification($request->input('specifications'), $product);
            }
            
            return response()->json(['msg' => 'Product Created Successfully', 'cls' => 'success']);
        } catch (\Throwable $e) {;
            return response()->json(['msg' => $e->getMessage(), 'cls' => 'danger']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}