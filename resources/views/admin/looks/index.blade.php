<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Shop the Look</h2>
            <a href="{{ route('admin.looks.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Add Look
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Look</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Products
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($looks as $look)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <img src="{{ asset('storage/' . $look->image_path) }}" alt="{{ $look->title }}"
                                                class="h-16 w-16 rounded-md object-cover">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $look->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $look->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $look->products_count }} outfit items
                                    </td>
                                    <td class="px-6 py-4"><span
                                            class="rounded-full px-2 py-1 text-xs font-medium {{ $look->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ $look->is_active ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm"><a
                                            href="{{ route('admin.looks.edit', $look) }}"
                                            class="mr-3 text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('admin.looks.destroy', $look) }}" method="POST"
                                            class="inline">@csrf @method('DELETE')<button type="submit"
                                                onclick="return confirm('Delete this look?')"
                                                class="text-red-600 hover:text-red-900">Delete</button></form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">No looks created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6">{{ $looks->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>