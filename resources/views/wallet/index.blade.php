<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">E-Wallet</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4 text-red-800">{{ $errors->first() }}</div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Available balance</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">₱{{ number_format($walletBalance, 2) }}</p>

                <form action="{{ route('wallet.top-up') }}" method="POST" class="mt-8 border-t border-gray-100 pt-6">
                    @csrf
                    <label for="amount" class="block text-sm font-medium text-gray-700">Top-up amount</label>
                    <div class="mt-1 flex gap-3">
                        <input id="amount" name="amount" type="number" min="0.01" max="100000" step="0.01"
                            value="{{ old('amount') }}" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                        <button type="submit" class="rounded-md bg-gray-800 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-700">
                            Add funds
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">This development wallet adds funds directly to your account.</p>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
