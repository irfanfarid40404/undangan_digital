<?php

namespace App\Http\Controllers\Admin;

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

class AdminPaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::query()
            ->with(['order.user', 'order.product'])
            ->where('status', Payment::STATUS_PENDING)
            ->latest()
            ->paginate(12);

        $methodSettings = PaymentMethodSetting::query()
            ->get()
            ->keyBy('method_code');

        $methodPresets = collect([
            [
                'code' => PaymentMethodSetting::METHOD_BANK,
                'label' => 'Transfer Bank',
                'icon' => 'bi-bank',
                'placeholder_provider' => 'Contoh: BCA',
                'placeholder_name' => 'Contoh: PT Undangan Digital',
                'placeholder_number' => 'Contoh: 1234567890',
            ],
            [
                'code' => PaymentMethodSetting::METHOD_EWALLET,
                'label' => 'E-Wallet',
                'icon' => 'bi-wallet2',
                'placeholder_provider' => 'Contoh: OVO',
                'placeholder_name' => 'Contoh: Admin Undangan',
                'placeholder_number' => 'Contoh: 081234567890',
            ],
            [
                'code' => PaymentMethodSetting::METHOD_QRIS,
                'label' => 'QRIS',
                'icon' => 'bi-qr-code-scan',
                'placeholder_provider' => 'Contoh: QRIS Bisnis',
                'placeholder_name' => 'Contoh: Toko Undangan',
                'placeholder_number' => 'Nomor referensi (opsional)',
            ],
        ])->map(function (array $preset) use ($methodSettings) {
            /** @var PaymentMethodSetting|null $setting */
            $setting = $methodSettings->get($preset['code']);

            return array_merge($preset, [
                'setting' => $setting,
                'qris_image_url' => $setting?->qris_image_path ? Storage::disk('public')->url($setting->qris_image_path) : null,
            ]);
        })->values();

        return view('admin.payments', [
            'payments' => $payments,
            'methodPresets' => $methodPresets,
        ]);
    }

    public function saveMethod(Request $request, string $methodCode): RedirectResponse
    {
        abort_unless(in_array($methodCode, [
            PaymentMethodSetting::METHOD_BANK,
            PaymentMethodSetting::METHOD_EWALLET,
            PaymentMethodSetting::METHOD_QRIS,
        ], true), 404);

        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:120'],
            'provider_name' => ['nullable', 'string', 'max:120'],
            'account_name' => ['nullable', 'string', 'max:120'],
            'account_number' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
            'qris_image' => ['nullable', 'image', 'max:5120'],
            'remove_qris_image' => ['sometimes', 'boolean'],
        ]);

        $setting = PaymentMethodSetting::query()->firstOrNew([
            'method_code' => $methodCode,
        ]);

        $setting->fill([
            'display_name' => $validated['display_name'],
            'provider_name' => $validated['provider_name'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        if ($request->boolean('remove_qris_image') && $setting->qris_image_path) {
            Storage::disk('public')->delete($setting->qris_image_path);
            $setting->qris_image_path = null;
        }

        if ($request->hasFile('qris_image')) {
            if ($setting->qris_image_path) {
                Storage::disk('public')->delete($setting->qris_image_path);
            }
            $setting->qris_image_path = $request->file('qris_image')->store('payment-methods', 'public');
        }

        $setting->save();

        return redirect()->route('admin.payments')->with('success', 'Metode pembayaran berhasil disimpan.');
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status !== Payment::STATUS_PENDING) {
            return redirect()->route('admin.payments')->with('error', 'Pembayaran sudah diproses.');
        }

        $payment->update(['status' => Payment::STATUS_VERIFIED]);

        $order = $payment->order;
        if ($order->status === Order::STATUS_PENDING_PAYMENT) {
            $order->transitionTo(Order::STATUS_PAID, 'Pembayaran diverifikasi');
            $order->transitionTo(Order::STATUS_PROCESSING, 'Masuk antrian desain');
            NotificationService::notifyOrderStatusChanged($order->user_id, 'paid');
        }

        return redirect()->route('admin.payments')->with('success', 'Pembayaran dikonfirmasi.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->update([
            'status' => Payment::STATUS_REJECTED,
            'admin_note' => $validated['admin_note'] ?? null,
        ]);

        NotificationService::notifyPaymentRejected($payment->order->user_id, $validated['admin_note']);

        return redirect()->route('admin.payments')->with('success', 'Pembayaran ditolak.');
    }
}
