<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HVBS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="relative min-h-screen bg-dots-darker bg-center bg-gray-100 selection:bg-red-500 selection:text-white">

        @if (Route::has('login'))
            <div class="fixed top-0 right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>                
                @endauth
            </div>
        @endif

        <div class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                
                <img src="images/logo-hokben.png" alt="Logo Perusahaan" class="w-48 h-auto mx-auto mb-6"> 
                
                <h1 class="text-4xl font-bold text-gray-800">
                    Hokben Vehicle Booking System
                </h1>
                <p class="text-lg text-gray-600 mt-2">
                    PT. Eka Boga Inti
                </p>
                <div class="mt-8">
                    <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700">
                        Mulai Booking
                    </a>
                </div>
            </div>
        </div>
        </div>
</body>
</html>