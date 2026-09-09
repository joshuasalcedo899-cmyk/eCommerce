<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItemReturn;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ReturnRequestController extends Controller
{
    public function index(): View
    {
        return $this->requestsView(false);
    }

    public function archive(): View
    {
        return $this->requestsView(true);
    }

    private function requestsView(bool $archived): View
    {
        $returnRequests = OrderItemReturn::with(['item.order.user', 'user', 'replacementProduct'])
            ->when($archived, function ($query) {
                $query->where(function ($query) {
                    $query->where('status', 'rejected')
                        ->orWhere(function ($query) {
                            $query->where('type', 'return')->where('status', 'approved');
                        })
                        ->orWhere('status', 'completed');
                });
            }, function ($query) {
                $query->where('status', 'pending')
                    ->orWhere(function ($query) {
                        $query->where('type', 'exchange')
                            ->whereIn('status', ['approved', 'replacement_selected']);
                    });
            })
            ->latest()
            ->paginate(15);

        return view('admin.returns.index', compact('returnRequests', 'archived'));
    }

    public function update(Request $request, OrderItemReturn $returnRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($returnRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        DB::transaction(function () use ($returnRequest, $validated) {
            $returnRequest = OrderItemReturn::query()
                ->with(['item.order'])
                ->lockForUpdate()
                ->findOrFail($returnRequest->id);

            if ($returnRequest->status !== 'pending') {
                abort(422, 'This request has already been reviewed.');
            }

            $returnRequest->update($validated);

            if ($validated['status'] !== 'approved') {
                return;
            }

            $item = $returnRequest->item;
            $order = $item->order;

            if ($returnRequest->type === 'return') {
                $item->update([
                    'status' => 'returned',
                ]);

                $order->forceFill([
                    'status' => 'returned',
                ])->save();

                Product::whereKey($item->product_id)
                    ->lockForUpdate()
                    ->increment('stock', $returnRequest->quantity);

                if ($order->payment_method === 'wallet') {
                    $user = User::query()->lockForUpdate()->findOrFail($order->user_id);
                    $refundAmount = $item->price * $returnRequest->quantity;

                    if (!$order->shipping_fee_refunded) {
                        $refundAmount += $order->shipping_fee;
                        $order->forceFill([
                            'shipping_fee_refunded' => true,
                        ])->save();
                    }

                    $user->increment('wallet_balance', $refundAmount);
                }
            }
        });

        return back()->with('success', 'Return or exchange request updated successfully.');
    }

    public function receiveExchange(Request $request, OrderItemReturn $returnRequest): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($returnRequest, $validated) {
            $exchange = OrderItemReturn::query()
                ->with(['item.order'])
                ->lockForUpdate()
                ->findOrFail($returnRequest->id);

            if ($exchange->type !== 'exchange' || $exchange->status !== 'replacement_selected') {
                abort(422, 'This exchange is not ready to be received.');
            }

            if (!$exchange->replacement_size) {
                abort(422, 'A replacement size must be selected before completing this exchange.');
            }

            $originalItem = OrderItem::query()
                ->lockForUpdate()
                ->findOrFail($exchange->order_item_id);
            $originalOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($originalItem->order_id);

            $originalItem->update([
                'status' => 'returned',
            ]);

            $user = User::query()->findOrFail($originalOrder->user_id);
            $newOrder = $user->orders()->create([
                'order_number' => 'EXC-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5)),
                'status' => 'processing',
                'is_exchange' => true,
                'exchange_from_order_id' => $originalOrder->getKey(),
                'payment_method' => 'exchange',
                'subtotal' => 0,
                'shipping_fee' => 0,
                'total' => 0,
                'shipping_name' => $originalOrder->shipping_name,
                'shipping_phone' => $originalOrder->shipping_phone,
                'shipping_address' => $originalOrder->shipping_address,
            ]);

            $newOrder->items()->create([
                'product_id' => $originalItem->product_id,
                'product_name' => $originalItem->product_name,
                'size' => $exchange->replacement_size,
                'exchanged' => true,
                'price' => $originalItem->price,
                'quantity' => $exchange->quantity,
                'subtotal' => 0,
            ]);

            $originalOrder->forceFill(['status' => 'returned'])->save();

            $exchange->update([
                'status' => 'completed',
                'admin_note' => $validated['admin_note'] ?? $exchange->admin_note,
            ]);
        });

        return back()->with('success', 'Exchange received, inspected, and completed.');
    }
}
