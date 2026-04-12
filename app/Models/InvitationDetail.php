<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'phone_number',
        'partner_one_name',
        'partner_two_name',
        'event_date',
        'location',
        'story',
        'music_choice',
        'quote_text',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
