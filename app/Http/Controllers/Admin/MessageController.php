<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageReplyMail;
use App\Domains\ECommerce\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $highlightId = $request->integer('highlight');
        if ($highlightId > 0) {
            ContactMessage::query()
                ->whereKey($highlightId)
                ->where('status', ContactMessage::STATUS_NEW)
                ->update(['status' => ContactMessage::STATUS_READ]);
        }

        $messages = ContactMessage::query()
            ->with('repliedBy:id,name')
            ->latest()
            ->paginate(20);

        return view('admin.messages.index', compact('messages'));
    }

    public function feed(Request $request): JsonResponse
    {
        $limit = (int) max(1, min($request->integer('limit', 8), 20));

        $messages = ContactMessage::query()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (ContactMessage $message) {
                return [
                    'id' => $message->id,
                    'name' => (string) ($message->name ?: 'Customer'),
                    'subject' => (string) ($message->subject ?: 'No subject'),
                    'message' => (string) ($message->message ?: ''),
                    'status' => (string) $message->status,
                    'is_unread' => $message->status === ContactMessage::STATUS_NEW,
                    'url' => route('admin.messages.index', ['highlight' => $message->id]),
                    'created_at_human' => optional($message->created_at)->diffForHumans(),
                    'created_at' => optional($message->created_at)->toIso8601String(),
                ];
            })
            ->values();

        return response()->json([
            'unread_count' => ContactMessage::query()
                ->where('status', ContactMessage::STATUS_NEW)
                ->count(),
            'messages' => $messages,
        ]);
    }

    public function markAsRead(int $message): JsonResponse
    {
        $record = ContactMessage::query()->findOrFail($message);

        if ($record->status === ContactMessage::STATUS_NEW) {
            $record->status = ContactMessage::STATUS_READ;
            $record->save();
        }

        return response()->json([
            'success' => true,
            'unread_count' => ContactMessage::query()
                ->where('status', ContactMessage::STATUS_NEW)
                ->count(),
        ]);
    }

    public function markAllRead(): JsonResponse
    {
        ContactMessage::query()
            ->where('status', ContactMessage::STATUS_NEW)
            ->update(['status' => ContactMessage::STATUS_READ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    public function reply(Request $request, ContactMessage $message): RedirectResponse
    {
        $validated = $request->validate([
            'reply_message' => ['required', 'string', 'max:5000'],
        ]);

        try {
            $adminName = (string) ($request->user()?->name ?: (config('mail.contact.name') ?: config('app.name')));
            Mail::to($message->email)->send(
                new ContactMessageReplyMail($message, $validated['reply_message'], $adminName)
            );

            $message->status = ContactMessage::STATUS_RESOLVED;
            $message->reply_message = $validated['reply_message'];
            $message->replied_by = $request->user()?->id;
            $message->replied_at = now();
            $message->save();

            return redirect()
                ->route('admin.messages.index', ['highlight' => $message->id])
                ->with('success', 'Reply sent successfully.');
        } catch (\Throwable) {
            return redirect()
                ->route('admin.messages.index', ['highlight' => $message->id])
                ->with('error', 'Unable to send reply right now. Please try again.');
        }
    }
}
