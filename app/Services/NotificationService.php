<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function notifyPaymentPending(int $userId, string $message = null): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'payment_pending',
            'data' => [
                'message' => $message ?? 'Pembayaran Anda menunggu verifikasi admin.',
                'level' => 'warning',
            ],
        ]);
    }

    public static function notifyPaymentRejected(int $userId, string $reason = null): void
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'payment_rejected',
            'data' => [
                'message' => $reason ?? 'Pembayaran ditolak. Silakan upload ulang bukti pembayaran.',
                'level' => 'danger',
            ],
        ]);
    }

    public static function notifyOrderStatusChanged(int $userId, string $status, string $note = null): void
    {
        $messages = [
            'paid' => 'Pembayaran Anda telah diverifikasi.',
            'processing' => 'Pesanan Anda sedang diproses.',
            'completed' => 'Pesanan Anda telah selesai!',
            'cancelled' => 'Pesanan Anda telah dibatalkan.',
        ];

        Notification::create([
            'user_id' => $userId,
            'type' => 'order_status_changed',
            'data' => [
                'message' => $note ?? $messages[$status] ?? 'Status pesanan diperbarui.',
                'level' => 'info',
            ],
        ]);
    }

    public static function clearOldNotifications(int $days = 30): void
    {
        Notification::where('created_at', '<', now()->subDays($days))->delete();
    }
}
