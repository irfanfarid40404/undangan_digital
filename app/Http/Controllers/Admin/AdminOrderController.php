<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\InvitationDetail;
use App\Services\NotificationService;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class AdminOrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders', [
            'orders' => Order::query()->with(['user', 'product', 'invitationDetail'])->latest()->get(),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                Order::STATUS_PENDING_PAYMENT,
                Order::STATUS_PAID,
                Order::STATUS_PROCESSING,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ])],
        ]);

        $order->transitionTo($validated['status'], 'Diperbarui admin');

        return redirect()->route('admin.orders')->with('success', 'Status pesanan disimpan.');
    }

    public function uploadFinalDesign(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'final_file' => ['required', 'file', 'mimes:pdf,zip,jpg,jpeg,png,html,htm', 'max:10240'],
        ]);

        $file = $request->file('final_file');
        $storageDir = "invitations/{$order->id}";
        $disk = Storage::disk('public');

        $originalName = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension());

        // Ensure directory exists
        if (! $disk->exists($storageDir)) {
            $disk->makeDirectory($storageDir);
        }

        // If zip, store and extract
        if ($ext === 'zip') {
            $zipPath = $file->storeAs($storageDir, $originalName, 'public');
            $absoluteZip = storage_path('app/public/'.$zipPath);
            $extractTo = storage_path('app/public/'.$storageDir);

            $zip = new \ZipArchive();
            if ($zip->open($absoluteZip) === true) {
                $zip->extractTo($extractTo);
                $zip->close();
                // remove zip after extraction
                $disk->delete($zipPath);
            }

            // find first html file in extracted folder
            $files = $disk->allFiles($storageDir);
            $html = collect($files)->first(fn($p) => in_array(strtolower(pathinfo($p, PATHINFO_EXTENSION)), ['html','htm']));
            $path = $html ?: ($storageDir.'/index.html');
        } else {
            // store the file into the folder (for html and other assets)
            $path = $file->storeAs($storageDir, $originalName, 'public');
            // if uploaded file is an image/pdf, we still save the path, but prefer html for serving
            if (! in_array($ext, ['html','htm'])) {
                // keep path but invitation may still be published without HTML
            }
        }

        $detail = $order->invitationDetail;
        if (! $detail) {
            $detail = InvitationDetail::create(['order_id' => $order->id]);
        }

        $detail->final_file_path = $path;
        $detail->save();

        // Generate a public slug for the invitation if not present
        if (! $order->public_slug) {
            do {
                $slug = Str::random(12);
            } while (Order::query()->where('public_slug', $slug)->exists());
            $order->public_slug = $slug;
            $order->save();
        }

        // Mark order completed and notify user with invitation URL
        $order->transitionTo(Order::STATUS_COMPLETED, 'Desain final dipublikasikan oleh admin');

        $invitationUrl = route('invitation.show', ['slug' => $order->public_slug]);
        NotificationService::notifyDesignReady($order->user_id, $invitationUrl);

        return redirect()->route('admin.orders')->with('success', 'Undangan dipublikasikan dan pengguna diberi tahu.');
    }

    public function publishInvitation(Request $request, Order $order): RedirectResponse
    {
        if (! $order->public_slug) {
            do {
                $slug = Str::random(12);
            } while (Order::query()->where('public_slug', $slug)->exists());
            $order->public_slug = $slug;
            $order->save();
        }

        $order->transitionTo(Order::STATUS_COMPLETED, 'Undangan dipublikasikan oleh admin');

        // Clear any uploaded final_file_path so the blade view is used consistently
        $detail = $order->invitationDetail;
        if ($detail) {
            $detail->final_file_path = null;
            $detail->save();
        }

        $invitationUrl = route('invitation.show', ['slug' => $order->public_slug]);
        NotificationService::notifyDesignReady($order->user_id, $invitationUrl);

        return redirect()->route('admin.orders')->with('success', 'Undangan dipublikasikan dan pengguna diberi tahu.');
    }
}
