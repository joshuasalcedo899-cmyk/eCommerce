<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Shopping Cart - {{ config('app.name', 'E-Commerce') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">

    @include('layouts.navigation')
    @if (false)
    {{-- Legacy navigation --}}
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center justify-between">
                <a href="{{ route('store.index') }}" class="inline-flex items-center">
                    <img src="{{ asset('image/like.png') }}" alt="{{ config('app.name', 'E-Commerce') }}"
                        class="h-9 w-auto object-contain">
                </a>

                <div class="flex items-center gap-4">
                    <a href="{{ route('store.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        Shop
                    </a>
                    <a href="{{ route('cart.index') }}" class="font-semibold text-sm text-gray-900">
                        Cart
                    </a>
                    @auth
                        <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Purchase History
                        </a>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-3xl font-bold mb-8">
            Shopping Cart
        </h1>

        @if (session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (count($items))

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- Cart Items --}}
                <div class="lg:col-span-2 space-y-4">

                    <form id="checkout-form" action="{{ route('cart.checkout') }}" method="POST">
                        @csrf
                    </form>

                    <div class="flex items-center justify-between rounded-lg bg-white px-5 py-4 shadow-sm">
                        <label for="select-all" class="flex items-center gap-3 text-sm font-medium text-gray-700">
                            <input id="select-all" type="checkbox" class="rounded border-gray-300 text-gray-800 focus:ring-gray-500"
                                @checked(count($selectedItems) === count($items))>
                            Select all items
                        </label>
                        <span id="selected-count" class="text-sm text-gray-500">
                            {{ count($selectedItems) }} selected
                        </span>
                    </div>

                    @foreach ($items as $item)

                        @php
                            $product = $item['product'];

                            $primaryImage = $product->images->firstWhere('is_primary', true)
                                ?? $product->images->first();
                        @endphp

                        <div class="bg-white rounded-lg shadow-sm p-5">

                            <div class="flex flex-col sm:flex-row gap-5">

                                <div class="flex items-start pt-1">
                                    <input type="checkbox" name="selected_items[]" value="{{ $product->id }}"
                                        form="checkout-form"
                                        data-subtotal="{{ $item['subtotal'] }}"
                                        class="cart-item-checkbox rounded border-gray-300 text-gray-800 focus:ring-gray-500"
                                        @checked(in_array($product->id, $selectedItems))>
                                </div>

                                {{-- Image --}}
                                <div class="w-full sm:w-32 h-32 flex-shrink-0">

                                    @if ($primaryImage)

                                        <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover rounded-lg">

                                    @else

                                        <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                            <span class="text-sm text-gray-500">
                                                No Image
                                            </span>
                                        </div>

                                    @endif

                                </div>

                                {{-- Product --}}
                                <div class="flex-1">

                                    <h2 class="text-lg font-semibold">
                                        {{ $product->name }}
                                    </h2>

                                    <p class="mt-1 text-gray-500">
                                        ₱{{ number_format($product->price, 2) }}
                                    </p>

                                    {{-- Quantity --}}
                                    <form action="{{ route('cart.update', $product) }}" method="POST"
                                        class="mt-4 flex items-center gap-3">
                                        @csrf
                                        @method('PATCH')

                                        <label for="quantity-{{ $product->id }}" class="text-sm text-gray-600">
                                            Quantity
                                        </label>

                                        <input id="quantity-{{ $product->id }}" name="quantity" type="number" min="1"
                                            max="{{ $product->stock }}" value="{{ $item['quantity'] }}"
                                            class="w-20 rounded-md border-gray-300">

                                        <button type="submit"
                                            class="px-3 py-2 text-sm bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                            Update
                                        </button>
                                    </form>

                                    {{-- Remove --}}
                                    <form action="{{ route('cart.remove', $product) }}" method="POST" class="mt-3">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                            Remove
                                        </button>
                                    </form>

                                </div>

                                {{-- Subtotal --}}
                                <div class="text-right">

                                    <p class="text-lg font-bold">
                                        ₱{{ number_format($item['subtotal'], 2) }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    @endforeach

                    {{-- Clear Cart --}}
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" onclick="return confirm('Are you sure you want to clear your cart?')"
                            class="text-sm text-red-600 hover:text-red-800">
                            Clear Cart
                        </button>
                    </form>

                </div>

                {{-- Summary --}}
                <div>

                    <div class="bg-white rounded-lg shadow-sm p-6">

                        <h2 class="text-xl font-semibold">
                            Order Summary
                        </h2>

                        <p class="mt-2 text-sm text-gray-500">
                            Selected items: <span id="selected-subtotal" class="font-semibold text-gray-900">{{ number_format($selectedTotal, 2) }}</span>
                        </p>

                        <div class="mt-6 flex justify-between">
                            <span class="text-gray-600">
                                Subtotal
                            </span>

                            <span class="font-semibold">
                                ₱{{ number_format($total, 2) }}
                            </span>
                        </div>

                        <div class="border-t mt-4 pt-4 flex justify-between">
                            <span class="text-lg font-bold">
                                Total
                            </span>

                            <span id="selected-total" class="text-xl font-bold">
                                ₱{{ number_format($total, 2) }}
                            </span>
                        </div>

                        <button type="submit" form="checkout-form"
                            class="block w-full text-center px-6 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Proceed to Checkout
                        </button>

                    </div>

                </div>

            </div>

        @else

            <div class="bg-white rounded-lg shadow-sm p-10 text-center">

                <h2 class="text-xl font-semibold">
                    Your cart is empty
                </h2>

                <p class="mt-2 text-gray-500">
                    Add some products to your cart to get started.
                </p>

                <a href="{{ route('store.index') }}"
                    class="inline-block mt-6 px-5 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                    Start Shopping
                </a>

            </div>

        @endif

    </main>

</body>

<script>
    const selectAll = document.getElementById('select-all');
    const itemCheckboxes = [...document.querySelectorAll('.cart-item-checkbox')];
    const selectedCount = document.getElementById('selected-count');
    const selectedSubtotal = document.getElementById('selected-subtotal');
    const selectedTotal = document.getElementById('selected-total');

    function updateSelectionState() {
        const checkedCount = itemCheckboxes.filter((checkbox) => checkbox.checked).length;
        const total = itemCheckboxes
            .filter((checkbox) => checkbox.checked)
            .reduce((sum, checkbox) => sum + Number(checkbox.dataset.subtotal), 0);
        const formattedTotal = `₱${total.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;

        selectedCount.textContent = `${checkedCount} selected`;
        selectedSubtotal.textContent = formattedTotal;
        selectedTotal.textContent = formattedTotal;
        selectAll.checked = checkedCount === itemCheckboxes.length;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
    }

    selectAll?.addEventListener('change', () => {
        itemCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
        updateSelectionState();
    });

    itemCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelectionState));
</script>

</html>
