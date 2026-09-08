<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Purchase History
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-md bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

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

                            <tbody class="bg-white divide-y divide-gray-200">

                                @foreach ($orders as $order)
                                    <tr>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="font-medium text-gray-900">
                                                {{ $order->order_number }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $order->created_at->format('M d, Y h:i A') }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                                            ₱{{ number_format($order->total, 2) }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a
                                                href="{{ route('orders.show', $order) }}"
                                                class="text-sm text-gray-600 hover:text-gray-900"
                                            >
                                                Track Purchase
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
                    <div class="p-12 text-center">

                        <h3 class="text-lg font-medium text-gray-900">
                            No orders yet
                        </h3>

                        <p class="mt-2 text-gray-500">
                            You haven't placed any orders yet.
                        </p>

                        <a
                            href="{{ route('store.index') }}"
                            class="inline-block mt-6 px-6 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-700"
                        >
                            Start Shopping
                        </a>

                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
