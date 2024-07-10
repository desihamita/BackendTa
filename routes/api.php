<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\SubDistrictController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPhotoController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SalesManagerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\OrderBahanaBakuController;
use App\Http\Controllers\OutboundItemsController;

use App\Manager\ScriptManager;

Route::get('test', [ScriptManager::class, 'getLocationData']);

Route::post('login', [AuthController::class, 'login']);

Route::get('divisions', [DivisionController::class, 'index']);
Route::get('districts/{id}', [DistrictController::class, 'index']);
Route::get('sub-districts/{id}', [SubDistrictController::class, 'index']);
Route::get('areas/{id}', [AreaController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum', 'auth:admin']], static function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('get-category-list', [CategoryController::class, 'get_category_list']);
    Route::get('get-sub-category-list/{category_id}', [SubCategoryController::class, 'get_sub_category_list']);
    Route::get('get-brand-list', [BrandController::class, 'get_brand_list']);
    Route::get('get-supplier-list', [SupplierController::class, 'get_supplier_list']);
    Route::get('get-attribute-list', [AttributeController::class, 'get_attribute_list']);
    Route::get('get-shop-list', [ShopController::class, 'get_shop_list']);
    Route::post('product-photo-upload/{id}', [ProductPhotoController::class, 'store']);

    Route::apiResource('category', CategoryController::class);
    Route::apiResource('sub-category', SubCategoryController::class);
    Route::apiResource('brand', BrandController::class);
    Route::apiResource('supplier', SupplierController::class);
    Route::apiResource('attribute', AttributeController::class);
    Route::apiResource('value', AttributeValueController::class);
    Route::apiResource('product', ProductController::class);
    Route::apiResource('shop', ShopController::class);
    Route::apiResource('sales-manager', SalesManagerController::class);
});

Route::group(['middleware' =>  ['auth:admin,sales_manager']], static function () {
    Route::apiResource('supplier', SupplierController::class)->only('index', 'show');
    Route::apiResource('product', ProductController ::class)->only('index', 'show');
    Route::apiResource('attribute', AttributeController::class);
    Route::apiResource('customer', CustomerController ::class);
    Route::apiResource('order', OrderController::class);
    Route::apiResource('order-bahan-baku', OrderBahanaBakuController::class);
    Route::apiResource('outbound-items', OutboundItemsController::class);

    Route::get('get-sales-manager-list', [SalesManagerController::class, 'getSalesManagerList']);
    Route::get('get-shop-list', [ShopController::class, 'get_shop_list']);
    Route::get('get-attribute-list', [AttributeController::class, 'get_attribute_list']);
    Route::get('get-product-list', [ProductController::class, 'get_product_list']);

    Route::get('get-product-column', [ProductController ::class, 'get_product_column']);
    Route::get('get-category-column', [CategoryController ::class, 'get_category_column']);
    Route::get('get-sub-category-column', [SubCategoryController ::class, 'get_sub_category_column']);
    Route::get('get-brand-column', [BrandController ::class, 'get_brand_column']);
    Route::get('get-supplier-column', [SupplierController ::class, 'get_supplier_column']);
    Route::get('get-attribute-column', [AttributeController ::class, 'get_attribute_column']);
    Route::get('get-shop-column', [ShopController ::class, 'get_shop_column']);

    Route::get('get-payment-methods', [PaymentMethodController::class, 'index']);
    Route::get('get-category-list', [CategoryController::class, 'get_category_list']);
    Route::get('get-sub-category-list/{category_id}', [SubCategoryController::class, 'get_sub_category_list']);
    Route::get('get-product-list-for-barcode', [ProductController::class, 'get_product_list_for_barcode']);
    Route::get('get-bahan-baku-list-for-barcode', [AttributeController::class, 'get_bahan_baku_list_for_barcode']);

    Route::get('get-sales-reports', [SalesReportController::class, 'get_sales_reports', 'get_attribute_reports']);
    Route::get('get-attribute-reports', [SalesReportController::class, 'get_attribute_reports']);

    Route::get('/export-orders', [OrderController::class, 'exportOrders']);
    Route::get('/export-bahan-baku', [OrderBahanaBakuController::class, 'exportBahanBaku']);
    Route::get('/export-items', [OutboundItemsController::class, 'exportItems']);
});

Route::group(['middleware' =>  ['auth:sales_manager']], static function () {
    //Route::apiResource('product', ProductController::class)->only('index', 'show');
});