@extends('layouts.app')

@section('content')
    <div class="container section" id="cart-page" data-update-url="{{ route('cart.update') }}"
        data-remove-url="{{ route('cart.remove') }}" data-apply-coupon-url="{{ route('cart.apply-coupon') }}"
        data-remove-coupon-url="{{ route('cart.remove-coupon') }}">
        <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 30px;">Shopping Cart</h1>

        @if(empty($cart['items']) || count($cart['items']) === 0)
            <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 12px;">
                <i class="fas fa-shopping-cart" style="font-size: 80px; color: #d1d5db; margin-bottom: 24px;"></i>
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 12px;">Your cart is empty</h2>
                <p style="color: #6b7280; margin-bottom: 24px;">Looks like you haven't added any products yet.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a>
            </div>
        @else
            <div style="display: grid; grid-template-columns: 1fr 380px; gap: 30px;">
                <!-- Cart Items -->
                <div class="card" style="padding: 24px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e5e7eb;">
                                <th style="text-align: left; padding: 16px 0; font-weight: 600;">Product</th>
                                <th style="text-align: center; padding: 16px 0; font-weight: 600;">Price</th>
                                <th style="text-align: center; padding: 16px 0; font-weight: 600;">Quantity</th>
                                <th style="text-align: right; padding: 16px 0; font-weight: 600;">Total</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            @foreach($cart['items'] as $item)
                                <tr style="border-bottom: 1px solid #f3f4f6;" data-item-id="{{ $item['id'] }}">
                                    <td style="padding: 20px 0;">
                                        <div style="display: flex; align-items: center; gap: 16px;">
                                            <img src="{{ $item['product_image'] }}" alt="{{ $item['product_name'] }}"
                                                style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                            <div>
                                                <h4 style="font-weight: 600; margin-bottom: 4px;">{{ $item['product_name'] }}</h4>
                                                @if($item['variation'])
                                                    <p style="font-size: 13px; color: #6b7280;">{{ $item['variation'] }}</p>
                                                @endif
                                                @if(!$item['in_stock'])
                                                    <span style="font-size: 12px; color: #ef4444;"><i
                                                            class="fas fa-exclamation-circle"></i> Out of Stock</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center; font-weight: 500;">{{ store_money($item['price']) }}</td>
                                    <td style="text-align: center;">
                                        <div
                                            style="display: inline-flex; align-items: center; border: 1px solid #e5e7eb; border-radius: 6px;">
                                            <button type="button" class="js-cart-qty"
                                                data-item-id="{{ $item['id'] }}"
                                                data-quantity="{{ $item['quantity'] - 1 }}"
                                                style="width: 36px; height: 36px; border: none; background: #f3f4f6; cursor: pointer;">-</button>
                                            <span
                                                style="width: 40px; text-align: center; font-weight: 500;">{{ $item['quantity'] }}</span>
                                            <button type="button" class="js-cart-qty"
                                                data-item-id="{{ $item['id'] }}"
                                                data-quantity="{{ $item['quantity'] + 1 }}"
                                                style="width: 36px; height: 36px; border: none; background: #f3f4f6; cursor: pointer;">+</button>
                                        </div>
                                    </td>
                                    <td style="text-align: right; font-weight: 600; color: #6366f1;">
                                        {{ store_money($item['total']) }}</td>
                                    <td style="text-align: right;">
                                        <button type="button" class="js-cart-remove" data-item-id="{{ $item['id'] }}"
                                            style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px;"
                                            title="Remove">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div
                        style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                        <a href="{{ route('products.index') }}" style="color: #6366f1; font-weight: 500;">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Order Summary -->
                <div>
                    <div class="card" style="padding: 24px; position: sticky; top: 100px;">
                        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Order Summary</h3>

                        <!-- Coupon -->
                        <div style="margin-bottom: 20px;">
                            @if($cart['coupon_code'])
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #dcfce7; border-radius: 8px;">
                                    <div>
                                        <span style="font-weight: 500; color: #166534;">{{ $cart['coupon_code'] }}</span>
                                        <span style="font-size: 13px; color: #166534; margin-left: 8px;">applied</span>
                                        @if(!empty($cart['has_free_shipping_coupon']))
                                            <p style="font-size: 12px; color: #166534; margin-top: 4px;">
                                                Free shipping will apply at checkout.
                                            </p>
                                        @endif
                                    </div>
                                    <button type="button" class="js-remove-coupon"
                                        style="background: none; border: none; color: #166534; cursor: pointer;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @else
                                <form class="js-coupon-form" style="display: flex; gap: 8px;">
                                    <input type="text" id="coupon-code" placeholder="Enter coupon code"
                                        style="flex: 1; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px;">
                                    <button type="submit" class="btn btn-outline" style="padding: 12px 16px;">Apply</button>
                                </form>
                            @endif
                        </div>

                        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #6b7280;">Subtotal</span>
                                <span style="font-weight: 500;">{{ store_money($cart['subtotal']) }}</span>
                            </div>

                            @if($cart['discount'] > 0)
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #16a34a;">
                                    <span>Discount</span>
                                    <span>-{{ store_money($cart['discount']) }}</span>
                                </div>
                            @endif

                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #6b7280;">Shipping</span>
                                <span style="color: #6b7280;">Calculated at checkout</span>
                            </div>

                            @if(!empty($cart['has_free_shipping_coupon']))
                                <div style="display: flex; justify-content: space-between; margin-bottom: 12px; color: #16a34a;">
                                    <span>Shipping Coupon</span>
                                    <span>Will be applied at checkout</span>
                                </div>
                            @endif

                            <div
                                style="display: flex; justify-content: space-between; padding-top: 16px; border-top: 1px solid #e5e7eb; margin-top: 16px;">
                                <span style="font-size: 18px; font-weight: 600;">Total</span>
                                <span
                                    style="font-size: 24px; font-weight: 700; color: #6366f1;">{{ store_money($cart['total']) }}</span>
                            </div>
                        </div>

                        @auth
                            @if(auth()->user()->hasRole('customer'))
                                <a href="{{ route('checkout.index') }}" class="btn btn-primary"
                                    style="width: 100%; margin-top: 24px; padding: 16px; font-size: 16px;">
                                    Proceed to Checkout <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                                </a>
                            @else
                                <button type="button" class="btn btn-outline"
                                    style="width: 100%; margin-top: 24px; padding: 16px; font-size: 16px; cursor: not-allowed; opacity: 0.75;"
                                    disabled>
                                    Customer Account Required
                                </button>
                                <p style="margin-top: 10px; font-size: 12px; color: #92400e;">
                                    Checkout/payment is available for customer role only.
                                </p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary"
                                style="width: 100%; margin-top: 24px; padding: 16px; font-size: 16px;">
                                Login to Checkout <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                            </a>
                        @endauth

                        <div style="text-align: center; margin-top: 16px;">
                            <p style="font-size: 13px; color: #6b7280;">
                                <i class="fas fa-lock"></i> Secure checkout powered by SSL
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
