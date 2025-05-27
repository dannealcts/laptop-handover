<x-app-layout>
    <div class="py-6 px-6 bg-gray-100 min-h-screen">
        <div class="max-w-4xl mx-auto bg-white shadow rounded-md p-8">

            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Laptop</h2>

            <form action="{{ route('admin.laptops.update', $laptop->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Purchased Date --}}
                <div class="form-group">
                    <label for="purchase_date">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date" class="form-control" 
                        value="{{ old('purchase_date', isset($laptop) ? $laptop->purchase_date : '') }}">
                </div>

                {{-- Asset Tag --}}
                <div class="mb-4">
                    <label for="asset_tag" class="block text-sm font-medium text-gray-700">Asset Tag</label>
                    <input type="text" name="asset_tag" id="asset_tag" value="{{ old('asset_tag', $laptop->asset_tag) }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('asset_tag') border-red-500 @enderror">
                    @error('asset_tag')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Brand --}}
                <div class="mb-4">
                    <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $laptop->brand) }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('brand') border-red-500 @enderror">
                    @error('brand')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Model --}}
                <div class="mb-4">
                    <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                    <input type="text" name="model" id="model" value="{{ old('model', $laptop->model) }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('model') border-red-500 @enderror">
                    @error('model')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Serial Number --}}
                <div class="mb-4">
                    <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number', $laptop->serial_number) }}"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('serial_number') border-red-500 @enderror">
                    @error('serial_number')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Specifications --}}
                <div class="mb-4">
                    <label for="specs" class="block text-sm font-medium text-gray-700">Specifications</label>
                    <textarea name="specs" id="specs" rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('specs') border-red-500 @enderror">{{ old('specs', $laptop->specs) }}</textarea>
                    @error('specs')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                        <option value="">Please select</option>
                        <option value="available" {{ old('status', $laptop->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="assigned" {{ old('status', $laptop->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="maintenance" {{ old('status', $laptop->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded shadow">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
