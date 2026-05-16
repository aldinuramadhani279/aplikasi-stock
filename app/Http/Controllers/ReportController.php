<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected TelegramService $telegram)
    {
    }

    public function index(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::today()->subDays(30);
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::today();

        $movements = StockMovement::with(['product.category', 'user'])
            ->whereBetween('created_at', [$dateFrom->startOfDay(), $dateTo->copy()->endOfDay()])
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total_in' => StockMovement::whereBetween('created_at', [$dateFrom, $dateTo->copy()->endOfDay()])
                ->where('type', 'IN')->sum('quantity'),
            'total_out' => StockMovement::whereBetween('created_at', [$dateFrom, $dateTo->copy()->endOfDay()])
                ->where('type', 'OUT')->sum('quantity'),
            'total_transactions' => StockMovement::whereBetween('created_at', [$dateFrom, $dateTo->copy()->endOfDay()])->count(),
        ];

        $lowStockProducts = Product::with('category')
            ->where('is_active', true)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->orderBy('current_stock')
            ->get();

        return view('reports.index', compact('movements', 'summary', 'lowStockProducts', 'dateFrom', 'dateTo'));
    }

    public function sendToTelegram(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        $this->telegram->sendDailyReport();

        return back()->with('success', 'Laporan berhasil dikirim ke Telegram.');
    }
}
