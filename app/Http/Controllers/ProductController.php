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
use Illuminate\Support\Facades\Schema;
use App\Http\Resources\ProductDetailsResource;

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

            DB::commit();
            return response()->json(['msg' => 'Product Created Successfully', 'cls' => 'success', 'product_id' => $product->id]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['msg' => $e->getMessage(), 'cls' => 'error']);
        }
    }

    public function show(Product $product)
    {
        $product->load([
            'category:id,name',
            'sub_category:id,name',
            'photos:id,photo,product_id',
            'created_by:id,name',
            'updated_by:id,name',
            'primary_photo',
            'product_attributes',
            'product_attributes.attributes',
            'product_attributes.values',
        ]);

        return new ProductDetailsResource($product);
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

    public function get_product_column()
    {
        $columns = Schema::getColumnListing('products');
        $formated_columns = [];
        foreach ($columns as $column) {
            $formated_columns[] = ['id' => $column, 'name' => ucfirst(str_replace('_', ' ', $column))];
        }
        return response()->json($formated_columns);
    }
}