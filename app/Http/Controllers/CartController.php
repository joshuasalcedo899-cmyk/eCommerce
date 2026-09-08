<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->session()->get('cart', []);

        $products = Product::whereIn('id', array_keys($cart))
            ->with('images')
            ->get()
            ->keyBy('id');

        $items = [];

        foreach ($cart as $productId => $quantity) {
            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
            ];
        }

        $total = collect($items)->sum('subtotal');
        $selectedItems = array_values(array_intersect(
            array_keys($cart),
            $request->session()->get('checkout_items', array_keys($cart))
        ));
        $selectedTotal = collect($items)
            ->filter(fn (array $item): bool => in_array($item['product']->id, $selectedItems))
            ->sum('subtotal');

        return view('cart.index', compact('items', 'total', 'selectedItems', 'selectedTotal'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        if (!$product->is_active || $product->stock <= 0) {
            return back()->with('error', 'This product is currently unavailable.');
        }

        $quantity = max(1, (int) $request->input('quantity', 1));

        $cart = $request->session()->get('cart', []);

        $currentQuantity = $cart[$product->id] ?? 0;
        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > $product->stock) {
            return back()->with(
                'error',
                'You cannot add more than the available stock.'
            );
        }

        $cart[$product->id] = $newQuantity;

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $quantity = (int) $request->input('quantity');

        $cart = $request->session()->get('cart', []);

        if (!isset($cart[$product->id])) {
            return back();
        }

        if ($quantity <= 0) {
            unset($cart[$product->id]);
        } elseif ($quantity > $product->stock) {
            return back()->with(
                'error',
                'The requested quantity exceeds available stock.'
            );
        } else {
            $cart[$product->id] = $quantity;
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        unset($cart[$product->id]);

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Product removed from cart.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');

        return redirect()
            ->route('cart.index')
            ->with('success', 'Cart cleared.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        $selectedItems = array_values(array_intersect(
            array_keys($cart),
            array_map('strval', $request->input('selected_items', []))
        ));

        if (empty($selectedItems)) {
            return back()->with('error', 'Select at least one item to continue.');
        }

        $request->session()->put('checkout_items', $selectedItems);

        return redirect()->route('checkout.index');
    }
}
