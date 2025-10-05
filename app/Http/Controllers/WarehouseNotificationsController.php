<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseNotificationsController extends Controller
{
    /**
     * Get notifications for the current user
     */
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();

        // Apply filters
        if ($request->filter && $request->filter !== 'all') {
            $query->where('data->type', $request->filter);
        }

        // Get paginated results
        $notifications = $query->paginate(10);

        if ($request->ajax()) {
            return view('components.warehouse-notifications-panel', [
                'notifications' => $notifications
            ])->render();
        }

        return $notifications;
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        return response()->json(['message' => 'All notifications cleared']);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }
}
