<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DistrictController extends Controller
{
    final public function index(string $id): JsonResponse
    {
        Log::info('Received ID:', ['id' => $id]);
        $districts = (new District())->getDistrictByDivisionId($id);
        return response()->json($districts);
    }
}