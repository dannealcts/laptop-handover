<x-app-layout>
    <div class="flex min-h-screen" x-data="{ loading: false }">
        @php $currentRoute = Route::currentRouteName(); @endphp

        @include('components.admin-sidebar')

        @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .select2-container--default .select2-selection--multiple {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                min-height: 2.5rem;
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
                border: 1px solid #d1d5db;
                border-radius: 0.375rem;
                background-color: #fff;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }

            .select2-selection__choice {
                background-color: #e5e7eb;
                color: #1f2937;
                font-size: 0.875rem;
                padding: 4px 10px;
                border-radius: 0.375rem;
                font-weight: 500;
                margin: 3px 4px 3px 0;
            }

            .select2-container {
                width: 100% !important;
            }
        </style>
        @endpush

        <main class="flex-1 p-8 bg-gray-50 shadow-inner rounded-lg mx-8 my-6">
            <h2 class="text-2xl font-bold mb-8 text-gray-800">Export Laptop Requests</h2>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.export.form') }}" class="flex flex-wrap items-end gap-4 mb-8" @submit="loading = true">
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="text-sm font-medium text-gray-700 mb-1">Select Users</label>
                    <select id="staff_ids" name="staff_ids[]" class="w-full rounded-md shadow-sm" multiple required>
                        <option value="all" {{ in_array('all', request('staff_ids', [])) ? 'selected' : '' }}>Show All Users</option>
                        @foreach ($staffList as $user)
                            <option value="{{ $user->id }}" {{ in_array($user->id, request('staff_ids', [])) ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded shadow-sm">
                        Search Requests
                    </button>
                </div>
            </form>

            @if(isset($requests) && count($requests) > 0)
            <form method="POST" action="{{ route('admin.export.selected') }}">
                @csrf

                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @foreach(request('staff_ids', []) as $id)
                    <input type="hidden" name="staff_ids[]" value="{{ $id }}">
                @endforeach

                <div class="bg-white shadow border rounded-lg overflow-x-auto mb-6">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Select</th>
                                <th class="px-4 py-3 text-left">Request ID</th>
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-left">Laptop/Part</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800">
                            @foreach($requests as $req)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} border-b hover:bg-blue-50 transition">
                                <td class="px-4 py-2">
                                    <input type="checkbox" name="selected_requests[]" value="{{ $req->id }}">
                                </td>
                                <td class="px-4 py-2 font-medium">{{ $req->id }}</td>
                                <td class="px-4 py-2">{{ $req->user->name }}</td>
                                <td class="px-4 py-2">
                                    @if($req->type === 'new' && $req->laptop)
                                        {{ $req->laptop->brand }} {{ $req->laptop->model }}
                                    @elseif(in_array($req->type, ['replacement', 'upgrade']))
                                        {{ $req->assigned_part ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2 capitalize">{{ $req->type }}</td>
                                <td class="px-4 py-2">{{ $req->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admin Remark</label>
                    <textarea name="remark" rows="3" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-2 rounded shadow-sm">
                        Export Selected Requests
                    </button>
                    <button formaction="{{ route('admin.export.all') }}" formmethod="POST"
                        class="bg-blue-700 hover:bg-blue-800 text-white font-medium px-6 py-2 rounded shadow-sm">
                        Export All (Grouped)
                    </button>
                </div>
            </form>
            @endif
        </main>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const $staffSelect = $('#staff_ids');

            $staffSelect.select2({
                placeholder: "Select users...",
                width: '100%'
            });

            $staffSelect.on('change', function () {
                const selected = $(this).val();
                if (selected.includes('all')) {
                    $(this).val(['all']).trigger('change');
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
