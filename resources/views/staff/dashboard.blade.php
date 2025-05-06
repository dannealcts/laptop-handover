<x-app-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-8 max-w-4xl mx-auto">

            <!-- Page Greeting -->
            <p class="text-lg font-semibold mb-2">Hi {{ Auth::user()->name }}</p>
            <p class="text-gray-600 mb-6">
                Welcome! Use the options below to make a request, view your request status, or return assigned devices.
            </p>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ route('staff.request-laptop.create') }}"
                   class="block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded text-center transition">
                    Make a Request
                </a>

                <a href="{{ route('staff.my-requests') }}"
                   class="block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded text-center transition">
                    View My Requests
                </a>

                <a href="{{ route('staff.return-laptop.create') }}"
                   class="block bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded text-center transition">
                    Return a Laptop
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
