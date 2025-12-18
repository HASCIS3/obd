<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>OBD - Connexion</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('images/logo_obd.jpg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-primary-600 to-primary-800 min-h-screen flex items-center justify-center p-4">
        <!-- Carte carrée 10x10cm (environ 380px) -->
        <div class="bg-white rounded-xl shadow-2xl p-6" style="width: 380px; height: 380px;">
            <div class="h-full flex flex-col justify-between">
                <!-- Logo -->
                <div class="text-center">
                    <img src="{{ asset('images/logo_obd.jpg') }}" alt="OBD" class="w-14 h-14 rounded-full object-cover border-2 border-primary-500 mx-auto">
                    <h1 class="mt-1 text-sm font-bold text-primary-700">OBD</h1>
                </div>
                
                <!-- Formulaire -->
                <div class="flex-1 flex items-center">
                    <div class="w-full">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
