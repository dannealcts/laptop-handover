<!-- resources/views/components/staff-sidebar.blade.php -->
<aside class="w-60 bg-gradient-to-b from-green-100 to-green-50 shadow-md min-h-screen transition-all duration-200">
    <div class="p-6">
        <nav class="space-y-2 text-sm font-medium">
            <a href="{{ route('staff.dashboard') }}"
               class="flex items-center gap-2 w-full px-4 py-2 rounded {{ $currentRoute === 'staff.dashboard' ? 'bg-green-600 text-white' : 'text-gray-800 hover:bg-green-200' }}">
                <i data-lucide="home" class="w-5 h-5"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('staff.request-laptop.create') }}"
               class="flex items-center gap-2 w-full px-4 py-2 rounded {{ $currentRoute === 'staff.request-laptop.create' ? 'bg-green-600 text-white' : 'text-gray-800 hover:bg-green-200' }}">
                <i data-lucide="laptop" class="w-5 h-5"></i> <span>Make a Request</span>
            </a>
            <a href="{{ route('staff.my-requests') }}"
               class="flex items-center gap-2 w-full px-4 py-2 rounded {{ $currentRoute === 'staff.my-requests' ? 'bg-green-600 text-white' : 'text-gray-800 hover:bg-green-200' }}">
                <i data-lucide="file-text" class="w-5 h-5"></i> <span>My Requests</span>
            </a>
            <a href="{{ route('staff.return-laptop.create') }}"
               class="flex items-center gap-2 w-full px-4 py-2 rounded {{ $currentRoute === 'staff.return-laptop.create' ? 'bg-green-600 text-white' : 'text-gray-800 hover:bg-green-200' }}">
                <i data-lucide="undo" class="w-5 h-5"></i> <span>Return Request</span>
            </a>
        </nav>
    </div>
</aside>
