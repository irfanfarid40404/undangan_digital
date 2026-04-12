<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $revenueMonth = Order::query()
            ->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $activeOrders = Order::query()
            ->whereIn('status', [Order::STATUS_PENDING_PAYMENT, Order::STATUS_PAID, Order::STATUS_PROCESSING])
            ->count();

        $pendingPayments = Payment::query()->where('status', Payment::STATUS_PENDING)->count();

        $recentOrders = Order::query()
            ->with(['user', 'product'])
            ->latest()
            ->limit(8)
            ->get();

        $activity = $recentOrders->map(function (Order $order) {
            return [
                'time' => $order->created_at->format('H:i'),
                'timestamp' => $order->created_at->format('Y-m-d H:i:s'),
                'event' => 'Pesanan '.$order->publicNumber(),
                'user' => $order->user?->email ?? '-',
            ];
        });

        $checkoutData = Order::query()
            ->with(['user', 'product', 'invitationDetail', 'payments' => fn ($q) => $q->latest()])
            ->latest()
            ->get()
            ->map(function (Order $order) {
                $latestPayment = $order->payments->first();
                $photoPaths = Storage::disk('public')->files("order-photos/{$order->id}");
                $photoUrls = collect($photoPaths)
                    ->map(fn (string $path) => Storage::disk('public')->url($path))
                    ->values()
                    ->all();
                $photoCount = count($photoUrls);

                return [
                    'order_id' => $order->id,
                    'order_number' => $order->publicNumber(),
                    'order_status' => $order->statusLabel(),
                    'created_at' => $order->created_at->format('d M Y H:i'),
                    'created_at_timestamp' => $order->created_at->format('Y-m-d H:i:s'),
                    'product' => $order->product?->name ?? '-',
                    'total_amount' => $order->total_amount,
                    'discount_amount' => $order->discount_amount,
                    'customer' => $order->user?->name ?? '-',
                    'email' => $order->user?->email ?? '-',
                    'phone' => $order->invitationDetail?->phone_number ?? '-',
                    'partner_one_name' => $order->invitationDetail?->partner_one_name ?? '-',
                    'partner_two_name' => $order->invitationDetail?->partner_two_name ?? '-',
                    'couple' => trim(($order->invitationDetail?->partner_one_name ?? '-') . ' & ' . ($order->invitationDetail?->partner_two_name ?? '-')),
                    'event_date' => $order->invitationDetail?->event_date?->format('d M Y') ?? '-',
                    'event_date_timestamp' => $order->invitationDetail?->event_date?->format('Y-m-d') ?? '',
                    'location' => $order->invitationDetail?->location ?? '-',
                    'story' => $order->invitationDetail?->story ?? '-',
                    'music' => $order->invitationDetail?->music_choice ?? '-',
                    'quote' => $order->invitationDetail?->quote_text ?? '-',
                    'notes' => $order->invitationDetail?->notes ?? '-',
                    'photo_count' => $photoCount,
                    'photo_urls' => $photoUrls,
                    'payment_status' => $latestPayment?->status ?? '-',
                    'payment_method' => $latestPayment?->method ?? '-',
                    'payment_admin_note' => $latestPayment?->admin_note ?? '-',
                    'proof_url' => $latestPayment?->proof_path ? Storage::disk('public')->url($latestPayment->proof_path) : null,
                ];
            });

        return view('admin.dashboard', [
            'revenueMonth' => $revenueMonth,
            'revenueMonthLabel' => 'Rp '.number_format($revenueMonth, 0, ',', '.'),
            'activeOrders' => $activeOrders,
            'pendingPayments' => $pendingPayments,
            'activity' => $activity,
            'checkoutData' => $checkoutData,
        ]);
    }
}
