<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /** List semua notifikasi user yang login */
    public function index()
    {
        $notifications = Notification::with('deployRequest.application')
            ->where('user_id', auth()->id())
            ->where('type', 'in_app')
            ->latest()
            ->paginate(15);

        // Tandai semua sebagai sudah dibaca saat halaman dibuka
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    /** Tandai satu notifikasi sebagai sudah dibaca */
    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->update(['is_read' => true]);

        return back();
    }
}
