@extends('layouts.app')

@section('content')
    <x-frontend.page-hero
        theme="credit"
        eyebrow="Payment Options"
        title="Credit Card Payments"
        summary="Pay securely at checkout using supported credit and debit cards. All payments are processed through encrypted channels."
        :tags="['Secure Checkout', 'Encrypted Transactions', '24/7 Support']" />

    <section class="section static-page-body">
        <div class="container">
            <div class="credit-layout">
                <article class="card credit-content">
                    <h2>How It Works</h2>
                    <ol>
                        <li>Add your products to cart and proceed to checkout.</li>
                        <li>Select your shipping address and delivery method.</li>
                        <li>Choose online payment to pay with your card.</li>
                        <li>Complete payment and receive instant confirmation.</li>
                    </ol>

                    <h3>Security Promise</h3>
                    <p>NovaMart does not expose or store full card details in storefront pages. Payment data is handled by secure payment providers with industry-standard safeguards.</p>

                    <h3>Need Assistance?</h3>
                    <p>If your card payment fails or remains pending, contact support with your order number for quick verification.</p>
                </article>

                <aside class="credit-side">
                    <div class="card credit-side__card">
                        <h3>Supported Cards</h3>
                        <p>Most major local and international card networks are supported through available payment gateways.</p>
                    </div>

                    <div class="card credit-side__card">
                        <h3>Payment Issues</h3>
                        <p>For declined or delayed transactions, confirm your card status with your bank and try again.</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">Contact Support</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
