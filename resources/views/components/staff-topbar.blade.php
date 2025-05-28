<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center items-center h-14">

            <!-- Nav Links with Box Highlight for Active Tab -->
            <div class="flex items-center space-x-4 text-sm font-medium text-gray-700">
                <a href="{{ route('staff.dashboard') }}"
                   class="px-4 py-2 rounded-md transition
                          {{ request()->routeIs('staff.dashboard') 
                              ? 'bg-green-100 text-green-700 font-semibold shadow-sm' 
                              : 'hover:bg-gray-100' }}">
                    Dashboard
                </a>

                <a href="{{ route('staff.requests.create') }}"
                   class="px-4 py-2 rounded-md transition
                          {{ request()->routeIs('staff.requests.create') 
                              ? 'bg-green-100 text-green-700 font-semibold shadow-sm' 
                              : 'hover:bg-gray-100' }}">
                    Make Request
                </a>

                <a href="{{ route('staff.requests.index') }}"
                   class="px-4 py-2 rounded-md transition
                          {{ request()->routeIs('staff.requests.index') 
                              ? 'bg-green-100 text-green-700 font-semibold shadow-sm' 
                              : 'hover:bg-gray-100' }}">
                    Request History
                </a>

                <a href="{{ route('staff.return.create') }}"
                   class="px-4 py-2 rounded-md transition
                          {{ request()->routeIs('staff.return.create') 
                              ? 'bg-green-100 text-green-700 font-semibold shadow-sm' 
                              : 'hover:bg-gray-100' }}">
                    Return Request
                </a>
            </div>

        </div>
    </div>
</nav>
