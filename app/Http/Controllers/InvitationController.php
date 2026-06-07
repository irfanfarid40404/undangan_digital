<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class InvitationController extends Controller
{
    public function show(Request $request, string $slug): View|Response
    {
        $order = Order::query()
            ->where('public_slug', $slug)
            ->with(['invitationDetail', 'product', 'user'])
            ->firstOrFail();


        // Always render the application blade view for invitations so CSS/JS from app loads properly.
        // This ignores any uploaded HTML file; uploaded assets are kept but not served directly.

        return view('invitation.show', [
            'order' => $order,
        ]);
    }
}
