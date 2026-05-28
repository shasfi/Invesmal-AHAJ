<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function index()
    {
        $notifications = $this->notificationService->getAllForUser(auth()->user(), 20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $this->notificationService->markAsRead($notification);
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        $this->notificationService->markAllAsRead(auth()->user());
        return back()->with('success', 'All notifications marked as read.');
    }
}