<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        $selectedItems = array_values(array_intersect(
            array_keys($cart),
            $request->session()->get('checkout_items', array_keys($cart))
        ));

        if (empty($cart) || empty($selectedItems)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $checkoutCart = array_intersect_key($cart, array_flip($selectedItems));

        $products = Product::whereIn('id', array_keys($checkoutCart))
            ->with('images')
            ->get()
            ->keyBy('id');

        $items = [];

        foreach ($checkoutCart as $productId => $quantity) {
            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];

            if (!$product->is_active || $product->stock < $quantity) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        "Some products in your cart are no longer available in the requested quantity."
                    );
            }

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
            ];
        }

        if (empty($items)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $subtotal = collect($items)->sum('subtotal');

        // We'll use a fixed shipping fee for now.
        $shippingFee = 100;

        $total = $subtotal + $shippingFee;

        return view('checkout.index', compact(
            'items',
            'subtotal',
            'shippingFee',
            'total'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod'],
        ]);

        $cart = $request->session()->get('cart', []);
        $selectedItems = array_values(array_intersect(
            array_keys($cart),
            $request->session()->get('checkout_items', array_keys($cart))
        ));
        $checkoutCart = array_intersect_key($cart, array_flip($selectedItems));

        if (empty($checkoutCart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $order = DB::transaction(function () use ($checkoutCart, $validated, $request){
            $products = Product::whereIn('id', array_keys($checkoutCart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;

            foreach ($checkoutCart as $productId => $quantity) {
                if (!isset($products[$productId])) {
                    abort(422, 'A product in your cart no longer exists.');
                }

                $product = $products[$productId];

                if (!$product->is_active) {
                    abort(422, "The product {$product->name} is no longer available.");
                }

                if ($product->stock < $quantity) {
                    abort(
                        422,
                        "There is not enough stock for {$product->name}."
                    );
                }

                $subtotal += $product->price * $quantity;
            }

            $shippingFee = 100;
            $total = $subtotal + $shippingFee;

            $order = $request->user()->orders()->create([
                'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5)),
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
            ]);

            foreach ($checkoutCart as $productId => $quantity) {
                $product = $products[$productId];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ]);

                $product->decrement('stock', $quantity);
            }

            return $order;
        });

        $remainingCart = array_diff_key($cart, $checkoutCart);
        $request->session()->put('cart', $remainingCart);
        $request->session()->forget('checkout_items');

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Your order has been placed successfully.');
    }
}
