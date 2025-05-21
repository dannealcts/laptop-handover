<x-app-layout>
    <div class="flex min-h-screen" x-data="{ loading: false }">
        @php $currentRoute = Route::currentRouteName(); @endphp

        @include('components.admin-sidebar')

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-50 shadow-inner rounded-lg mx-8 my-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Export Requests</h2>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.export.form') }}" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4" @submit="loading = true">
                <div>
                    <label class="block mb-1 text-sm font-medium">Start Date:</label>
                    <input type="date" name="start_date" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">End Date:</label>
                    <input type="date" name="end_date" class="w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Select Users:</label>
                    <select id="staff_ids" name="staff_ids[]" multiple class="w-full border-gray-300 rounded-md shadow-sm">
                        @foreach ($staffList as $user)
                            <option value="{{ $user->id }}" title="{{ $user->email }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block mb-1 text-sm font-medium">Export Mode:</label>
                    <select name="export_mode" class="w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="all_filtered">All filtered (grouped)</option>
                        <option value="selected_ids">Only selected request IDs</option>
                    </select>
                </div>

                <div class="md:col-span-3 text-right">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 shadow-sm">
                        Filter Requests
                    </button>
                </div>
            </form>

            @if(isset($requests) && count($requests) > 0)
            <form method="POST" action="{{ route('admin.export.selected') }}">
                @csrf

                <!-- If export all is enabled -->
                @if(request('export_mode') === 'all_filtered')
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    @foreach(request('staff_ids', []) as $id)
                        <input type="hidden" name="staff_ids[]" value="{{ $id }}">
                    @endforeach
                @endif

                <div class="bg-white shadow-md rounded-lg overflow-x-auto mb-6">
                    <table class="w-full text-sm table-auto">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="px-3 py-2">Select</th>
                                <th class="px-3 py-2">Request ID</th>
                                <th class="px-3 py-2">User</th>
                                <th class="px-3 py-2">Laptop/Part</th>
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} border-b hover:bg-blue-50 transition-colors duration-150">
                                <td class="px-3 py-2">
                                    <input type="checkbox" name="selected_requests[]" value="{{ $req->id }}">
                                </td>
                                <td class="px-3 py-2 font-medium text-gray-800">{{ $req->id }}</td>
                                <td class="px-3 py-2">{{ $req->user->name }}</td>
                                <td class="px-3 py-2">
                                    @if($req->type === 'new' && $req->laptop)
                                        {{ $req->laptop->brand }} {{ $req->laptop->model }}
                                    @elseif(in_array($req->type, ['replacement', 'upgrade']))
                                        {{ $req->assigned_part ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-3 py-2 capitalize">{{ $req->type }}</td>
                                <td class="px-3 py-2">{{ $req->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 font-medium">Admin Remark:</label>
                    <textarea name="remark" rows="3" class="w-full border border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded shadow-sm">
                        Export Selected Requests
                    </button>

                    @if(request('export_mode') === 'all_filtered')
                        <button formaction="{{ route('admin.export.all') }}" formmethod="POST"
                            class="bg-purple-700 hover:bg-purple-800 text-white font-medium px-5 py-2 rounded shadow-sm">
                            Export All (Grouped)
                        </button>
                    @endif
                </div>
            </form>
            @endif

            <!-- Spinner -->
            <div x-show="loading" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-50 backdrop-blur-sm z-50">
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
                    transition: border-color 0.2s, box-shadow 0.2s;
                }
                .select2-container--default .select2-selection--multiple:hover,
                .select2-container--default .select2-selection--multiple:focus-within {
                    border-color: #2563eb;
                    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.3);
                }
                .select2-selection__choice {
                    max-width: 100%;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    display: inline-block;
                    vertical-align: middle;
                }
                .select2-container--default .select2-selection__choice {
                    background-color: #e5e7eb;
                    border: none;
                    color: #1f2937;
                    font-size: 0.875rem;
                    padding: 4px 10px;
                    border-radius: 0.375rem;
                    font-weight: 500;
                    margin: 3px 4px 3px 0;
                }
                .select2-container--default .select2-selection--multiple .select2-search__field {
                    font-size: 0.875rem;
                    font-weight: 400;
                    padding: 0;
                    margin: 0;
                    min-width: 120px;
                    border: none !important;
                    outline: none !important;
                    background-color: transparent;
                    line-height: 1.25rem;
                    flex-grow: 1;
                }
                .select2-container {
                    width: 100% !important;
                    z-index: 9999 !important;
                }
            </style>
            @endpush

            @push('scripts')
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    $('#staff_ids').select2({
                        placeholder: "Select users...",
                        width: '100%'
                    });
                });
            </script>
            @endpush
        </main>
    </div>
</x-app-layout>
