<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $product->name }} - {{ config('app.name', 'E-Commerce') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">

    {{-- Navigation --}}
    <nav class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center justify-between">

                <a href="{{ route('store.index') }}" class="inline-flex items-center">
                    <img src="{{ asset('image/like.png') }}" alt="{{ config('app.name', 'E-Commerce') }}"
                        class="h-9 w-auto object-contain">
                </a>

                <a href="{{ route('store.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Back to Products
                </a>

            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="bg-white rounded-lg shadow-sm p-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

                {{-- Images --}}
                <div>

                    @php
                        $primaryImage = $product->images->firstWhere('is_primary', true)
                            ?? $product->images->first();
                    @endphp

                    @if ($primaryImage)

                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $primaryImage->image_path) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">
                        </div>

                        @if ($product->images->count() > 1)

                            <div class="grid grid-cols-4 gap-3 mt-4">

                                @foreach ($product->images->sortBy('sort_order') as $image)

                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}"
                                        class="w-full aspect-square object-cover rounded-md">

                                @endforeach

                            </div>

                        @endif

                    @else

                        <div class="aspect-square bg-gray-200 rounded-lg flex items-center justify-center">
                            <span class="text-gray-500">
                                No Image Available
                            </span>
                        </div>

                    @endif

                </div>

                {{-- Product Information --}}
                <div>

                    <p class="text-sm text-gray-500">
                        {{ $product->category->name }}
                    </p>

                    <h1 class="mt-2 text-3xl font-bold">
                        {{ $product->name }}
                    </h1>

                    <p class="mt-4 text-3xl font-bold">
                        ₱{{ number_format($product->price, 2) }}
                    </p>

                    <div class="mt-6 border-t pt-6">

                        <h2 class="font-semibold">
                            Description
                        </h2>

                        <p class="mt-2 text-gray-600 whitespace-pre-line">
                            {{ $product->description ?: 'No description available.' }}
                        </p>

                    </div>

                    <div class="mt-6">

                        <p class="text-sm text-gray-500">
                            Stock available:
                        </p>

                        <p class="font-semibold">
                            {{ $product->stock }}
                        </p>

                    </div>

                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-8">
                        @csrf

                        <label for="quantity" class="block text-sm font-medium text-gray-700">
                            Quantity
                        </label>

                        <div class="flex gap-3 mt-2">

                            <input id="quantity" name="quantity" type="number" min="1" max="{{ $product->stock }}"
                                value="1" class="w-24 rounded-md border-gray-300">

                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                Add to Cart
                            </button>

                        </div>
                    </form>

                </div>

            </div>

        </div>

        {{-- Related Products --}}
        @if ($relatedProducts->count())

            <div class="mt-12">

                <h2 class="text-2xl font-bold mb-6">
                    Related Products
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    @foreach ($relatedProducts as $related)

                        @php
                            $relatedImage = $related->images->firstWhere('is_primary', true)
                                ?? $related->images->first();
                        @endphp

                        <a href="{{ route('store.show', $related) }}"
                            class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">

                            @if ($relatedImage)
                                <img src="{{ asset('storage/' . $relatedImage->image_path) }}" alt="{{ $related->name }}"
                                    class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    No Image
                                </div>
                            @endif

                            <div class="p-4">

                                <h3 class="font-semibold">
                                    {{ $related->name }}
                                </h3>

                                <p class="mt-2 font-bold">
                                    ₱{{ number_format($related->price, 2) }}
                                </p>

                            </div>

                        </a>

                    @endforeach

                </div>

            </div>

        @endif

    </main>

</body>

</html>