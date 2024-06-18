<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Http\Request;
use App\Http\Resources\OrderListResource;

class OrderController extends Controller
{
    public function index(Request $request) {
        $orders = (new Order())->getAllOrders($request->all(), auth());
        return OrderListResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = (new Order)->placeOrder($request->all(), auth()->user());
            DB::commit();
            return response()->json(['msg' => 'Order Placed Successfully', 'cls' => 'success', 'flag' =>1]);
        } catch (\Throwable $th) {
            Log::error('ORDER_PLACED_FAILED', ['message' => $th->getMessage(),$th]);
            DB::rollback();
            return response()->json(['msg' => $th->getMessage(), 'cls' => 'warning']);
        }
    }

    public function show(Order $order) {
        $order->load(['customer', 'payment_method', 'sales_manager', 'shop', 'order_details']);
        return new OrderDetailsResource($order);
    }

    public function update(UpdateOrderRequest $request, Order $order) {
        // Update order logic
    }

    public function destroy(Order $order) {
        // Delete order logic
    }
}