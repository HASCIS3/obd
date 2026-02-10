<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Bienvenue</h2>
        <p class="text-sm text-gray-600 mt-1">Sélectionnez votre espace de connexion</p>
    </div>

    <div class="space-y-3">
        <!-- Espace Administration -->
        <a href="{{ route('login.staff') }}" 
           class="flex items-center p-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white rounded-xl hover:from-primary-700 hover:to-primary-800 transition shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="text-left">
                <div class="font-semibold">Administration & Coachs</div>
                <div class="text-xs text-white/80">Gestion du centre sportif</div>
            </div>
            <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        <!-- Espace Athlète -->
        <a href="{{ route('login.athlete') }}" 
           class="flex items-center p-4 bg-gradient-to-r from-success-600 to-success-700 text-white rounded-xl hover:from-success-700 hover:to-success-800 transition shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="text-left">
                <div class="font-semibold">Espace Athlète</div>
                <div class="text-xs text-white/80">Suivi personnel et performances</div>
            </div>
            <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>

        <!-- Espace Parent -->
        <a href="{{ route('login.parent') }}" 
           class="flex items-center p-4 bg-gradient-to-r from-warning-600 to-warning-700 text-white rounded-xl hover:from-warning-700 hover:to-warning-800 transition shadow-lg">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="text-left">
                <div class="font-semibold">Espace Parent</div>
                <div class="text-xs text-white/80">Suivi de vos enfants</div>
            </div>
            <svg class="w-5 h-5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
            Centre Sportif OBD - Tous droits réservés
        </p>
    </div>
</x-guest-layout>
