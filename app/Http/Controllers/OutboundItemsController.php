<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOutboundItemsRequest;
use App\Http\Requests\UpdateOutboundItemsRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OutboundItems;
use App\Models\Attribute;
use App\Http\Resources\OutboundItemsListResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;

class OutboundItemsController extends Controller
{
    public function index(Request $request)
    {
        $items = (new OutboundItems())->getAllOutboundItems($request->all(), auth());
        return OutboundItemsListResource::collection($items);
    }

    public function store(StoreOutboundItemsRequest $request)
    {
        try {
            DB::beginTransaction();

            $outboundItem = (new OutboundItems())->placeOutboundItem($request->all(), auth()->user());

            $attribute = Attribute::findOrFail($request->input('attribute_id'));
            $attribute->stock -= $request->input('quantity');
            $attribute->save();

            DB::commit();
            return response()->json(['msg' => 'Permintaan Barang Keluar Berhasil', 'cls' => 'success', 'flag' => 1]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['msg' => $th->getMessage(), 'cls' => 'warning']);
        }
    }

    public function exportItems()
    {
        return Excel::download(new ItemsExport, 'BarangKeluar.xlsx');
    }
}