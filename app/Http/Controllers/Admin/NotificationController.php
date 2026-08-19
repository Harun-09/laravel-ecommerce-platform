<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $highlightId = (string) $request->query('highlight', '');
        if ($highlightId !== '') {
            $request->user()
                ->notifications()
                ->whereKey($highlightId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function feed(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = (int) max(1, min($request->integer('limit', 8), 20));

        $notifications = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn(DatabaseNotification $notification) => $this->transformNotification($notification))
            ->values();

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, string $notification): JsonResponse
    {
        $record = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        if ($record->read_at === null) {
            $record->markAsRead();
        }

        return response()->json([
            'success' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    private function transformNotification(DatabaseNotification $notification): array
    {
        $data = is_array($notification->data) ? $notification->data : [];
        $event = strtolower((string) ($data['event'] ?? ''));
        $title = match ($event) {
            'placed' => 'New Order Placed',
            'shipped' => 'Order Shipped',
            'delivered' => 'Order Delivered',
            'refund' => 'Refund Update',
            default => 'System Notification',
        };

        $message = (string) ($data['message'] ?? 'You have a new notification.');
        $orderId = (int) ($data['order_id'] ?? 0);
        $targetUrl = '#';

        if ($orderId > 0) {
            try {
                $targetUrl = route('admin.orders.show', $orderId);
            } catch (\Throwable) {
                $targetUrl = '#';
            }
        }

        return [
            'id' => $notification->id,
            'title' => $title,
            'message' => $message,
            'url' => $targetUrl,
            'is_read' => $notification->read_at !== null,
            'created_at_human' => optional($notification->created_at)->diffForHumans(),
            'created_at' => optional($notification->created_at)->toIso8601String(),
        ];
    }
}
