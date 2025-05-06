<x-app-layout>
    <div class="flex min-h-screen">
    
    {{-- SIDEBAR --}}
    @php $currentRoute = Route::currentRouteName(); @endphp
    @include('components.staff-sidebar')

        <!-- Return Laptop Form -->
        <main class="w-full p-6">
            <div class="bg-white p-6 rounded shadow-md">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <span class="ml-2">Return Laptop</span>
                </h2>

                <!-- Error Message -->
                @if(session('error'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded shadow-sm">
                        <strong>Notice:</strong> {{ session('error') }}
                    </div>
                @endif

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- No Assigned Laptop -->
                @if ($assignedLaptops->isEmpty())
                    <p class="text-red-600">You currently have no assigned laptops to return.</p>
                @else
                    <!-- Return Form -->
                    <form method="POST" action="{{ route('staff.return-laptop.store') }}">
                        @csrf

                        <!-- Laptop Selection -->
                        <div class="mb-4">
                            <label for="laptop_id" class="block font-medium mb-1">Select Laptop to Return</label>
                            <select name="laptop_id" id="laptop_id" class="w-full border rounded p-2" required>
                                <option value="" disabled selected>Choose a laptop</option>
                                @foreach ($assignedLaptops as $laptop)
                                    <option value="{{ $laptop->id }}">
                                        {{ $laptop->asset_tag }} - {{ $laptop->brand }} {{ $laptop->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reason for Return -->
                        <div class="mb-4">
                            <label for="reason" class="block font-medium mb-1">Reason for Return</label>
                            <textarea name="reason" id="reason" class="w-full border rounded p-2" rows="4" required></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                            Submit
                        </button>
                    </form>
                @endif
            </div>
        </main>
    </div>
</x-app-layout>
