<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Manager\ReportManager;
use App\Manager\PriceManager;

class SalesReportController extends Controller
{
    public function get_sales_reports()
    {
        $reportManager = new ReportManager();
        $report = [
            'total_product' => $reportManager->total_product,
            'total_stock' => $reportManager->total_stock,
            'low_stock' => $reportManager->low_stock,
            'buy_value' => PriceManager::priceFormat($reportManager->buy_stock_price),
            'sale_value' => PriceManager::priceFormat($reportManager->sale_stock_price),
            'possible_profit' => PriceManager::priceFormat($reportManager->possible_profit),
        ];
        return response()->json($report);
    }
}