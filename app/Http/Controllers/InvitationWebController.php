<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationWebController extends Controller
{
    public function home(): View
    {
        $catalogPreview = Product::query()
            ->where('is_active', true)
            ->latest()
            ->limit(3)
            ->get();

        return view('home', [
            'catalogPreview' => $catalogPreview,
        ]);
    }

    public function flowFailed(Request $request): View
    {
        return view('errors.flow-failed', [
            'title' => (string) $request->input('title', 'Terjadi kesalahan') ?: 'Terjadi kesalahan',
            'message' => (string) $request->input('message', 'Silakan coba lagi atau hubungi support.') ?: 'Silakan coba lagi atau hubungi support.',
            'backUrl' => (string) $request->input('back', '') ?: route('home'),
        ]);
    }
}
