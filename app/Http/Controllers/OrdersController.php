<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
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

        $order->load('items');

        return view('orders.show', compact('order'));
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
        });

        return redirect()
            ->route('orders.show', $order)
            ->with(
                'success',
                'Your order has been cancelled successfully.'
            );
    }
}