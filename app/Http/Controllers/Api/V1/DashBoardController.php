<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    public function summary()
    {
        try {

            $totalCategories = Category::count();
            $totalProducts   = Product::count();
            $totalInvoices   = Invoice::count();

            // Revenue calculation
            $totalRevenue = Invoice::sum('grand_total');

            // Stock Alert
            $stockAlerts = Product::with('category')
                ->whereColumn('stock_qty', '<=', 'low_stock_threshold')
                ->where('low_stock_threshold', '>', 0)
                ->orderBy('stock_qty')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard summary fetched successfully',
                'data' => [
                    'total_categories' => $totalCategories,
                    'total_products'   => $totalProducts,
                    'total_invoices'   => $totalInvoices,
                    'total_revenue'    => round((float)$totalRevenue, 2),
                    'stock_alerts'     => $stockAlerts
                ]
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while fetching dashboard summary',
                'error'   => $e->getMessage()
            ], 500);

        }
    }
}