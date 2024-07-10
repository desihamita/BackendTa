<?php

namespace App\Manager;

use Illuminate\Support\Collection;
use App\Models\Attribute;
use App\Models\OrderBahanaBaku;
use Carbon\Carbon;

class ReportAttributeManager {
    public const LOW_STOCK_ALERT = 5;
    public int $total_attribute = 0;
    public int $total_stock = 0;
    public int $low_stock = 0;
    public int $buy_stock_price = 0;
    public int $sale_stock_price = 0;
    public int $possible_profit = 0;
    public int $total_sale = 0;
    public int $total_sale_today = 0;
    public int $total_purchase = 0;
    public int $total_purchase_today = 0;
    public int $total_expense = 0;

    private bool $is_admin = false;
    private int $sales_admin_id;
    private Collection $attributes;
    private Collection $orders;

    public function __construct($auth)
    {
        if ($auth->guard('admin')->check()) {
            $this->is_admin = true;
        }
        $this->sales_admin_id = $auth->user()->id;

        $this->getAttributes();
        $this->setTotalAttribute();
        $this->calculateStock();
        $this->findLowStock();
        $this->caluculateBuyStockPrice();
        $this->caluculateSaleStockPrice();
        $this->caluculatePossibleProfit();

        $this->getOrders();
        $this->calculateTotalSale();
        $this->calculateTotalSaleToday();
        $this->caluculateTotalPurchase();
        $this->caluculatePurchaseToday();
        $this->calculateTotalExpense();
    }

    public function getAttributes(): Collection
    {
        $attributeModel = new Attribute();
        $this->attributes = $attributeModel->getAllAttribute();
        return $this->attributes;
    }

    private function setTotalAttribute(): void
    {
        $this->total_attribute = $this->attributes->count();
    }

    private function calculateStock()
    {
        $this->total_stock = $this->attributes->sum('stock');
    }

    private function findLowStock()
    {
        $this->low_stock = $this->attributes->where('stock', '<=', self::LOW_STOCK_ALERT)->count();
    }

    private function caluculateBuyStockPrice()
    {
        foreach ($this->attributes as $attribute) {
            $this->buy_stock_price += ($attribute->price * $attribute->stock);
        }
    }

    private function caluculateSaleStockPrice()
    {
        foreach ($this->attributes as $attribute) {
            $this->sale_stock_price += ($attribute->price * $attribute->stock);
        }
    }

    private function caluculateTotalPurchase()
    {
        $this->total_purchase = $this->buy_stock_price;
    }

    private function caluculatePurchaseToday()
    {
        $attribute_buy_today = $this->attributes->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]);
        foreach ($attribute_buy_today as $attribute) {
            $this->total_purchase_today += ($attribute->price * $attribute->stock);
        }
    }

    private function caluculatePossibleProfit()
    {
        $this->possible_profit = $this->sale_stock_price - $this->buy_stock_price;
    }

    private function getOrders()
    {
        return $this->orders = (new OrderBahanaBaku())->getAllOrderForReport($this->is_admin, $this->sales_admin_id);
    }

    private function calculateTotalSale()
    {
        $this->total_sale = $this->orders->sum('total');
    }

    private function calculateTotalSaleToday()
    {
        $this->total_sale_today = $this->orders->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->sum('total');
    }

    private function calculateTotalExpense()
    {
        $this->total_expense = $this->total_purchase;
    }
}