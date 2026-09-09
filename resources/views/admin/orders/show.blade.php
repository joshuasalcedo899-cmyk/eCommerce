<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order {{ $order->order_number }}
            </h2>

            <a
                href="{{ route('admin.orders.index') }}"
                class="text-sm text-gray-600 hover:text-gray-900"
            >
                ← All Orders
            </a>

        </div>
    </x-slot>

    <div class="py-12">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Main --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Order Information --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <div class="flex justify-between items-start">

                            <div>
                                <h1 class="text-xl font-bold text-gray-900">
                                    {{ $order->order_number }}
                                </h1>

                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $order->created_at->format('M d, Y h:i A') }}
                                </p>
                            </div>

                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'shipped' => 'bg-purple-100 text-purple-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'returned' => 'bg-orange-100 text-orange-800',
                                ];
                            @endphp

                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>

                        </div>

                    </div>

                    {{-- Items --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Order Items
                        </h2>

                        <div class="mt-6 divide-y divide-gray-200">

                            @foreach ($order->items as $item)

                                <div class="py-5 flex justify-between gap-4">

                                    <div>
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

                                    <p class="font-medium text-gray-900">
                                        ₱{{ number_format($item->subtotal, 2) }}
                                    </p>

                                </div>

                            @endforeach

                        </div>

                        <div class="border-t pt-5 mt-3 space-y-3">

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
                                <span>Total</span>

                                <span>
                                    ₱{{ number_format($order->total, 2) }}
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">

                    {{-- Update Status --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Update Status
                        </h2>

                        <form
                            action="{{ route('admin.orders.status', $order) }}"
                            method="POST"
                            class="mt-5"
                        >
                            @csrf
                            @method('PATCH')

                            <select
                                name="status"
                                class="block w-full rounded-md border-gray-300 shadow-sm"
                            >

                                @foreach ([
                                    'pending',
                                    'processing',
                                    'shipped',
                                    'delivered',
                                    'cancelled',
                                    'returned'
                                ] as $status)

                                    <option
                                        value="{{ $status }}"
                                        @selected($order->status === $status)
                                    >
                                        {{ ucfirst($status) }}
                                    </option>

                                @endforeach

                            </select>

                            <button
                                type="submit"
                                class="w-full mt-4 px-5 py-2.5 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Update Status
                            </button>

                        </form>

                    </div>

                    {{-- Customer --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Customer
                        </h2>

                        <div class="mt-5 space-y-4 text-sm">

                            <div>
                                <p class="text-gray-500">
                                    Name
                                </p>

                                <p class="font-medium text-gray-900">
                                    {{ $order->user->name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500">
                                    Email
                                </p>

                                <p class="font-medium text-gray-900">
                                    {{ $order->user->email }}
                                </p>
                            </div>

                        </div>

                    </div>

                    {{-- Shipping --}}
                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h2 class="text-lg font-semibold text-gray-900">
                            Shipping
                        </h2>

                        <div class="mt-5 space-y-4 text-sm">

                            <div>
                                <p class="text-gray-500">
                                    Name
                                </p>

                                <p class="font-medium">
                                    {{ $order->shipping_name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500">
                                    Phone
                                </p>

                                <p class="font-medium">
                                    {{ $order->shipping_phone }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500">
                                    Address
                                </p>

                                <p class="font-medium whitespace-pre-line">
                                    {{ $order->shipping_address }}
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</x-app-layout>