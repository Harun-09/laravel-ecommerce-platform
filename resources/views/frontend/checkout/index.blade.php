@extends('layouts.app')

@section('content')
<div class="container section">
    <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 30px;">Checkout</h1>
    
    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST"
        data-shipping-endpoint="{{ route('checkout.shipping-methods') }}"
        data-base-cart-total="{{ (float) $cart->total }}"
        data-selected-shipping-method="{{ (int) old('shipping_method', $selectedShippingMethod?->id ?? 0) }}">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 30px;">
            <!-- Checkout Form -->
            <div>
                <!-- Shipping Information -->
                <div class="card" style="padding: 24px; margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">
                        <i class="fas fa-truck" style="color: #6366f1; margin-right: 8px;"></i>
                        Shipping Information
                    </h3>
                    
                    @if($addresses->isNotEmpty())
                        <div style="margin-bottom: 20px;">
                            <label style="font-weight: 500; margin-bottom: 12px; display: block;">Select Saved Address</label>
                            <div style="display: grid; gap: 12px;">
                                @foreach($addresses as $address)
                                    <label style="display: flex; gap: 12px; padding: 16px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;">
                                        <input type="radio" name="saved_address" value="{{ $address->id }}"
                                               class="js-saved-address"
                                               data-name="{{ $address->name }}"
                                               data-phone="{{ $address->phone }}"
                                               data-address-line="{{ $address->address_line_1 }}"
                                               data-city="{{ $address->city }}"
                                               data-postal-code="{{ $address->postal_code }}"
                                               {{ $address->is_default ? 'checked' : '' }}>
                                        <div>
                                            <p style="font-weight: 600;">{{ $address->name }} - {{ $address->phone }}</p>
                                            <p style="font-size: 14px; color: #6b7280;">{{ $address->address_line_1 }}, {{ $address->city }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">
                    @endif
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="shipping_name" class="form-control" required 
                                   value="{{ old('shipping_name', auth()->user()->name ?? '') }}">
                            @error('shipping_name')
                                <span style="color: #ef4444; font-size: 13px;">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="shipping_phone" class="form-control" required 
                                   value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}">
                            @error('shipping_phone')
                                <span style="color: #ef4444; font-size: 13px;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="shipping_email" class="form-control" 
                               value="{{ old('shipping_email', auth()->user()->email ?? '') }}">
                    </div>
                    
                    <div class="form-group">
                        <label>Address *</label>
                        <textarea name="shipping_address" class="form-control" rows="2" required>{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <span style="color: #ef4444; font-size: 13px;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label>City *</label>
                            <select name="shipping_city" id="shipping_city" class="form-control" required>
                                <option value="">Select City</option>
                                <option value="Dhaka" {{ $selectedCity == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                <option value="Chittagong" {{ $selectedCity == 'Chittagong' ? 'selected' : '' }}>Chittagong</option>
                                <option value="Sylhet" {{ $selectedCity == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                                <option value="Rajshahi" {{ $selectedCity == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                                <option value="Khulna" {{ $selectedCity == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                                <option value="Barisal" {{ $selectedCity == 'Barisal' ? 'selected' : '' }}>Barisal</option>
                                <option value="Rangpur" {{ $selectedCity == 'Rangpur' ? 'selected' : '' }}>Rangpur</option>
                                <option value="Mymensingh" {{ $selectedCity == 'Mymensingh' ? 'selected' : '' }}>Mymensingh</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Postal Code</label>
                            <input type="text" name="shipping_postal_code" class="form-control" 
                                   value="{{ old('shipping_postal_code') }}">
                        </div>
                    </div>
                </div>
                <!-- Shipping Method -->
                <div class="card" style="padding: 24px; margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">
                        <i class="fas fa-shipping-fast" style="color: #6366f1; margin-right: 8px;"></i>
                        Shipping Method
                    </h3>

                    <div style="margin-bottom: 14px; display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; background: #eff6ff; border-radius: 999px;">
                        <i class="fas fa-map-marker-alt" style="color: #2563eb;"></i>
                        <span style="font-size: 13px; color: #1e40af; font-weight: 600;">Delivery Zone:</span>
                        <span id="delivery-zone-badge" style="font-size: 13px; color: #1d4ed8;">
                            {{ $shippingZone?->name ?? 'Not Selected' }}
                        </span>
                    </div>
                    
                    <div id="shipping-methods">
                        @forelse($shippingMethods as $method)
                            @php
                                $methodQuote = $method->calculateQuote($cart, $selectedPaymentMethod === 'cod');
                                $isSelectedMethod = (int) old('shipping_method', $selectedShippingMethod?->id) === (int) $method->id;
                            @endphp
                            <label style="display: flex; align-items: center; justify-content: space-between; padding: 16px; border: 2px solid #e5e7eb; border-radius: 8px; margin-bottom: 12px; cursor: pointer;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <input
                                        type="radio"
                                        name="shipping_method"
                                        value="{{ $method->id }}"
                                        data-shipping-cost="{{ $methodQuote['shipping_cost'] }}"
                                        data-shipping-discount="{{ $methodQuote['shipping_discount'] }}"
                                        data-cod-fee="{{ $methodQuote['cod_fee'] }}"
                                        data-total-cost="{{ $methodQuote['total_shipping_cost'] }}"
                                        data-cod-available="{{ $method->is_cod_available ? '1' : '0' }}"
                                        {{ $isSelectedMethod ? 'checked' : '' }}
                                    >
                                    <div>
                                        <p style="font-weight: 600;">{{ $method->name }}</p>
                                        <p style="font-size: 13px; color: #6b7280;">{{ $method->description ?? 'Est. delivery: ' . $method->estimated_days }}</p>
                                        @if(!$method->is_cod_available)
                                            <p style="font-size: 12px; color: #dc2626; margin-top: 4px;">COD not available on this method</p>
                                        @endif
                                        @if(!empty($methodQuote['is_free_shipping_applied']))
                                            <p style="font-size: 12px; color: #166534; margin-top: 4px;">Free shipping coupon applied</p>
                                        @endif
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <p style="font-weight: 600; color: #6366f1;">
                                        {{ $methodQuote['total_shipping_cost'] > 0 ? store_money($methodQuote['total_shipping_cost']) : 'FREE' }}
                                    </p>
                                    @if($methodQuote['cod_fee'] > 0)
                                        <p style="font-size: 12px; color: #6b7280;">Includes COD fee {{ store_money($methodQuote['cod_fee']) }}</p>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div style="padding: 16px; border-radius: 8px; background: #fff7ed; color: #9a3412;">
                                No shipping method is available for this delivery zone.
                            </div>
                        @endforelse
                    </div>
                </div>
                <!-- Payment Method -->
                <div class="card" style="padding: 24px; margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">
                        <i class="fas fa-credit-card" style="color: #6366f1; margin-right: 8px;"></i>
                        Payment Method
                    </h3>
                    
                    <div style="display: grid; gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="payment_method" value="cod" {{ $selectedPaymentMethod === 'cod' ? 'checked' : '' }}>
                            <i class="fas fa-money-bill-wave" style="font-size: 24px; color: #16a34a;"></i>
                            <div>
                                <p style="font-weight: 600;">Cash on Delivery</p>
                                <p style="font-size: 13px; color: #6b7280;">Pay when you receive your order</p>
                            </div>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;">
                            <input
                                type="radio"
                                name="payment_method"
                                value="stripe"
                                {{ $selectedPaymentMethod === 'stripe' ? 'checked' : '' }}
                                {{ $isStripeConfigured ? '' : 'disabled' }}
                            >
                            <i class="fas fa-credit-card" style="font-size: 24px; color: #635bff;"></i>
                            <div>
                                <p style="font-weight: 600;">Stripe (Card Payment)</p>
                                <p style="font-size: 13px; color: #6b7280;">Pay securely with Visa, MasterCard, and international cards</p>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer;">
                            <input
                                type="radio"
                                name="payment_method"
                                value="sslcommerz"
                                {{ $selectedPaymentMethod === 'sslcommerz' ? 'checked' : '' }}
                                {{ $isSslcommerzConfigured ? '' : 'disabled' }}
                            >
                            <i class="fas fa-credit-card" style="font-size: 24px; color: #e5383b;"></i>
                            <div>
                                <p style="font-weight: 600;">SSLCOMMERZ (Online Payment)</p>
                                <p style="font-size: 13px; color: #6b7280;">bKash, Nagad, Visa, MasterCard, Mobile Banking & more</p>
                            </div>
                        </label>
                        @if(!$isSslcommerzConfigured)
                            <p style="font-size: 12px; color: #b45309; margin-top: -2px;">
                                Online payment is currently disabled. Please choose Cash on Delivery.
                            </p>
                        @endif
                    </div>
                    @error('payment_method')
                        <span style="color: #ef4444; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Order Notes -->
                <div class="card" style="padding: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">
                        <i class="fas fa-sticky-note" style="color: #6366f1; margin-right: 8px;"></i>
                        Order Notes (Optional)
                    </h3>
                    <textarea name="customer_notes" class="form-control" rows="3" placeholder="Any special instructions for delivery...">{{ old('customer_notes') }}</textarea>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div>
                <div class="card" style="padding: 24px; position: sticky; top: 100px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Order Summary</h3>
                    
                    <!-- Cart Items -->
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                        @foreach($cart->items as $item)
                            <div style="display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                <img src="{{ $item->product->primary_image_url }}" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                <div style="flex: 1;">
                                    <p style="font-size: 14px; font-weight: 500;">{{ Str::limit($item->product->name, 40) }}</p>
                                    <p style="font-size: 13px; color: #6b7280;">Qty: {{ $item->quantity }}</p>
                                </div>
                                <p style="font-weight: 600;">{{ store_money($item->total_price) }}</p>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 16px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">Subtotal</span>
                            <span>{{ store_money($cart->subtotal) }}</span>
                        </div>
                        
                        @if($cart->discount_amount > 0)
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #16a34a;">
                                <span>Discount</span>
                                <span>-{{ store_money($cart->discount_amount) }}</span>
                            </div>
                        @endif
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">Shipping</span>
                            <span id="shipping-cost">{{ store_money($shippingQuote['shipping_cost'] ?? 0) }}</span>
                        </div>

                        <div id="shipping-discount-row" style="display: {{ ($shippingQuote['shipping_discount'] ?? 0) > 0 ? 'flex' : 'none' }}; justify-content: space-between; margin-bottom: 8px; color: #16a34a;">
                            <span>Shipping Discount</span>
                            <span id="shipping-discount">-{{ store_money($shippingQuote['shipping_discount'] ?? 0) }}</span>
                        </div>

                        <div id="cod-fee-row" style="display: {{ ($shippingQuote['cod_fee'] ?? 0) > 0 ? 'flex' : 'none' }}; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">COD Fee</span>
                            <span id="cod-fee">{{ store_money($shippingQuote['cod_fee'] ?? 0) }}</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; padding-top: 16px; border-top: 1px solid #e5e7eb; margin-top: 16px;">
                            <span style="font-size: 18px; font-weight: 600;">Total</span>
                            <span id="order-total" style="font-size: 24px; font-weight: 700; color: #6366f1;">
                                {{ store_money($cart->total + ($shippingQuote['total_shipping_cost'] ?? 0)) }}
                            </span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 24px; padding: 16px; font-size: 16px;">
                        <i class="fas fa-lock" style="margin-right: 8px;"></i> Place Order
                    </button>
                    
                    <p style="text-align: center; font-size: 13px; color: #6b7280; margin-top: 16px;">
                        By placing this order, you agree to our 
                        <a href="{{ route('page.show', 'terms-conditions') }}" style="color: #6366f1;">Terms & Conditions</a>
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
