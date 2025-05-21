<x-app-layout>
    @include('components.staff-topbar')

    <div class="max-w-3xl mx-auto space-y-6">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center gap-2 mt-4">
            Request Form
        </h2>

            <!-- Wrapper for both sections -->
            <div class="max-w-3xl mx-auto space-y-6">

                <!-- Error Display -->
                @if ($errors->any())
                    <div class="bg-red-100 text-red-800 p-4 rounded border border-red-300 shadow-sm">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Assigned Laptops -->
                @if($assignedLaptops->count())
                    <div class="bg-blue-50 border border-blue-300 text-blue-900 p-5 rounded-xl shadow-sm">
                        <h3 class="text-base font-semibold mb-3 flex items-center gap-2">
                            Laptops Currently Assigned to You
                        </h3>
                        <ul class="list-disc pl-5 space-y-2 text-sm">
                            @foreach($assignedLaptops as $laptop)
                                <li>
                                    <strong>Asset Tag:</strong> {{ $laptop->asset_tag }}<br>
                                    <strong>Brand:</strong> {{ $laptop->brand }}<br>
                                    <strong>Model:</strong> {{ $laptop->model }}<br>
                                    <strong>Serial No:</strong> {{ $laptop->serial_number }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-300 p-4 rounded text-sm text-yellow-800 shadow-sm flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-500"></i>
                        No assigned laptops found.
                    </div>
                @endif

                <!-- Request Form -->
                <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200">
                    <form action="{{ route('staff.request-laptop.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Request Type -->
                        <div class="mb-4">
                            <label for="type" class="block font-medium text-gray-700">Request Type</label>
                            <select name="type" id="type" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">Select Request Type</option>
                                <option value="new">New Laptop</option>
                                <option value="replacement">Replacement</option>
                                <option value="upgrade">Upgrade</option>
                            </select>
                        </div>

                        <!-- Assigned Laptop Selector -->
                        <div id="target-laptop-section" class="mb-4 hidden">
                            <label for="target_laptop_id" class="block font-medium text-gray-700">Select Laptop</label>
                            <select name="target_laptop_id" id="target_laptop_id"
                                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">Choose Assigned Laptop</option>
                                @foreach($assignedLaptops as $laptop)
                                    <option value="{{ $laptop->id }}">
                                        {{ $laptop->asset_tag }} - {{ $laptop->brand }} {{ $laptop->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Replacement Fields -->
                        <div class="mb-4 hidden" id="replacement-fields">
                            <label class="block font-medium text-gray-700">Replacement Part</label>
                            <select name="replacement_part" class="w-full mt-1 border-gray-300 rounded-md">
                                <option value="">Select Part</option>
                                <option value="battery">Battery</option>
                                <option value="keyboard">Keyboard</option>
                                <option value="motherboard">Motherboard</option>
                                <option value="screen">Screen</option>
                                <option value="charger">Charger</option>
                                <option value="laptop">Laptop</option>
                                <option value="others">Others</option>
                            </select>
                            <input type="text" name="other_replacement"
                                   placeholder="If others, please specify"
                                   class="w-full mt-2 border-gray-300 rounded-md">
                        </div>

                        <!-- Upgrade Fields -->
                        <div class="mb-4 hidden" id="upgrade-fields">
                            <label class="block font-medium text-gray-700">Upgrade Type</label>
                            <select name="upgrade_type" class="w-full mt-1 border-gray-300 rounded-md">
                                <option value="">Select Upgrade</option>
                                <option value="memory">Memory</option>
                                <option value="processor">Processor</option>
                                <option value="hard_disk">Hard Disk</option>
                            </select>
                        </div>

                        <!-- Justification -->
                        <div class="mb-4">
                            <label for="justification" class="block font-medium text-gray-700">Justification</label>
                            <select name="justification" class="w-full mt-1 border-gray-300 rounded-md">
                                <option value="">Select Reason</option>
                                <option value="I am a new Celcom Timur Sabah Staff and need company laptop">New staff</option>
                                <option value="Laptop is broken and beyond repair">Broken laptop</option>
                                <option value="Laptop cannot support current business requirement">Cannot support business</option>
                                <option value="Laptop performance has degraded">Performance degraded</option>
                                <option value="Laptop has been stolen and replacement request granted by HR">Stolen & approved</option>
                                <option value="Laptop has already reached 5 years of usage">Over 5 years old</option>
                                <option value="others">Others</option>
                            </select>
                            <input type="text" name="other_justification" placeholder="If others, please specify"
                                   class="w-full mt-2 border-gray-300 rounded-md">
                        </div>

                        <!-- Upload File -->
                        <div class="mb-6">
                            <label for="signed_form" class="block font-medium text-gray-700">
                                Upload Signed Request Form (PDF/Image)
                            </label>
                            <input type="file" name="signed_form" accept=".pdf,.png,.jpg,.jpeg"
                                   class="w-full mt-1 border-gray-300 rounded-md" required>
                            @error('signed_form')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-md shadow-md transition flex justify-center items-center gap-2">
                            Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Lucide Icons Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // Toggle visibility for request type-specific fields
        const requestType = document.querySelector('select[name="type"]');
        const laptopSection = document.getElementById('target-laptop-section');
        const replacementSection = document.getElementById('replacement-fields');
        const upgradeSection = document.getElementById('upgrade-fields');

        requestType.addEventListener('change', function () {
            const type = this.value;

            // Show/hide laptop section
            laptopSection.classList.toggle('hidden', !(type === 'replacement' || type === 'upgrade'));
            document.getElementById('target_laptop_id').value = '';

            // Show/hide part-specific sections
            replacementSection.classList.toggle('hidden', type !== 'replacement');
            upgradeSection.classList.toggle('hidden', type !== 'upgrade');
        });
    </script>
</x-app-layout>
