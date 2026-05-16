<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockAlert;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::where('is_active', true)->count(),
            'total_stock' => Product::where('is_active', true)->sum('current_stock'),
            'low_stock' => Product::where('is_active', true)
                ->whereColumn('current_stock', '<=', 'minimum_stock')
                ->where('current_stock', '>', 0)
                ->count(),
            'out_of_stock' => Product::where('is_active', true)->where('current_stock', 0)->count(),
            'today_transactions' => StockMovement::whereDate('created_at', today())->count(),
            'today_in' => StockMovement::whereDate('created_at', today())->where('type', 'IN')->sum('quantity'),
            'today_out' => StockMovement::whereDate('created_at', today())->where('type', 'OUT')->sum('quantity'),
        ];

        // Chart data: last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData[] = [
                'date' => $date->format('d/m'),
                'in' => StockMovement::whereDate('created_at', $date)->where('type', 'IN')->sum('quantity'),
                'out' => StockMovement::whereDate('created_at', $date)->where('type', 'OUT')->sum('quantity'),
            ];
        }

        $lowStockProducts = Product::with('category')
            ->where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock')
            ->limit(10)
            ->get();

        $recentMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact('stats', 'chartData', 'lowStockProducts', 'recentMovements'));
    }
}
