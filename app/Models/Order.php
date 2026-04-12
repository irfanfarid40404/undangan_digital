<?php

namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING_PAYMENT = 'pending_payment';

    public const STATUS_PAID = 'paid';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'product_id',
        'status',
        'total_amount',
        'discount_amount',
        'public_slug',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
            'discount_amount' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Order $order) {
            $order->histories()->create([
                'from_status' => null,
                'to_status' => $order->status,
                'note' => 'Pesanan dibuat — menunggu pembayaran',
            ]);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function invitationDetail(): HasOne
    {
        return $this->hasOne(InvitationDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class)->orderBy('id');
    }

    public function publicNumber(): string
    {
        return 'INV-'.$this->id;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_PAYMENT => 'Menunggu pembayaran',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_PROCESSING => 'Diproses tim desain',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function transitionTo(string $newStatus, ?string $note = null): void
    {
        $from = $this->status;
        if ($from === $newStatus) {
            return;
        }
        $this->update(['status' => $newStatus]);
        $this->histories()->create([
            'from_status' => $from,
            'to_status' => $newStatus,
            'note' => $note,
        ]);

        // Create notification for user about status change
        if (in_array($newStatus, [self::STATUS_PAID, self::STATUS_PROCESSING, self::STATUS_COMPLETED, self::STATUS_CANCELLED])) {
            NotificationService::notifyOrderStatusChanged($this->user_id, $newStatus, $note);
        }
    }
}
