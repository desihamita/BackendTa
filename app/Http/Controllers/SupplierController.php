<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Address;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Manager\ImageManager;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\SupplierListResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\SupplierEditResource;
use Illuminate\Support\Facades\Schema;

class SupplierController extends Controller
{
    final public function index(Request $request): AnonymousResourceCollection
    {
        $suppliers = (new Supplier())->getSupplierList($request->all());
        return SupplierListResource::collection($suppliers);
    }

    final public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplierData = (new Supplier())->prepareData($request->all(), auth());
        $addressData = (new Address())->prepareData($request->all());

        if($request->has('logo')) {
            $name = Str::slug($supplierData['name'] . now());
            $supplierData['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                Supplier::IMAGE_UPLOAD_PATH,
                Supplier::THUMB_IMAGE_UPLOAD_PATH,
                Supplier::LOGO_WIDTH,
                Supplier::LOGO_HEIGHT,
                Supplier::LOGO_THUMB_WIDTH,
                Supplier::LOGO_THUMB_HEIGHT
            );
        }

        try {
            DB::beginTransaction();
            $supplier = Supplier::create($supplierData);
            $supplier->address()->create($addressData);
            DB::commit();
            return response()->json(['msg' => 'Berhasil Menambahkan Data Pemasok', 'cls' => 'success']);
        } catch (\Throwable $e) {
            if (isset($supplierData['logo'])) {
                ImageManager::deletePhoto(Supplier::IMAGE_UPLOAD_PATH, $supplierData['logo']);
                ImageManager::deletePhoto(Supplier::THUMB_IMAGE_UPLOAD_PATH, $supplierData['logo']);
            }
            Log::error('SUPPLIER_STORE_FAILED', ['supplierData' => $supplierData, 'addressData' => $addressData, 'exception' => $e]);
            DB::rollback();
            return response()->json(['msg' => 'Something went wrong', 'cls' => 'warning']);
        }
    }

    final public function show(Supplier $supplier): SupplierEditResource
    {
        $supplier->load('address');
        return new SupplierEditResource($supplier);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier_data = (new Supplier())->prepareData($request->all(), auth());
        $address_data = (new Address())->prepareData($request->all());

        if($request->has('logo')) {
            $name = Str::slug($supplier_data['name'] . now());
            $supplier_data['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                Supplier::IMAGE_UPLOAD_PATH,
                Supplier::THUMB_IMAGE_UPLOAD_PATH,
                Supplier::LOGO_WIDTH,         
                Supplier::LOGO_HEIGHT,
                Supplier::LOGO_THUMB_WIDTH,
                Supplier::LOGO_THUMB_HEIGHT,
                $supplier->logo
            );
        }

        try {
            DB::beginTransaction();
            $supplier_data = $supplier->update($supplier_data);
            $supplier->address()->update($address_data);
            DB::commit();
            return response()->json(['msg' => 'Berhasil Mengubah Data Pemasok', 'cls' => 'success']);
        } catch (\Throwable $e) {
            if (isset($supplier_data['logo'])) {
                ImageManager::deletePhoto(Supplier::IMAGE_UPLOAD_PATH, $supplier_data['logo']);
                ImageManager::deletePhoto(Supplier::THUMB_IMAGE_UPLOAD_PATH, $supplier_data['logo']);
            }
            Log::error('SUPPLIER_STORE_FAILED', ['supplier data' => $supplier_data, 'address data' => $address_data, 'exception' => $e]);
            DB::rollback();
            return response()->json(['msg' => 'Ada yang salah', 'cls' => 'warning', 'flag' => 'true']);
        }
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        try {
            if (!empty($supplier->logo)) {
                ImageManager::deletePhoto(Supplier::IMAGE_UPLOAD_PATH, $supplier->logo);
                ImageManager::deletePhoto(Supplier::THUMB_IMAGE_UPLOAD_PATH, $supplier->logo);
            }
            (new Address())->deleteAddressBySupplierId($supplier);
            $supplier->delete();
            return response()->json(['msg' => 'Berhasil Menghapus Data Pemasok', 'cls' => 'success']);
        } catch (\Throwable $e) {
            Log::error('SUPPLIER_DELETE_FAILED', ['supplier' => $supplier, 'exception' => $e]);
            return response()->json(['msg' => 'Ada yang salah', 'cls' => 'warning']);
        }
    }

    final public function get_supplier_list(): JsonResponse
    {
        $suppliers = (new Supplier())->getSupplierSelectList();
        return response()->json($suppliers);
    }

    public function get_supplier_column()
    {
        $columns = Schema::getColumnListing('suppliers');
        return response()->json($columns);
    }
}