<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Look;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LookController extends Controller
{
    public function index(): View
    {
        $looks = Look::withCount('products')->latest()->paginate(10);
        return view('admin.looks.index', compact('looks'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.looks.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateLook($request);
        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['image_path'] = $request->file('image')->store('looks', 'public');
        $productIds = $validated['products'];
        unset($validated['image'], $validated['products']);

        $look = Look::create($validated);
        $this->syncProducts($look, $productIds);

        return redirect()->route('admin.looks.index')->with('success', 'Look created successfully.');
    }

    public function edit(Look $look): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $look->load(['products', 'variants.products']);
        return view('admin.looks.edit', compact('look', 'products'));
    }

    public function update(Request $request, Look $look): RedirectResponse
    {
        $validated = $this->validateLook($request, false);
        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_active'] = $request->boolean('is_active');
        $productIds = $validated['products'];
        unset($validated['image'], $validated['products']);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($look->image_path);
            $validated['image_path'] = $request->file('image')->store('looks', 'public');
        }

        $look->update($validated);
        $this->syncProducts($look, $productIds);

        return redirect()->route('admin.looks.index')->with('success', 'Look updated successfully.');
    }

    public function destroy(Look $look): RedirectResponse
    {
        Storage::disk('public')->delete($look->image_path);
        $look->delete();
        return redirect()->route('admin.looks.index')->with('success', 'Look deleted successfully.');
    }

    private function validateLook(Request $request, bool $imageRequired = true): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['integer', 'exists:products,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function syncProducts(Look $look, array $productIds): void
    {
        $look->products()->sync(collect($productIds)->values()->mapWithKeys(
            fn ($id, $index) => [$id => ['sort_order' => $index]]
        )->all());
    }
}
