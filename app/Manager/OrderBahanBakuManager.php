<?php

namespace App\Manager;

use Carbon\Carbon;
use App\Models\Attribute;
use App\Manager\PriceManager;

class OrderBahanBakuManager
{
    private const ORDER_PREFIX = 'SAJ';

    public static function generateOrderNumber(int $shop_id): string {
        return self::ORDER_PREFIX . $shop_id . Carbon::now()->format('dmy') . random_int(100, 999);
    }

    public static function handleOrderData(array $input) {
        $sub_total = 0;
        $total = 0;
        $quantity = 0;
        $order_details = [];

        if (isset($input['carts'])) {
            foreach ($input['carts'] as $key => $cart) {
                $attribute = (new Attribute())->getAttributeById($key);

                if ($attribute && $attribute->stock >= $cart['quantity']) {
                    $price = PriceManager::calculate_sell_price_bahan_baku($attribute->price);

                    $quantity += $cart['quantity'];
                    $sub_total += $attribute->price * $cart['quantity'];
                    $total += $price['price'] * $cart['quantity'];

                    $attribute_data['stock'] = $attribute->stock + $cart['quantity'];
                    $attribute->update($attribute_data);
                    $attribute->quantity = $cart['quantity'];
                    $order_details[] = $attribute;
                } else {
                    info('ATTRIBUTE_STOCK_OUT', ['attribute' => $attribute, 'cart' => $cart]);
                    return ['error_description' => $attribute->name . ' Ingedients stock out or not exist'];
                }
            }
        }

        return [
            'sub_total' => $sub_total,
            'total' => $total,
            'quantity' => $quantity,
            'order_details' => $order_details,
        ];
    }

    public static function decidePaymentStatus(int $amount, int $paid_amount) {
        if ($amount <= $paid_amount) {
            return 1; // Paid
        } elseif ($paid_amount <= 0) {
            return 3; // Unpaid
        } else {
            return 2; // Partially Paid
        }
    }
}