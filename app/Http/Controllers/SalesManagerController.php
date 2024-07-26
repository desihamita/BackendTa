<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\UpdateSalesManagerRequest;
use App\Http\Resources\SalesManagerListResource;
use App\Http\Resources\SalesManagerEditResource;
use App\Http\Requests\StoreSalesManagerRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Manager\ImageManager;
use App\Models\SalesManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Address;

class SalesManagerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $salesManagers = (new SalesManager())->getSalesManagerList($request->all());
        return SalesManagerListResource::collection($salesManagers);
    }

    public function store(StoreSalesManagerRequest $request): JsonResponse
    {
        $salesManagerData = (new SalesManager())->prepareData($request->all(), auth());
        $addressData = (new Address())->prepareData($request->all());

        if ($request->has('photo')) {
            $name = Str::slug($salesManagerData['name'] . now() . '-photo');
            $salesManagerData['photo'] = ImageManager::processImageUpload(
                $request->input('photo'),
                $name,
                SalesManager::PHOTO_UPLOAD_PATH,
                SalesManager::THUMB_PHOTO_UPLOAD_PATH,
                SalesManager::PHOTO_WIDTH,
                SalesManager::PHOTO_HEIGHT,
                SalesManager::PHOTO_THUMB_WIDTH,
                SalesManager::PHOTO_THUMB_HEIGHT
            );
        }

        if (!empty($request->input('password'))) {
            $salesManagerData['password'] = Hash::make($request->input('password'));
        } else {
            unset($salesManagerData['password']);
        }

        try {
            DB::beginTransaction();
            $salesManager = SalesManager::create($salesManagerData);
            $salesManager->address()->create($addressData);
            DB::commit();
            return response()->json(['msg' => 'Berhasil Menambahkan Data Karyawan', 'cls' => 'success']);
        } catch (\Throwable $e) {
            if (isset($salesManagerData['photo'])) {
                ImageManager::deletePhoto(SalesManager::PHOTO_UPLOAD_PATH, $salesManagerData['photo']);
                ImageManager::deletePhoto(SalesManager::THUMB_PHOTO_UPLOAD_PATH, $salesManagerData['photo']);
            }
            Log::error('SALES_MANAGER_STORE_FAILED', ['salesManagerData' => $salesManagerData, 'addressData' => $addressData, 'exception' => $e]);
            DB::rollback();
            return response()->json(['msg' => 'Ada yang salah', 'cls' => 'warning']);
        }
    }

    public function show(SalesManager $salesManager): SalesManagerEditResource
    {
        $salesManager->load('address');
        return new SalesManagerEditResource($salesManager);
    }

    public function update(UpdateSalesManagerRequest $request, SalesManager $salesManager): JsonResponse
    {
        $salesManagerData = (new SalesManager())->prepareData($request->all(), auth());
        $addressData = (new Address())->prepareData($request->all());

        if ($request->has('photo')) {
            $name = Str::slug($salesManagerData['name'] . now() . '-photo');
            $salesManagerData['photo'] = ImageManager::processImageUpload(
                $request->input('photo'),
                $name,
                SalesManager::PHOTO_UPLOAD_PATH,
                SalesManager::THUMB_PHOTO_UPLOAD_PATH,
                SalesManager::PHOTO_WIDTH,
                SalesManager::PHOTO_HEIGHT,
                SalesManager::PHOTO_THUMB_WIDTH,
                SalesManager::PHOTO_THUMB_HEIGHT
            );
        }

        try {
            DB::beginTransaction();
            $salesManager_data = $sales_manager->update($supplier_data);
            $salesManager->address()->update($address_data);
            DB::commit();
            return response()->json(['msg' => 'Berhasil Mengubah Data Karyawan', 'cls' => 'success']);
        } catch (\Throwable $e) {
            if (isset($salesManagerData['photo'])) {
                ImageManager::deletePhoto(SalesManager::PHOTO_UPLOAD_PATH, $salesManagerData['photo']);
                ImageManager::deletePhoto(SalesManager::THUMB_PHOTO_UPLOAD_PATH, $salesManagerData['photo']);
            }
            Log::error('SALES_MANAGER_UPDATE_FAILED', ['salesManagerData' => $salesManagerData, 'addressData' => $addressData, 'exception' => $e]);
            DB::rollback();
            return response()->json(['msg' => 'Ada yang salah', 'cls' => 'warning']);
        }
    }

    public function destroy(SalesManager $salesManager): JsonResponse
    {
        try {
            if(!empty($salesManager->photo)) {
                ImageManager::deletePhoto(SalesManager::PHOTO_UPLOAD_PATH, $salesManager->photo);
                ImageManager::deletePhoto(SalesManager::THUMB_PHOTO_UPLOAD_PATH, $salesManager->photo);
            }
            (new Address())->deleteAddressBySalesManagerId($salesManager);
            $salesManager->delete();
            return response()->json(['msg' => 'Berhasil Menghapus Data Karyawan', 'cls' => 'warning']);
        } catch (\Throwable $e) {
            Log::error('SALES_MANAGER_DELETE_FAILED', ['supplier' => $salesManager, 'exception' => $e]);
            return response()->json(['msg' => 'Ada yang salah', 'cls' => 'warning']);
        }
    }

    public function getSalesManagerList()
    {
        $salesManagers = (new SalesManager())->getSalesManagerIdAndName();
        return response()->json($salesManagers);

   }
}