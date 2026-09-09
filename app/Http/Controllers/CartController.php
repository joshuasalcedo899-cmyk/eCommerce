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

        $productIds = collect(array_keys($cart))->map(fn ($key) => $this->cartKeyParts($key)[0])->unique()->values();
        $products = Product::whereIn('id', $productIds)
            ->with('images')
            ->get()
            ->keyBy('id');

        $items = [];

        foreach ($cart as $cartKey => $quantity) {
            [$productId, $size] = $this->cartKeyParts($cartKey);

            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];

            $items[] = [
                'cart_key' => $cartKey,
                'product' => $product,
                'size' => $size,
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
            ->filter(fn (array $item): bool => in_array($item['cart_key'], $selectedItems, true))
            ->sum('subtotal');

        return view('cart.index', compact('items', 'total', 'selectedItems', 'selectedTotal'));
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        if (!$product->is_active || $product->stock <= 0) {
            return back()->with('error', 'This product is currently unavailable.');
        }

        $size = trim((string) $request->input('size', ''));
        $availableSizes = array_filter(array_map('trim', explode(',', (string) $product->sizes)));

        if ($availableSizes && !in_array($size, $availableSizes, true)) {
            return back()->with('error', 'Please choose an available size.');
        }

        $quantity = max(1, (int) $request->input('quantity', 1));

        $cart = $request->session()->get('cart', []);

        $cartKey = $this->cartKey($product->id, $size);
        $currentQuantity = $cart[$cartKey] ?? 0;
        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > $product->stock) {
            return back()->with(
                'error',
                'You cannot add more than the available stock.'
            );
        }

        $cart[$cartKey] = $newQuantity;

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $quantity = (int) $request->input('quantity');
        $currentSize = trim((string) $request->input('current_size', ''));
        $size = trim((string) $request->input('size', $currentSize));
        $availableSizes = array_filter(array_map('trim', explode(',', (string) $product->sizes)));

        if ($availableSizes && !in_array($size, $availableSizes, true)) {
            return back()->with('error', 'Please choose an available size.');
        }

        $cart = $request->session()->get('cart', []);

        $cartKey = $this->cartKey($product->id, $currentSize);

        if (!isset($cart[$cartKey])) {
            return back();
        }

        if ($quantity <= 0) {
            unset($cart[$cartKey]);
        } elseif ($quantity > $product->stock) {
            return back()->with(
                'error',
                'The requested quantity exceeds available stock.'
            );
        } else {
            $newCartKey = $this->cartKey($product->id, $size);

            if ($newCartKey !== $cartKey) {
                $quantity += (int) ($cart[$newCartKey] ?? 0);
                unset($cart[$cartKey]);
            }

            if ($quantity > $product->stock) {
                return back()->with('error', 'The combined quantity exceeds available stock.');
            }

            $cart[$newCartKey] = $quantity;
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        $cartKey = $this->cartKey($product->id, trim((string) $request->input('size', '')));
        unset($cart[$cartKey]);

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

    private function cartKey(int|string $productId, string $size = ''): string
    {
        return $productId . '|' . rawurlencode($size);
    }

    private function cartKeyParts(string|int $cartKey): array
    {
        [$productId, $encodedSize] = array_pad(explode('|', (string) $cartKey, 2), 2, '');

        return [(int) $productId, rawurldecode($encodedSize)];
    }
}
