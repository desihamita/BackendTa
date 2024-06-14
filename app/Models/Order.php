<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded =  [];

    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSED = 2;
    public const STATUS_COMPLETED = 3;
    public const SHIPMENT_STATUS_COMPLETED = 1;
    public const PAYMENT_STATUS_PAID = 1;
    public const PAYMENT_STATUS_PARTIALLY_PAID = 3;
    public const PAYMENT_STATUS_UNPAID = 3;

    public function getAllOrders(array $input, $auth)
    {
        $is_admin = $auth->guard('admin')->check();

        $query = self::query();

        $query->with([
            'customer:id,name,phone',
            'payment_method:id,name',
            'sales_manager:id,name',
            'shop:id,name'
        ]);

        if (!$is_admin) {
            $query->where('shop_id', $auth->user()->shop_id);
        }
        return $query->paginate(10);
    }

    public function customer(): BelongTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment_method(): BelongTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function sales_manager(): BelongTo
    {
        return $this->belongsTo(SalesManager::class);
    }

    public function shop(): BelongTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetails::class);
    }
}