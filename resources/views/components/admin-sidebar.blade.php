<aside class="w-52 bg-gradient-to-b from-blue-100 to-blue-50 shadow-lg min-h-screen transition-all duration-200">
    <div class="p-6">
        <nav class="space-y-2">
            @php $currentRoute = Route::currentRouteName(); @endphp

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-2 px-4 py-2 rounded {{ $currentRoute === 'admin.dashboard' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-100' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>

            <!-- Laptop Inventory -->
            <a href="{{ route('admin.laptops.index') }}"
               class="flex items-center gap-2 px-4 py-2 rounded {{ $currentRoute === 'admin.laptops.index' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-100' }}">
                <i data-lucide="laptop" class="w-5 h-5"></i>
                <span>Laptop Inventory</span>
            </a>

            <!-- Requests -->
            <a href="{{ route('admin.requests.index') }}"
               class="flex items-center gap-2 px-4 py-2 rounded {{ $currentRoute === 'admin.requests.index' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-100' }}">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span>Requests</span>
            </a>

            <!-- Return Requests -->
            <a href="{{ route('admin.return.index') }}"
               class="flex items-center gap-2 px-4 py-2 rounded {{ $currentRoute === 'admin.return.index' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-100' }}">
                <i data-lucide="undo" class="w-5 h-5"></i>
                <span>Return Requests</span>
            </a>

            <!-- History -->
            <a href="{{ route('admin.history') }}"
               class="flex items-center gap-2 px-4 py-2 rounded {{ $currentRoute === 'admin.history' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-100' }}">
                <i data-lucide="history" class="w-5 h-5"></i>
                <span>Histories</span>
            </a>

            <!-- Export -->
            <a href="{{ route('admin.export.form') }}"
               class="flex items-center gap-2 px-4 py-2 rounded {{ $currentRoute === 'admin.export.form' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-blue-100' }}">
                <i data-lucide="download" class="w-5 h-5"></i>
                <span>Export Request</span>
            </a>
        </nav>
    </div>
</aside>
