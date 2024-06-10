<?php

namespace App\Manager;

class PriceManager{
    public const CURRENCY_SYMBOL = 'Rp.';
    public const CURRENCY_NAME = 'Rupiah';

    public static function calculate_sell_price(
        int $price,
        int $discount_percent,
        int $discount_amount,
        string|null $discount_start = '',
        string|null $discount_end = '',
    ) {
        return self::calculate_price($price, $discount_percent, $discount_amount);
        if(empty($discount_start)){

        } elseif (empty($discount_end)) {

        } elseif (empty($discount_start) && empty($discount_end)) {

        }
    }

    private static function calculate_price(
        int $price,
        int $discount_percent,
        int $discount_amount,
    ): int
    {
        $temp_price = $price;
        if (!empty($discount_percent)) {
            $temp_price = ($price * $discount_amount)/100;
        }
        if (!empty($discount_amount)) {
            $temp_price -= $discount_amount;
        }
        return $price-$temp_price;
    }
}