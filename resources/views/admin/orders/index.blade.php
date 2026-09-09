<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $archived ? 'Order Archive' : 'Orders' }}
            </h2>

            <div class="flex items-center gap-4">
                <a href="{{ $archived ? route('admin.orders.index') : route('admin.orders.archive') }}"
                    class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ $archived ? 'Active Orders' : 'Order Archive' }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white shadow-sm rounded-lg p-6 mb-6">

                <form
                    method="GET"
                    action="{{ $archived ? route('admin.orders.archive') : route('admin.orders.index') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                >

                    <div>
                        <label
                            for="search"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Search
                        </label>

                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ request('search') }}"
                            placeholder="Order number, customer..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                    </div>

                    <div>
                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option value="">All Statuses</option>

                            @foreach (($archived ? ['delivered', 'cancelled', 'returned'] : ['pending', 'processing', 'shipped']) as $status)
                                <option
                                    value="{{ $status }}"
                                    @selected(request('status') === $status)
                                >
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">

                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ $archived ? route('admin.orders.archive') : route('admin.orders.index') }}"
                            class="px-5 py-2.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Reset
                        </a>

                    </div>

                </form>

            </div>

            {{-- Orders --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                @if ($orders->count())

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Order
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Customer
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Status
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Total
                                    </th>

                                    <th class="px-6 py-3">
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">

                                @foreach ($orders as $order)

                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium text-gray-900">
                                                {{ $order->order_number }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">

                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $order->user->name }}
                                            </div>

                                            <div class="text-sm text-gray-500">
                                                {{ $order->user->email }}
                                            </div>

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y h:i A') }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">

                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'processing' => 'bg-blue-100 text-blue-800',
                                                    'shipped' => 'bg-purple-100 text-purple-800',
                                                    'delivered' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                ];
                                            @endphp

                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>

                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                                            ₱{{ number_format($order->total, 2) }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right">

                                            <a
                                                href="{{ route('admin.orders.show', $order) }}"
                                                class="text-sm text-gray-600 hover:text-gray-900"
                                            >
                                                View
                                            </a>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    <div class="p-6">
                        {{ $orders->links() }}
                    </div>

                @else

                    <div class="p-12 text-center text-gray-500">
                        No orders found.
                    </div>

                @endif

            </div>

        </div>
    </div>
</x-app-layout>