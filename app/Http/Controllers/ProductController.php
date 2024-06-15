<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductAttribute;
use App\Models\ProductSPecifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductListResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ProductListForBarcodeResource;

class ProductController extends Controller
{
    final public function index(Request $request): AnonymousResourceCollection
    {
        $products = (new Product())->getProductList($request);
        return ProductListResource::collection($products);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = (new Product())->storeProduct($request->all(), auth()->id());

            if($request->has('attributes')){
                (new ProductAttribute())->storeAttributeData($request->input('attributes'), $product);
            }

            if($request->has('specifications')){
                (new ProductSpecifications())->storeProductSpecification($request->input('specifications'), $product);
            }

            DB::commit();
            return response()->json(['msg' => 'Product Created Successfully', 'cls' => 'success', 'product_id' => $product->id]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['msg' => $e->getMessage(), 'cls' => 'error']);
        }
    }

    public function show(Product $product)
    {
        //
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    public function destroy(Product $product)
    {
        //
    }

    public function get_product_list_for_barcode(Request $request)
    {
        $products = (new Product())->getProductForBarcode($request->all());
        return ProductListForBarcodeResource::collection($products);
    }
}