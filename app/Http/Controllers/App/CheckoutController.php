<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $orderId = (int) $request->session()->get('checkout_order_id', 0);
        if (! $orderId) {
            $fallback = Order::query()
                ->where('user_id', $request->user()->id)
                ->where('status', Order::STATUS_PENDING_PAYMENT)
                ->latest()
                ->first();
            if ($fallback) {
                $request->session()->put('checkout_order_id', $fallback->id);
                $orderId = $fallback->id;
            }
        }

        if (! $orderId) {
            return redirect()
                ->route('user.orders.index')
                ->with('error', 'Tidak ada pesanan untuk checkout. Buat pesanan dari katalog.');
        }

        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->with('product')
            ->findOrFail($orderId);

        return view('checkout.index', [
            'order' => $order,
        ]);
    }
}
