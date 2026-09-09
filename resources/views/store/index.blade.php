<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'E-Commerce') }}</title>

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
                    <a href="{{ route('store.index') }}" class="font-semibold text-sm text-gray-900">
                        Shop
                    </a>
                    <a href="{{ route('cart.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
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

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                                        this.closest('form').submit();">
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

    {{-- Main --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="mb-8">
            <h1 class="text-3xl font-bold">
                Our Products
            </h1>

            <p class="mt-2 text-gray-600">
                Browse our available products.
            </p>
        </div>

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('store.index') }}" class="bg-white p-4 rounded-lg shadow-sm mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">
                        Search
                    </label>

                    <input id="search" name="search" type="text" value="{{ request('search') }}"
                        placeholder="Search products..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 focus:ring-gray-500 focus:border-gray-500">
                        Category
                    </label>

                    <select id="category" name="category"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                        <option value="">All Categories</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Search
                    </button>
                </div>

            </div>
        </form>

        {{-- Products --}}
        @if ($products->count())

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @foreach ($products as $product)

                    @php
                        $primaryImage = $product->images->firstWhere('is_primary', true)
                            ?? $product->images->first();
                    @endphp

                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">

                        {{-- Image --}}
                        <a href="{{ route('store.show', $product) }}">

                            @if ($primaryImage)
                                <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $product->name }}"
                                    class="w-full h-56 object-cover">
                            @else
                                <div class="w-full h-56 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">
                                        No Image
                                    </span>
                                </div>
                            @endif

                        </a>

                        {{-- Details --}}
                        <div class="p-5">

                            <p class="text-sm text-gray-500">
                                {{ $product->category->name }}
                            </p>

                            <h2 class="mt-1 text-lg font-semibold">
                                {{ $product->name }}
                            </h2>

                            <p class="mt-2 text-xl font-bold">
                                ₱{{ number_format($product->price, 2) }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $product->stock }} in stock
                            </p>

                            <a href="{{ route('store.show', $product) }}"
                                class="mt-4 block w-full text-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                View Product
                            </a>

                        </div>

                    </div>

                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $products->links() }}
            </div>

        @else

            <div class="bg-white rounded-lg shadow-sm p-10 text-center">

                <h2 class="text-xl font-semibold">
                    No products found
                </h2>

                <p class="mt-2 text-gray-500">
                    Try changing your search or category filter.
                </p>

            </div>

        @endif

        @if ($looks->count())
            <section class="mt-14">
                <div class="flex items-end justify-between gap-4">
                    <div><h2 class="text-2xl font-bold">Shop the Look</h2><p class="mt-1 text-gray-600">See the outfit, then shop every piece.</p></div>
                    <a href="{{ route('looks.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">View all looks</a>
                </div>
                <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($looks as $look)
                        <a href="{{ route('looks.show', $look) }}" class="overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                            <img src="{{ asset('storage/' . $look->image_path) }}" alt="{{ $look->title }}" class="aspect-[4/5] w-full object-cover">
                            <div class="p-4"><h3 class="font-semibold">{{ $look->title }}</h3><p class="mt-1 text-sm text-gray-500">Shop this outfit</p></div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </main>

</body>

</html>
