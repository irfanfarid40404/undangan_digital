<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'theme',
        'price',
        'image_url',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function resolvedImageUrl(): ?string
    {
        if (! $this->image_url) {
            return null;
        }

        $path = trim((string) $this->image_url);

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, ['/storage/', 'storage/'])) {
            return '/'.ltrim($path, '/');
        }

        if (Str::startsWith($path, ['/public/', 'public/'])) {
            $path = preg_replace('#^/?public/#', '', $path) ?? $path;
        }

        return '/storage/'.ltrim($path, '/');
    }
}
