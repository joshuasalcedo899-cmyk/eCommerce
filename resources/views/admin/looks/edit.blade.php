<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Edit Shop the Look</h2></x-slot>
    <div class="py-12"><div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.looks.update', $look) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('admin.looks.form')
            </form>
        </div>
        <div class="mt-6 rounded-lg bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between"><h3 class="text-lg font-semibold text-gray-900">Outfit combinations</h3><a href="{{ route('admin.looks.variants.create', $look) }}" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Add Combination</a></div>
            <div class="mt-5 space-y-3">
                @forelse ($look->variants as $variant)
                    <div class="flex items-center gap-4 rounded-md border border-gray-200 p-3">
                        <img src="{{ asset('storage/' . $variant->image_path) }}" alt="{{ $variant->title }}" class="h-16 w-16 rounded object-cover">
                        <div class="min-w-0 flex-1"><p class="font-medium">{{ $variant->title }}</p><p class="text-sm text-gray-500">{{ $variant->products->count() }} products</p></div>
                        <a href="{{ route('admin.looks.variants.edit', [$look, $variant]) }}" class="text-sm text-indigo-600">Edit</a>
                        <form method="POST" action="{{ route('admin.looks.variants.destroy', [$look, $variant]) }}">@csrf @method('DELETE')<button type="submit" onclick="return confirm('Remove this combination?')" class="text-sm text-red-600">Remove</button></form>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Add combinations to let customers switch the outfit on the model image.</p>
                @endforelse
            </div>
        </div>
    </div></div>
</x-app-layout>
