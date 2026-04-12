<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $startDate = isset($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : now()->startOfMonth();

        $endDate = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : now()->endOfDay();

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $paidStatuses = [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED];

        $ordersInRange = Order::query()->whereBetween('created_at', [$startDate, $endDate]);
        $totalOrders = (clone $ordersInRange)->count();

        $paidOrdersQuery = (clone $ordersInRange)->whereIn('status', $paidStatuses);
        $paidOrders = (clone $paidOrdersQuery)->count();
        $revenue = (int) ((clone $paidOrdersQuery)->sum('total_amount') ?? 0);

        $conversion = $totalOrders > 0
            ? round(($paidOrders / $totalOrders) * 100, 1)
            : 0;

        $aov = $paidOrders > 0
            ? (int) round($revenue / $paidOrders)
            : 0;

        $paymentsInRange = Payment::query()->whereBetween('created_at', [$startDate, $endDate]);
        $totalPayments = (clone $paymentsInRange)->count();
        $rejectedPayments = (clone $paymentsInRange)->where('status', Payment::STATUS_REJECTED)->count();
        $refundRate = $totalPayments > 0
            ? round(($rejectedPayments / $totalPayments) * 100, 1)
            : 0.0;

        $rangeDays = $startDate->diffInDays($endDate) + 1;
        $chartLabels = [];
        $chartValues = [];

        if ($rangeDays <= 31) {
            $cursor = $startDate->copy();
            while ($cursor->lte($endDate)) {
                $dayRevenue = Order::query()
                    ->whereIn('status', $paidStatuses)
                    ->whereDate('created_at', $cursor->toDateString())
                    ->sum('total_amount');
                $chartLabels[] = $cursor->translatedFormat('d M');
                $chartValues[] = round($dayRevenue / 1_000_000, 2);
                $cursor->addDay();
            }
        } else {
            $cursor = $startDate->copy()->startOfMonth();
            $endMonth = $endDate->copy()->startOfMonth();
            while ($cursor->lte($endMonth)) {
                $monthRevenue = Order::query()
                    ->whereIn('status', $paidStatuses)
                    ->whereYear('created_at', $cursor->year)
                    ->whereMonth('created_at', $cursor->month)
                    ->sum('total_amount');
                $chartLabels[] = $cursor->translatedFormat('M Y');
                $chartValues[] = round($monthRevenue / 1_000_000, 2);
                $cursor->addMonth();
            }
        }

        $topProducts = (clone $paidOrdersQuery)
            ->with('product')
            ->get(['product_id', 'total_amount'])
            ->groupBy('product_id')
            ->map(function ($rows) {
                $first = $rows->first();
                return [
                    'name' => $first?->product?->name ?? 'Produk tidak diketahui',
                    'orders_count' => $rows->count(),
                    'revenue' => (int) $rows->sum('total_amount'),
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        $reportOrders = (clone $ordersInRange)
            ->with(['user', 'product', 'payments' => fn ($q) => $q->latest()])
            ->latest()
            ->limit(100)
            ->get();

        return view('admin.reports', [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'totalOrders' => $totalOrders,
            'paidOrders' => $paidOrders,
            'revenue' => $revenue,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'conversion' => $conversion,
            'aov' => $aov,
            'refundRate' => $refundRate,
            'topProducts' => $topProducts,
            'reportOrders' => $reportOrders,
        ]);
    }
}
