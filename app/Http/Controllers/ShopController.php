<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\UpdateShopRequest;
use Illuminate\Support\Str;
use App\Models\Address;
use App\Manager\ImageManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ShopListResource;
use App\Http\Resources\ShopEditResource;

class ShopController extends Controller
{
    final public function index(Request $request): AnonymousResourceCollection
    {
        $shops = (new Shop())->getShopList($request->all());
        return ShopListResource::collection($shops);
    }

    final public function store(StoreShopRequest $request): JsonResponse
    {
        $shopData = (new Shop())->prepareData($request->all(), auth());
        $addressData = (new Address())->prepareData($request->all());

        if($request->has('logo')) {
            $name = Str::slug($shopData['name'] . now());
            $shopData['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                Shop::IMAGE_UPLOAD_PATH,
                Shop::THUMB_IMAGE_UPLOAD_PATH,
                Shop::LOGO_WIDTH,
                Shop::LOGO_HEIGHT,
                Shop::LOGO_THUMB_WIDTH,
                Shop::LOGO_THUMB_HEIGHT
            );
        }

        try {
            DB::beginTransaction();
            $shop = Shop::create($shopData);
            $shop->address()->create($addressData);
            DB::commit();
            return response()->json(['msg' => 'Shop Created Successfully', 'cls' => 'success']);
        } catch (\Throwable $e) {
            if (isset($shopData['logo'])) {
                ImageManager::deletePhoto(Shop::IMAGE_UPLOAD_PATH, $shopData['logo']);
                ImageManager::deletePhoto(Shop::THUMB_IMAGE_UPLOAD_PATH, $shopData['logo']);
            }

            Log::error('SHOP_STORE_FAILED', ['shopData' => $shopData, 'addressData' => $addressData, 'exception' => $e]);
            DB::rollback();
            return response()->json(['msg' => 'Something went wrong', 'cls' => 'warning']);
        }
    }

    final public function show(Shop $shop): ShopEditResource
    {
        $shop->load('address');
        return new ShopEditResource($shop);
    }

    final public function update(UpdateShopRequest $request, Shop $shop)
    {
        $shop_data = (new Shop())->prepareData($request->all(), auth());
        $address_data = (new Address())->prepareData($request->all());

        if($request->has('logo')) {
            $name = Str::slug($shop_data['name'] . now());
            $shop_data['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                Shop::IMAGE_UPLOAD_PATH,
                Shop::THUMB_IMAGE_UPLOAD_PATH,
                Shop::LOGO_WIDTH,
                Shop::LOGO_HEIGHT,
                Shop::LOGO_THUMB_WIDTH,
                Shop::LOGO_THUMB_HEIGHT,
                $shop->logo
            );
        }

        try {
            DB::beginTransaction();
            $shop_data = $shop->update($shop_data);
            $shop->address()->update($address_data);
            DB::commit();
            return response()->json(['msg' => 'Shop Updated  Successfully', 'cls' => 'success']);
        } catch (\Throwable $e) {
            Log::error('Shop_STORE_FAILED', ['shop' => $shop_data, 'address data' => $address_data, 'exception' => $e]);
            DB::rollback();
            return response()->json(['msg' => 'Something went wrong', 'cls' => 'warning', 'flag' => 'true']);
        }
    }

    final public function destroy(Shop $shop): JsonResponse
    {
        try {
            if (!empty($shop->logo)) {
                ImageManager::deletePhoto(Shop::IMAGE_UPLOAD_PATH, $shop->logo);
                ImageManager::deletePhoto(Shop::THUMB_IMAGE_UPLOAD_PATH, $shop->logo);
            }
            (new Address())->deleteAddressBySupplierId($shop);
            $shop->delete();
            return response()->json(['msg' => 'Shop Deleted Successfully', 'cls' => 'success']);
        } catch (\Throwable $e) {
            Log::error('SHOP_DELETE_FAILED', ['shop' => $shop, 'exception' => $e]);
            return response()->json(['msg' => 'Something went wrong', 'cls' => 'warning']);
        }
    }


    final public function get_shop_list(): JsonResponse
    {
        $shops = (new Shop())->getShopListIdName();
        return response()->json($shops);
    }
}