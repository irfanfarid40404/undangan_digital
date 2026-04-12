<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products', [
            'products' => Product::query()->withCount('orders')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image_url'] = $this->normalizeImageUrl($data['image_url'] ?? null);
        if ($request->hasFile('image_file')) {
            $data['image_url'] = $request->file('image_file')->store('product-images', 'public');
        }
        $data['slug'] = $this->uniqueSlug($data['name']);
        Product::query()->create($data);

        return redirect()->route('admin.products')->with('success', 'Produk ditambahkan.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image_url'] = $this->normalizeImageUrl($data['image_url'] ?? null);
        if ($request->hasFile('image_file')) {
            $this->deleteStoredImage($product->image_url);
            $data['image_url'] = $request->file('image_file')->store('product-images', 'public');
        }
        $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        $product->update($data);

        return redirect()->route('admin.products')->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orders()->exists()) {
            if ($product->is_active) {
                $product->update(['is_active' => false]);

                return redirect()->route('admin.products')->with('success', 'Produk dipakai pesanan, jadi dinonaktifkan dari katalog.');
            }

            return redirect()->route('admin.products')->with('success', 'Produk sudah nonaktif karena dipakai pesanan.');
        }

        $this->deleteStoredImage($product->image_url);
        $product->delete();

        return redirect()->route('admin.products')->with('success', 'Produk dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:80'],
            'theme' => ['nullable', 'string', 'max:80'],
            'price' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    private function deleteStoredImage(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://', '//'])) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'produk';
        $slug = $base;
        $i = 1;
        while (Product::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function normalizeImageUrl(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '//'])) {
            return $value;
        }

        $value = preg_replace('#^/?public/#', '', $value) ?? $value;
        $value = preg_replace('#^/?storage/#', '', $value) ?? $value;

        return ltrim($value, '/');
    }
}
