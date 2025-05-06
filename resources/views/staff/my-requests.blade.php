<x-app-layout>
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @php $currentRoute = Route::currentRouteName(); @endphp
        @include('components.staff-sidebar')

        <main class="w-full p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Request Histories</h2>

            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <!-- Filter -->
            <div class="mb-4">
                <label for="statusFilter" class="block text-sm font-medium text-gray-700">Filter by Status:</label>
                <select id="statusFilter" class="mt-1 block w-64 px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    <option value="all">All</option>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                    <option value="Received">Returned</option>
                </select>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto bg-white shadow rounded">
                <table class="w-full table-auto text-sm border border-gray-200">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-left">Requested Part</th>
                            <th class="px-4 py-2 text-left">Assigned Part</th>
                            <th class="px-4 py-2 text-left">Justification / Reason</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Assigned Laptop</th>
                            <th class="px-4 py-2 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($histories as $history)
                            <tr class="status-row" data-status="{{ $history['status'] }}">
                                <td class="px-4 py-2 capitalize">{{ $history['type'] }}</td>
                                <td class="px-4 py-2">{{ $history['requested_part'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $history['assigned_part'] ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $history['justification'] }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $badgeClasses = [
                                            'Pending'   => 'bg-yellow-200 text-yellow-800',
                                            'Approved'  => 'bg-green-200 text-green-800',
                                            'Rejected'  => 'bg-red-200 text-red-800',
                                            'Completed' => 'bg-blue-200 text-blue-800',
                                            'Received'  => 'bg-purple-200 text-purple-800',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClasses[$history['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $history['status'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    {{ $history['laptop'] ?? '-' }}

                                    {{-- Accessories List --}}
                                    @if (!empty($history['accessories']))
                                        <ul class="text-xs text-gray-500 mt-1 list-disc list-inside">
                                            @foreach ($history['accessories'] as $accessory)
                                                <li>{{ $accessory['accessory_name'] }} (x{{ $accessory['quantity'] }})</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($history['date'])->format('d M Y, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Filter Script -->
    <script>
        document.getElementById('statusFilter').addEventListener('change', function () {
            const selected = this.value;
            document.querySelectorAll('.status-row').forEach(row => {
                const status = row.getAttribute('data-status');
                row.style.display = (selected === 'all' || status === selected) ? '' : 'none';
            });
        });
    </script>
</x-app-layout>
