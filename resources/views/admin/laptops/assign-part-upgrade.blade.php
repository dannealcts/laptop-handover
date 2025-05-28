<x-app-layout>
    <div class="flex min-h-screen">

    @php $currentRoute = Route::currentRouteName(); @endphp

    @include('components.admin-sidebar')

        {{-- Main Content: Assign Part/Upgrade --}}
        <main class="flex-1 p-8 bg-white shadow-md rounded-lg mx-8 my-6">
            <h2 class="text-2xl font-bold mb-4">Assign Part / Upgrade</h2>

            {{-- Request Details --}}
            <div class="mb-6 space-y-1">
                <p><strong>Staff:</strong> {{ $request->user->name }}</p>
                <p><strong>Type:</strong> {{ ucfirst($request->type) }}</p>
                <p><strong>Requested Part:</strong>
                    @if ($request->type === 'replacement')
                        {{ $request->replacement_part === 'Others' ? $request->other_replacement : $request->replacement_part }}
                    @elseif ($request->type === 'upgrade')
                        {{ $request->upgrade_type }}
                    @endif
                </p>
                <p><strong>Justification:</strong> {{ $request->justification ?? $request->other_justification ?? '-' }}</p>
            </div>

            @if($request->assignedLaptop)
                <div class="bg-green-100 border border-green-300 text-green-800 p-4 rounded mb-4 shadow-sm">
                    <h2 class="text-lg font-semibold mb-2">Assigned Laptop Information</h2>
                    <ul class="list-disc pl-5">
                        <li><strong>Brand:</strong> {{ $request->assignedLaptop->brand }}</li>
                        <li><strong>Model:</strong> {{ $request->assignedLaptop->model }}</li>
                        <li><strong>Serial Number:</strong> {{ $request->assignedLaptop->serial_number }}</li>
                        <li><strong>Asset Tag:</strong> {{ $request->assignedLaptop->asset_tag }}</li>
                    </ul>
                </div>
            @endif

            {{-- Assignment Form --}}
            <form method="POST" action="{{ route('admin.assign.part.store', $request->id) }}">
                @csrf

                {{-- Assigned Part/Upgrade --}}
                <div class="mb-4">
                    <label for="assigned_part" class="block font-medium mb-1">
                        Assigned Part / Upgrade <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="assigned_part" name="assigned_part" value="{{ old('assigned_part') }}"
                        class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                {{-- Quantity --}}
                <div class="mb-4">
                    <label for="quantity" class="block font-medium mb-1">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1"
                        class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded shadow">
                    Submit
                </button>
            </form>
        </main>
    </div>
</x-app-layout>
