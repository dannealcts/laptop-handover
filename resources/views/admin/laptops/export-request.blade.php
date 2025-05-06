<x-app-layout>
    <div class="flex min-h-screen" x-data="{ loading: false }">
        
    @php $currentRoute = Route::currentRouteName(); @endphp

    @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-white shadow-md rounded-lg mx-8 my-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Export Staff Laptop Request</h2>

            <!-- Search Form -->
            <form method="POST" action="{{ route('admin.export-request.search') }}" class="mb-6 flex items-center gap-4" @submit="loading = true">
                @csrf
                <input type="text" name="keyword" placeholder="Search by name or email..." value="{{ old('keyword') }}"
                    class="w-1/3 rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Search
                </button>
            </form>

            <!-- Spinner Centered -->
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
            @if(isset($staff))
                <div class="mb-6 bg-white p-4 rounded shadow">
                    <h3 class="text-lg font-semibold mb-2">Staff: {{ $staff->name }} ({{ $staff->email }})</h3>

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

                    <!-- Admin Remark -->
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
            @endif
        </main>
    </div>
</x-app-layout>
