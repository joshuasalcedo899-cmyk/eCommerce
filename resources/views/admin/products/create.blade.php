<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Add Product
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">
                            Category
                        </label>

                        <select name="category_id" id="category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            <option value="">Select a category</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div class="mt-6">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Product Name
                        </label>

                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>

                        <textarea name="description" id="description" rows="5"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div class="mt-6">
                        <label for="sizes" class="block text-sm font-medium text-gray-700">
                            Available Sizes
                        </label>

                        <input type="text" name="sizes" id="sizes" value="{{ old('sizes') }}"
                            placeholder="Example: S, M, L, XL"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                        <p class="mt-1 text-sm text-gray-500">
                            Enter sizes separated by commas.
                        </p>

                        @error('sizes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div class="mt-6">
                        <label for="price" class="block text-sm font-medium text-gray-700">
                            Price
                        </label>

                        <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" step="0.01"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>

                        @error('price')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Stock --}}
                    <div class="mt-6">
                        <label for="stock" class="block text-sm font-medium text-gray-700">
                            Stock
                        </label>

                        <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>

                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Active --}}
                    <div class="mt-6">
                        <label class="inline-flex items-center">

                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                                class="rounded border-gray-300 text-gray-800 focus:ring-gray-500">

                            <span class="ml-2 text-sm text-gray-600">
                                Active product
                            </span>

                        </label>
                    </div>

                    {{-- Product Images --}}
                    <div class="mt-6">
                        <x-input-label for="images" value="Product Images" />

                        <input id="images" name="images[]" type="file" multiple
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />

                        <p class="mt-1 text-sm text-gray-500">
                            You can select multiple images. The first image will be the primary image.
                            Maximum 5MB per image.
                        </p>

                        <x-input-error :messages="$errors->get('images')" class="mt-2" />

                        @foreach ($errors->get('images.*') as $messages)
                            @foreach ($messages as $message)
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @endforeach
                        @endforeach
                    </div>

                    {{-- Actions --}}
                    <div class="mt-8 flex items-center gap-3">
                        <button type="submit"
                            class="rounded-md bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">
                            Create Product
                        </button>

                        <a href="{{ route('admin.products.index') }}"
                            class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>