<?php

namespace App\Manager;

use App\Models\Product;
use Illuminate\Support\Collection;

class ReportManager{
    public const LOW_STOCK_ALERT = 5;
    public int $total_product = 0;
    public int $total_stock = 0;
    public int $low_stock = 0;
    public int $buy_stock_price = 0;
    public int $sale_stock_price = 0;
    public int $possible_profit = 0;
    private Collection $products;

    public function __construct()
    {
        $this->getProducts();
        $this->setTotalProduct();
        $this->calculateStock();
        $this->findLowStock();
        $this->caluculateBuyStockPrice();
        $this->caluculateSaleStockPrice();
        $this->caluculatePossibleProfit();
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

    private function caluculatePossibleProfit()
    {
        $this->possible_profit = $this->sale_stock_price - $this->buy_stock_price;
    }
}