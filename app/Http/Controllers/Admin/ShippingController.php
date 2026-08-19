<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\ShippingMethod;
use App\Domains\ECommerce\Models\ShippingZone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShippingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view settings')->only(['index']);
        $this->middleware('can:edit settings')->only([
            'storeZone',
            'updateZone',
            'destroyZone',
            'storeMethod',
            'updateMethod',
            'destroyMethod',
        ]);
    }

    public function index()
    {
        $zones = ShippingZone::query()
            ->with(['methods' => fn($query) => $query->ordered()])
            ->ordered()
            ->get();

        return view('admin.shipping.index', compact('zones'));
    }

    public function storeZone(Request $request)
    {
        $data = $this->validateZone($request);

        ShippingZone::create($data);

        return back()->with('success', 'Delivery zone created successfully.');
    }

    public function updateZone(Request $request, ShippingZone $shippingZone)
    {
        $data = $this->validateZone($request, $shippingZone->id);

        $shippingZone->update($data);

        return back()->with('success', 'Delivery zone updated successfully.');
    }

    public function destroyZone(ShippingZone $shippingZone)
    {
        if ($shippingZone->methods()->exists()) {
            return back()->with('error', 'Delete methods under this zone first.');
        }

        $shippingZone->delete();

        return back()->with('success', 'Delivery zone deleted successfully.');
    }

    public function storeMethod(Request $request)
    {
        $data = $this->validateMethod($request);

        ShippingMethod::create($data);

        return back()->with('success', 'Shipping method created successfully.');
    }

    public function updateMethod(Request $request, ShippingMethod $shippingMethod)
    {
        $data = $this->validateMethod($request);

        $shippingMethod->update($data);

        return back()->with('success', 'Shipping method updated successfully.');
    }

    public function destroyMethod(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();

        return back()->with('success', 'Shipping method deleted successfully.');
    }

    private function validateZone(Request $request, ?int $zoneId = null): array
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'code' => [
                'nullable',
                'string',
                'max:60',
                'regex:/^[a-z0-9_\\-]+$/',
                Rule::unique('shipping_zones', 'code')->ignore($zoneId),
            ],
            'regions' => 'nullable|string|max:2000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        return [
            'name' => $request->name,
            'code' => $request->filled('code') ? strtolower(trim((string) $request->code)) : null,
            'regions' => $this->parseRegions((string) $request->input('regions', '')),
            'order' => (int) $request->input('order', 0),
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function validateMethod(Request $request): array
    {
        $request->validate([
            'shipping_zone_id' => 'required|exists:shipping_zones,id',
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:flat,weight_based,price_based,free',
            'cost' => 'required|numeric|min:0',
            'cod_fee' => 'nullable|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'per_kg_cost' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|string|max:80',
            'order' => 'nullable|integer|min:0',
            'is_cod_available' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        return [
            'shipping_zone_id' => (int) $request->shipping_zone_id,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'cost' => (float) $request->cost,
            'cod_fee' => (float) $request->input('cod_fee', 0),
            'minimum_order_amount' => $request->filled('minimum_order_amount') ? (float) $request->minimum_order_amount : null,
            'per_kg_cost' => $request->filled('per_kg_cost') ? (float) $request->per_kg_cost : null,
            'estimated_days' => $request->estimated_days,
            'order' => (int) $request->input('order', 0),
            'is_cod_available' => $request->boolean('is_cod_available', true),
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    private function parseRegions(string $regions): array
    {
        return collect(explode(',', $regions))
            ->map(fn($region) => trim($region))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

