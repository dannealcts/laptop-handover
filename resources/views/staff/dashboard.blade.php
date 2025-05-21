<x-app-layout>
    <div class="py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto">
            <div class="bg-white shadow-md rounded-xl p-10">
                <!-- Greeting -->
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Hi {{ Auth::user()->name }}</h2>
                <p class="text-gray-600 mb-6">Welcome! Use the options below to make a request, view your status, or return a laptop.</p>

                <!-- Success Flash Message -->
                @if (session('success'))
                    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Action Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Make a Request -->
                    <a href="{{ route('staff.make-request.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-6 px-4 rounded-xl text-center shadow-md hover:shadow-xl transition transform hover:-translate-y-1">
                        <div class="flex flex-col items-center">
                            <i data-lucide="plus" class="w-6 h-6 mb-2"></i>
                            Make a Request
                        </div>
                    </a>

                    <!-- View My Requests -->
                    <a href="{{ route('staff.my-requests') }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-6 px-4 rounded-xl text-center shadow-md hover:shadow-xl transition transform hover:-translate-y-1">
                        <div class="flex flex-col items-center">
                            <i data-lucide="list-checks" class="w-6 h-6 mb-2"></i>
                            View My Requests
                        </div>
                    </a>

                    <!-- Return Laptop -->
                    <a href="{{ route('staff.return-laptop.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold py-6 px-4 rounded-xl text-center shadow-md hover:shadow-xl transition transform hover:-translate-y-1">
                        <div class="flex flex-col items-center">
                            <i data-lucide="undo" class="w-6 h-6 mb-2"></i>
                            Return a Laptop
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lucide Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</x-app-layout>
