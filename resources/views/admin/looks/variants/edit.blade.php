<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Edit Outfit Combination</h2></x-slot>
    <div class="py-12"><div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8"><div class="rounded-lg bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.looks.variants.update', [$look, $variant]) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('admin.looks.variants.form')</form>
    </div></div></div>
</x-app-layout>
