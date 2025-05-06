<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laptop Handover System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 flex items-center justify-center min-h-screen">

    {{-- 🖥️ Welcome Card --}}
    <div class="text-center px-6 py-12 bg-blue-100 shadow-md rounded-lg max-w-xl w-full">
        {{-- 🔖 Title & Tagline --}}
        <h1 class="text-3xl font-bold mb-4">💻 Laptop Handover System</h1>
        <p class="mb-6 text-lg">
            Welcome to the internal asset management platform for staff and admin.
        </p>

        {{-- 🔐 Auth Options --}}
        @if (Route::has('login'))
            <div class="space-x-4">
                @auth
                    {{-- Already logged in --}}
                    <a href="{{ url('/dashboard') }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Go to Dashboard
                    </a>
                @else
                    {{-- Login Button --}}
                    <a href="{{ route('login') }}"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        Login
                    </a>

                    {{-- Register Button --}}
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        @endif

        {{-- 🧾 Footer --}}
        <div class="mt-8">
            <p class="text-sm text-gray-600">
                © {{ date('Y') }} Celcom Timur Sabah IT Dept.
            </p>
        </div>
    </div>

</body>
</html>
