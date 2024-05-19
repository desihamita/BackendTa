<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
    final public function index(): JsonResponse
    {
        $areas = (new Area())->getAreasList();
        return response()->json($areas);
    }
}