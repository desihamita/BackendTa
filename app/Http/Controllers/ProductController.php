<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductAttribute;
use App\Models\ProductSpecifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductListResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ProductListForBarcodeResource;
use Illuminate\Support\Facades\Schema;
use App\Http\Resources\ProductDetailsResource;
use App\Http\Resources\ProductEditResource;
use App\Manager\ImageManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

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
            'created_by:id,name',
            'updated_by:id,name',
        ]);

        return [
            'edit' => new ProductEditResource($product),
            'details' => new ProductDetailsResource($product),
        ];
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product_data = $request->validated();

        if ($request->has('photo')) {
            $name = Str::slug($product_data['name'] . now());
            $product_data['photo'] = ImageManager::processImageUpload(
                $request->input('photo'),
                $name,
                Product::PHOTO_UPLOAD_PATH,
                Product::THUMB_PHOTO_UPLOAD_PATH,
                Product::PHOTO_WIDTH,
                Product::PHOTO_HEIGHT,
                Product::PHOTO_THUMB_WIDTH,
                Product::PHOTO_THUMB_HEIGHT,
                $product->photo
            );
        }

        $product->update($product_data);

        return response()->json(['msg' => 'Product Updated Successfully', 'cls' => 'success']);
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();
            $product->delete();
            DB::commit();
            return response()->json(['msg' => 'Product Deleted Successfully', 'cls' => 'success']);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['msg' => $e->getMessage(), 'cls' => 'error']);
        }
    }

    public function get_product_list_for_barcode(Request $request)
    {
        $products = (new Product())->getProductForBarcode($request->all());
        return ProductListForBarcodeResource::collection($products);
    }

    final public function get_product_list(): JsonResponse
    {
        $products = (new Product())->getProductListWithValue();
        return response()->json($products);
    }

    public function get_product_column()
    {
        $columns = Schema::getColumnListing('products');
        $formatted_columns = [];
        foreach ($columns as $column) {
            $formatted_columns[] = ['id' => $column, 'name' => ucfirst(str_replace('_', ' ', $column))];
        }
        return response()->json($formatted_columns);
    }
}