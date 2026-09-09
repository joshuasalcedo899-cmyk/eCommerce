<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\OrderItemReturn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrdersController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        $order->load('items.product', 'items.returnRequests');
        $exchangeProducts = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->with('images')
            ->orderBy('name')
            ->get();

        return view('orders.show', compact('order', 'exchangeProducts'));
    }

    public function requestReturnOrExchange(Request $request, Order $order, OrderItem $item): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id && $item->order_id === $order->id, 403);

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Returns and exchanges are available after an order is delivered.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:return,exchange'],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                Rule::when($request->input('type') === 'exchange', ['max:1']),
            ],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $requestCreated = DB::transaction(function () use ($request, $item, $validated) {
            $lockedItem = OrderItem::query()->whereKey($item->id)->lockForUpdate()->firstOrFail();
            $requestedQuantity = $lockedItem->returnRequests()
                ->whereIn('status', ['pending', 'approved'])
                ->sum('quantity');

            if ($validated['quantity'] > $lockedItem->quantity - $requestedQuantity) {
                return false;
            }

            $request->user()->returnRequests()->create([
                'order_item_id' => $lockedItem->id,
                ...$validated,
            ]);

            return true;
        });

        if (!$requestCreated) {
            return back()->with('error', 'The requested quantity exceeds the remaining eligible quantity.');
        }

        return back()->with('success', 'Your return or exchange request has been submitted.');
    }

    public function chooseReplacement(Request $request, Order $order, OrderItemReturn $returnRequest): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $returnRequest->load('item');
        abort_unless(
            $returnRequest->user_id === $request->user()->id
                && $returnRequest->item->order_id === $order->id,
            403
        );

        if ($returnRequest->type !== 'exchange' || $returnRequest->status !== 'approved') {
            return back()->with('error', 'This exchange is not ready for a replacement selection.');
        }

        $validated = $request->validate([
            'replacement_items' => ['required', 'array', 'size:1'],
            'replacement_items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'replacement_items.*.quantity' => ['required', 'integer', 'size:1'],
            'replacement_items.*.size' => ['nullable', 'string', 'max:50'],
            'settlement_method' => ['nullable', 'in:wallet,cod'],
            'shipping_name' => ['required', 'string', 'max:255'],
            'shipping_phone' => ['required', 'string', 'max:50'],
            'shipping_address' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $validated, $returnRequest) {
            $exchange = OrderItemReturn::query()
                ->with('item.order')
                ->lockForUpdate()
                ->findOrFail($returnRequest->id);

            if ($exchange->type !== 'exchange' || $exchange->status !== 'approved') {
                abort(422, 'This exchange is not ready for a replacement selection.');
            }

            $originalItem = OrderItem::query()->lockForUpdate()->findOrFail($exchange->order_item_id);
            $replacementProducts = Product::query()
                ->whereIn('id', collect($validated['replacement_items'])->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $replacementQuantity = collect($validated['replacement_items'])->sum('quantity');

            if ($replacementQuantity !== (int) $exchange->quantity) {
                throw ValidationException::withMessages([
                    'replacement_items' => "Choose exactly {$exchange->quantity} replacement item(s).",
                ]);
            }

            $replacementSubtotal = 0;
            $replacementLines = [];

            foreach ($validated['replacement_items'] as $line) {
                $product = $replacementProducts->get((int) $line['product_id']);

                if (!$product || !$product->is_active || $product->stock < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'replacement_items' => 'One of the selected products is no longer available in the requested quantity.',
                    ]);
                }

                $size = trim((string) ($line['size'] ?? '')) ?: null;
                $availableSizes = array_filter(array_map('trim', explode(',', (string) $product->sizes)));

                if ($availableSizes && (!$size || !in_array($size, $availableSizes, true))) {
                    throw ValidationException::withMessages([
                        'replacement_items' => "Choose an available size for {$product->name}.",
                    ]);
                }

                $replacementSubtotal += (float) $product->price * $line['quantity'];
                $replacementLines[] = [
                    'product_id' => $product->id,
                    'size' => $size,
                    'price' => $product->price,
                    'quantity' => $line['quantity'],
                ];
            }

            $exchangeShippingFee = 100;
            $originalValue = (float) $exchange->item->order->total;
            $replacementSubtotal = round($replacementSubtotal, 2);
            $newExchangeBill = round($replacementSubtotal + $exchangeShippingFee, 2);
            $priceDifference = round($newExchangeBill - $originalValue, 2);
            $settlementMethod = $priceDifference > 0
                ? ($validated['settlement_method'] ?? null)
                : ($priceDifference < 0 ? 'wallet' : null);

            if ($priceDifference > 0 && !$settlementMethod) {
                throw ValidationException::withMessages([
                    'settlement_method' => 'Choose how to pay the additional exchange amount.',
                ]);
            }

            if ($priceDifference > 0 && $settlementMethod === 'wallet') {
                $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);

                if ((float) $user->wallet_balance < $priceDifference) {
                    throw ValidationException::withMessages([
                        'settlement_method' => 'Your e-wallet balance is not enough to pay the exchange difference.',
                    ]);
                }

                $user->decrement('wallet_balance', $priceDifference);
            }

            $exchange->replacementItems()->delete();
            $exchange->replacementItems()->createMany($replacementLines);
            $exchange->forceFill([
                'replacement_product_id' => count($replacementLines) === 1 ? $replacementLines[0]['product_id'] : null,
                'replacement_size' => count($replacementLines) === 1 ? $replacementLines[0]['size'] : null,
                'replacement_subtotal' => $replacementSubtotal,
                'price_difference' => $priceDifference,
                'settlement_method' => $settlementMethod,
                'shipping_name' => $validated['shipping_name'],
                'shipping_phone' => $validated['shipping_phone'],
                'shipping_address' => $validated['shipping_address'],
                'status' => 'replacement_selected',
            ])->save();
        });

        return back()->with('success', 'Replacement items selected. Please send the original item back for inspection.');
    }

    public function cancel(
        Request $request,
        Order $order
    ): RedirectResponse {
        // Make sure the customer owns this order.
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        // Customers can only cancel pending orders.
        if ($order->status !== 'pending') {
            return back()->with(
                'error',
                'Only pending orders can be cancelled.'
            );
        }

        DB::transaction(function () use ($order) {
            $user = $order->user()->lockForUpdate()->firstOrFail();
            $order->load('items');

            foreach ($order->items as $item) {
                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($product) {
                    $product->increment(
                        'stock',
                        $item->quantity
                    );
                }
            }

            $order->update([
                'status' => 'cancelled',
            ]);

            if ($order->payment_method === 'wallet') {
                $user->increment('wallet_balance', $order->total);
            }
        });

        return redirect()
            ->route('orders.show', $order)
            ->with(
                'success',
                'Your order has been cancelled successfully.'
            );
    }
}
