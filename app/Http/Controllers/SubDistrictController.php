<?php

namespace App\Http\Controllers;

use App\Models\SubDistrict;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubDistrictRequest;
use App\Http\Requests\UpdateSubDistrictRequest;
use Illuminate\Http\JsonResponse;

class SubDistrictController extends Controller
{
    final public function index(int $id): JsonResponse
    {
        $subDistricts = (new SubDistrict())->getSubDistrictByDistrictId($id);
        return response()->json($subDistricts);
    }
}