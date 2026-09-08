<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $look->title }} - {{ config('app.name', 'E-Commerce') }}</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="bg-gray-100 text-gray-900">
    @include('layouts.navigation')
    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div x-data="{ selected: 'base' }">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <div>
                    @if ($look->variants->count())
                        <img x-show="selected === 'base'" src="{{ asset('storage/' . $look->image_path) }}" alt="{{ $look->title }}" class="w-full rounded-lg object-cover shadow-sm">
                        @foreach ($look->variants as $variant)
                            <img x-show="selected === '{{ $variant->id }}'" src="{{ asset('storage/' . $variant->image_path) }}" alt="{{ $variant->title }}" class="w-full rounded-lg object-cover shadow-sm">
                        @endforeach
                    @else
                        <img src="{{ asset('storage/' . $look->image_path) }}" alt="{{ $look->title }}" class="w-full rounded-lg object-cover shadow-sm">
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium uppercase tracking-wide text-gray-500">Shop the Look</p>
                    <h1 class="mt-2 text-3xl font-bold">{{ $look->title }}</h1>
                    <p class="mt-4 whitespace-pre-line text-gray-600">{{ $look->description }}</p>
                    @if ($look->variants->count())
                        <div class="mt-8"><h2 class="text-lg font-semibold">Choose a combination</h2><div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" @click="selected = 'base'" :class="selected === 'base' ? 'bg-gray-800 text-white' : 'bg-white text-gray-700'" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium">Original combination</button>
                            @foreach ($look->variants as $variant)
                                <button type="button" @click="selected = '{{ $variant->id }}'" :class="selected === '{{ $variant->id }}' ? 'bg-gray-800 text-white' : 'bg-white text-gray-700'" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium">{{ $variant->title }}</button>
                            @endforeach
                        </div></div>
                        <div x-show="selected === 'base'" class="mt-8"><h2 class="text-xl font-semibold">Original combination</h2><div class="mt-4 space-y-3">
                            @forelse ($look->products as $product)
                                @php($image = $product->images->firstWhere('is_primary', true) ?? $product->images->first())
                                <div class="flex items-center gap-4 rounded-lg bg-white p-4 shadow-sm">@if ($image)<img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-md object-cover">@endif<div class="min-w-0 flex-1"><a href="{{ route('store.show', $product) }}" class="font-semibold hover:underline">{{ $product->name }}</a><p class="mt-1 font-medium">₱{{ number_format($product->price, 2) }}</p></div><form action="{{ route('cart.add', $product) }}" method="POST">@csrf<input type="hidden" name="quantity" value="1"><button type="submit" class="rounded-md bg-gray-800 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700">Add</button></form></div>
                            @empty<p class="text-gray-500">Products in the original combination are currently unavailable.</p>@endforelse
                        </div></div>
                        @foreach ($look->variants as $variant)
                            <div x-show="selected === '{{ $variant->id }}'" class="mt-8"><h2 class="text-xl font-semibold">{{ $variant->title }}</h2><div class="mt-4 space-y-3">
                                @forelse ($variant->products as $product)
                                    @php($image = $product->images->firstWhere('is_primary', true) ?? $product->images->first())
                                    <div class="flex items-center gap-4 rounded-lg bg-white p-4 shadow-sm">@if ($image)<img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-md object-cover">@endif<div class="min-w-0 flex-1"><a href="{{ route('store.show', $product) }}" class="font-semibold hover:underline">{{ $product->name }}</a><p class="mt-1 font-medium">₱{{ number_format($product->price, 2) }}</p></div><form action="{{ route('cart.add', $product) }}" method="POST">@csrf<input type="hidden" name="quantity" value="1"><button type="submit" class="rounded-md bg-gray-800 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700">Add</button></form></div>
                                @empty<p class="text-gray-500">Products in this combination are currently unavailable.</p>@endforelse
                            </div></div>
                        @endforeach
                    @else
                        <div class="mt-8"><h2 class="text-xl font-semibold">Shop this outfit</h2><div class="mt-4 space-y-3">
                            @forelse ($look->products as $product)
                                @php($image = $product->images->firstWhere('is_primary', true) ?? $product->images->first())
                                <div class="flex items-center gap-4 rounded-lg bg-white p-4 shadow-sm">@if ($image)<img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-md object-cover">@endif<div class="min-w-0 flex-1"><a href="{{ route('store.show', $product) }}" class="font-semibold hover:underline">{{ $product->name }}</a><p class="mt-1 font-medium">₱{{ number_format($product->price, 2) }}</p></div><form action="{{ route('cart.add', $product) }}" method="POST">@csrf<input type="hidden" name="quantity" value="1"><button type="submit" class="rounded-md bg-gray-800 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700">Add</button></form></div>
                            @empty<p class="text-gray-500">Products in this look are currently unavailable.</p>@endforelse
                        </div></div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</body>
</html>
