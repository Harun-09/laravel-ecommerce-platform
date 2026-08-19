@extends('admin.layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h1>Courier & Shipping</h1>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
            <span>Shipping</span>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3>Create Delivery Zone</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.shipping.zones.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 12px;">
                <div class="form-group" style="margin: 0;">
                    <label>Zone Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Inside Dhaka" required>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label>Code</label>
                    <input type="text" name="code" class="form-control" placeholder="inside_dhaka">
                </div>
                <div class="form-group" style="margin: 0;">
                    <label>Order</label>
                    <input type="number" name="order" class="form-control" value="0" min="0">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 12px;">
                <label>Regions (comma separated cities/areas)</label>
                <textarea name="regions" class="form-control" rows="2" placeholder="Dhaka, Mirpur, Uttara"></textarea>
            </div>

            <label style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked>
                <span>Active</span>
            </label>

            <div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Zone
                </button>
            </div>
        </form>
    </div>
</div>

@forelse($zones as $zone)
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3>{{ $zone->name }} ({{ $zone->code ?: 'no-code' }})</h3>
            <form action="{{ route('admin.shipping.zones.destroy', $zone) }}" method="POST" onsubmit="return confirm('Delete this zone?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Delete Zone
                </button>
            </form>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.shipping.zones.update', $zone) }}" method="POST" style="margin-bottom: 20px;">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 12px;">
                    <div class="form-group" style="margin: 0;">
                        <label>Zone Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $zone->name }}" required>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $zone->code }}">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>Order</label>
                        <input type="number" name="order" class="form-control" value="{{ $zone->order }}" min="0">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label>Regions (comma separated)</label>
                    <textarea name="regions" class="form-control" rows="2">{{ implode(', ', $zone->regions ?? []) }}</textarea>
                </div>

                <div style="display: flex; align-items: center; gap: 14px;">
                    <label style="display: inline-flex; align-items: center; gap: 8px;">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $zone->is_active ? 'checked' : '' }}>
                        <span>Active Zone</span>
                    </label>

                    <button type="submit" class="btn btn-outline">
                        <i class="fas fa-save"></i> Save Zone
                    </button>
                </div>
            </form>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Type</th>
                            <th>Base Cost</th>
                            <th>COD Fee</th>
                            <th>Min Order</th>
                            <th>Est. Days</th>
                            <th>Flags</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zone->methods as $method)
                            <tr>
                                <td colspan="8" style="padding: 0; border-bottom: none;">
                                    <form action="{{ route('admin.shipping.methods.update', $method) }}" method="POST" style="padding: 12px; border-bottom: 1px solid #e2e8f0;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="shipping_zone_id" value="{{ $zone->id }}">
                                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                                            <input type="text" name="name" class="form-control" value="{{ $method->name }}" placeholder="Method name" required>
                                            <select name="type" class="form-control">
                                                <option value="flat" {{ $method->type === 'flat' ? 'selected' : '' }}>Flat</option>
                                                <option value="weight_based" {{ $method->type === 'weight_based' ? 'selected' : '' }}>Weight</option>
                                                <option value="price_based" {{ $method->type === 'price_based' ? 'selected' : '' }}>Price</option>
                                                <option value="free" {{ $method->type === 'free' ? 'selected' : '' }}>Free</option>
                                            </select>
                                            <input type="number" step="0.01" min="0" name="cost" class="form-control" value="{{ $method->cost }}" placeholder="Base cost">
                                            <input type="number" step="0.01" min="0" name="cod_fee" class="form-control" value="{{ $method->cod_fee }}" placeholder="COD fee">
                                            <input type="number" step="0.01" min="0" name="minimum_order_amount" class="form-control" value="{{ $method->minimum_order_amount }}" placeholder="Min order">
                                            <input type="text" name="estimated_days" class="form-control" value="{{ $method->estimated_days }}" placeholder="2-3 days">
                                        </div>
                                        <div style="display: grid; grid-template-columns: 3fr 1fr 1fr auto auto; gap: 10px; align-items: center;">
                                            <input type="text" name="description" class="form-control" value="{{ $method->description }}" placeholder="Description">
                                            <input type="number" min="0" name="order" class="form-control" value="{{ $method->order }}" placeholder="Order">

                                            <label style="display: inline-flex; align-items: center; gap: 6px;">
                                                <input type="hidden" name="is_cod_available" value="0">
                                                <input type="checkbox" name="is_cod_available" value="1" {{ $method->is_cod_available ? 'checked' : '' }}>
                                                <span style="font-size: 13px;">COD</span>
                                            </label>

                                            <label style="display: inline-flex; align-items: center; gap: 6px;">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" name="is_active" value="1" {{ $method->is_active ? 'checked' : '' }}>
                                                <span style="font-size: 13px;">Active</span>
                                            </label>

                                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                                <button type="submit" class="btn btn-sm btn-outline">
                                                    <i class="fas fa-save"></i> Update
                                                </button>
                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    formaction="{{ route('admin.shipping.methods.destroy', $method) }}"
                                                    formmethod="POST"
                                                    onclick="event.preventDefault(); if(confirm('Delete this method?')) { const methodInput = document.createElement('input'); methodInput.type = 'hidden'; methodInput.name = '_method'; methodInput.value = 'DELETE'; this.form.appendChild(methodInput); this.form.submit(); }"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: #64748b; padding: 20px;">No methods for this zone yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 18px; padding-top: 18px; border-top: 1px dashed #cbd5e1;">
                <h4 style="font-size: 15px; margin-bottom: 10px;">Add Method to {{ $zone->name }}</h4>
                <form action="{{ route('admin.shipping.methods.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="shipping_zone_id" value="{{ $zone->id }}">

                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="name" class="form-control" placeholder="Standard Delivery" required>
                        <select name="type" class="form-control">
                            <option value="flat">Flat</option>
                            <option value="weight_based">Weight</option>
                            <option value="price_based">Price</option>
                            <option value="free">Free</option>
                        </select>
                        <input type="number" step="0.01" min="0" name="cost" class="form-control" placeholder="Base cost" required>
                        <input type="number" step="0.01" min="0" name="cod_fee" class="form-control" value="0" placeholder="COD fee">
                        <input type="number" step="0.01" min="0" name="minimum_order_amount" class="form-control" placeholder="Min order">
                        <input type="text" name="estimated_days" class="form-control" placeholder="2-3 days">
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr auto auto auto; gap: 10px; align-items: center;">
                        <input type="text" name="description" class="form-control" placeholder="Description">
                        <input type="number" min="0" name="order" class="form-control" value="0" placeholder="Order">

                        <label style="display: inline-flex; align-items: center; gap: 6px;">
                            <input type="hidden" name="is_cod_available" value="0">
                            <input type="checkbox" name="is_cod_available" value="1" checked>
                            <span style="font-size: 13px;">COD</span>
                        </label>

                        <label style="display: inline-flex; align-items: center; gap: 6px;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span style="font-size: 13px;">Active</span>
                        </label>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Method
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="card">
        <div class="card-body" style="text-align: center; color: #64748b;">
            No delivery zone found. Create your first zone above.
        </div>
    </div>
@endforelse
@endsection
