<x-app-layout>
    <div class="py-6 px-6 bg-gray-100 min-h-screen">
        <div class="max-w-3xl mx-auto bg-white shadow rounded-md p-8">

            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Assign Laptop</h2>

            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 space-y-1">
                <p><strong class="text-gray-700">Requested by:</strong> {{ $request->user->name }}</p>
                <p><strong class="text-gray-700">Request Type:</strong> {{ ucfirst($request->type) }}</p>
                <p><strong class="text-gray-700">Justification:</strong> {{ $request->justification ?? $request->other_justification }}</p>
            </div>

            <form method="POST" action="{{ route('admin.assign-laptop', $request->id) }}">
                @csrf

                <!-- Laptop Selection -->
                <div class="mb-6">
                    <label for="laptop_id" class="block text-sm font-medium text-gray-700 mb-1">Select Available Laptop</label>
                    <select name="laptop_id" id="laptop_id" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Choose Laptop</option>
                        @foreach ($availableLaptops as $laptop)
                            <option value="{{ $laptop->id }}">
                                {{ $laptop->asset_tag }} - {{ $laptop->brand }} {{ $laptop->model }}
                            </option>
                        @endforeach
                    </select>
                    @error('laptop_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Accessories Selection -->
                <div class="mb-6">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Accessories</h3>

                    <div id="accessories-container" class="space-y-3">
                        <div class="flex gap-4 items-center">
                            <select name="accessories[]" class="rounded border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200">
                                <option value="">-- Select Accessory --</option>
                                <option value="Laptop Bag">Laptop Bag</option>
                                <option value="Mouse">Mouse</option>
                                <option value="Keyboard">Keyboard</option>
                                <option value="Docking Station">Docking Station</option>
                                <option value="Charger">Charger</option>
                                <option value="Headset">Headset</option>
                            </select>
                            <input type="number" name="accessories_quantity[]" value="1" min="1"
                                class="w-20 rounded border-gray-300 text-sm px-2 py-1 focus:ring focus:ring-blue-200">
                            <button type="button" onclick="addAccessoryField()" class="text-blue-600 hover:underline text-sm">+ Add</button>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-4">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded shadow">
                        Assign Laptop & Accessories
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- JS for Add/Remove Accessories -->
    <script>
        function addAccessoryField() {
            const container = document.getElementById('accessories-container');
            const html = `
                <div class="flex gap-4 items-center">
                    <select name="accessories[]" class="rounded border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200">
                        <option value="">-- Select Accessory --</option>
                        <option value="Laptop Bag">Laptop Bag</option>
                        <option value="Mouse">Mouse</option>
                        <option value="Keyboard">Keyboard</option>
                        <option value="Docking Station">Docking Station</option>
                        <option value="Charger">Charger</option>
                        <option value="Headset">Headset</option>
                    </select>
                    <input type="number" name="accessories_quantity[]" value="1" min="1"
                        class="w-20 rounded border-gray-300 text-sm px-2 py-1 focus:ring focus:ring-blue-200">
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:underline text-sm">Remove</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
</x-app-layout>
<x-app-layout>
    <div class="py-6 px-6 bg-gray-100 min-h-screen">
        <div class="max-w-3xl mx-auto bg-white shadow rounded-md p-8">

            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Assign Laptop</h2>

            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 space-y-1">
                <p><strong class="text-gray-700">Requested by:</strong> {{ $request->user->name }}</p>
                <p><strong class="text-gray-700">Request Type:</strong> {{ ucfirst($request->type) }}</p>
                <p><strong class="text-gray-700">Justification:</strong> {{ $request->justification ?? $request->other_justification }}</p>
            </div>

            <!-- ✅ Route name updated here -->
            <form method="POST" action="{{ route('admin.assign.laptop', $request->id) }}">
                @csrf

                <!-- Laptop Selection -->
                <div class="mb-6">
                    <label for="laptop_id" class="block text-sm font-medium text-gray-700 mb-1">Select Available Laptop</label>
                    <select name="laptop_id" id="laptop_id" required
                        class="w-full border border-gray-300 rounded-md px-4 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Choose Laptop</option>
                        @foreach ($availableLaptops as $laptop)
                            <option value="{{ $laptop->id }}">
                                {{ $laptop->asset_tag }} - {{ $laptop->brand }} {{ $laptop->model }}
                            </option>
                        @endforeach
                    </select>
                    @error('laptop_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Accessories Selection -->
                <div class="mb-6">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Accessories</h3>

                    <div id="accessories-container" class="space-y-3">
                        <div class="flex gap-4 items-center">
                            <select name="accessories[]" class="rounded border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200">
                                <option value="">-- Select Accessory --</option>
                                <option value="Laptop Bag">Laptop Bag</option>
                                <option value="Mouse">Mouse</option>
                                <option value="Keyboard">Keyboard</option>
                                <option value="Docking Station">Docking Station</option>
                                <option value="Charger">Charger</option>
                                <option value="Headset">Headset</option>
                            </select>
                            <input type="number" name="accessories_quantity[]" value="1" min="1"
                                class="w-20 rounded border-gray-300 text-sm px-2 py-1 focus:ring focus:ring-blue-200">
                            <button type="button" onclick="addAccessoryField()" class="text-blue-600 hover:underline text-sm">+ Add</button>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-4">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded shadow">
                        Assign Laptop & Accessories
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- JS for Add/Remove Accessories -->
    <script>
        function addAccessoryField() {
            const container = document.getElementById('accessories-container');
            const html = `
                <div class="flex gap-4 items-center">
                    <select name="accessories[]" class="rounded border-gray-300 px-3 py-2 text-sm focus:ring focus:ring-blue-200">
                        <option value="">-- Select Accessory --</option>
                        <option value="Laptop Bag">Laptop Bag</option>
                        <option value="Mouse">Mouse</option>
                        <option value="Keyboard">Keyboard</option>
                        <option value="Docking Station">Docking Station</option>
                        <option value="Charger">Charger</option>
                        <option value="Headset">Headset</option>
                    </select>
                    <input type="number" name="accessories_quantity[]" value="1" min="1"
                        class="w-20 rounded border-gray-300 text-sm px-2 py-1 focus:ring focus:ring-blue-200">
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:underline text-sm">Remove</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
</x-app-layout>
