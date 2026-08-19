@extends('layouts.app')

@section('content')
    @php
        $summary = $page->meta_description ?: \Illuminate\Support\Str::limit(trim(strip_tags($page->content)), 220);
    @endphp

    <x-frontend.page-hero
        theme="contact"
        eyebrow="Support Center"
        :title="$page->title"
        :summary="$summary"
        :tags="[
            'Response within business hours',
            'Dedicated customer support',
            'Vendor assistance available',
        ]" />

    <section class="section static-page-body">
        <div class="container">
            <div class="contact-layout">
                <div class="contact-left">
                    <article class="card contact-content">
                        {!! $page->content !!}
                    </article>

                    <div class="contact-highlights">
                        <article class="card contact-highlight">
                            <h3>Call Us</h3>
                            <p>Need immediate support for orders or account issues.</p>
                            <a href="tel:+8801700000000">+880 1700-000000</a>
                        </article>

                        <article class="card contact-highlight">
                            <h3>Email Support</h3>
                            <p>Share detailed queries and get a tracked response.</p>
                            <a href="mailto:support@novamart.com">support@novamart.com</a>
                        </article>

                        <article class="card contact-highlight">
                            <h3>Business Hours</h3>
                            <p>Saturday - Thursday</p>
                            <strong>9:00 AM - 9:00 PM</strong>
                        </article>
                    </div>
                </div>

                <aside class="card contact-form-card">
                    <h3>Send a Message</h3>
                    <p class="contact-form-note">
                        Fill in the form and our team will follow up with you shortly.
                    </p>

                    @if(session('success'))
                        <div class="contact-alert contact-alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="contact-alert contact-alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form class="contact-form" action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="contact-name">Full Name</label>
                            <input id="contact-name" name="name" type="text" class="form-control"
                                placeholder="Enter your full name"
                                value="{{ old('name', auth()->user()->name ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="contact-email">Email Address</label>
                            <input id="contact-email" name="email" type="email" class="form-control"
                                placeholder="you@example.com"
                                value="{{ old('email', auth()->user()->email ?? '') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="contact-phone">Phone Number</label>
                            <input id="contact-phone" name="phone" type="tel" class="form-control"
                                placeholder="+8801XXXXXXXXX" value="{{ old('phone', auth()->user()->phone ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label for="contact-subject">Subject</label>
                            <select id="contact-subject" name="subject" class="form-control" required>
                                <option value="">Select a topic</option>
                                <option value="Order Support" {{ old('subject') === 'Order Support' ? 'selected' : '' }}>Order Support</option>
                                <option value="Payment Issue" {{ old('subject') === 'Payment Issue' ? 'selected' : '' }}>Payment Issue</option>
                                <option value="Return & Refund" {{ old('subject') === 'Return & Refund' ? 'selected' : '' }}>Return & Refund</option>
                                <option value="Vendor Query" {{ old('subject') === 'Vendor Query' ? 'selected' : '' }}>Vendor Query</option>
                                <option value="General Inquiry" {{ old('subject') === 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="contact-message">Message</label>
                            <textarea id="contact-message" name="message" class="form-control" rows="5"
                                placeholder="Write your message..." required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary contact-submit">Submit Inquiry</button>
                    </form>
                </aside>
            </div>
        </div>
    </section>
@endsection
