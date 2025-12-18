@section('title', 'Modifier ' . $discipline->nom)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('disciplines.show', $discipline) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier la discipline</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $discipline->nom }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('disciplines.update', $discipline) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form-group label="Nom de la discipline" name="nom" required>
                    <x-input name="nom" :value="old('nom', $discipline->nom)" required />
                </x-form-group>

                <x-form-group label="Description" name="description">
                    <x-textarea name="description" :value="old('description', $discipline->description)" rows="3" />
                </x-form-group>

                <x-form-group label="Tarif mensuel (FCFA)" name="tarif_mensuel" required>
                    <x-input type="number" name="tarif_mensuel" :value="old('tarif_mensuel', $discipline->tarif_mensuel)" min="0" step="500" required />
                </x-form-group>

                <x-form-group label="Statut" name="actif">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="actif" 
                            value="1"
                            {{ old('actif', $discipline->actif) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                        >
                        <span class="ml-2 text-sm text-gray-700">Discipline active</span>
                    </label>
                </x-form-group>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('disciplines.show', $discipline) }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Mettre a jour</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
