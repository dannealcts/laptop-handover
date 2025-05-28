<x-app-layout>
    <div class="flex min-h-screen">
        @php $currentRoute = Route::currentRouteName(); @endphp

        @include('components.admin-sidebar')

        <!-- Main Dashboard Content -->
        <main class="flex-1 p-8 mx-8 my-6">

@if (session('success'))
    <div class="mb-6 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-6 px-4 py-3 bg-red-100 border border-red-300 text-red-800 rounded shadow-sm">
        {{ session('error') }}
    </div>
@endif


            <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Overview</h1>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="p-6 rounded-lg shadow text-center bg-white">
                    <div class="text-4xl font-bold text-blue-600">{{ $totalLaptops }}</div>
                    <div class="text-gray-600 mt-2">Total Laptops</div>
                </div>
                <div class="p-6 rounded-lg shadow text-center bg-white">
                    <div class="text-4xl font-bold text-yellow-600">{{ $pendingRequests }}</div>
                    <div class="text-gray-600 mt-2">Pending Requests</div>
                </div>
                <div class="p-6 rounded-lg shadow text-center bg-white">
                    <div class="text-4xl font-bold text-green-600">{{ $assignedDevices }}</div>
                    <div class="text-gray-600 mt-2">Assigned Devices</div>
                </div>
                <div class="p-6 rounded-lg shadow text-center bg-white">
                    <div class="text-4xl font-bold text-orange-500">{{ $maintenanceDevices }}</div>
                    <div class="text-gray-600 mt-2">Under Maintenance</div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div x-data="{ activities: {{ Js::from($recentActivities) }} }" class="w-full">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Activities</h3>

                <div class="shadow-md rounded p-6 bg-white">
                    <template x-if="!activities.length">
                        <p class="text-gray-500 italic">No recent activities found.</p>
                    </template>

                    <template x-for="(activity, index) in activities" :key="index">
                        <div class="border-b border-gray-200 py-2 text-gray-700">
                            • <span x-text="activity.message"></span>
                            <span class="text-sm text-gray-500 float-right" x-text="activity.time"></span>
                        </div>
                    </template>

                    <div class="flex justify-center mt-4">
                        <a href="{{ url('/admin/activities') }}" class="text-blue-600 text-sm hover:underline">
                            Show Full Activity History
                        </a>
                    </div>
                </div>
            </div>

            <!-- Eligible for Upgrade Section -->
            @if ($eligibleLaptops->count())
                <div class="w-full mt-10">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Laptops Eligible for Upgrade</h3>

                    <div class="shadow-md rounded bg-white p-6">
                        <table class="w-full text-sm text-left border rounded">
                            <thead class="bg-red-50 text-red-700 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 border-b">Tag No</th>
                                    <th class="px-4 py-3 border-b">Purchase Date</th>
                                    <th class="px-4 py-3 border-b">Assigned User</th>
                                    <th class="px-4 py-3 border-b text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($eligibleLaptops as $laptop)
                                    <tr class="hover:bg-red-50 border-b">
                                        <td class="px-4 py-2 font-semibold">{{ $laptop->asset_tag }}</td>
                                        <td class="px-4 py-2">
                                            {{ \Carbon\Carbon::parse($laptop->purchase_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{
                                                optional(
                                                    $laptop->requests
                                                        ->whereIn('status', ['approved', 'assigned', 'completed'])
                                                        ->sortByDesc('created_at')
                                                        ->first()
                                                )?->user->name ?? '-'
                                            }}
                                        </td>
                                        @php
    $latestRequest = $laptop->requests
        ->whereIn('status', ['approved', 'assigned', 'completed'])
        ->sortByDesc('created_at')
        ->first();
@endphp

<td class="px-4 py-2 text-center">
    @if ($latestRequest && $latestRequest->user)
        <form method="POST" action="{{ route('admin.notify-upgrade', $latestRequest->user->id) }}">
            @csrf
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm shadow">
                Notify
            </button>
        </form>
    @else
        <span class="text-gray-400 italic text-sm">No assigned user</span>
    @endif
</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>
</x-app-layout>
