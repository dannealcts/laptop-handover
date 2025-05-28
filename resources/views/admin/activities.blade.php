<x-app-layout>
    <div class="flex min-h-screen">

    @php $currentRoute = Route::currentRouteName(); @endphp

    @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 mx-8 my-6" x-data="{ loading: false }">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Activity History</h2>

            <!-- Filters Form -->
            <form 
                method="GET" 
                action="{{ route('admin.activities') }}" 
                class="flex flex-wrap items-end gap-4 bg-white p-4 rounded-lg shadow"
                @submit="loading = true"
            >
                <div class="flex flex-col">
                    <label for="from_date" class="text-gray-600 text-sm mb-1">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}"
                        class="border-gray-300 rounded-lg p-2 text-sm focus:ring focus:ring-blue-200">
                </div>

                <div class="flex flex-col">
                    <label for="to_date" class="text-gray-600 text-sm mb-1">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}"
                        class="border-gray-300 rounded-lg p-2 text-sm focus:ring focus:ring-blue-200">
                </div>

                <div class="flex flex-col">
                    <label for="keyword" class="text-gray-600 text-sm mb-1">Keyword</label>
                    <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}"
                        placeholder="Search activity..." class="border-gray-300 rounded-lg p-2 text-sm focus:ring focus:ring-blue-200">
                </div>

                <div class="flex gap-2 mt-6">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                        Filter
                    </button>
                    <a href="{{ route('admin.activities') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400 transition">
                        Reset
                    </a>
                </div>
            </form>

            <!-- Activities Table -->
            <div x-show="!loading" class="overflow-x-auto bg-white shadow rounded-lg mt-8">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-gray-700 font-semibold">Date</th>
                            <th class="px-6 py-3 text-gray-700 font-semibold">Activity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-4">{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">{{ $activity->message }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                    No activities found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div x-show="!loading" class="mt-6">
                {{ $activities->withQueryString()->links('pagination::tailwind') }}
            </div>
        </main>
    </div>
</x-app-layout>
