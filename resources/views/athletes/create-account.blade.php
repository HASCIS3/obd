@section('title', 'Creer compte athlete')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('athletes.show', $athlete) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Creer un compte</h2>
                <p class="mt-1 text-sm text-gray-500">Compte de connexion pour {{ $athlete->nom_complet }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form method="POST" action="{{ route('athletes.account.store', $athlete) }}">
                @csrf

                <div class="space-y-4">
                    <x-form-group label="Nom" name="name" required>
                        <x-input name="name" :value="old('name', $athlete->nom_complet)" required />
                    </x-form-group>

                    <x-form-group label="Email" name="email" required>
                        <x-input type="email" name="email" :value="old('email', $athlete->email)" required />
                    </x-form-group>

                    <x-form-group label="Mot de passe" name="password" required>
                        <x-password-input name="password" required />
                    </x-form-group>

                    <x-form-group label="Confirmation" name="password_confirmation" required>
                        <x-password-input name="password_confirmation" required />
                    </x-form-group>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('athletes.show', $athlete) }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Creer</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
