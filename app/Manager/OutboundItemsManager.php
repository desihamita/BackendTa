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
                $attribute = Attribute::find($item['attribute_id']);

                if ($attribute && $attribute->stock >= $item['quantity']) {
                    $quantity += $item['quantity'];

                    $attribute->decrement('stock', $item['quantity']);

                    $item_detail = [
                        'attribute_id' => $attribute->id,
                        'quantity' => $item['quantity'],
                    ];

                    $item_details[] = $item_detail; // Append to item_details array
                } else {
                    Log::info('ATTRIBUTE_STOCK_OUT', ['attribute' => $attribute, 'item' => $item]);
                    return ['error_description' => $attribute->name . ' stock is out or does not exist'];
                }
            }
        }

        return [
            'quantity' => $quantity,
            'item_details' => $item_details,
        ];
    }
}
