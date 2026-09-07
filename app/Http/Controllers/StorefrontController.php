<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images'])
            ->where('is_active', true)
            ->where('stock', '>', 0);

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        $products = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('store.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        abort_unless(
            $product->is_active && $product->stock > 0,
            404
        );

        $product->load(['category', 'images']);

        $relatedProducts = Product::with('images')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        return view(
            'store.show',
            compact('product', 'relatedProducts')
        );
    }
}