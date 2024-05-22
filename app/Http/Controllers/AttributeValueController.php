<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use Illuminate\Http\JsonResponse;

class AttributeValueController extends Controller
{
    final public function store(StoreAttributeValueRequest $request): JsonResponse
    {
        $value_data = $request->all();
        $value_data['user_id'] = auth()->id();

        AttributeValue::create($value_data);

        return response()->json(['msg' => 'Value Created Successfully', 'cls' => 'success']);
    }

    final public function update(UpdateAttributeValueRequest $request, AttributeValue $value): JsonResponse
    {
        $value_data = $request->all();
        $value->update($value_data);
        return response()->json(['msg' => 'Value Updated Successfully', 'cls' => 'success']);
    }

    final public function destroy(AttributeValue $value): JsonResponse
    {
        $value->delete();
        return response()->json(['msg' => 'Value Deleted Successfully', 'cls' => 'warning']);
    }
}
