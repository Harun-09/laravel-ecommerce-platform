<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Wishlist;
use App\Domains\ECommerce\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlist = auth()->user()->wishlist()
            ->with(['product.vendor', 'product.primaryImage'])
            ->latest()
            ->paginate(20);

        return view('frontend.wishlist.index', compact('wishlist'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $added = Wishlist::toggle(auth()->user(), $product);

        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'success' => true,
                'added' => $added,
                'message' => $added ? 'Added to wishlist' : 'Removed from wishlist',
            ]);
        }

        return back()->with('success', $added ? 'Added to wishlist' : 'Removed from wishlist');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->delete();

        if ($this->shouldReturnJson($request)) {
            return response()->json([
                'success' => true,
                'message' => 'Removed from wishlist',
            ]);
        }

        return back()->with('success', 'Removed from wishlist');
    }

    private function shouldReturnJson(Request $request): bool
    {
        return $request->ajax() || $request->expectsJson() || $request->wantsJson();
    }
}
