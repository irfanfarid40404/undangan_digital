<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignController extends Controller
{
    public function __invoke(Request $request, ?string $slug = null): View
    {
        $product = $slug
            ? Product::query()->where('slug', $slug)->where('is_active', true)->firstOrFail()
            : Product::query()->where('is_active', true)->orderBy('id')->firstOrFail();

        return view('product.design', [
            'product' => $product,
            'slug' => $product->slug,
        ]);
    }
}
