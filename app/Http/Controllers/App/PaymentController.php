<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethodSetting;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request): View|RedirectResponse
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
                ->with('error', 'Tidak ada pesanan pembayaran aktif.');
        }

        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->with(['product', 'payments' => fn ($q) => $q->latest()])
            ->findOrFail($orderId);

        $latestPayment = $order->payments->first();
        $mustReuploadProof = $latestPayment && $latestPayment->status === Payment::STATUS_REJECTED;

        $methodIcons = [
            PaymentMethodSetting::METHOD_BANK => 'bi-bank',
            PaymentMethodSetting::METHOD_EWALLET => 'bi-wallet2',
            PaymentMethodSetting::METHOD_QRIS => 'bi-qr-code-scan',
        ];

        $paymentMethods = PaymentMethodSetting::query()
            ->where('is_active', true)
            ->whereIn('method_code', array_keys($methodIcons))
            ->orderByRaw("CASE method_code WHEN 'pay_bank' THEN 1 WHEN 'pay_ew' THEN 2 WHEN 'pay_qr' THEN 3 ELSE 99 END")
            ->get()
            ->map(function (PaymentMethodSetting $setting) use ($methodIcons) {
                return [
                    'code' => $setting->method_code,
                    'label' => $setting->display_name,
                    'icon' => $methodIcons[$setting->method_code] ?? 'bi-credit-card',
                    'provider_name' => $setting->provider_name,
                    'account_name' => $setting->account_name,
                    'account_number' => $setting->account_number,
                    'notes' => $setting->notes,
                    'qris_image_url' => $setting->qris_image_path ? Storage::disk('public')->url($setting->qris_image_path) : null,
                ];
            })
            ->values();

        if ($paymentMethods->isEmpty()) {
            $paymentMethods = collect([
                ['code' => 'pay_bank', 'label' => 'Transfer Bank', 'icon' => 'bi-bank', 'provider_name' => 'Belum diatur admin', 'account_name' => '-', 'account_number' => '-', 'notes' => null, 'qris_image_url' => null],
                ['code' => 'pay_ew', 'label' => 'E-Wallet', 'icon' => 'bi-wallet2', 'provider_name' => 'Belum diatur admin', 'account_name' => '-', 'account_number' => '-', 'notes' => null, 'qris_image_url' => null],
                ['code' => 'pay_qr', 'label' => 'QRIS', 'icon' => 'bi-qr-code-scan', 'provider_name' => 'Belum diatur admin', 'account_name' => '-', 'account_number' => '-', 'notes' => null, 'qris_image_url' => null],
            ]);
        }

        return view('payment.index', [
            'order' => $order,
            'latestPayment' => $latestPayment,
            'mustReuploadProof' => $mustReuploadProof,
            'simulateFail' => $request->boolean('fail'),
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function store(Request $request): RedirectResponse
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

        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($orderId);

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return redirect()->route('user.orders.show', $order)->with('error', 'Pesanan ini sudah tidak menunggu pembayaran.');
        }

        $allowedMethods = PaymentMethodSetting::query()
            ->where('is_active', true)
            ->whereIn('method_code', [
                PaymentMethodSetting::METHOD_BANK,
                PaymentMethodSetting::METHOD_EWALLET,
                PaymentMethodSetting::METHOD_QRIS,
            ])
            ->pluck('method_code')
            ->all();

        if (empty($allowedMethods)) {
            $allowedMethods = [
                PaymentMethodSetting::METHOD_BANK,
                PaymentMethodSetting::METHOD_EWALLET,
                PaymentMethodSetting::METHOD_QRIS,
            ];
        }

        $validated = $request->validate([
            'method' => ['required', 'string', Rule::in($allowedMethods)],
            'proof' => ['required', 'file', 'image', 'max:8192'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payment-proofs', 'public');
        }

        Payment::query()->create([
            'order_id' => $order->id,
            'method' => $validated['method'],
            'amount' => $order->total_amount,
            'status' => Payment::STATUS_PENDING,
            'proof_path' => $proofPath,
        ]);

        NotificationService::notifyPaymentPending($order->user_id);

        return redirect()->route('user.payment')->with('success', 'Bukti / konfirmasi pembayaran terkirim. Menunggu verifikasi admin.');
    }

    /** Demo cepat: tandai sudah bayar tanpa admin (hanya lingkungan lokal / uji). */
    public function demoPaid(Request $request): RedirectResponse
    {
        if (! app()->environment('local')) {
            abort(404);
        }

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
        $order = Order::query()->where('user_id', $request->user()->id)->findOrFail($orderId);

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return redirect()
                ->route('user.orders.show', $order)
                ->with('error', 'Demo hanya untuk pesanan yang masih menunggu pembayaran.');
        }

        Payment::query()->create([
            'order_id' => $order->id,
            'method' => 'demo',
            'amount' => $order->total_amount,
            'status' => Payment::STATUS_VERIFIED,
        ]);

        $order->transitionTo(Order::STATUS_PAID, 'Demo: pembayaran otomatis diverifikasi');
        $order->transitionTo(Order::STATUS_PROCESSING, 'Masuk antrian desain');
        $order->transitionTo(Order::STATUS_COMPLETED, 'Demo: undangan selesai');

        return redirect()->route('user.orders.show', $order);
    }
}
