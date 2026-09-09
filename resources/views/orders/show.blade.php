<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order {{ $order->order_number }}
            </h2>

            <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← My Orders
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-md bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4 text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            @php
                $statusClasses = [
                    'pending' => 'text-yellow-800 border-2 !border-yellow-800',
                    'processing' => 'text-blue-800 border-2 !border-blue-800',
                    'shipped' => 'text-indigo-800 border-2 !border-indigo-800',
                    'delivered' => 'text-green-800 border-2 !border-green-800',
                    'cancelled' => 'text-red-800 border-2 !border-red-800',
                    'returned' => 'text-orange-800 border-2 !border-orange-800',
                ];
            @endphp

            {{-- Order Status --}}
            <div class="bg-white shadow-sm rounded-lg p-6 flex flex-col gap-4">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div>
                        <p class="text-sm text-gray-500">
                            Order Number
                        </p>

                        <h1 class="text-xl font-bold text-gray-900">
                            {{ $order->order_number }}
                        </h1>

                        <p class="mt-1 text-sm text-gray-500">
                            Placed {{ $order->created_at->format('M d, Y h:i A') }}
                        </p>
                    </div>

                    <div class="flex items-center sm:flex-col sm:items-end gap-3">
                        <div>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold tracking-wider uppercase border-2 rounded-lg {{ $statusClasses[$order->status] ?? 'text-gray-800 !border-gray-300' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        

                        <div>
                            @if ($order->status === 'pending')
                                <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit"
                                        class="rounded-md bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
                
                <div>
                @php
                    $statusSteps = [
                        'pending' => 'Order Placed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                    ];

                    $statusOrder = array_keys($statusSteps);

                    $currentIndex = array_search(
                        $order->status,
                        $statusOrder,
                        true
                    );
                @endphp

                @if (in_array($order->status, ['cancelled', 'returned'], true))
                    <div class="mb-8 rounded-lg border border-red-200 bg-red-50 p-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-semibold {{ $order->status === 'returned' ? 'text-orange-800' : 'text-red-800' }}">
                                    {{ $order->status === 'returned' ? 'Order Returned' : 'Order Cancelled' }}
                                </h3>

                                <p class="text-sm {{ $order->status === 'returned' ? 'text-orange-700' : 'text-red-600' }}">
                                    {{ $order->status === 'returned' ? 'This order was returned because it was exchanged.' : 'This order has been cancelled.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6">
                        <h2 class="mb-6 text-lg font-semibold text-gray-900">
                            Order Status
                        </h2>

                        <div class="space-y-6">
                            @foreach ($statusSteps as $status => $label)
                                            @php
                                                $stepIndex = array_search(
                                                    $status,
                                                    $statusOrder,
                                                    true
                                                );

                                                $completed = $stepIndex < $currentIndex;
                                                $current = $stepIndex === $currentIndex;
                                            @endphp

                                            <div class="flex items-start gap-4">
                                                {{-- Status icon --}}
                                                <div class="flex flex-col items-center">
                                                    <div class="
                                                                                        flex h-10 w-10 items-center justify-center
                                                                                        rounded-full
                                                                                        border-2
                                                                                        {{ $completed || $current
                                ? 'border-gray-800 bg-gray-800 text-white'
                                : 'border-gray-300 bg-white text-gray-400'
                                                                                        }}
                                                                                    ">
                                                        @if ($completed || ($current && $order->status === 'delivered'))
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M5 13l4 4L19 7" />
                                                            </svg>
                                                        @else
                                                            <span class="text-sm font-semibold">
                                                                {{ $stepIndex + 1 }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    {{-- Connector --}}
                                                    @if (!$loop->last)
                                                                        <div class="
                                                                                                                                    mt-2 h-6 w-0.5
                                                                                                                                    {{ $completed
                                                        ? 'bg-gray-800'
                                                        : 'bg-gray-300'
                                                                                                                                    }}
                                                                                                                                "></div>
                                                    @endif
                                                </div>

                                                {{-- Text --}}
                                                <div class="pt-1">
                                                    <h3 class="
                                                                                        font-medium
                                                                                        {{ $completed || $current
                                ? 'text-gray-900'
                                : 'text-gray-400'
                                                                                        }}
                                                                                    ">
                                                        {{ $label }}
                                                    </h3>

                                                    @if ($current)
                                                        <p class="mt-1 text-sm text-gray-500">
                                                            Current status
                                                        </p>
                                                    @elseif ($completed)
                                                        <p class="mt-1 text-sm text-gray-500">
                                                            Completed
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                </div>

            </div>

            {{-- Order Items --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mt-6">

                <h2 class="text-lg font-semibold text-gray-900">
                    Items
                </h2>

                <div class="mt-6 divide-y divide-gray-200">

                    @foreach ($order->items as $item)
                        <div class="grid gap-5 py-5 md:grid-cols-[minmax(0,1fr)_auto_minmax(18rem,1.15fr)] md:items-start">

                            <div class="min-w-0">
                                <p class="font-medium text-gray-900">
                                    {{ $item->product_name }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    ₱{{ number_format($item->price, 2) }}
                                    ×
                                    {{ $item->quantity }}
                                </p>

                                @if ($item->size)
                                    <p class="mt-1 text-sm font-medium text-gray-700">Size: {{ $item->size }}</p>
                                @endif

                                @if ($item->exchanged)
                                    <span class="mt-2 inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">
                                        Exchanged
                                    </span>
                                @endif
                                @if ($item->status === 'returned')
                                    <span class="mt-2 inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-800">
                                        Returned
                                    </span>
                                @endif
                            </div>

                            <p class="shrink-0 font-medium text-gray-900 md:pt-1 md:text-right">
                                ₱{{ number_format($item->subtotal, 2) }}
                            </p>

                            <div class="min-w-0 space-y-3">
                            @if ($order->status === 'delivered')
                                @php
                                    $requestedQuantity = $item->returnRequests
                                        ->whereIn('status', ['pending', 'approved', 'replacement_selected'])
                                        ->sum('quantity');
                                    $remainingQuantity = $item->quantity - $requestedQuantity;
                                    $hasActiveRequest = $item->returnRequests->contains(
                                        fn ($returnRequest) => strtolower((string) $returnRequest->type) === 'exchange'
                                            && !in_array(strtolower((string) $returnRequest->status), ['rejected', 'completed'], true)
                                    );
                                @endphp

                                @if ($remainingQuantity > 0 && !$hasActiveRequest)
                                    <details class="w-full sm:max-w-sm">
                                        <summary class="flex cursor-pointer list-none items-center justify-between rounded-md border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 transition hover:border-gray-300 hover:bg-gray-100">
                                            <span>Return or exchange</span>
                                            <span class="text-xs font-medium text-gray-500">{{ $remainingQuantity }} eligible</span>
                                        </summary>
                                        <form action="{{ route('orders.return-exchange.store', [$order, $item]) }}" method="POST"
                                            class="mt-3 space-y-4 rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                                            @csrf
                                            <div>
                                                <label for="type-{{ $item->id }}" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Request type</label>
                                                <select id="type-{{ $item->id }}" name="type" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                                    <option value="return">Return item</option>
                                                    <option value="exchange">Exchange item</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label for="quantity-{{ $item->id }}" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Quantity</label>
                                                <input id="quantity-{{ $item->id }}" name="quantity" type="number" min="1" max="{{ $remainingQuantity }}" value="1" required
                                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                            </div>
                                            <div>
                                                <label for="reason-{{ $item->id }}" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Reason</label>
                                                <textarea id="reason-{{ $item->id }}" name="reason" rows="3" maxlength="1000" required
                                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500"></textarea>
                                            </div>
                                            <button type="submit" class="w-full rounded-md bg-gray-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                                Submit request
                                            </button>
                                        </form>
                                    </details>
                                @endif
                            @endif

                            @foreach ($item->returnRequests as $returnRequest)
                                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                                    <span class="font-medium text-gray-700">{{ ucfirst($returnRequest->type) }} request</span>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $returnRequest->status === 'approved' ? 'bg-green-100 text-green-800' : ($returnRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : ($returnRequest->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst($returnRequest->status) }}
                                    </span>
                                    <span class="text-gray-500">Qty {{ $returnRequest->quantity }}</span>
                                </div>

                                @if ($returnRequest->type === 'exchange' && $returnRequest->status === 'approved')
                                    <div x-data="{
                                            products: @js($exchangeProducts->map(fn ($product) => [
                                                'id' => $product->id,
                                                'name' => $product->name,
                                                'price' => (float) $product->price,
                                                'stock' => $product->stock,
                                                'sizes' => array_values(array_filter(array_map('trim', explode(',', (string) $product->sizes)))),
                                            ])->values()),
                                            lines: [{ product_id: '', quantity: 1, size: '' }],
                                            originalValue: {{ (float) $order->total }},
                                            exchangeShippingFee: 100,
                                            selectedProduct(line) { return this.products.find(product => product.id === Number(line.product_id)); },
                                            total() { return this.lines.reduce((sum, line) => { const product = this.selectedProduct(line); return sum + (product ? product.price : 0); }, 0); },
                                            newBill() { return this.total() + this.exchangeShippingFee; }
                                        }">
                                        <button type="button" @click="$refs.exchangeDialog.showModal()" class="mt-3 w-full rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-50">
                                            Choose replacement products
                                        </button>

                                        <dialog x-ref="exchangeDialog" @click.self="$el.close()" class="w-full max-w-2xl rounded-lg bg-white p-0 shadow-xl backdrop:bg-gray-900/50" aria-labelledby="exchange-dialog-title-{{ $returnRequest->id }}">
                                            <div>
                                                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                                                        <div>
                                                            <h2 id="exchange-dialog-title-{{ $returnRequest->id }}" class="text-lg font-semibold text-gray-900">Choose replacement products</h2>
                                                            <p class="mt-1 text-sm text-gray-500">Select one replacement item and confirm delivery details.</p>
                                                        </div>
                                                        <button type="button" @click="$refs.exchangeDialog.close()" class="rounded-md px-2 py-1 text-2xl leading-none text-gray-400 hover:bg-gray-100 hover:text-gray-700" aria-label="Close exchange dialog">&times;</button>
                                                    </div>

                                                    <form action="{{ route('orders.return-exchange.replacement', [$order, $returnRequest]) }}" method="POST" class="max-h-[75vh] space-y-4 overflow-y-auto p-6">
                                        @csrf
                                        @method('PATCH')
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Choose replacement product</p>
                                            <p class="mt-1 text-xs text-gray-500">Choose one available product and size.</p>
                                        </div>

                                        <template x-for="(line, index) in lines" :key="index">
                                            <div class="space-y-2 rounded-md border border-gray-200 bg-gray-50 p-3">
                                                <div class="flex gap-2">
                                                    <select x-model.number="line.product_id" :name="`replacement_items[${index}][product_id]`" @change="line.size = ''" required class="block min-w-0 flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                                        <option value="">Choose a product</option>
                                                        <template x-for="product in products" :key="product.id">
                                                            <option :value="product.id" x-text="`${product.name} - ₱${product.price.toFixed(2)} (${product.stock} available)`"></option>
                                                        </template>
                                                    </select>
                                                    <input type="hidden" :name="`replacement_items[${index}][quantity]`" value="1">
                                                </div>
                                                <select x-show="selectedProduct(line)?.sizes.length" x-model="line.size" :name="`replacement_items[${index}][size]`" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                                    <option value="">Choose a size</option>
                                                    <template x-for="size in (selectedProduct(line)?.sizes || [])" :key="size">
                                                        <option :value="size" x-text="size"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </template>

                                        <div class="grid gap-3 border-t border-gray-200 pt-4 sm:grid-cols-2">
                                            <div>
                                                <label for="exchange-shipping-name-{{ $returnRequest->id }}" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Recipient name</label>
                                                <input id="exchange-shipping-name-{{ $returnRequest->id }}" name="shipping_name" type="text" value="{{ old('shipping_name', $order->shipping_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                            </div>
                                            <div>
                                                <label for="exchange-shipping-phone-{{ $returnRequest->id }}" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Mobile number</label>
                                                <input id="exchange-shipping-phone-{{ $returnRequest->id }}" name="shipping_phone" type="text" value="{{ old('shipping_phone', $order->shipping_phone) }}" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label for="exchange-shipping-address-{{ $returnRequest->id }}" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Delivery address</label>
                                                <textarea id="exchange-shipping-address-{{ $returnRequest->id }}" name="shipping_address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                                            </div>
                                        </div>

                                        <div class="border-t border-gray-200 pt-3 text-sm">
                                            <div class="flex justify-between"><span>Original bill paid</span><span>₱<span x-text="originalValue.toFixed(2)"></span></span></div>
                                            <div class="flex justify-between"><span>Replacement product</span><span>₱<span x-text="total().toFixed(2)"></span></span></div>
                                            <div class="flex justify-between"><span>New shipping fee</span><span>₱<span x-text="exchangeShippingFee.toFixed(2)"></span></span></div>
                                            <div class="flex justify-between font-semibold"><span>New exchange bill</span><span>₱<span x-text="newBill().toFixed(2)"></span></span></div>
                                            <p x-show="newBill() < originalValue" class="mt-2 text-green-800">The difference will be credited to your e-wallet.</p>
                                            <p x-show="newBill() > originalValue" class="mt-2 text-orange-800">Additional amount: ₱<span x-text="(newBill() - originalValue).toFixed(2)"></span></p>
                                        </div>

                                        <div x-show="newBill() > originalValue" class="space-y-2">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Payment for the difference</p>
                                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="settlement_method" value="wallet" @checked($order->payment_method === 'wallet') :required="newBill() > originalValue"> E-Wallet (₱{{ number_format(auth()->user()->wallet_balance, 2) }})</label>
                                            <label class="flex items-center gap-2 text-sm"><input type="radio" name="settlement_method" value="cod" @checked($order->payment_method !== 'wallet') :required="newBill() > originalValue"> Cash on Delivery</label>
                                        </div>

                                        <button type="submit" class="w-full rounded-md bg-gray-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">Confirm replacement</button>
                                                    </form>
                                            </div>
                                        </dialog>
                                    </div>
                                @elseif ($returnRequest->type === 'exchange' && $returnRequest->status === 'replacement_selected')
                                    <p class="mt-2 text-sm text-blue-700">Replacement size selected: {{ $returnRequest->replacement_size }}. Please return the original item for inspection.</p>
                                @endif
                            @endforeach

                            </div>
                        </div>
                    @endforeach
                    

                </div>

            </div>

            {{-- Shipping + Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

                {{-- Shipping --}}
                <div class="bg-white shadow-sm rounded-lg p-6">

                    <h2 class="text-lg font-semibold text-gray-900">
                        Shipping Information
                    </h2>

                    <div class="mt-5 space-y-3 text-sm">

                        <div>
                            <p class="text-gray-500">
                                Name
                            </p>

                            <p class="font-medium text-gray-900">
                                {{ $order->shipping_name }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">
                                Phone
                            </p>

                            <p class="font-medium text-gray-900">
                                {{ $order->shipping_phone }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500">
                                Address
                            </p>

                            <p class="font-medium text-gray-900 whitespace-pre-line">
                                {{ $order->shipping_address }}
                            </p>
                        </div>

                    </div>

                </div>

                {{-- Summary --}}
                <div class="bg-white shadow-sm rounded-lg p-6">

                    <h2 class="text-lg font-semibold text-gray-900">
                        Order Summary
                    </h2>

                    <div class="mt-5 space-y-3">

                        <div class="flex justify-between">
                            <span class="text-gray-600">
                                Subtotal
                            </span>

                            <span>
                                ₱{{ number_format($order->subtotal, 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">
                                Shipping
                            </span>

                            <span>
                                ₱{{ number_format($order->shipping_fee, 2) }}
                            </span>
                        </div>

                        <div class="border-t pt-3 flex justify-between text-lg font-bold">
                            <span>
                                Total
                            </span>

                            <span>
                                ₱{{ number_format($order->total, 2) }}
                            </span>
                        </div>

                        <div class="pt-3">
                            <p class="text-sm text-gray-500">
                                Payment Method
                            </p>

                            <p class="font-medium">
                                {{ $order->payment_method === 'wallet' ? 'E-Wallet' : 'Cash on Delivery' }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
