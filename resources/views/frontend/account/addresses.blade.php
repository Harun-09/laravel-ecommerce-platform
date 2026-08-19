@extends('layouts.app')

@section('content')
    <div class="container section">
        <div class="account-layout-grid">
            @include('frontend.account.partials.sidebar', ['user' => auth()->user()])

            <div>
                <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 20px;">My Addresses</h1>

                @if($errors->any())
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="card" style="padding: 24px; margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Add New Address</h3>

                    <form action="{{ route('account.addresses.store') }}" method="POST">
                        @csrf

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Email (Optional)</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>

                        <div class="form-group">
                            <label>Address Line 1</label>
                            <textarea name="address_line_1" class="form-control" rows="2" required>{{ old('address_line_1') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Address Line 2 (Optional)</label>
                            <textarea name="address_line_2" class="form-control" rows="2">{{ old('address_line_2') }}</textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                            </div>
                            <div class="form-group">
                                <label>Postal Code</label>
                                <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" name="country" class="form-control" value="{{ old('country', 'Bangladesh') }}">
                            </div>
                            <div class="form-group">
                                <label>Address Type</label>
                                <select name="type" class="form-control" required>
                                    <option value="shipping" {{ old('type') === 'shipping' ? 'selected' : '' }}>Shipping</option>
                                    <option value="billing" {{ old('type') === 'billing' ? 'selected' : '' }}>Billing</option>
                                    <option value="both" {{ old('type', 'both') === 'both' ? 'selected' : '' }}>Both</option>
                                </select>
                            </div>
                        </div>

                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px; font-size: 14px;">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                            Set as default address
                        </label>

                        <button type="submit" class="btn btn-primary">Save Address</button>
                    </form>
                </div>

                <div class="card" style="padding: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Saved Addresses</h3>

                    @if($addresses->isEmpty())
                        <p style="color: #6b7280;">No addresses added yet.</p>
                    @else
                        <div style="display: grid; gap: 16px;">
                            @foreach($addresses as $address)
                                <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <strong>{{ $address->name }}</strong>
                                            @if($address->is_default)
                                                <span style="font-size: 11px; background: #dcfce7; color: #166534; padding: 3px 8px; border-radius: 999px;">Default</span>
                                            @endif
                                        </div>
                                        <span style="font-size: 12px; color: #6b7280; text-transform: capitalize;">
                                            {{ $address->type }}
                                        </span>
                                    </div>

                                    <form action="{{ route('account.addresses.update', $address) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                            <input type="text" name="name" class="form-control" value="{{ $address->name }}" required>
                                            <input type="text" name="phone" class="form-control" value="{{ $address->phone }}" required>
                                        </div>

                                        <div style="margin-top: 12px;">
                                            <textarea name="address_line_1" class="form-control" rows="2" required>{{ $address->address_line_1 }}</textarea>
                                        </div>

                                        <div style="margin-top: 12px;">
                                            <textarea name="address_line_2" class="form-control" rows="2">{{ $address->address_line_2 }}</textarea>
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-top: 12px;">
                                            <input type="text" name="city" class="form-control" value="{{ $address->city }}" required>
                                            <input type="text" name="state" class="form-control" value="{{ $address->state }}">
                                            <input type="text" name="postal_code" class="form-control" value="{{ $address->postal_code }}">
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                                            <input type="text" name="country" class="form-control" value="{{ $address->country }}">
                                            <select name="type" class="form-control">
                                                <option value="shipping" {{ $address->type === 'shipping' ? 'selected' : '' }}>Shipping</option>
                                                <option value="billing" {{ $address->type === 'billing' ? 'selected' : '' }}>Billing</option>
                                                <option value="both" {{ $address->type === 'both' ? 'selected' : '' }}>Both</option>
                                            </select>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;">
                                            <label style="display: flex; align-items: center; gap: 8px; font-size: 14px;">
                                                <input type="checkbox" name="is_default" value="1" {{ $address->is_default ? 'checked' : '' }}>
                                                Set as default
                                            </label>
                                            <div style="display: flex; gap: 8px;">
                                                <button type="submit" class="btn btn-outline" style="padding: 8px 12px;">Update</button>
                                            </div>
                                        </div>
                                    </form>

                                    <form action="{{ route('account.addresses.delete', $address) }}" method="POST" style="margin-top: 10px;"
                                        onsubmit="return confirm('Delete this address?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="border: none; background: none; color: #dc2626; font-size: 13px; cursor: pointer;">
                                            <i class="fas fa-trash-alt"></i> Delete Address
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
