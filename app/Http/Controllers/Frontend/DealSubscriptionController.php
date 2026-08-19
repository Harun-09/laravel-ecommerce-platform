<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\DealSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealSubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'source' => ['nullable', 'string', 'max:60'],
        ]);

        $email = strtolower(trim((string) $validated['email']));
        $source = trim((string) ($validated['source'] ?? 'product_show'));
        if ($source === '') {
            $source = 'product_show';
        }

        $existing = DealSubscription::query()
            ->where('email', $email)
            ->first();

        if ($existing) {
            $alreadyActive = (bool) $existing->is_active;

            $existing->fill([
                'user_id' => auth()->id() ?: $existing->user_id,
                'source' => $source,
                'is_active' => true,
                'subscribed_at' => $existing->subscribed_at ?: now(),
                'meta' => [
                    'ip' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                ],
            ]);
            $existing->save();

            $message = $alreadyActive
                ? 'This email is already subscribed to deal alerts.'
                : 'Deal alert subscription reactivated successfully.';

            if ($this->expectsJsonResponse($request)) {
                return response()->json([
                    'success' => true,
                    'already_subscribed' => $alreadyActive,
                    'message' => $message,
                ]);
            }

            return back()->with('success', $message);
        }

        DealSubscription::query()->create([
            'email' => $email,
            'user_id' => auth()->id(),
            'source' => $source,
            'subscribed_at' => now(),
            'is_active' => true,
            'meta' => [
                'ip' => $request->ip(),
                'user_agent' => (string) $request->userAgent(),
            ],
        ]);

        $message = 'Thanks. You are subscribed to product deal alerts.';

        if ($this->expectsJsonResponse($request)) {
            return response()->json([
                'success' => true,
                'already_subscribed' => false,
                'message' => $message,
            ], 201);
        }

        return back()->with('success', $message);
    }

    private function expectsJsonResponse(Request $request): bool
    {
        return $request->expectsJson() || $request->wantsJson() || $request->ajax();
    }
}
