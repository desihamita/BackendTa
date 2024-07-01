<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TransactionBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'trxingredients';
    protected $guarded = [];

    public const CREDIT = 1;
    public const DEBIT = 2;
    public const STATUS_SUCCESS = 1;
    public const STATUS_FAILED = 1;

    public function storeTransaction($input, $orderBahanBaku, $auth)
    {
        $transaction_bahan_baku_data = $this->prepareData($input, $orderBahanBaku, $auth);
        return self::query()->create($transaction_bahan_baku_data);
    }

    public function prepareData($input, $orderBahanBaku, $auth)
    {
        Log::info('Order Bahan Baku:', ['orderBahanBaku' => $orderBahanBaku]);

        return [
            'order_bahan_baku_id' => is_object($orderBahanBaku) ? $orderBahanBaku->id : 0,
            'transaction_type' => self::CREDIT,
            'status' => self::CREDIT,
            'trxIngredients_id' => $input['order_summary']['trxIngredients_id'],
            'supplier_id' => $input['order_summary']['supplier_id'],
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payment_method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

}