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

use App\Manager\ScriptManager;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('test', [ScriptManager::class, 'getCountry']);

Route::post('login', [AuthController::class, 'login']);

Route::get('divisions', [DivisionController::class, 'index']);
Route::get('districts/{id}', [DistrictController::class, 'index']);
Route::get('sub-districts/{id}', [SubDistrictController::class, 'index']);
Route::get('areas/{id}', [AreaController::class, 'index']);

Route::group(['middleware' => 'auth:sanctum'], static function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('get-category-list', [CategoryController::class, 'get_category_list']);
    Route::get('get-sub-category-list/{category_id}', [SubCategoryController::class, 'get_sub_category_list']);
    Route::get('get-brand-list', [BrandController::class, 'get_brand_list']);
    Route::get('get-country-list', [CountryController::class, 'get_country_list']);

    Route::apiResource('category', CategoryController::class);
    Route::apiResource('sub-category', SubCategoryController::class);
    Route::apiResource('brand', BrandController::class);
    Route::apiResource('supplier', SupplierController::class);
    Route::apiResource('attribute', AttributeController::class);
    Route::apiResource('value', AttributeValueController::class);
});