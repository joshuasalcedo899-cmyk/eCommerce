<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit Product
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <form
                    action="{{ route('admin.products.update', $product) }}"
                    method="POST"
                >
                    @csrf
                    @method('PUT')

                    {{-- Category --}}
                    <div>
                        <label
                            for="category_id"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Category
                        </label>

                        <select
                            name="category_id"
                            id="category_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >
                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(old('category_id', $product->category_id) == $category->id)
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Name --}}
                    <div class="mt-6">
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Product Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $product->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mt-6">
                        <label
                            for="description"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Description
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="5"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div class="mt-6">
                        <label for="sizes" class="block text-sm font-medium text-gray-700">
                            Available Sizes
                        </label>

                        <input type="text" name="sizes" id="sizes" value="{{ old('sizes', $product->sizes) }}"
                            placeholder="Example: S, M, L, XL"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

                        <p class="mt-1 text-sm text-gray-500">
                            Enter sizes separated by commas.
                        </p>
                    </div>

                    {{-- Price --}}
                    <div class="mt-6">
                        <label
                            for="price"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Price
                        </label>

                        <input
                            type="number"
                            name="price"
                            id="price"
                            value="{{ old('price', $product->price) }}"
                            min="0"
                            step="0.01"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >
                    </div>

                    {{-- Stock --}}
                    <div class="mt-6">
                        <label
                            for="stock"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Stock
                        </label>

                        <input
                            type="number"
                            name="stock"
                            id="stock"
                            value="{{ old('stock', $product->stock) }}"
                            min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >
                    </div>

                    {{-- Active --}}
                    <div class="mt-6">
                        <label class="inline-flex items-center">

                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', $product->is_active))
                                class="rounded border-gray-300 text-indigo-600 shadow-sm"
                            >

                            <span class="ml-2 text-sm text-gray-600">
                                Active product
                            </span>

                        </label>
                    </div>

                    {{-- Buttons --}}
                    <div class="mt-6 flex gap-3">

                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                        >
                            Update Product
                        </button>

                        <a
                            href="{{ route('admin.products.index') }}"
                            class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300"
                        >
                            Cancel
                        </a>

                    </div>

                </form>
                
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <h3 class="text-lg font-semibold text-gray-900">
                            Product Images
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Upload images for this product. You can set one image as the primary image.
                        </p>

                        @if (session('success'))
                            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Existing Images --}}
                        @if ($product->images->count())
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">

                                @foreach ($product->images->sortBy('sort_order') as $image)
                                    <div class="border rounded-lg p-3">

                                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                            <img
                                                src="{{ asset('storage/' . $image->image_path) }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover"
                                            >
                                        </div>

                                        @if ($image->is_primary)
                                            <div class="mt-2 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Primary
                                                </span>
                                            </div>
                                        @else
                                            <form
                                                action="{{ route('admin.products.images.primary', [$product, $image]) }}"
                                                method="POST"
                                                class="mt-2"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="w-full px-3 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                                                >
                                                    Set as Primary
                                                </button>
                                            </form>
                                        @endif

                                        <form
                                            action="{{ route('admin.products.images.destroy', [$product, $image]) }}"
                                            method="POST"
                                            class="mt-2"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Are you sure you want to delete this image?')"
                                                class="w-full px-3 py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700"
                                            >
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="mt-6 p-6 text-center border-2 border-dashed rounded-lg text-gray-500">
                                No images uploaded yet.
                            </div>
                        @endif

                        {{-- Upload Images --}}
                        <form
                            action="{{ route('admin.products.images.store', $product) }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="mt-8"
                        >
                            @csrf

                            <label
                                for="images"
                                class="block font-medium text-sm text-gray-700"
                            >
                                Upload Images
                            </label>

                            <input
                                id="images"
                                name="images[]"
                                type="file"
                                multiple
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                class="block mt-2 w-full border-gray-300 rounded-md shadow-sm"
                            >

                            @if ($errors->has('images'))
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $errors->first('images') }}
                                </p>
                            @endif

                            <button
                                type="submit"
                                class="mt-4 px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                            >
                                Upload Images
                            </button>
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>