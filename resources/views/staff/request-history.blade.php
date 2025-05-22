<x-app-layout>
    @include('components.staff-topbar')

    <div class="min-h-screen bg-gray-50">
        <main class="w-full px-4 sm:px-6 lg:px-8 py-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Page Title -->
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2 mt-4">
                    Request History
                </h2>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded shadow-sm border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filter -->
                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status:</label>
                    <select id="statusFilter"
                            class="w-64 border border-gray-300 rounded-md shadow-sm px-3 py-2">
                        <option value="all">All</option>
                        <option value="Completed">Completed</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Received">Returned</option>
                    </select>
                </div>

                <!-- Request Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($histories as $history)
                        <div class="status-card bg-white p-5 rounded-xl shadow-sm border border-gray-200 relative"
                             data-status="{{ $history['status'] }}">

                            <!-- Status Badge -->
                            @php
                                $badgeClasses = [
                                    'Pending'   => 'bg-yellow-100 text-yellow-800',
                                    'Approved'  => 'bg-green-100 text-green-800',
                                    'Rejected'  => 'bg-red-100 text-red-800',
                                    'Completed' => 'bg-blue-100 text-blue-800',
                                    'Received'  => 'bg-purple-100 text-purple-800',
                                ];
                            @endphp
                            <span class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold {{ $badgeClasses[$history['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $history['status'] }}
                            </span>

                            <!-- Type -->
                            <div class="mb-1 text-sm text-gray-600">
                                <span class="font-semibold text-gray-700">Type:</span> {{ ucfirst($history['type']) }}
                            </div>

                            <!-- Requested Part -->
                            @if (!empty($history['requested_part']) && $history['requested_part'] !== '-')
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold text-gray-700">Requested Part:</span> {{ $history['requested_part'] }}
                                </div>
                            @endif

                            <!-- Assigned Part -->
                            @if (!empty($history['assigned_part']) && $history['assigned_part'] !== '-')
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold text-gray-700">Assigned Part:</span> {{ $history['assigned_part'] }}
                                </div>
                            @endif

                            <!-- Assigned Laptop -->
                            @if (!empty($history['laptop']))
                                <div class="text-sm text-gray-600 mt-1">
                                    <span class="font-semibold text-gray-700">Assigned Laptop:</span>
                                    {{ $history['laptop'] }}
                                </div>
                            @endif

                            <!-- Accessories -->
                            @if (!empty($history['accessories']))
                                <ul class="text-xs text-gray-500 mt-1 list-disc list-inside">
                                    @foreach ($history['accessories'] as $accessory)
                                        <li>{{ $accessory['accessory_name'] }} (x{{ $accessory['quantity'] }})</li>
                                    @endforeach
                                </ul>
                            @endif

                            <!-- Justification -->
                            @if ($history['justification'])
                                <div class="text-xs text-gray-500 mt-3 italic line-clamp-2">
                                    "{{ $history['justification'] }}"
                                </div>
                            @endif

                            <!-- Date -->
                            <div class="text-xs text-gray-400 mt-4">
                                <i data-lucide="clock" class="inline w-4 h-4 mr-1"></i>
                                {{ \Carbon\Carbon::parse($history['date'])->format('d M Y, H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>

    <!-- Lucide & Filter Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // Filter logic
        document.getElementById('statusFilter').addEventListener('change', function () {
            const selected = this.value;
            document.querySelectorAll('.status-card').forEach(card => {
                const status = card.getAttribute('data-status');
                card.style.display = (selected === 'all' || status === selected) ? 'block' : 'none';
            });
        });
    </script>
</x-app-layout>
