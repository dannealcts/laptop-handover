<x-app-layout>
    <div class="flex min-h-screen">
       
    @php $currentRoute = Route::currentRouteName(); @endphp
        
    @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Histories</h2>

            <!-- Completed Requests -->
            <div class="mb-10">
                <h3 class="text-lg font-semibold text-green-600 flex items-center mb-3">
                    Completed Requests
                </h3>
                <div class="bg-white shadow rounded p-4 overflow-x-auto">
                    <table class="w-full text-sm table-auto">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Staff Name</th>
                                <th class="px-4 py-2 text-left">Request Type</th>
                                <th class="px-4 py-2 text-left">Item / Part</th>
                                <th class="px-4 py-2 text-left">Action Taken</th>
                                <th class="px-4 py-2 text-left">Request Completed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($completedRequests as $request)
                                <tr>
                                    <td class="px-4 py-2 font-semibold text-gray-800">{{ $request->user->name }}</td>
                                    <td class="px-4 py-2 capitalize">{{ $request->type }}</td>
                                    <td class="px-4 py-2">
                                        @if ($request->type === 'replacement')
                                            {{ $request->assigned_part ?? 'Replacement Part' }}
                                        @else
                                            {{ $request->laptop->brand ?? '-' }} {{ $request->laptop->model ?? '-' }}
                                        @endif

                                        {{-- Accessories --}}
                                        @if($request->accessories->isNotEmpty())
                                            <ul class="text-xs text-gray-600 mt-1 list-disc list-inside">
                                                @foreach ($request->accessories as $accessory)
                                                    <li>{{ $accessory->accessory_name }} (x{{ $accessory->quantity }})</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $request->type === 'replacement' ? 'Replacement done' : 'Handover recorded' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ \Carbon\Carbon::parse($request->completed_at)->format('d M Y, h:i A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Return Request History -->
            <div>
                <h3 class="text-lg font-semibold text-blue-600 flex items-center mb-3">
                    Return Request History
                </h3>
                <div class="bg-white shadow rounded p-4 overflow-x-auto">
                    <table class="w-full text-sm table-auto">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Staff Name</th>
                                <th class="px-4 py-2 text-left">Returned Laptop</th>
                                <th class="px-4 py-2 text-left">Return Reason</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Return Date</th>
                                <th class="px-4 py-2 text-left">Form</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($returnHistories as $return)
                                <tr>
                                    <td class="px-4 py-2 font-semibold text-gray-800">{{ $return->user->name }}</td>
                                    <td class="px-4 py-2">{{ $return->laptop->model ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $return->reason }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $statusColors = [
                                                'pending'   => 'bg-yellow-200 text-yellow-900',
                                                'received'  => 'bg-green-200 text-green-800',
                                                'completed' => 'bg-blue-200 text-blue-800',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$return->status] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ ucfirst($return->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $return->received_at ? \Carbon\Carbon::parse($return->received_at)->format('d M Y, h:i A') : '-' }}
                                    </td>
                                    <td class="px-4 py-2">
                                        @if ($return->admin_validation_form)
                                            <a href="{{ asset('storage/' . $return->admin_validation_form) }}"
                                               target="_blank"
                                               class="inline-flex items-center text-blue-600 hover:underline text-sm">
                                                <i data-lucide="file-text" class="w-4 h-4 mr-1"></i> View
                                            </a>
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $returnHistories->appends([
                        'handover_page' => request('handover_page'),
                        'completed_page' => request('completed_page')
                    ])->links('pagination::tailwind') }}
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
