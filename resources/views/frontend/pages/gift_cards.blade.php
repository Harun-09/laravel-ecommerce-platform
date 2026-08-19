@extends('layouts.app')

@section('content')
    <x-frontend.page-hero
        theme="gift"
        eyebrow="Gift Solutions"
        title="Gift Cards"
        summary="NovaMart Gift Cards are being prepared for a safer and smoother launch. This service is not active yet."
        :tags="['Launching Soon', 'Digital Delivery', 'Secure Redemption']" />

    <section class="section static-page-body">
        <div class="container">
            <div class="gift-layout">
                <article class="card gift-content">
                    <h2>Current Status</h2>
                    <p>Gift Card purchase and redemption are not available in checkout at this moment. We will enable it after full payment and fraud checks are completed.</p>

                    <h3>What to Expect</h3>
                    <ul>
                        <li>Multiple card values for personal and business gifting.</li>
                        <li>Digital codes with clear validity and terms.</li>
                        <li>Easy redemption flow during checkout.</li>
                    </ul>

                    <h3>Need a Gift Right Now?</h3>
                    <p>You can still purchase products directly and send them to your recipient address at checkout.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Shop Products</a>
                </article>

                <aside class="gift-side">
                    <div class="card gift-side__card">
                        <h3>Get Updates</h3>
                        <p>For availability announcements, follow NovaMart notices and support updates.</p>
                    </div>

                    <div class="card gift-side__card">
                        <h3>Support Team</h3>
                        <p>Need help with gifting options or bulk purchase guidance?</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary">Contact Support</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
