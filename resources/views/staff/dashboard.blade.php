<x-app-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto">
            <div class="bg-white shadow-md rounded-2xl px-8 py-10">

                <!-- Header with Greeting + Assigned Laptop + Bell Notification -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div class="flex flex-col">
                        <h2 class="text-3xl font-bold text-gray-800 mb-1 flex flex-wrap items-center gap-2">
                            Hi {{ Auth::user()->name }}

                            @if ($assignedLaptop)
                                <span class="bg-gray-100 border border-gray-300 text-sm font-medium px-3 py-1 rounded-full text-gray-700">
                                    {{ $assignedLaptop->asset_tag }} - {{ $assignedLaptop->brand }} {{ $assignedLaptop->model }}
                                </span>
                            @endif

                            @if ($eligibleLaptop)
                                <div class="relative">
                                    <i data-lucide="bell" class="w-6 h-6 text-yellow-500"></i>
                                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center animate-pulse">!</span>
                                </div>
                            @endif
                        </h2>

                        @if ($assignedLaptop && isset($timeLeftReadable))
                            <p class="text-sm text-yellow-600 font-medium mt-1">
                                {{ $timeLeftReadable }} left to be eligible for upgrade
                            </p>
                        @endif

                        <p class="text-gray-600">Welcome back! Use the options below to manage your laptop requests.</p>
                    </div>
                </div>

                <!-- Flash Message -->
                @if (session('success'))
                    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Action Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                    <!-- Make a Request -->
                    <a href="{{ route('staff.make-request.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-6 px-4 rounded-xl text-center shadow-md hover:shadow-xl transition transform hover:-translate-y-1">
                        <div class="flex flex-col items-center">
                            <i data-lucide="plus" class="w-6 h-6 mb-2"></i>
                            Make Request
                        </div>
                    </a>

                    <!-- View My Requests -->
                    <a href="{{ route('staff.request-history') }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-6 px-4 rounded-xl text-center shadow-md hover:shadow-xl transition transform hover:-translate-y-1">
                        <div class="flex flex-col items-center">
                            <i data-lucide="list-checks" class="w-6 h-6 mb-2"></i>
                            Request History
                        </div>
                    </a>

                    <!-- Return Laptop -->
                    <a href="{{ route('staff.return-laptop.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-6 px-4 rounded-xl text-center shadow-md hover:shadow-xl transition transform hover:-translate-y-1">
                        <div class="flex flex-col items-center">
                            <i data-lucide="undo" class="w-6 h-6 mb-2"></i>
                            Return Request
                        </div>
                    </a>
                </div>

                <!-- Upgrade Notification Box -->
                @if (isset($eligibleLaptop))
                    <div class="p-6 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-900 rounded-xl shadow-sm">
                        <h3 class="text-lg font-bold mb-2">You're Eligible for a Laptop Upgrade</h3>
                        <p class="mb-2 leading-relaxed">
                            Your current assigned laptop 
                            <strong>({{ $eligibleLaptop->asset_tag }} - {{ $eligibleLaptop->brand }} {{ $eligibleLaptop->model }})</strong> 
                            was purchased on 
                            <strong>{{ \Carbon\Carbon::parse($eligibleLaptop->purchase_date)->format('d M Y') }}</strong>.
                        </p>
                        <p class="mb-4">You are eligible for a laptop upgrade under the 5-year replacement policy.</p>
                        <a href="{{ route('staff.make-request.create') }}"
                           class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded shadow">
                            Make Upgrade Request
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</x-app-layout>
