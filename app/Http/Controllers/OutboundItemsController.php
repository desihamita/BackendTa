<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOutboundItemsRequest;
use App\Http\Requests\UpdateOutboundItemsRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\OutboundItems;
use App\Http\Resources\OutboundItemsListResource;

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

            $outboundItem = (new OutboundItems)->placeOutboundItem($request->all(), auth()->user());

            DB::commit();

            return response()->json(['msg' => 'Outbound Item Placed Successfully', 'cls' => 'success', 'flag' => 1, 'item_id' => $outboundItem->id]);
        } catch (\Throwable $th) {
            Log::info('OUTBOUND_ITEM_PLACED_FAILED', ['message' => $th->getMessage(), $th]);
            DB::rollback();
            return response()->json(['msg' => $th->getMessage(), 'cls' => 'warning']);
        }
    }

    public function show(OutboundItems $outboundItem)
    {
        $outboundItem->load([
            'sales_manager',
            'shop',
            'order_details',
        ]);
        return new OutboundItemDetailsResource($outboundItem);
    }

    public function update(UpdateOutboundItemsRequest $request, OutboundItems $outboundItem)
    {
        // Implement update functionality if needed
    }

    public function destroy(OutboundItems $outboundItem)
    {
        // Implement destroy functionality if needed
    }
}