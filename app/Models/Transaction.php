<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const CREDIT = 1;
    public const DEBIT = 2;
    public const STATUS_SUCCESS = 1;
    public const STATUS_FAILED = 1;

    public function storeTransaction($input, $order, $auth)
    {
        $transaction_data = $this->prepareData($input, $order, $auth);
        return self::query()->create($transaction_data);
    }

    public function prepareData($input, $order, $auth)
    {
        return [
            'order_id' => $order->id ?? 0,
            'transaction_type' => self::CREDIT,
            'status' => self::CREDIT,

            'trx_id' => $input['order_summary']['trx_id'],
            'customer_id' => $input['order_summary']['customer_id'],
            'amount' => $input['order_summary']['paid_amount'],
            'payment_method_id' => $input['order_summary']['payment_method_id'],

            'transactionable_type' => SalesManager::class,
            'transactionable_id' => $auth->id,
        ];
    }

    final public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}