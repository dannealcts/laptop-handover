<x-app-layout>
    @include('components.staff-topbar')

    <div class="min-h-screen bg-gray-50">
        <main class="w-full p-6">
            <div class="bg-white border border-gray-200 p-8 rounded-xl shadow-md max-w-3xl mx-auto">

                <!-- Title -->
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    Return Laptop
                </h2>

                <!-- Alert: Error -->
                @if(session('error'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-300 text-yellow-800 px-4 py-3 rounded shadow-sm">
                        <strong>Notice:</strong> {{ session('error') }}
                    </div>
                @endif

                <!-- Alert: Success -->
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- If no laptops assigned -->
                @if ($assignedLaptops->isEmpty())
                    <p class="text-red-600">You currently have no assigned laptops to return.</p>
                @else
                    <!-- Form -->
                    <form method="POST" action="{{ route('staff.return.store') }}" class="space-y-5">
                        @csrf

                        <!-- Select Laptop -->
                        <div>
                            <label for="laptop_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Select Laptop to Return
                            </label>
                            <select name="laptop_id" id="laptop_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                <option value="" disabled selected>Choose a laptop</option>
                                @foreach ($assignedLaptops as $laptop)
                                    <option value="{{ $laptop->id }}">
                                        {{ $laptop->asset_tag }} - {{ $laptop->brand }} {{ $laptop->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                                Reason for Return
                            </label>
                            <textarea name="reason" id="reason" rows="4" required
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
                        </div>

                        <!-- Submit -->
                        <div class="pt-2">
                            <button type="submit"
                                    class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold text-sm px-5 py-2.5 rounded-md shadow-sm transition">
                                Submit
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</x-app-layout>
