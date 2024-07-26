<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\AttributeListResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\attributeEditResource;
use App\Http\Resources\attributeDetailsResource;
use App\Manager\ImageManager;
use App\Http\Resources\AttributeListForBarcodeResource;

class AttributeController extends Controller
{
    final public function index(Request $request): AnonymousResourceCollection
    {
        $attributes = (new Attribute())->getAttributeList($request->all());
        return AttributeListResource::collection($attributes);
    }

    public function store(StoreAttributeRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $attribute = (new Attribute())->storeAttribute($request->all(), auth()->id());
            DB::commit();
            return response()->json(['msg' => 'Berhasil Menambahkan Data Bahan Baku', 'cls' => 'success', 'attribute_id' => $attribute->id]);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['msg' => $e->getMessage(), 'cls' => 'error']);
        }
    }

    public function show(Attribute $attribute)
    {
        $attribute->load([
            'category:id,name',
            'sub_category:id,name',
            'brand:id,name',
            'supplier:id,name',
            'created_by:id,name',
            'updated_by:id,name',
        ]);

        return [
            'edit' => new attributeEditResource($attribute),
            'details' => new attributeDetailsResource($attribute),
        ];
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        $attribute_data = $request->validated();

        if ($request->has('photo')) {
            $name = Str::slug($attribute_data['name'] . now());
            $attribute_data['photo'] = ImageManager::processImageUpload(
                $request->input('photo'),
                $name,
                Attribute::PHOTO_UPLOAD_PATH,
                Attribute::THUMB_PHOTO_UPLOAD_PATH,
                Attribute::PHOTO_WIDTH,
                Attribute::PHOTO_HEIGHT,
                Attribute::PHOTO_THUMB_WIDTH,
                Attribute::PHOTO_THUMB_HEIGHT,
                $attribute->photo
            );
        }

        $attribute->update($attribute_data);

        return response()->json(['msg' => 'Berhasil Mengubah Data Bahan Baku', 'cls' => 'success']);
    }

    final public function destroy(Attribute $attribute): JsonResponse
    {
        if (!empty($attribute->photo)) {
            ImageManager::deletePhoto(Attribute::IMAGE_UPLOAD_PATH, $attribute->photo);
            ImageManager::deletePhoto(Attribute::THUMB_IMAGE_UPLOAD_PATH, $attribute->photo);
        }
        $attribute->delete();
        return response()->json(['msg' => 'Berhasil Menghapus Data Bahan Baku', 'cls' => 'warning']);
    }

    final public function get_attribute_list(): JsonResponse
    {
        $attributes = (new Attribute())->getAttributeListWithValue();
        return response()->json($attributes);
    }

    public function get_bahan_baku_list_for_barcode(Request $request)
    {
        $attributes = (new Attribute())->getBahanBakuForBarcode($request->all());
        return AttributeListForBarcodeResource::collection($attributes);
    }

    public function get_attribute_column()
    {
        $columns = Schema::getColumnListing('categories');
        $formated_columns = [];
        foreach ($columns as $column) {
            $formated_columns[] = ['id' => $column, 'name' => ucfirst(str_replace('_', ' ', $column))];
        }
        return response()->json($formated_columns);
    }
}