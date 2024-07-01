<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderBahanaBakuRequest;
use App\Http\Requests\UpdateOrderBahanaBakuRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\OrderBahanaBaku;
use App\Http\Resources\OrderBahanaBakuListResource;

class OrderBahanaBakuController extends Controller
{
    public function index(Request $request)
    {
        $orders = (new OrderBahanaBaku())->getAllOrders($request->all(), auth());
        return OrderBahanaBakuListResource::collection($orders);
    }

    public function store(StoreOrderBahanaBakuRequest $request)
    {
        try {
            DB::beginTransaction();
            $orderBahanBaku = (new OrderBahanaBaku)->placeOrder($request->all(), auth()->user());
            DB::commit();
            return response()->json(['msg' => 'Order Ingredients Placed Successfully', 'cls' => 'success', 'flag' => 1, 'order_id' => $orderBahanBaku->id]);
        } catch (\Throwable $th) {
            Log::info('ORDER_BAHAN_BAKU_PLACED_FAILED', ['message' => $th->getMessage(), $th]);
            DB::rollback();
            return response()->json(['msg' => $th->getMessage(), 'cls' => 'warning']);
        }
    }

    public function show(OrderBahanaBaku $orderBahanaBaku)
    {
        // Implement show functionality if needed
    }

    public function update(UpdateOrderBahanaBakuRequest $request, OrderBahanaBaku $orderBahanaBaku)
    {
        // Implement update functionality if needed
    }

    public function destroy(OrderBahanaBaku $orderBahanaBaku)
    {
        // Implement destroy functionality if needed
    }
}