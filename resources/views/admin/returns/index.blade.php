<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $archived ? 'Return & Exchange Archive' : 'Return & Exchange Requests' }}</h2>
            <div class="flex items-center gap-4">
                <a href="{{ $archived ? route('admin.returns.index') : route('admin.returns.archive') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ $archived ? 'Active Requests' : 'Return Archive' }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-md bg-red-50 p-4 text-red-800">{{ session('error') }}</div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Order / Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Request</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($returnRequests as $returnRequest)
                                <tr>
                                    <td class="px-6 py-4 text-sm">
                                        <p class="font-semibold text-gray-900">{{ $returnRequest->item->order->order_number }}</p>
                                        <p class="mt-1 text-gray-500">{{ $returnRequest->user->name }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <p class="font-medium text-gray-900">{{ $returnRequest->item->product_name }}</p>
                                        <p class="mt-1 text-gray-500">Quantity: {{ $returnRequest->quantity }}</p>
                                        @if ($returnRequest->replacement_size)
                                            <p class="mt-1 text-blue-700">Replacement size: {{ $returnRequest->replacement_size }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <p class="font-medium text-gray-900">{{ ucfirst($returnRequest->type) }}</p>
                                        <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $returnRequest->status === 'approved' ? 'bg-green-100 text-green-800' : ($returnRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($returnRequest->status) }}
                                        </span>
                                    </td>
                                    <td class="max-w-xs px-6 py-4 text-sm text-gray-700">
                                        <p class="line-clamp-3">{{ $returnRequest->reason }}</p>
                                        @if ($returnRequest->admin_note)
                                            <p class="mt-2 text-xs text-gray-500">Note: {{ $returnRequest->admin_note }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($returnRequest->status === 'pending')
                                            <form action="{{ route('admin.returns.update', $returnRequest) }}" method="POST" class="space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                <input name="admin_note" type="text" maxlength="1000" placeholder="Optional note"
                                                    class="block w-full min-w-48 rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                                <div class="flex gap-2">
                                                    <button name="status" value="approved" type="submit" class="rounded-md bg-green-700 px-3 py-2 text-xs font-semibold text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Approve {{ $returnRequest->type === 'exchange' ? 'Exchange' : 'Return' }}</button>
                                                    <button name="status" value="rejected" type="submit" class="rounded-md bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Reject</button>
                                                </div>
                                            </form>
                                        @elseif ($returnRequest->type === 'exchange' && $returnRequest->status === 'replacement_selected')
                                            <form action="{{ route('admin.returns.receive', $returnRequest) }}" method="POST" class="space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                <input name="admin_note" type="text" maxlength="1000" placeholder="Inspection note" class="block w-full min-w-48 rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                                <button type="submit" class="rounded-md bg-blue-700 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-800">Mark received & complete</button>
                                            </form>
                                        @else
                                            <span class="text-sm text-gray-500">Reviewed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No return or exchange requests.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6">{{ $returnRequests->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
