<x-app-layout>
    <div class="flex min-h-screen">
        
    {{-- SIDEBAR --}}
    @php $currentRoute = Route::currentRouteName(); @endphp
    @include('components.staff-sidebar')

        {{-- MAIN CONTENT --}}
        <main class="w-full p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Laptop Request Details</h2>

            {{-- Error Message --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 text-red-800 p-4 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($assignedLaptops->count())
            <div class="bg-blue-50 border border-blue-300 text-blue-900 p-4 rounded mb-4 shadow-sm">
                <h2 class="text-base font-semibold mb-2">💻 Laptops Currently Assigned to You</h2>
                <ul class="list-disc pl-5">
                    @foreach($assignedLaptops as $laptop)
                        <li class="mb-2">
                            <strong>Asset Tag:</strong> {{ $laptop->asset_tag }}<br>
                            <strong>Brand:</strong> {{ $laptop->brand }}<br>
                            <strong>Model:</strong> {{ $laptop->model }}<br>
                            <strong>Serial No:</strong> {{ $laptop->serial_number }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-300 p-4 rounded mb-4 text-sm text-yellow-800 shadow-sm">
                ⚠️ No assigned laptop found for your profile.
            </div>
        @endif

            <form action="{{ route('staff.request-laptop.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Request Type --}}
                <div class="mb-4">
                    <label for="type" class="block font-medium">Request Type</label>
                    <select name="type" id="type" class="w-full border-gray-300 rounded-md">
                        <option value="">Select Request Type</option>
                        <option value="new">New Laptop</option>
                        <option value="replacement">Replacement</option>
                        <option value="upgrade">Upgrade</option>
                    </select>
                </div>

                <div id="target-laptop-section" class="mt-4 mb-4 hidden">
                <label for="target_laptop_id" class="block font-medium">Select Laptop</label>
                <select name="target_laptop_id" id="target_laptop_id"
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-200">
                    <option value="">Choose Assigned Laptop</option>
                    @foreach($assignedLaptops as $laptop)
                        <option value="{{ $laptop->id }}">
                            {{ $laptop->asset_tag }} - {{ $laptop->brand }} {{ $laptop->model }}
                        </option>
                    @endforeach
                </select>
            </div>

                {{-- Replacement Fields --}}
                <div class="mb-4" id="replacement-fields" style="display:none;">
                    <label class="block font-medium">Replacement Part</label>
                    <select name="replacement_part" class="w-full border-gray-300 rounded-md">
                        <option value="">Select Part</option>
                        <option value="battery">Battery</option>
                        <option value="keyboard">Keyboard</option>
                        <option value="motherboard">Motherboard</option>
                        <option value="screen">Screen</option>
                        <option value="charger">Charger</option>
                        <option value="laptop">Laptop</option>
                        <option value="others">Others</option>
                    </select>
                    <input type="text" name="other_replacement" placeholder="If others, please specify"
                           class="mt-2 w-full border-gray-300 rounded-md">
                </div>

                {{-- Upgrade Fields --}}
                <div class="mb-4" id="upgrade-fields" style="display:none;">
                    <label class="block font-medium">Upgrade Type</label>
                    <select name="upgrade_type" class="w-full border-gray-300 rounded-md">
                        <option value="">Select Upgrade</option>
                        <option value="memory">Memory</option>
                        <option value="processor">Processor</option>
                        <option value="hard_disk">Hard Disk</option>
                    </select>
                </div>

                {{-- Justification --}}
                <div class="mb-4">
                    <label for="justification" class="block font-medium">Justification</label>
                    <select name="justification" class="w-full border-gray-300 rounded-md">
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
                           class="mt-2 w-full border-gray-300 rounded-md">
                </div>

                {{-- Upload Signed Request Form --}}
                <div class="mb-6">
                    <label for="signed_form" class="block font-medium">Upload Signed Request Form (PDF/Image)</label>
                    <input type="file" name="signed_form" accept=".pdf,.png,.jpg,.jpeg"
                           class="w-full border-gray-300 rounded-md mt-1" required>
                    @error('signed_form')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                    Submit Request
                </button>
            </form>
        </main>
    </div>

    {{-- Dynamic Field Toggle Script --}}
    <script>
        document.getElementById('type').addEventListener('change', function () {
            const type = this.value;
            document.getElementById('replacement-fields').style.display = type === 'replacement' ? 'block' : 'none';
            document.getElementById('upgrade-fields').style.display = type === 'upgrade' ? 'block' : 'none';
        });

        const requestType = document.querySelector('select[name="type"]');
        const laptopSection = document.getElementById('target-laptop-section');

        requestType.addEventListener('change', function () {
            if (this.value === 'replacement' || this.value === 'upgrade') {
                laptopSection.classList.remove('hidden');
            } else {
                laptopSection.classList.add('hidden');
                document.getElementById('target_laptop_id').value = '';
            }
        });
    </script>
</x-app-layout>