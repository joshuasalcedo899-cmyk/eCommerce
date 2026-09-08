<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
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

        if ($request->user()->reviews()->where('product_id', $product->id)->exists()) {
            return back()->with('error', 'You already reviewed this product. Use Edit on your comment to change it.');
        }

        $request->user()->reviews()->create([
            'product_id' => $product->id,
            ...$validated,
        ]);

        return back()->with('success', 'Your product review has been saved.');
    }

    public function update(Request $request, Product $product, ProductReview $review): RedirectResponse
    {
        abort_unless($review->product_id === $product->id && $review->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $review->update($validated);

        return back()->with('success', 'Your product review has been updated.');
    }
}
