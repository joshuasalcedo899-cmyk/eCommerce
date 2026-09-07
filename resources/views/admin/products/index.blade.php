<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Products
            </h2>

            <a
                href="{{ route('admin.products.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
            >
                Add Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Product
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Category
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Price
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Stock
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Status
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse ($products as $product)

                                <tr>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            {{ $product->name }}
                                        </div>

                                        <div class="text-sm text-gray-500">
                                            {{ $product->slug }}
                                        </div>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-gray-600">
                                        {{ $product->category->name }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        ₱{{ number_format($product->price, 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        {{ $product->stock }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">

                                        @if ($product->is_active)
                                            <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                                Inactive
                                            </span>
                                        @endif

                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">

                                        <a
                                            href="{{ route('admin.products.edit', $product) }}"
                                            class="mr-3 text-indigo-600 hover:text-indigo-900"
                                        >
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('admin.products.destroy', $product) }}"
                                            method="POST"
                                            class="inline"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                onclick="return confirm('Are you sure you want to delete this product?')"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="6"
                                        class="px-6 py-8 text-center text-gray-500"
                                    >
                                        No products found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="p-6">
                    {{ $products->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>