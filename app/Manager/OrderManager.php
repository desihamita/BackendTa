<?php

namespace App\Manager;

use Carbon\Carbon;
use App\Models\Product;
use App\Manager\PriceManager;

class OrderManager
{
    private const ORDER_PREFIX = 'SAJ';

    public static function generateOrderNumber(int $shop_id): string {
        return self::ORDER_PREFIX . $shop_id . Carbon::now()->format('dmy') . random_int(100, 999);
    }

    public static function handleOrderData(array $input) {
        $sub_total = 0;
        $discount = 0;
        $total = 0;
        $quantity = 0;
        $order_details = [];

        if (isset($input['carts'])) {
            foreach ($input['carts'] as $key => $cart) {
                $product = (new Product())->getProductById($key);

                if ($product && $product->stock >= $cart['quantity']) {
                    $price = PriceManager::calculate_sell_price($product->price, $product->discount_percent, $product->discount_fixed, $product->discount_start, $product->discount_end);

                    $discount += $price['discount'] * $cart['quantity'];
                    $quantity += $cart['quantity'];
                    $sub_total += $product->price * $cart['quantity'];
                    $total += $price['price'] * $cart['quantity'];

                    $product_data['stock'] = $product->stock - $cart['quantity'];
                    $product->update($product_data);
                    $product->quantity = $cart['quantity'];
                    $order_details[] = $product;
                } else {
                    info('PRODUCT_STOCK_OUT', ['product' => $product, 'cart' => $cart]);
                    return ['error_description' => $product->name . ' Product stock out or not exist'];
                }
            }
        }

        return [
            'sub_total' => $sub_total,
            'discount' => $discount,
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