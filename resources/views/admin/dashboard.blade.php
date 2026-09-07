<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin Dashboard
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Main Statistics --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Products --}}
                <a
                    href="{{ route('admin.products.index') }}"
                    class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 hover:border-gray-400 transition"
                >
                    <p class="text-sm font-medium text-gray-500">
                        Products
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalProducts }}
                    </p>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $activeProducts }} active
                    </p>
                </a>

                {{-- Categories --}}
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 hover:border-gray-400 transition"
                >
                    <p class="text-sm font-medium text-gray-500">
                        Categories
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalCategories }}
                    </p>
                </a>

                {{-- Customers --}}
                <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
                    <p class="text-sm font-medium text-gray-500">
                        Customers
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalCustomers }}
                    </p>
                </div>

                {{-- Orders --}}
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 hover:border-gray-400 transition"
                >
                    <p class="text-sm font-medium text-gray-500">
                        Total Orders
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalOrders }}
                    </p>
                </a>

                {{-- Pending --}}
                <a
                    href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                    class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 hover:border-gray-400 transition"
                >
                    <p class="text-sm font-medium text-gray-500">
                        Pending Orders
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $pendingOrders }}
                    </p>
                </a>

            </div>

            {{-- Order Status --}}
            <div class="mt-8">
                <div class="rounded-lg bg-white border border-gray-200 shadow-sm">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="font-semibold text-gray-900">
                            Order Overview
                        </h3>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 p-6">

                        <a
                            href="{{ route('admin.orders.index', ['status' => 'processing']) }}"
                            class="hover:underline"
                        >
                            <p class="text-sm text-gray-500">
                                Processing
                            </p>

                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                {{ $processingOrders }}
                            </p>
                        </a>

                        <a
                            href="{{ route('admin.orders.index', ['status' => 'shipped']) }}"
                            class="hover:underline"
                        >
                            <p class="text-sm text-gray-500">
                                Shipped
                            </p>

                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                {{ $shippedOrders }}
                            </p>
                        </a>

                        <a
                            href="{{ route('admin.orders.index', ['status' => 'delivered']) }}"
                            class="hover:underline"
                        >
                            <p class="text-sm text-gray-500">
                                Delivered
                            </p>

                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                {{ $deliveredOrders }}
                            </p>
                        </a>

                        <a
                            href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}"
                            class="hover:underline"
                        >
                            <p class="text-sm text-gray-500">
                                Cancelled
                            </p>

                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                {{ $cancelledOrders }}
                            </p>
                        </a>

                    </div>
                </div>
            </div>

            {{-- Inventory Alerts --}}
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Low Stock --}}
                <div class="rounded-lg bg-white border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                        <h3 class="font-semibold text-gray-900">
                            Low Stock
                        </h3>

                        <span class="text-sm text-gray-500">
                            {{ $lowStockProducts->count() }}
                        </span>
                    </div>

                    @if ($lowStockProducts->isEmpty())
                        <div class="p-6 text-sm text-gray-500">
                            No low-stock products.
                        </div>
                    @else
                        <div class="divide-y divide-gray-200">
                            @foreach ($lowStockProducts as $product)
                                <a
                                    href="{{ route('admin.products.edit', $product) }}"
                                    class="flex items-center justify-between px-6 py-4 hover:bg-gray-50"
                                >
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $product->name }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $product->category->name ?? 'No category' }}
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                                        {{ $product->stock }} left
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Out of Stock --}}
                <div class="rounded-lg bg-white border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                        <h3 class="font-semibold text-gray-900">
                            Out of Stock
                        </h3>

                        <span class="text-sm text-gray-500">
                            {{ $outOfStockProducts->count() }}
                        </span>
                    </div>

                    @if ($outOfStockProducts->isEmpty())
                        <div class="p-6 text-sm text-gray-500">
                            No out-of-stock products.
                        </div>
                    @else
                        <div class="divide-y divide-gray-200">
                            @foreach ($outOfStockProducts as $product)
                                <a
                                    href="{{ route('admin.products.edit', $product) }}"
                                    class="flex items-center justify-between px-6 py-4 hover:bg-gray-50"
                                >
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $product->name }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $product->category->name ?? 'No category' }}
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">
                                        Out of stock
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- Recent Orders --}}
            <div class="mt-8 rounded-lg bg-white border border-gray-200 shadow-sm">

                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                    <h3 class="font-semibold text-gray-900">
                        Recent Orders
                    </h3>

                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="text-sm font-medium text-gray-700 hover:underline"
                    >
                        View all
                    </a>
                </div>

                @if ($recentOrders->isEmpty())
                    <div class="p-6 text-sm text-gray-500">
                        No orders yet.
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach ($recentOrders as $order)
                            <a
                                href="{{ route('admin.orders.show', $order) }}"
                                class="flex flex-col gap-2 px-6 py-4 hover:bg-gray-50 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $order->order_number }}
                                    </p>

                                    <p class="text-sm text-gray-500">
                                        {{ $order->user->name }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <span class="text-sm text-gray-500">
                                        ₱{{ number_format($order->total, 2) }}
                                    </span>

                                    <span class="rounded-full px-3 py-1 text-xs font-medium
                                        @switch($order->status)
                                            @case('pending')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case('processing')
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case('shipped')
                                                bg-purple-100 text-purple-800
                                                @break
                                            @case('delivered')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('cancelled')
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch
                                    ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>