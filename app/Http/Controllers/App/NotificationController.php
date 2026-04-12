<?php

namespace App\Http\Controllers\App;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, Notification $notification)
    {
        $request->user()->notifications()->where('id', $notification->id)->update(['read_at' => now()]);

        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }

    public function delete(Request $request, Notification $notification)
    {
        $request->user()->notifications()->where('id', $notification->id)->delete();

        return back();
    }
}
