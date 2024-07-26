<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Http\Request;
use App\Http\Resources\OrderListResource;
use App\Http\Resources\OrderDetailsResource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exports\OrdersExport;

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
            return response()->json(['msg' => 'Berhasil Membuat Pesanan', 'cls' => 'success', 'flag' => 1, 'order_id' => $order->id]);
        } catch (\Throwable $th) {
            Log::info('ORDER_PLACED_FAILED', ['message' => $th->getMessage(),$th]);
            DB::rollback();
            return response()->json(['msg' => $th->getMessage(), 'cls' => 'warning']);
        }
    }

    public function show(Order $order) {
        $order->load([
            'customer',
            'payment_method',
            'sales_manager',
            'shop',
            'order_details',
            'transactions',
            'transactions.customer',
            'transactions.payment_method',
            'transactions.transactionable',
        ]);
        return new OrderDetailsResource($order);
    }

    public function exportOrders()
    {
        return Excel::download(new OrdersExport, 'PesananProduk.xlsx');
    }
}