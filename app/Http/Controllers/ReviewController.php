<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $hasPurchased = $request->user()
            ->orders()
            ->whereNotIn('status', ['cancelled'])
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'You can review products you have purchased.');
        }

        $request->user()->reviews()->updateOrCreate(
            ['product_id' => $product->id],
            $validated
        );

        return back()->with('success', 'Your product review has been saved.');
    }
}
