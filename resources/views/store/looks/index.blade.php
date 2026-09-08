<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Shop the Look - {{ config('app.name', 'E-Commerce') }}</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body class="bg-gray-100 text-gray-900">
    @include('layouts.navigation')
    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8"><h1 class="text-3xl font-bold">Shop the Look</h1><p class="mt-2 text-gray-600">Discover complete outfits and shop each piece.</p></div>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($looks as $look)
                <a href="{{ route('looks.show', $look) }}" class="overflow-hidden rounded-lg bg-white shadow-sm transition hover:shadow-md">
                    <img src="{{ asset('storage/' . $look->image_path) }}" alt="{{ $look->title }}" class="aspect-[4/5] w-full object-cover">
                    <div class="p-5"><h2 class="text-lg font-semibold">{{ $look->title }}</h2><p class="mt-1 text-sm text-gray-500">{{ $look->products->count() }} outfit items</p></div>
                </a>
            @empty
                <div class="col-span-full rounded-lg bg-white p-12 text-center text-gray-500">No looks are available yet.</div>
            @endforelse
        </div>
        <div class="mt-8">{{ $looks->links() }}</div>
    </main>
</body>
</html>
