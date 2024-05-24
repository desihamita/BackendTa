<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DistrictController extends Controller
{
    final public function index(int $id): JsonResponse
    {
        $districts = (new District())->getDistrictByDivisionId($id);
        return response()->json($districts);
    }
}