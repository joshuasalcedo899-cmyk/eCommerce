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
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
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

                @if ($order->status === 'cancelled')
                    <div class="mb-8 rounded-lg border border-red-200 bg-red-50 p-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-semibold text-red-800">
                                    Order Cancelled
                                </h3>

                                <p class="text-sm text-red-600">
                                    This order has been cancelled.
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
                        <div class="py-5 flex items-center justify-between gap-4">

                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $item->product_name }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    ₱{{ number_format($item->price, 2) }}
                                    ×
                                    {{ $item->quantity }}
                                </p>
                            </div>

                            <p class="font-medium text-gray-900">
                                ₱{{ number_format($item->subtotal, 2) }}
                            </p>

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
                                Cash on Delivery
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>