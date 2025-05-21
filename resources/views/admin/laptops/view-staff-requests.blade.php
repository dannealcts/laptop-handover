<x-app-layout>
    <div class="flex min-h-screen" x-data="{ loading: false }">
        @php $currentRoute = Route::currentRouteName(); @endphp

        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50 mx-8 my-6 rounded-lg">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">All Staff Requests</h2>

            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4 shadow-sm">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 text-red-800 p-3 rounded mb-4 shadow-sm">{{ session('error') }}</div>
            @endif

            <!-- Filter -->
            <form method="GET" action="{{ route('admin.view-staff-requests') }}" class="mb-4 flex flex-wrap items-center gap-3" @submit="loading = true">
                <label for="status" class="text-sm text-gray-700 font-medium">Filter Status:</label>
                <select name="status" id="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">Filter</button>
            </form>

            <!-- Spinner -->
            <div x-show="loading" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-50 backdrop-blur-sm z-50">
                <svg class="animate-spin h-12 w-12" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
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

            <!-- Table -->
            <div x-show="!loading" class="bg-white shadow-md rounded-lg overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-2">Staff</th>
                            <th class="px-4 py-2">Request Type</th>
                            <th class="px-4 py-2">Part</th>
                            <th class="px-4 py-2">Justification</th>
                            <th class="px-4 py-2">Submitted At</th>
                            <th class="px-4 py-2">Form</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $index => $request)
                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} border-b hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-4 py-2">{{ $request->user->name }}</td>
                                <td class="px-4 py-2 capitalize">{{ $request->type }}</td>
                                <td class="px-4 py-2">
                                    @if ($request->type === 'replacement')
                                        {{ strtolower($request->replacement_part) === 'others' ? $request->other_replacement ?? 'Others' : $request->replacement_part }}
                                    @elseif ($request->type === 'upgrade')
                                        {{ $request->upgrade_type ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $request->justification }}</td>
                                <td class="px-4 py-2">{{ $request->created_at->format('d M Y, H:i A') }}</td>
                                <td class="px-4 py-2">
                                    @if ($request->signed_form)
                                        <a href="{{ asset('storage/' . $request->signed_form) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline text-sm">
                                            <i data-lucide="file-text" class="w-4 h-4 mr-1"></i> View
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">No file</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @php
                                        $badgeClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $badgeClasses[$request->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if ($request->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.requests.reject', $request->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Reject</button>
                                            </form>
                                        </div>
                                    @elseif ($request->status === 'approved')
                                        @if ($request->type === 'new' || ($request->type === 'replacement' && strtolower($request->replacement_part) === 'laptop'))
                                            <a href="{{ route('admin.assign-form', $request->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">Assign</a>
                                        @else
                                            <a href="{{ route('admin.requests.assign-part-upgrade', $request->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">Assign Part/Upgrade</a>
                                        @endif
                                    @elseif ($request->status === 'completed')
                                        <span class="text-green-700 italic">Completed</span>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading" class="mt-4">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        </main>
    </div>
</x-app-layout>
