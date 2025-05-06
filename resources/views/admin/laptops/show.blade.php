<!--<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">🔍 Laptop Details</h2>
    </x-slot>

    <div class="p-4">
        <p><strong>Asset Tag:</strong> {{ $laptop->asset_tag }}</p>
        <p><strong>Brand:</strong> {{ $laptop->brand }}</p>
        <p><strong>Model:</strong> {{ $laptop->model }}</p>
        <p><strong>Serial Number:</strong> {{ $laptop->serial_number }}</p>
        <p><strong>Specifications:</strong> {{ $laptop->specs }}</p>
        <p><strong>Status:</strong> {{ $laptop->status }}</p>
        <a href="{{ route('admin.laptops.index') }}" class="text-blue-600 mt-4 inline-block">← Back to Inventory</a>
    </div>
</x-app-layout>
