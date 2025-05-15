<x-app-layout>
    <div class="flex min-h-screen" x-data="{ loading: false }">
        
    @php $currentRoute = Route::currentRouteName(); @endphp

    @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-white shadow-md rounded-lg mx-8 my-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Export Requests</h2>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.export.form') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4" @submit="loading = true">
                <div>
                    <label class="block mb-1 text-sm font-medium">Start Date:</label>
                    <input type="date" name="start_date" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">End Date:</label>
                    <input type="date" name="end_date" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Select Users:</label>
                    <select name="staff_ids[]" multiple class="w-full border-gray-300 rounded-md shadow-sm">
                        @foreach ($staffList as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block mb-1 text-sm font-medium">Export Mode:</label>
                    <select name="export_mode" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="all_filtered">All filtered (grouped)</option>
                        <option value="selected_ids">Only selected request IDs</option>
                    </select>
                </div>

                <div class="md:col-span-3 text-right">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">
                        Filter Requests
                    </button>
                </div>
            </form>

            @if(request('export_mode') === 'all_filtered' && isset($requests) && count($requests) > 0)
            <form method="POST" action="{{ route('admin.export.all') }}" class="mb-6">
                @csrf
                <!-- Keep date range -->
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                <!-- Pass staff IDs if selected -->
                @foreach(request('staff_ids', []) as $id)
                    <input type="hidden" name="staff_ids[]" value="{{ $id }}">
                @endforeach

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded">
                    Export All (Grouped)
                </button>
            </form>
            @endif

            <!-- Display Request List with Checkboxes -->
            @if(isset($requests) && count($requests) > 0)
            <form method="POST" action="{{ route('admin.export.selected') }}">
                @csrf

                <table class="w-full text-sm mb-6 border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2">Select</th>
                            <th class="px-3 py-2">Request ID</th>
                            <th class="px-3 py-2">User</th>
                            <th class="px-3 py-2">Laptop/Part</th>
                            <th class="px-3 py-2">Type</th>
                            <th class="px-3 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $req)
                        <tr class="border-b">
                            <td class="px-3 py-2">
                                <input type="checkbox" name="selected_requests[]" value="{{ $req->id }}">
                            </td>
                            <td class="px-3 py-2">{{ $req->id }}</td>
                            <td class="px-3 py-2">{{ $req->user->name }}</td>
                            <td class="px-3 py-2">
                                @if($req->type === 'new' && $req->laptop)
                                    {{ $req->laptop->brand }} {{ $req->laptop->model }}
                                @elseif(in_array($req->type, ['replacement', 'upgrade']))
                                    {{ $req->assigned_part ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2 capitalize">{{ $req->type }}</td>
                            <td class="px-3 py-2">{{ $req->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mb-4">
                    <label class="block mb-2 font-medium">Admin Remark:</label>
                    <textarea name="remark" rows="3" class="w-full border border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded">
                    Export Selected Requests
                </button>
            </form>
            @endif

            <!-- Spinner Loading Effect -->
            <div x-show="loading" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-50 z-50">
                <svg class="animate-spin h-16 w-16" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="35" stroke-width="10" fill="none" stroke="url(#rainbow)" stroke-dasharray="180" stroke-linecap="round"></circle>
                    <defs>
                        <linearGradient id="rainbow" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#ff5f6d" />
                            <stop offset="25%" stop-color="#ffc371" />
                            <stop offset="50%" stop-color="#47cf73" />
                            <stop offset="75%" stop-color="#00c6ff" />
                            <stop offset="100%" stop-color="#845ec2" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            <!-- Staff Request Preview -->
            <!--@if(isset($staff))
                <div class="mb-6 bg-white p-4 rounded shadow">
                    <h3 class="text-lg font-semibold mb-2">Staff: {{ $staff->name }} ({{ $staff->email }})</h3>

                    Inspection

                    <table class="w-full table-auto text-sm mb-4">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-3 py-2 text-left">Item</th>
                                <th class="px-3 py-2 text-left">Type</th>
                                <th class="px-3 py-2 text-left">Qty</th>
                                <th class="px-3 py-2 text-left">Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                                <tr class="border-b">
                                    <td class="px-3 py-2">
                                        @if($req->type === 'new' && $req->laptop)
                                            {{ $req->laptop->brand }} {{ $req->laptop->model }}
                                        @elseif(in_array($req->type, ['replacement', 'upgrade']) && $req->replacement_part !== 'Laptop')
                                            {{ $req->assigned_part ?? 'Not assigned' }}
                                        @else
                                            -
                                        @endif

                                        {{-- Accessories List --}}
                                        @if ($req->accessories && $req->accessories->isNotEmpty())
                                            <ul class="text-xs text-gray-600 mt-1 list-disc list-inside">
                                                @foreach ($req->accessories as $acc)
                                                    <li>{{ $acc->accessory_name }} (x{{ $acc->quantity }})</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 capitalize">
                                        @if($req->type === 'new')
                                            Laptop
                                        @elseif($req->type === 'replacement')
                                            Replacement
                                        @elseif($req->type === 'upgrade')
                                            Upgrade
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $req->assigned_quantity ?? 1 }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($req->type === 'new')
                                            Unit
                                        @elseif(in_array($req->type, ['replacement', 'upgrade']))
                                            Piece
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <form action="{{ route('admin.export-request.generate', $staff->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block mb-2 font-medium">Admin Remark:</label>
                            <textarea name="remark" rows="3" class="w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded">
                            Export to Excel
                        </button>
                    </form>
                </div>
            @endif-->
        </main>
    </div>
</x-app-layout>
