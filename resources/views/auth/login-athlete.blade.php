<x-guest-layout>
    <div class="text-center mb-4">
        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <h2 class="text-lg font-bold text-gray-800">Espace Athlète</h2>
        <p class="text-xs text-gray-500">Connexion réservée aux athlètes du centre</p>
    </div>

    <x-auth-session-status class="mb-2" :status="session('status')" />

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.athlete.submit') }}" class="space-y-4">
        @csrf

        <div>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-green-500 focus:border-green-500"
                placeholder="Votre email">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <div>
            <div class="relative" x-data="{ show: false }">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-1 focus:ring-green-500 focus:border-green-500 pr-10"
                    placeholder="Mot de passe">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <label for="remember_me" class="inline-flex items-center cursor-pointer">
            <input id="remember_me" type="checkbox" name="remember" class="w-3 h-3 rounded border-gray-300 text-green-600">
            <span class="ml-2 text-xs text-gray-600">Se souvenir de moi</span>
        </label>

        <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
            Se connecter
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('login.select') }}" class="text-xs text-gray-500 hover:text-green-600">
            ← Retour à la sélection
        </a>
    </div>
</x-guest-layout>
