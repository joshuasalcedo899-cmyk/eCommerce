<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Look;
use App\Models\LookVariant;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LookVariantController extends Controller
{
    public function create(Look $look): View
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('admin.looks.variants.create', compact('look', 'products'));
    }

    public function store(Request $request, Look $look): RedirectResponse
    {
        $validated = $this->validateVariant($request);
        $productIds = $validated['products'];
        $validated['look_id'] = $look->id;
        $validated['image_path'] = $request->file('image')->store('look-variants', 'public');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $look->variants()->max('sort_order') + 1;
        unset($validated['image'], $validated['products']);

        $variant = LookVariant::create($validated);
        $this->syncProducts($variant, $productIds);

        return redirect()->route('admin.looks.edit', $look)->with('success', 'Outfit combination added.');
    }

    public function edit(Look $look, LookVariant $variant): View
    {
        abort_unless($variant->look_id === $look->id, 404);
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $variant->load('products');
        return view('admin.looks.variants.edit', compact('look', 'variant', 'products'));
    }

    public function update(Request $request, Look $look, LookVariant $variant): RedirectResponse
    {
        abort_unless($variant->look_id === $look->id, 404);
        $validated = $this->validateVariant($request, false);
        $productIds = $validated['products'];
        $validated['is_active'] = $request->boolean('is_active');
        unset($validated['image'], $validated['products']);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($variant->image_path);
            $validated['image_path'] = $request->file('image')->store('look-variants', 'public');
        }

        $variant->update($validated);
        $this->syncProducts($variant, $productIds);
        return redirect()->route('admin.looks.edit', $look)->with('success', 'Outfit combination updated.');
    }

    public function destroy(Look $look, LookVariant $variant): RedirectResponse
    {
        abort_unless($variant->look_id === $look->id, 404);
        Storage::disk('public')->delete($variant->image_path);
        $variant->delete();
        return back()->with('success', 'Outfit combination removed.');
    }

    private function validateVariant(Request $request, bool $imageRequired = true): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['integer', 'exists:products,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function syncProducts(LookVariant $variant, array $productIds): void
    {
        $variant->products()->sync(collect($productIds)->values()->mapWithKeys(
            fn ($id, $index) => [$id => ['sort_order' => $index]]
        )->all());
    }
}
