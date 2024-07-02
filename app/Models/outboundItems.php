<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Manager\OutboundItemsManager;
use App\Models\Transaction;
use App\Models\Supplier;
use App\Models\TransactionOutboundItems;

class OutboundItems extends Model
{
    use HasFactory;

    protected $table = 'outbound_items';
    protected $guarded = [];

    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSED = 2;
    public const STATUS_COMPLETED = 3;
    public const SHIPMENT_STATUS_COMPLETED = 1;
    public const PAYMENT_STATUS_PAID = 1;
    public const PAYMENT_STATUS_PARTIALLY_PAID = 2;
    public const PAYMENT_STATUS_UNPAID = 3;

    public function getAllOutboundItems(array $input, $auth)
    {
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

    public function placeOutboundItem(array $input, $auth)
    {
        $outbound_item_data = $this->prepareData($input, $auth);

        if (isset($outbound_item_data['error_description'])) {
            return $outbound_item_data;
        }

        $outboundItem = self::query()->create($outbound_item_data['item_data']);
        (new OutboundItemDetails())->storeOutboundItemDetails($outbound_item_data['item_details'], $outboundItem);
        (new TransactionOutboundItems())->storeTransaction($input, $outboundItem, $auth);
        return $outboundItem;
    }

    public function prepareData(array $input, $auth)
    {
        $price = OutboundItemsManager::handleItemData($input);

        if (isset($price['error_description'])) {
            return $price;
        } else {
            $item_data = [
                'sales_manager_id' => $auth->id,
                'shop_id' => $auth->shop_id,
                'sub_total' => $price['sub_total'],
                'total' => $price['total'],
                'quantity' => $price['quantity'],
                'supplier_id' => $input['item_summary']['supplier_id'],
                'paid_amount' => $input['item_summary']['paid_amount'],
                'due_amount' => $input['item_summary']['due_amount'],
                'item_status' => self::STATUS_COMPLETED,
                'item_number' => OutboundItemsManager::generateItemNumber($auth->shop_id),
                'payment_method_id' => $input['item_summary']['payment_method_id'],
                'payment_status' => OutboundItemsManager::decidePaymentStatus($price['total'], $input['item_summary']['paid_amount']),
                'shipment_status' => self::SHIPMENT_STATUS_COMPLETED,
            ];

            return ['item_data' => $item_data, 'item_details' => $price['item_details']];
        }
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function sales_manager()
    {
        return $this->belongsTo(SalesManager::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function item_details()
    {
        return $this->hasMany(ItemDetails::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getAllItemForReport(bool $is_admin, int $sales_admin_id, array $columns = ['*'])
    {
        $query = DB::table('outbound_items')->select($columns);
        if (!$is_admin) {
            $query->where('sales_manager_id', $sales_admin_id);
        }
        $items = $query->get();
        return collect($items);
    }
}