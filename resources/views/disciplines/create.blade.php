@section('title', 'Nouvelle discipline')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('disciplines.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouvelle discipline</h2>
                <p class="mt-1 text-sm text-gray-500">Ajouter une nouvelle discipline sportive</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('disciplines.store') }}" method="POST">
                @csrf

                <x-form-group label="Nom de la discipline" name="nom" required>
                    <x-input name="nom" :value="old('nom')" placeholder="Ex: Football, Basketball..." required />
                </x-form-group>

                <x-form-group label="Description" name="description">
                    <x-textarea name="description" :value="old('description')" rows="3" placeholder="Description de la discipline..." />
                </x-form-group>

                <x-form-group label="Tarif mensuel (FCFA)" name="tarif_mensuel" required>
                    <x-input type="number" name="tarif_mensuel" :value="old('tarif_mensuel', 15000)" min="0" step="500" required />
                </x-form-group>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('disciplines.index') }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Enregistrer</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
