<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Manager\OrderBahanBakuManager;
use App\Models\Transaction;
use App\Models\Supplier;
use App\Models\TransactionBahanBaku;

class OrderBahanaBaku extends Model
{
    use HasFactory;

    protected $table = 'order_ingredients';
    protected $guarded = [];

    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSED = 2;
    public const STATUS_COMPLETED = 3;
    public const SHIPMENT_STATUS_COMPLETED = 1;
    public const PAYMENT_STATUS_PAID = 1;
    public const PAYMENT_STATUS_PARTIALLY_PAID = 2;
    public const PAYMENT_STATUS_UNPAID = 3;

    public function getAllOrders(array $input, $auth) {
        $is_admin = $auth->guard('admin')->check();

        $query = self::query()->with([
            'supplier:id,name',
            'payment_method:id,name',
            'sales_manager:id,name',
            'shop:id,name',
        ]);

        if (!$is_admin) {
            $query->where('shop_id', $auth->user()->shop_id);
        }

        return $query->paginate(10);
    }

    public function placeOrder(array $input, $auth) {
        $orderBahanBaku_data = $this->prepareData($input, $auth);

        if (isset($orderBahanBaku_data['error_description'])) {
            return $orderBahanBaku_data;
        }

        $orderBahanBaku = self::query()->create($orderBahanBaku_data['order_data']);
        (new OrderBahanBakuDetails())->storeOrderDetails($orderBahanBaku_data['order_details'], $orderBahanBaku);
        (new TransactionBahanBaku())->storeTransaction($input, $orderBahanBaku, $auth);
        return $orderBahanBaku;
    }

    public function prepareData(array $input, $auth) {
        $price = OrderBahanBakuManager::handleOrderData($input);

        if (isset($price['error_description'])) {
            return $price;
        } else {
            $order_data = [
                'sales_manager_id' => $auth->id,
                'shop_id' => $auth->shop_id,
                'sub_total' => $price['sub_total'],
                'total' => $price['total'],
                'quantity' => $price['quantity'],
                'supplier_id' => $input['order_summary']['supplier_id'],
                'paid_amount' => $input['order_summary']['paid_amount'],
                'due_amount' => $input['order_summary']['due_amount'],
                'order_status' => self::STATUS_COMPLETED,
                'order_number' => OrderBahanBakuManager::generateOrderNumber($auth->shop_id),
                'payment_method_id' => $input['order_summary']['payment_method_id'],
                'payment_status' => OrderBahanBakuManager::decidePaymentStatus($price['total'], $input['order_summary']['paid_amount']),
                'shipment_status' => self::SHIPMENT_STATUS_COMPLETED,
            ];

            return ['order_data' => $order_data, 'order_details' => $price['order_details']];
        }
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function payment_method() {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function sales_manager() {
        return $this->belongsTo(SalesManager::class);
    }

    public function shop() {
        return $this->belongsTo(Shop::class);
    }

    public function order_details() {
        return $this->hasMany(OrderBahanBakuDetails::class, 'order_ingredients_id');
    }

    public function transactions() {
        return $this->hasMany(TransactionBahanBaku::class, 'order_bahan_baku_id');
    }

    public function getAllOrderForReport(bool $is_admin, int $sales_admin_id, array $columns = ['*']) {
        $query = DB::table('order_ingredients')->select($columns);
        if (!$is_admin) {
            $query->where('sales_manager_id', $sales_admin_id);
        }
        $orders = $query->get();
        return collect($orders);
    }
}
