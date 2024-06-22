<?php

namespace App\Manager;

use Illuminate\Support\Collection;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

class ReportManager{
    public const LOW_STOCK_ALERT = 5;
    public int $total_product = 0;
    public int $total_stock = 0;
    public int $low_stock = 0;
    public int $buy_stock_price = 0;
    public int $sale_stock_price = 0;
    public int $possible_profit = 0;
    public int $total_sale = 0;
    public int $total_sale_today = 0;
    public int $total_purchase = 0;
    public int $total_purchase_today = 0;

    private bool $is_admin = false;
    private int $sales_admin_id;
    private Collection $products;
    private Collection $orders;

    public function __construct($auth)
    {
        if ($auth->guard('admin')->check()) {
            $this->is_admin = true;
        }
        $this->sales_admin_id = $auth->user()->id;

        $this->getProducts();
        $this->setTotalProduct();
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
    }

    public function getProducts(): Collection
    {
        $productModel = new Product();
        $this->products = $productModel->getAllProduct();
        return $this->products;
    }

    private function setTotalProduct(): void
    {
        $this->total_product = $this->products->count();
    }

    private function calculateStock()
    {
        $this->total_stock = $this->products->sum('stock');
    }

    private function findLowStock()
    {
        $this->low_stock = $this->products->where('stock', '<=', self::LOW_STOCK_ALERT)->count();
    }

    private function caluculateBuyStockPrice()
    {
        foreach ($this->products as $product) {
            $this->buy_stock_price += ($product->cost * $product->stock);
        }
    }

    private function caluculateSaleStockPrice()
    {
        foreach ($this->products as $product) {
            $this->sale_stock_price += ($product->price * $product->stock);
        }
    }

    private function caluculateTotalPurchase()
    {
        $this->total_purchase = $this->buy_stock_price;
    }

    private function caluculatePurchaseToday()
    {
        $product_buy_today = $this->products->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]);
        foreach ($product_buy_today as $product) {
            $this->total_purchase_today += ($product->cost * $product->stock);
        }
    }

    private function caluculatePossibleProfit()
    {
         $this->possible_profit = $this->sale_stock_price - $this->buy_stock_price;
    }

    private function getOrders()
    {
        return $this->orders = (new Order())->getAllOrderForReport($this->is_admin, $this->sales_admin_id);
    }

    private function calculateTotalSale()
    {
        $this->total_sale = $this->orders->sum('total');
    }

    private function calculateTotalSaleToday()
    {
        $this->total_sale_today = $this->orders->whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->sum('total');
    }
}