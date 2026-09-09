<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $product->name }} - {{ config('app.name', 'E-Commerce') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">

    @if (session('success') || session('error'))
        <div x-data="{ visible: true }" x-init="setTimeout(() => visible = false, 5000)" x-show="visible"
            x-transition.opacity.duration.200ms role="status" aria-live="polite"
            class="fixed right-4 top-4 z-50 flex max-w-sm items-start gap-3 rounded-lg border bg-white px-4 py-3 shadow-lg {{ session('error') ? 'border-red-200' : 'border-green-200' }}">
            <span class="mt-0.5 text-lg {{ session('error') ? 'text-red-600' : 'text-green-600' }}" aria-hidden="true">
                {{ session('error') ? '!' : '✓' }}
            </span>
            <p class="flex-1 text-sm font-medium text-gray-800">
                {{ session('error') ?: session('success') }}
            </p>
            <button type="button" @click="visible = false"
                class="-mr-1 -mt-1 rounded p-1 text-xl leading-none text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                aria-label="Dismiss notification">
                &times;
            </button>
        </div>
    @endif

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

                        @if ($product->sizes)
                            <fieldset x-data="{ selectedSize: '' }">
                                <legend class="text-sm font-semibold text-gray-700">Available sizes</legend>
                                <div class="mt-2 flex flex-wrap gap-2">
                                @foreach (array_filter(array_map('trim', explode(',', $product->sizes))) as $size)
                                    <label class="cursor-pointer" @click="selectedSize = '{{ addslashes($size) }}'">
                                        <input type="radio" name="size" value="{{ $size }}" required class="peer sr-only" x-model="selectedSize">
                                        <span :class="selectedSize === '{{ addslashes($size) }}' ? 'border-gray-900 bg-gray-900 text-white' : 'border-gray-300 bg-white text-gray-700'" class="inline-flex min-w-12 items-center justify-center rounded-md border px-3 py-2 text-sm font-semibold transition hover:border-gray-500 peer-focus-visible:ring-2 peer-focus-visible:ring-gray-500 peer-focus-visible:ring-offset-2">
                                            {{ $size }}
                                        </span>
                                    </label>
                                @endforeach
                                </div>
                            </fieldset>
                        @endif

                        <label for="quantity" class="block text-sm font-medium text-gray-700">
                            Quantity
                        </label>

                        <div class="mt-2 flex gap-3">

                            <input id="quantity" name="quantity" type="number" min="1" max="{{ $product->stock }}"
                                value="1" class="w-24 rounded-md border-gray-300">

                            <button type="submit" href="{{ route('cart.index') }}"
                                class="flex-1 px-6 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                Add to Cart
                            </button>

                        </div>
                    </form>

                </div>

            </div>

        </div>

        {{-- Reviews --}}
        <section id="reviews" class="mt-12">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Customer Reviews</h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $reviews->count() }} {{ $reviews->count() === 1 ? 'review' : 'reviews' }}
                        @if ($reviews->count())
                            · {{ number_format($averageRating, 1) }} out of 5
                        @endif
                    </p>
                </div>

                @if (auth()->check() && $hasPurchased)
                    <span class="text-sm font-medium text-green-700">Verified purchase</span>
                @endif
            </div>

            @auth
                @if ($hasPurchased)
                    @if (!$existingReview)
                        <form action="{{ route('reviews.store', $product) }}" method="POST"
                            class="mt-6 rounded-lg bg-white p-6 shadow-sm">
                            @csrf

                            <h3 class="font-semibold text-gray-900">Leave a review</h3>

                            @if ($errors->any())
                                <div class="mt-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <div class="mt-4" x-data="{ rating: {{ (int) old('rating', 5) }} }">
                                <label class="block text-sm font-medium text-gray-700">Rating</label>
                                <input type="hidden" id="rating" name="rating" x-model="rating">
                                <div class="mt-2 flex items-center gap-1" role="radiogroup" aria-label="Rating">
                                    @for ($rating = 1; $rating <= 5; $rating++)
                                        <button type="button" @click="rating = {{ $rating }}"
                                            :class="rating >= {{ $rating }} ? 'opacity-100' : 'opacity-30 grayscale'"
                                            :aria-checked="rating === {{ $rating }}" role="radio"
                                            aria-label="{{ $rating }} out of 5 stars"
                                            class="rounded p-1 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            <img src="{{ asset('image/star.png') }}" alt="" class="h-7 w-7 object-contain">
                                        </button>
                                    @endfor
                                    <span class="ml-2 text-sm text-gray-600" x-text="`${rating} out of 5`"></span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="comment" class="block text-sm font-medium text-gray-700">Review</label>
                                <textarea id="comment" name="comment" rows="4" maxlength="1000" required
                                    class="mt-1 block w-full rounded-md border-gray-300">{{ old('comment') }}</textarea>
                            </div>

                            <button type="submit"
                                class="mt-4 rounded-md bg-gray-800 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-700">
                                Submit Review
                            </button>
                        </form>
                    @endif
                @else
                    <p class="mt-6 rounded-lg bg-white p-5 text-sm text-gray-600 shadow-sm">
                        Purchase this product to leave a verified review.
                    </p>
                @endif
            @else
                <p class="mt-6 rounded-lg bg-white p-5 text-sm text-gray-600 shadow-sm">
                    <a href="{{ route('login') }}" class="font-medium text-gray-900 underline">Log in</a>
                    to review this product after purchasing it.
                </p>
            @endauth

            <div class="mt-6 space-y-4">
                @forelse ($reviews as $review)
                    <article class="rounded-lg bg-white p-5 shadow-sm"
                        @if (auth()->id() === $review->user_id) x-data="{ editing: {{ old('review_id') == $review->id ? 'true' : 'false' }} }" @endif>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $review->user->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <div class="flex items-center gap-1" aria-label="{{ $review->rating }} out of 5 stars">
                                @for ($star = 1; $star <= $review->rating; $star++)
                                    <img src="{{ asset('image/star.png') }}" alt="" class="h-5 w-5 object-contain">
                                @endfor
                                <span class="ml-1 text-sm font-medium text-gray-700">{{ $review->rating }}/5</span>
                                </div>
                                @if (auth()->id() === $review->user_id)
                                    <button type="button" @click="editing = !editing"
                                        class="text-sm font-medium text-gray-700 underline underline-offset-2 hover:text-gray-900"
                                        x-text="editing ? 'Cancel' : 'Edit'"></button>
                                @endif
                            </div>
                        </div>
                        <p class="mt-3 whitespace-pre-line text-gray-700" x-show="!editing">{{ $review->comment }}</p>

                        @if (auth()->id() === $review->user_id)
                            <form x-show="editing" x-cloak action="{{ route('reviews.update', [$product, $review]) }}"
                                method="POST" class="mt-4 border-t border-gray-100 pt-4">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="review_id" value="{{ $review->id }}">

                                @if ($errors->any() && old('review_id') == $review->id)
                                    <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <div x-data="{ rating: {{ (int) old('rating', $review->rating) }} }">
                                    <label class="block text-sm font-medium text-gray-700">Rating</label>
                                    <input type="hidden" name="rating" x-model="rating">
                                    <div class="mt-2 flex items-center gap-1" role="radiogroup" aria-label="Rating">
                                        @for ($rating = 1; $rating <= 5; $rating++)
                                            <button type="button" @click="rating = {{ $rating }}"
                                                :class="rating >= {{ $rating }} ? 'opacity-100' : 'opacity-30 grayscale'"
                                                :aria-checked="rating === {{ $rating }}" role="radio"
                                                aria-label="{{ $rating }} out of 5 stars"
                                                class="rounded p-1 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                                <img src="{{ asset('image/star.png') }}" alt="" class="h-6 w-6 object-contain">
                                            </button>
                                        @endfor
                                    </div>
                                </div>

                                <label for="comment-{{ $review->id }}" class="mt-4 block text-sm font-medium text-gray-700">Comment</label>
                                <textarea id="comment-{{ $review->id }}" name="comment" rows="3" maxlength="1000" required
                                    class="mt-1 block w-full rounded-md border-gray-300">{{ old('comment', $review->comment) }}</textarea>
                                <button type="submit"
                                    class="mt-3 rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700">
                                    Save changes
                                </button>
                            </form>
                        @endif
                    </article>
                @empty
                    <div class="rounded-lg bg-white p-6 text-center text-sm text-gray-500 shadow-sm">
                        No reviews yet. Be the first to review this product.
                    </div>
                @endforelse
            </div>
        </section>

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
