<div>
    <label for="title" class="block text-sm font-medium text-gray-700">Look title</label>
    <input id="title" name="title" value="{{ old('title', $look->title ?? '') }}" required
        class="mt-1 block w-full rounded-md border-gray-300">
    @error('title')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
<div class="mt-6">
    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
    <textarea id="description" name="description" rows="4"
        class="mt-1 block w-full rounded-md border-gray-300">{{ old('description', $look->description ?? '') }}</textarea>
</div>
<div class="mt-6">
    <label for="image" class="block text-sm font-medium text-gray-700">Model image</label>
    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/jpg,image/webp"
        class="mt-1 block w-full rounded-md border-gray-300" {{ isset($look) ? '' : 'required' }}>
    <p class="mt-1 text-sm text-gray-500">Maximum 10MB. Use a clear image showing the full outfit.</p>
    @error('image')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
<div class="mt-6">
    <p class="block text-sm font-medium text-gray-700">Products in this look</p>
    <div
        class="mt-2 grid max-h-72 grid-cols-1 gap-2 overflow-y-auto rounded-md border border-gray-200 p-3 sm:grid-cols-2">
        @foreach ($products as $product)
            <label class="flex items-center gap-3 rounded-md p-2 hover:bg-gray-50">
                <input type="checkbox" name="products[]" value="{{ $product->id }}" @checked(in_array($product->id, old('products', isset($look) ? $look->products->pluck('id')->all() : [])))
                    class="rounded border-gray-300 text-gray-800">
                <span class="text-sm text-gray-700">{{ $product->name }} <span
                        class="text-gray-400">(₱{{ number_format($product->price, 2) }})</span></span>
            </label>
        @endforeach
    </div>
    @error('products')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
<label class="mt-6 inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1"
        @checked(old('is_active', $look->is_active ?? true)) class="rounded border-gray-300 text-gray-800"><span
        class="text-sm text-gray-600">Show this look in the storefront</span></label>
<div class="mt-6 flex gap-3"><button type="submit"
        class="rounded-md bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">{{ isset($look) ? 'Save Changes' : 'Create Look' }}</button><a
        href="{{ route('admin.looks.index') }}"
        class="rounded-md bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200">Cancel</a>
</div>