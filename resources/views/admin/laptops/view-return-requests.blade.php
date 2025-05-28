<x-app-layout>
    <div class="flex min-h-screen">
        @php $currentRoute = Route::currentRouteName(); @endphp

        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50 shadow-inner mx-8 my-6 rounded-lg">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Pending Return Requests</h2>

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Table -->
            <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="w-full text-sm text-left whitespace-nowrap">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-2">Staff</th>
                            <th class="px-4 py-2">Laptop</th>
                            <th class="px-4 py-2">Reason</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-center w-1/4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returnRequests as $index => $return)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} border-b hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-4 py-2">{{ $return->user->name }}</td>
                                <td class="px-4 py-2">{{ $return->laptop->asset_tag }} - {{ $return->laptop->model }}</td>
                                <td class="px-4 py-2">{{ $return->reason }}</td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full shadow-sm bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center align-middle">
                                    <div class="flex items-center justify-center gap-2">

                                        <!-- File Upload Form -->
                                        <form action="{{ route('admin.return.complete', $return->id) }}" 
                                              enctype="multipart/form-data" 
                                              onsubmit="return validateFileInput(this)" class="inline-flex items-center gap-2">
                                            @csrf
                                            <label class="inline-flex items-center space-x-2 border rounded px-2 py-1 bg-white shadow-sm hover:bg-gray-50 cursor-pointer text-sm">
                                                <input type="file" 
                                                       name="admin_validation_form" 
                                                       class="hidden" 
                                                       onchange="this.nextElementSibling.textContent = this.files[0]?.name || 'Choose File'">
                                                <span class="text-gray-600">Choose File</span>
                                            </label>
                                            <button type="submit" 
                                                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
                                                Submit
                                            </button>
                                        </form>

                                        <!-- Delete Form -->
                                        <form action="{{ route('admin.return.delete', $return->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Delete this return request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- ✅ JS Validation -->
    <script>
        function validateFileInput(form) {
            const fileInput = form.querySelector('input[type="file"]');
            if (!fileInput.files.length) {
                alert('⚠️ Please select a file before submitting.');
                return false;
            }
            return true;
        }
    </script>
</x-app-layout>
