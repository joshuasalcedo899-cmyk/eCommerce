<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\OrderItemReturn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        return view('orders.show', compact('order'));
    }

    public function requestReturnOrExchange(Request $request, Order $order, OrderItem $item): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id && $item->order_id === $order->id, 403);

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Returns and exchanges are available after an order is delivered.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:return,exchange'],
            'quantity' => ['required', 'integer', 'min:1'],
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
            'replacement_size' => ['required', 'string', 'max:50'],
        ]);

        $product = $returnRequest->item->product;
        $availableSizes = array_filter(array_map('trim', explode(',', (string) $product->sizes)));

        if (!$product || !in_array($validated['replacement_size'], $availableSizes, true)) {
            return back()->with('error', 'Please choose another available size for this product.');
        }

        $returnRequest->update([
            'replacement_size' => $validated['replacement_size'],
            'status' => 'replacement_selected',
        ]);

        return back()->with('success', 'Replacement selected. Please send the original item back for inspection.');
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
