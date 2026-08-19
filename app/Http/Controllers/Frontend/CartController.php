<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCartData();
        return view('frontend.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variation_id' => 'nullable|exists:product_variations,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $result = $this->cartService->addToCart($product, $request->quantity, $request->variation_id);

        if ($this->shouldReturnJson($request)) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $result = $this->cartService->updateQuantity($request->item_id, $request->quantity);

        if ($this->shouldReturnJson($request)) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
        ]);

        $result = $this->cartService->removeItem($request->item_id);

        if ($this->shouldReturnJson($request)) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $result = $this->cartService->applyCoupon($request->coupon_code);

        if ($this->shouldReturnJson($request)) {
            return response()->json($result);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function removeCoupon(Request $request)
    {
        $result = $this->cartService->removeCoupon();

        if ($this->shouldReturnJson($request)) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    public function getCartCount()
    {
        $cart = $this->cartService->getCartData();
        return response()->json(['count' => $cart['total_items']]);
    }

    private function shouldReturnJson(Request $request): bool
    {
        return $request->ajax() || $request->expectsJson() || $request->wantsJson();
    }
}
