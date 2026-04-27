<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\Order;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('partials.navbar-landing', function ($view): void {
            $cartCount = 0;
            $user = Auth::user();
            if ($user) {
                $cartCount = Order::where('user_id', $user->id)
                    ->whereNotIn('status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
                    ->count();
            }
            $view->with('navCartCount', $cartCount);
        });

        View::composer('layouts.user', function ($view): void {
            $notifications = [];
            $unreadCount = 0;
            $user = Auth::user();

            if ($user) {
                $notifications = Notification::where('user_id', $user->id)
                    ->orderBy('read_at', 'asc') // unread first (NULL comes first)
                    ->latest('created_at')
                    ->limit(8)
                    ->get();

                $unreadCount = Notification::where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with('userNotifications', $notifications);
            $view->with('userNotificationCount', $unreadCount);
        });
    }
}
