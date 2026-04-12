<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderWizardController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $productId = (int) $request->query('product', 0);
        $product = $productId
            ? Product::query()->where('is_active', true)->whereKey($productId)->first()
            : null;

        if (! $product) {
            $product = Product::query()->where('is_active', true)->orderBy('id')->first();
        }

        if (! $product) {
            return redirect()
                ->route('user.catalog')
                ->with('error', 'Belum ada produk. Hubungi admin.');
        }

        return view('order.form', [
            'product' => $product,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'phone_number' => ['required', 'string', 'max:25'],
            'p1' => ['required', 'string', 'max:120'],
            'p2' => ['required', 'string', 'max:120'],
            'date' => ['required', 'date'],
            'loc' => ['required', 'string', 'max:255'],
            'story' => ['required', 'string', 'max:5000'],
            'music' => ['required', 'string', 'max:120'],
            'quote' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['required', 'image', 'max:5120'],
        ]);

        $product = Product::query()->whereKey($validated['product_id'])->where('is_active', true)->firstOrFail();

        $discount = 0;
        $total = max(0, $product->price - $discount);

        $order = Order::query()->create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'status' => Order::STATUS_PENDING_PAYMENT,
            'total_amount' => $total,
            'discount_amount' => $discount,
        ]);

        $order->invitationDetail()->create([
            'phone_number' => $validated['phone_number'],
            'partner_one_name' => $validated['p1'],
            'partner_two_name' => $validated['p2'],
            'event_date' => $validated['date'],
            'location' => $validated['loc'],
            'story' => $validated['story'] ?? null,
            'music_choice' => $validated['music'] ?? null,
            'quote_text' => $validated['quote'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $photoFiles = $request->file('photos', []);
        $list = is_array($photoFiles) ? $photoFiles : [$photoFiles];
        foreach ($list as $file) {
            if ($file && $file->isValid()) {
                $file->store("order-photos/{$order->id}", 'public');
            }
        }

        $request->session()->put('checkout_order_id', $order->id);

        return redirect()->route('user.checkout');
    }
}
