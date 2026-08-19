<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view reviews')->only(['index', 'show']);
        $this->middleware('can:approve reviews')->only(['approve', 'reject', 'reply']);
        $this->middleware('can:delete reviews')->only(['destroy']);
    }

    public function index(Request $request): View
    {
        $query = Review::query()
            ->with(['product.vendor', 'user', 'order'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhereHas('product', fn($productQuery) => $productQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $status = strtolower(trim((string) $request->status));
            if ($status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        if ($request->filled('rating')) {
            $rating = (int) $request->rating;
            if ($rating >= 1 && $rating <= 5) {
                $query->where('rating', $rating);
            }
        }

        if ($request->filled('verified')) {
            $verified = (string) $request->verified;
            if (in_array($verified, ['yes', 'no'], true)) {
                $query->where('is_verified_purchase', $verified === 'yes');
            }
        }

        if ($request->filled('replied')) {
            $replied = (string) $request->replied;
            if (in_array($replied, ['yes', 'no'], true)) {
                $query->where(function ($builder) use ($replied): void {
                    if ($replied === 'yes') {
                        $builder->whereNotNull('admin_reply')
                            ->where('admin_reply', '!=', '');
                        return;
                    }

                    $builder->whereNull('admin_reply')
                        ->orWhere('admin_reply', '');
                });
            }
        }

        if ($request->filled('product')) {
            $query->where('product_id', (int) $request->product);
        }

        $reviews = $query->paginate(20);

        $stats = [
            'total' => Review::query()->count(),
            'pending' => Review::query()->where('is_approved', false)->count(),
            'approved' => Review::query()->where('is_approved', true)->count(),
            'verified' => Review::query()->where('is_verified_purchase', true)->count(),
            'replied' => Review::query()
                ->whereNotNull('admin_reply')
                ->where('admin_reply', '!=', '')
                ->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show(Review $review): View
    {
        $review->load(['product.vendor', 'user', 'order', 'images', 'helpfuls.user']);

        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review): RedirectResponse
    {
        if (!$review->is_approved) {
            $review->approve();
        }

        return back()->with('success', 'Review approved successfully.');
    }

    public function reject(Review $review): RedirectResponse
    {
        if ($review->is_approved) {
            $review->reject();
        } else {
            $review->update(['is_approved' => false]);
            $review->product?->updateRating();
        }

        return back()->with('success', 'Review marked as rejected.');
    }

    public function reply(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $review->addReply($validated['admin_reply']);

        return back()->with('success', 'Admin reply saved successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $product = $review->product;

        $review->delete();
        $product?->updateRating();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
