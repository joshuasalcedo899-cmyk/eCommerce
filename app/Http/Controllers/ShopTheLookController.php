<?php

namespace App\Http\Controllers;

use App\Models\Look;
use Illuminate\View\View;

class ShopTheLookController extends Controller
{
    public function index(): View
    {
        $looks = Look::where('is_active', true)->with(['products.images'])->latest()->paginate(9);
        return view('store.looks.index', compact('looks'));
    }

    public function show(Look $look): View
    {
        abort_unless($look->is_active, 404);
        $look->load([
            'products' => fn ($query) => $query->where('is_active', true)->where('stock', '>', 0)->with('images'),
            'variants' => fn ($query) => $query->where('is_active', true)->with(['products' => fn ($products) => $products->where('is_active', true)->where('stock', '>', 0)->with('images')]),
        ]);
        $otherLooks = Look::where('is_active', true)
            ->where('id', '!=', $look->id)
            ->latest()
            ->take(4)
            ->get();
        return view('store.looks.show', compact('look', 'otherLooks'));
    }
}
