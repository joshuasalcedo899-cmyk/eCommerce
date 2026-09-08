<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Add Category
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <form
                    action="{{ route('admin.categories.store') }}"
                    method="POST"
                >
                    @csrf

                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >

                        @error('name')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

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
                            rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mt-6 flex gap-3">

                        <button
                            type="submit"
                            class="rounded-md bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700"
                        >
                            Create Category
                        </button>

                        <a
                            href="{{ route('admin.categories.index') }}"
                            class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300"
                        >
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>