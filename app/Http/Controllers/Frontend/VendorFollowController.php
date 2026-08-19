<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Vendor;
use App\Domains\ECommerce\Models\VendorFollow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorFollowController extends Controller
{
    public function toggle(Request $request, Vendor $vendor): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please sign in to follow stores.',
            ], 401);
        }

        $existing = VendorFollow::query()
            ->where('vendor_id', $vendor->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
        } else {
            VendorFollow::query()->create([
                'vendor_id' => $vendor->id,
                'user_id' => $user->id,
            ]);
            $following = true;
        }

        $followers = VendorFollow::query()
            ->where('vendor_id', $vendor->id)
            ->count();

        return response()->json([
            'success' => true,
            'following' => $following,
            'followers' => $followers,
            'message' => $following ? 'Store followed.' : 'Store unfollowed.',
        ]);
    }
}
