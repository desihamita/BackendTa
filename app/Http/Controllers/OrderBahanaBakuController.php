<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderBahanaBakuRequest;
use App\Http\Requests\UpdateOrderBahanaBakuRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\OrderBahanaBaku;
use App\Http\Resources\OrderBahanBakuListResource;
use App\Http\Resources\OrderBahanBakuDetailsResource;
use App\Exports\OrderBahanBakuExport;
use Maatwebsite\Excel\Facades\Excel;

class OrderBahanaBakuController extends Controller
{
    public function index(Request $request)
    {
        $orderIngredients = (new OrderBahanaBaku())->getAllOrders($request->all(), auth());
        return OrderBahanBakuListResource::collection($orderIngredients);
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

    public function show(OrderBahanaBaku $orderBahanBaku) {
        $orderBahanBaku->load([
            'supplier',
            'payment_method',
            'sales_manager',
            'shop',
            'order_details.brand',
            'order_details.category',
            'order_details.sub_category',
            'order_details.supplier',
            'transactions',
            'transactions.supplier',
            'transactions.payment_method',
            'transactions.transactionable',
        ]);
        return new OrderBahanBakuDetailsResource($orderBahanBaku);
    }

    public function exportBahanBaku()
    {
        return Excel::download(new OrderBahanBakuExport, 'PesananBahanBaku.xlsx');
    }
}