<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Checkout
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

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
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('checkout.store') }}"
                method="POST"
                class="grid grid-cols-1 lg:grid-cols-3 gap-8"
            >
                @csrf

                {{-- Shipping Information --}}
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h3 class="text-lg font-semibold text-gray-900">
                            Shipping Information
                        </h3>

                        <div class="mt-6 space-y-5">

                            <div>
                                <label
                                    for="shipping_name"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Full Name
                                </label>

                                <input
                                    id="shipping_name"
                                    name="shipping_name"
                                    type="text"
                                    value="{{ old('shipping_name', auth()->user()->name) }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >
                            </div>

                            <div>
                                <label
                                    for="shipping_phone"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Phone Number
                                </label>

                                <input
                                    id="shipping_phone"
                                    name="shipping_phone"
                                    type="text"
                                    value="{{ old('shipping_phone') }}"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >
                            </div>

                            <div>
                                <label
                                    for="shipping_address"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Shipping Address
                                </label>

                                <textarea
                                    id="shipping_address"
                                    name="shipping_address"
                                    rows="4"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                >{{ old('shipping_address') }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Payment --}}
                    <div class="bg-white shadow-sm rounded-lg p-6 mt-6">

                        <h3 class="text-lg font-semibold text-gray-900">
                            Payment Method
                        </h3>

                        <div class="mt-4">
                            <label class="flex items-center gap-3">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="cod"
                                    checked
                                    class="text-gray-800 focus:ring-gray-500"
                                >

                                <span>
                                    Cash on Delivery
                                </span>
                            </label>
                        </div>

                    </div>
                </div>

                {{-- Order Summary --}}
                <div>
                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h3 class="text-lg font-semibold text-gray-900">
                            Order Summary
                        </h3>

                        <div class="mt-6 space-y-4">

                            @foreach ($items as $item)
                                <div class="flex justify-between gap-4">

                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $item['product']->name }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $item['quantity'] }}
                                            ×
                                            ₱{{ number_format($item['product']->price, 2) }}
                                        </p>
                                    </div>

                                    <p class="font-medium text-gray-900">
                                        ₱{{ number_format($item['subtotal'], 2) }}
                                    </p>

                                </div>
                            @endforeach

                        </div>

                        <div class="border-t mt-6 pt-6 space-y-3">

                            <div class="flex justify-between">
                                <span class="text-gray-600">
                                    Subtotal
                                </span>

                                <span class="font-medium">
                                    ₱{{ number_format($subtotal, 2) }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">
                                    Shipping
                                </span>

                                <span class="font-medium">
                                    ₱{{ number_format($shippingFee, 2) }}
                                </span>
                            </div>

                            <div class="flex justify-between text-lg font-bold pt-3 border-t">
                                <span>
                                    Total
                                </span>

                                <span>
                                    ₱{{ number_format($total, 2) }}
                                </span>
                            </div>

                        </div>

                        <button
                            type="submit"
                            class="w-full mt-6 px-6 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                        >
                            Place Order
                        </button>

                        <a
                            href="{{ route('cart.index') }}"
                            class="block text-center mt-4 text-sm text-gray-600 hover:text-gray-900"
                        >
                            ← Back to Cart
                        </a>

                    </div>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>