<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return $this->ordersView($request, false);
    }

    public function archive(Request $request): View
    {
        return $this->ordersView($request, true);
    }

    private function ordersView(Request $request, bool $archived): View
    {
        $query = Order::with('user')
            ->whereIn('status', $archived ? ['delivered', 'cancelled', 'returned'] : ['pending', 'processing', 'shipped']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('shipping_name', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        $orders = $query
            ->oldest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'archived'));
    }

    public function show(Order $order): View
    {
        $order->load([
            'user',
            'items.product',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(
        Request $request,
        Order $order
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:pending,processing,shipped,delivered,cancelled,returned',
            ],
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        if ($newStatus === $oldStatus) {
            return back()->with(
                'success',
                'Order status remains unchanged.'
            );
        }

        $allowedTransitions = [
            'pending' => [
                'processing',
                'cancelled',
            ],

            'processing' => [
                'shipped',
                'cancelled',
            ],

            'shipped' => [
                'delivered',
            ],

            'delivered' => [],

            'returned' => [],

            'cancelled' => [
                'processing',
            ],
        ];

        if (
            !in_array(
                $newStatus,
                $allowedTransitions[$oldStatus] ?? [],
                true
            )
        ) {
            return back()->with(
                'error',
                "Cannot change order status from {$oldStatus} to {$newStatus}."
            );
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus) {
            

            if (
                $newStatus === 'cancelled' &&
                $oldStatus !== 'cancelled'
            ) {
                $order->load('items');

                foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)
                        ->lockForUpdate()
                        ->increment('stock', $item->quantity);
                }
            }

            if (
                $oldStatus === 'cancelled' &&
                $newStatus !== 'cancelled'
            ) {
                $order->load('items');

                foreach ($order->items as $item) {
                    $product = Product::where('id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        abort(
                            422,
                            "Product {$item->product_name} no longer exists."
                        );
                    }

                    if ($product->stock < $item->quantity) {
                        abort(
                            422,
                            "There is not enough stock to reopen this order."
                        );
                    }

                    $product->decrement(
                        'stock',
                        $item->quantity
                    );
                }
            }


            $order->update([
                'status' => $newStatus,
            ]);
        });

        return back()->with(
            'success',
            'Order status updated successfully.'
        );
    }
}
