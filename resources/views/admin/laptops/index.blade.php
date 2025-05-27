<x-app-layout>
    <div class="flex min-h-screen">
        @php $currentRoute = Route::currentRouteName(); @endphp

        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50 mx-8 my-6 rounded-lg" x-data="{ loading: false }">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Laptop Inventory Management</h2>

            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <a href="{{ route('admin.laptops.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
                    + Add New Laptop
                </a>

                <form action="{{ route('admin.laptops.index') }}" method="GET" class="flex flex-wrap items-center gap-2" @submit="loading = true">
                    <input type="text" name="search" placeholder="Search laptops..." value="{{ request('search') }}" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2" />

                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-sm">
                        Search
                    </button>
                </form>
            </div>

            <!-- Loading Spinner -->
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
                            <th class="px-4 py-2">Tag No</th>
                            <th class="px-4 py-2">Brand</th>
                            <th class="px-4 py-2">Model</th>
                            <th class="px-4 py-2">Serial No</th>
                            <th class="px-4 py-2">Specs</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Purchased Date</th>
                            <th class="px-4 py-2">Upgrade Eligible</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($laptops as $index => $laptop)
                        <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} border-b hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-4 py-2">{{ $laptop->asset_tag }}</td>
                            <td class="px-4 py-2">{{ $laptop->brand }}</td>
                            <td class="px-4 py-2">{{ $laptop->model }}</td>
                            <td class="px-4 py-2">{{ $laptop->serial_number }}</td>
                            <td class="px-4 py-2">{{ $laptop->specs }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $badgeClass = [
                                        'available'   => 'bg-green-100 text-green-800',
                                        'assigned'    => 'bg-yellow-100 text-yellow-900',
                                        'maintenance' => 'bg-orange-100 text-orange-800',
                                    ][$laptop->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $badgeClass }}">
                                    {{ ucfirst($laptop->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                {{ $laptop->purchase_date ? \Carbon\Carbon::parse($laptop->purchase_date)->format('Y-m-d') : '-' }}
                            </td>
                            <td>
                                @if ($laptop->isEligibleForUpgrade())
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold shadow-sm">Yes</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold shadow-sm">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.laptops.edit', $laptop->id) }}"
                                       class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">Edit</a>

                                    <form action="{{ route('admin.laptops.destroy', $laptop->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this laptop?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading" class="mt-4">
                {{ $laptops->links() }}
            </div>
        </main>
    </div>
</x-app-layout>
