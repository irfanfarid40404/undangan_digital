<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $activeOrders = Order::query()->where('user_id', $user->id)
            ->whereNotIn('status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
            ->count();

        $awaitingPayment = Order::query()->where('user_id', $user->id)
            ->where('status', Order::STATUS_PENDING_PAYMENT)
            ->count();

        $completed = Order::query()->where('user_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->count();

        return view('dashboard.index', [
            'activeOrders' => $activeOrders,
            'awaitingPayment' => $awaitingPayment,
            'completed' => $completed,
        ]);
    }
}
