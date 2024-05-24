<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
    final public function index(int $id): JsonResponse
    {
        $areas = (new Area())->getAreaBySubDistrictId($id);
        return response()->json($areas);
    }
}