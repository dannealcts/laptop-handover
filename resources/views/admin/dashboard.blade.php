<x-app-layout>
    <div class="flex min-h-screen">
        
    @php $currentRoute = Route::currentRouteName(); @endphp
    
    @include('components.admin-sidebar')

        <!-- Main Dashboard Content -->
        <main class="flex-1 p-8 mx-8 my-6">
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
                <div x-data="{
                        activities: {{ Js::from($recentActivities) }}
                    }" 
                    class="w-full"
                >
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
            </div>
        </main>
    </div>
</x-app-layout>