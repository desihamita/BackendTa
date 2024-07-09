<?php
namespace App\Manager;

use Carbon\Carbon;
use App\Models\Attribute;
use App\Models\OutboundItems;

class OutboundItemsManager
{
    private const ITEM_PREFIX = 'OUT';

    public static function generateOrderNumber(int $shop_id): string
    {
        return self::ITEM_PREFIX . $shop_id . Carbon::now()->format('dmy') . random_int(100, 999);
    }

    public static function handleItemData(array $input) {
        $quantity = 0;
        $item_details = [];

        if (isset($input['items'])) {
            foreach ($input['items'] as $key => $item) {
                $attribute = (new Attribute())->getAttributeById($key);

                if ($attribute && $attribute->stock >= $item['quantity']) {
                    $quantity += $item['quantity'];

                    $attribute_data['stock'] = $attribute->stock - $item['quantity'];
                    $attribute->update($attribute_data);
                    $attribute->quantity = $item['quantity'];
                    $item_details[] = $attribute;

                } else {
                    Log::info('ATTRIBUTE_STOCK_OUT', ['attribute' => $attribute, 'item' => $item]);
                    return ['error_description' => $attribute->name . ' stok habis atau tidak tersedia'];
                }
            }
        }

        return [
            'quantity' => $quantity,
            'item_details' => $item_details,
        ];
    }
}