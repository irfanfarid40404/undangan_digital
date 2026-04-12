<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethodSetting extends Model
{
    use HasFactory;

    public const METHOD_BANK = 'pay_bank';

    public const METHOD_EWALLET = 'pay_ew';

    public const METHOD_QRIS = 'pay_qr';

    protected $fillable = [
        'method_code',
        'display_name',
        'provider_name',
        'account_name',
        'account_number',
        'qris_image_path',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
