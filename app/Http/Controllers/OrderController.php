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
    public function index(Request $request)
    {
        $orders = (new Order())->getAllOrders($request->all(), auth());
        return OrderListResource::collection($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        //
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'payment_method', 'sales_manager:id,name', 'shop', 'order_details']);
        return new OrderDetailsResource($order);
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    public function destroy(Order $order)
    {
        //
    }
}