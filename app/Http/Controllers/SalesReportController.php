<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Manager\ReportManager;
use App\Manager\ReportAttributeManager;
use App\Manager\PriceManager;

class SalesReportController extends Controller
{
    public function get_sales_reports()
    {
        $reportManager = new ReportManager(auth());
        $report = [
            'total_product' => $reportManager->total_product,
            'total_stock_product' => $reportManager->total_stock,
            'low_stock_product' => $reportManager->low_stock,
            'buy_value_product' => PriceManager::priceFormat($reportManager->buy_stock_price),
            'sale_value_product' => PriceManager::priceFormat($reportManager->sale_stock_price),
            'possible_profit_product' => PriceManager::priceFormat($reportManager->possible_profit),
            'total_sale_product' => PriceManager::priceFormat($reportManager->total_sale),
            'total_sale_today_product' => PriceManager::priceFormat($reportManager->total_sale_today),
            'total_purchase_product' => PriceManager::priceFormat($reportManager->total_purchase),
            'total_purchase_today_product' => PriceManager::priceFormat($reportManager->total_purchase_today),
            'total_expense_product' => PriceManager::priceFormat($reportManager->total_expense),
        ];
        return response()->json($report);
    }

    public function get_attribute_reports()
    {
        $reportBahanBakuManager = new ReportAttributeManager(auth());
        $report = [
            'total_attribute' => $reportBahanBakuManager->total_attribute,
            'total_stock' => $reportBahanBakuManager->total_stock,
            'low_stock' => $reportBahanBakuManager->low_stock,
            'buy_value' => PriceManager::priceFormat($reportBahanBakuManager->buy_stock_price),
            'sale_value' => PriceManager::priceFormat($reportBahanBakuManager->sale_stock_price),
            'possible_profit' => PriceManager::priceFormat($reportBahanBakuManager->possible_profit),
            'total_sale' => PriceManager::priceFormat($reportBahanBakuManager->total_sale),
            'total_sale_today' => PriceManager::priceFormat($reportBahanBakuManager->total_sale_today),
            'total_purchase' => PriceManager::priceFormat($reportBahanBakuManager->total_purchase),
            'total_purchase_today' => PriceManager::priceFormat($reportBahanBakuManager->total_purchase_today),
            'total_expense' => PriceManager::priceFormat($reportBahanBakuManager->total_expense),
        ];
        return response()->json($report);
    }
}