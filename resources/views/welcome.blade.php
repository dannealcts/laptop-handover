<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptop Handover System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white flex items-center justify-center font-sans">
    <div class="bg-blue-50 shadow-lg rounded-2xl w-full max-w-2xl p-10">
        <!-- Header -->
        <div class="flex items-center space-x-4 mb-6">
            <img src="/images/logo.png" alt="Company Logo" class="max-h-12 object-contain">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Celcom Timur Sabah IT Dept</h1>
                <p class="text-sm text-gray-500">Asset Management Portal</p>
            </div>
        </div>

        <!-- Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Laptop Handover System</h2>
        <p class="text-gray-600 mb-8 text-base">Securely manage and track company-issued devices.</p>

        <!-- Auth Buttons -->
        @if (Route::has('login'))
            <div class="flex flex-col space-y-4">
                @auth
                    @php
                        $user = Auth::user();
                        $redirectUrl = $user->role === 'admin' ? route('admin.dashboard') :
                                       ($user->role === 'staff' ? route('staff.dashboard') : route('logout'));
                    @endphp

                    <a href="{{ $redirectUrl }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-md font-medium">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="bg-green-600 hover:bg-green-700 text-white text-center py-3 rounded-md font-medium">
                        Login
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                        class="bg-gray-700 text-white text-center py-3 rounded-md font-medium hover:bg-gray-800">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        @endif

        <!-- Footer -->
        <p class="mt-10 text-xs text-center text-gray-400">&copy; {{ date('Y') }} Celcom Timur Sabah IT Department – v1.0</p>
    </div>
</body>
</html>
