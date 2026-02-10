<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Parent-Portail</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo_obd.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_obd.jpg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation mobile-first -->
        <nav class="bg-green-700 shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('parent.dashboard') }}" class="flex items-center">
                            <img src="{{ asset('images/logo_obd.jpg') }}" alt="OBD" class="h-10 w-10 rounded-full">
                            <span class="ml-2 text-white font-bold text-lg hidden sm:block">Portail Parent</span>
                        </a>
                    </div>

                    <!-- Menu desktop -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="{{ route('parent.dashboard') }}" class="text-white hover:bg-green-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('parent.dashboard') ? 'bg-green-800' : '' }}">
                            Accueil
                        </a>
                        <a href="{{ route('parent.enfants') }}" class="text-white hover:bg-green-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('parent.enfants*') ? 'bg-green-800' : '' }}">
                            Mes Enfants
                        </a>
                        <a href="{{ route('parent.calendrier') }}" class="text-white hover:bg-green-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('parent.calendrier') ? 'bg-green-800' : '' }}">
                            Calendrier
                        </a>
                        <a href="{{ route('parent.profil') }}" class="text-white hover:bg-green-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('parent.profil') ? 'bg-green-800' : '' }}">
                            Mon Profil
                        </a>
                    </div>

                    <!-- User menu -->
                    <div class="flex items-center">
                        <span class="text-white text-sm mr-3 hidden sm:block">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-white hover:bg-green-600 p-2 rounded-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Navigation mobile (bottom) -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
            <div class="flex justify-around py-2">
                <a href="{{ route('parent.dashboard') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('parent.dashboard') ? 'text-green-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-xs">Accueil</span>
                </a>
                <a href="{{ route('parent.enfants') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('parent.enfants*') ? 'text-green-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-xs">Enfants</span>
                </a>
                <a href="{{ route('parent.calendrier') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('parent.calendrier') ? 'text-green-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs">Calendrier</span>
                </a>
                <a href="{{ route('parent.profil') }}" class="flex flex-col items-center px-3 py-1 {{ request()->routeIs('parent.profil') ? 'text-green-600' : 'text-gray-500' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-xs">Profil</span>
                </a>
            </div>
        </nav>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Contenu principal -->
        <main class="pb-20 md:pb-6">
            @yield('content')
        </main>
    </div>
</body>
</html>
