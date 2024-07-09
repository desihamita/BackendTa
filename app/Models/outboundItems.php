<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Manager\OutboundItemsManager;
use Illuminate\Support\Facades\Log;

class OutboundItems extends Model
{
    use HasFactory;

    protected $table = 'outbound_items';
    protected $guarded = [];

    protected $fillable = [
        'quantity',
        'date',
        'keterangan',
        'sales_manager_id',
        'shop_id'
    ];

    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSED = 2;
    public const STATUS_COMPLETED = 3;
    public const SHIPMENT_STATUS_COMPLETED = 1;
    public const PAYMENT_STATUS_PAID = 1;
    public const PAYMENT_STATUS_PARTIALLY_PAID = 2;
    public const PAYMENT_STATUS_UNPAID = 3;

    public function getAllOutboundItems(array $input, $auth)
    {
        $per_page = $input['per_page'] ?? 10;
        $query = self::query();

        if (!empty($input['search'])) {
            $query->where('quantity', 'like', '%'.$input['search'].'%')
                  ->orWhere('date', 'like', '%'.$input['search'].'%')
                  ->orWhere('keterangan', 'like', '%'.$input['search'].'%');;
        }

        if (!empty($input['order_by'])) {
            $query->orderBy($input['order_by'], $input['direction'] ?? 'asc');
        }

        return $query->with('sales_manager:id,name','shop:id,name',)->paginate($per_page);
    }

    public function placeOutboundItem(array $input, $auth)
    {
        $outbound_item_data = $this->prepareData($input, $auth);

        if (isset($outbound_item_data['error_description'])) {
            return $outbound_item_data;
        }

        $outboundItem = self::query()->create($outbound_item_data['item_data']);

        (new OutboundItemDetails())->storeOutboundItemDetails($outbound_item_data['item_details'], $outboundItem);
    }

    public function prepareData(array $input, $auth)
    {
        $item = OutboundItemsManager::handleItemData($input);

        if (isset($item['error_description'])) {
            return $item;
        } else {
            $item_data = [
                'sales_manager_id' => $auth->id,
                'shop_id' => $auth->shop_id,
                'quantity' => $input['quantity'],
                'date' => $input['date'],
                'keterangan' => $input['keterangan'],
            ];

            return ['item_data' => $item_data, 'item_details' => $item['item_details']];
        }
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
