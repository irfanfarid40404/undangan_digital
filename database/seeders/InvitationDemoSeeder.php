<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InvitationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $covers = config('invitation_demo_media.covers', []);

        User::query()->updateOrCreate(
            ['email' => 'admin@einvite.test'],
            [
                'name' => 'Admin E-Invite',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'demo@einvite.test'],
            [
                'name' => 'Pengguna Demo',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        $templates = [
            ['Rose Garden', 'rose-garden', 'Minimal', 'Floral', 149000],
            ['Luxe Gold', 'luxe-gold', 'Klasik', 'Gold', 199000],
            ['Pastel Bloom', 'pastel-bloom', 'Ceria', 'Pastel', 159000],
            ['Midnight Spark', 'midnight-spark', 'Modern', 'Dark', 179000],
            ['Ocean Breeze', 'ocean-breeze', 'Fresh', 'Blue', 169000],
            ['Velvet Romance', 'velvet-romance', 'Premium', 'Velvet', 249000],
        ];

        foreach ($templates as $i => [$name, $slug, $cat, $theme, $price]) {
            Product::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'category' => $cat,
                    'theme' => $theme,
                    'price' => $price,
                    'image_url' => $covers[$i % max(count($covers), 1)] ?? null,
                    'description' => 'Template undangan digital: '.$name,
                    'is_active' => true,
                ]
            );
        }
    }
}
