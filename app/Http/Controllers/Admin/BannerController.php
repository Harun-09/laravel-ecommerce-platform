<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view banners')->only(['index']);
        $this->middleware('can:create banners')->only(['create', 'store']);
        $this->middleware('can:edit banners')->only(['edit', 'update', 'restore']);
        $this->middleware('can:delete banners')->only(['destroy', 'forceDestroy']);
    }

    public function index(Request $request)
    {
        $showTrashed = $request->boolean('trashed');
        $query = Banner::query();

        if ($showTrashed) {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('subtitle', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        $banners = $query->ordered()->latest()->paginate(20);
        $positions = $this->positions();
        $activeCount = Banner::count();
        $trashedCount = Banner::onlyTrashed()->count();

        return view('admin.banners.index', compact(
            'banners',
            'positions',
            'activeCount',
            'trashedCount',
            'showTrashed',
        ));
    }

    public function create()
    {
        $positions = $this->positions();

        return view('admin.banners.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(requireImage: true));

        $data = $this->buildPayload($request, $validated, defaultActive: true);
        $data['image'] = $request->file('image')->store('banners', 'public');

        if ($request->hasFile('mobile_image')) {
            $data['mobile_image'] = $request->file('mobile_image')->store('banners', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        $positions = $this->positions();

        return view('admin.banners.edit', compact('banner', 'positions'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate($this->rules());

        if ($request->boolean('remove_image') && !$request->hasFile('image')) {
            return back()
                ->withErrors(['image' => 'Banner image is required unless you upload a replacement image.'])
                ->withInput();
        }

        $data = $this->buildPayload($request, $validated);

        if ($request->boolean('remove_image') && $banner->image) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }

            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        if ($request->boolean('remove_mobile_image') && $banner->mobile_image) {
            Storage::disk('public')->delete($banner->mobile_image);
            $data['mobile_image'] = null;
        }

        if ($request->hasFile('mobile_image')) {
            if ($banner->mobile_image) {
                Storage::disk('public')->delete($banner->mobile_image);
            }

            $data['mobile_image'] = $request->file('mobile_image')->store('banners', 'public');
        }

        $finalImage = $data['image'] ?? $banner->image;
        if (empty($finalImage)) {
            return back()
                ->withErrors(['image' => 'Banner image cannot be empty.'])
                ->withInput();
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner moved to trash successfully.');
    }

    public function restore(int $bannerId)
    {
        $banner = Banner::onlyTrashed()->findOrFail($bannerId);
        $banner->restore();

        return redirect()->route('admin.banners.index', ['trashed' => 1])
            ->with('success', 'Banner restored successfully.');
    }

    public function forceDestroy(int $bannerId)
    {
        $banner = Banner::onlyTrashed()->findOrFail($bannerId);

        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        if ($banner->mobile_image) {
            Storage::disk('public')->delete($banner->mobile_image);
        }

        $banner->forceDelete();

        return redirect()->route('admin.banners.index', ['trashed' => 1])
            ->with('success', 'Banner permanently deleted.');
    }

    private function rules(bool $requireImage = false): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'mobile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],
            'remove_mobile_image' => ['nullable', 'boolean'],
            'link' => ['nullable', 'string', 'max:2048'],
            'button_text' => ['nullable', 'string', 'max:120'],
            'position' => ['required', Rule::in(array_keys($this->positions()))],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    private function buildPayload(Request $request, array $validated, bool $defaultActive = false): array
    {
        return [
            'title' => trim($validated['title']),
            'subtitle' => $validated['subtitle'] ?? null,
            'link' => $validated['link'] ?? null,
            'button_text' => $validated['button_text'] ?? null,
            'position' => $validated['position'],
            'order' => (int) ($validated['order'] ?? 0),
            'is_active' => $request->boolean('is_active', $defaultActive),
            'starts_at' => $request->filled('starts_at') ? $validated['starts_at'] : null,
            'ends_at' => $request->filled('ends_at') ? $validated['ends_at'] : null,
        ];
    }

    private function positions(): array
    {
        return [
            'hero' => 'Hero',
            'sidebar' => 'Sidebar',
            'category' => 'Category',
            'popup' => 'Popup',
            'footer' => 'Footer',
        ];
    }
}
