<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\AttributeListResource;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    final public function index(Request $request): AnonymousResourceCollection
    {
        $attributes = (new Attribute())->getAttributeList($request->all());
        return AttributeListResource::collection($attributes);
    }

    final public function store(StoreAttributeRequest $request): JsonResponse
    {
        $attribute_data = $request->all();
        $attribute_data['user_id'] = auth()->id();

        Attribute::create($attribute_data);

        return response()->json(['msg' => 'Attribute Created Successfully', 'cls' => 'success']);
    }

    final public function update(UpdateAttributeRequest $request, Attribute $attribute): JsonResponse
    {
        $attribute_data = $request->all();

        $attribute->update($attribute_data);

        return response()->json(['msg' => 'Attribute Updated Successfully', 'cls' => 'success']);
    }

    final public function destroy(Attribute $attribute): JsonResponse
    {
        $attribute->delete();
        return response()->json(['msg' => 'Product Attribute deleted Successfully', 'cls' => 'warning']);
    }

    final public function get_attribute_list(): JsonResponse
    {
        $attributes = (new Attribute())->getAttributeListWithValue();
        return response()->json($attributes);
    }
}