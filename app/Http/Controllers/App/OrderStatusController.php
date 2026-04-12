<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with('product')
            ->latest()
            ->get();

        return view('order.index', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, Order $order): View|RedirectResponse
    {
        abort_if($order->user_id !== $request->user()->id, 403);

        $order->load([
            'product',
            'invitationDetail',
            'histories',
            'payments' => fn ($q) => $q->latest(),
        ]);

        $latestPayment = $order->payments->first();
        if (
            $latestPayment
            && $latestPayment->status === Payment::STATUS_REJECTED
            && $order->status === Order::STATUS_PENDING_PAYMENT
        ) {
            return redirect()->route('flow.failed', [
                'title' => 'Pembayaran tidak disetujui',
                'message' => $latestPayment->admin_note ?: 'Bukti pembayaran ditolak. Silakan unggah bukti yang lebih jelas.',
                'back' => route('user.payment'),
            ]);
        }

        return view('order.status', [
            'order' => $order,
            'latestPayment' => $latestPayment,
        ]);
    }
}
