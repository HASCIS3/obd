@section('title', 'Modifier suivi scolaire')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('suivis-scolaires.show', $suiviScolaire) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier le suivi scolaire</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $suiviScolaire->athlete->nom_complet }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('suivis-scolaires.update', $suiviScolaire) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <x-form-group label="Etablissement" name="etablissement">
                    <x-input name="etablissement" :value="old('etablissement', $suiviScolaire->etablissement)" />
                </x-form-group>

                <div class="grid grid-cols-2 gap-4">
                    <x-form-group label="Classe" name="classe">
                        <x-input name="classe" :value="old('classe', $suiviScolaire->classe)" />
                    </x-form-group>

                    <x-form-group label="Annee scolaire" name="annee_scolaire">
                        <x-input name="annee_scolaire" :value="old('annee_scolaire', $suiviScolaire->annee_scolaire)" />
                    </x-form-group>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-form-group label="Moyenne generale" name="moyenne_generale">
                        <x-input type="number" name="moyenne_generale" :value="old('moyenne_generale', $suiviScolaire->moyenne_generale)" min="0" max="20" step="0.01" />
                    </x-form-group>

                    <x-form-group label="Rang" name="rang">
                        <x-input type="number" name="rang" :value="old('rang', $suiviScolaire->rang)" min="1" />
                    </x-form-group>
                </div>

                <x-form-group label="Observations" name="observations">
                    <x-textarea name="observations" :value="old('observations', $suiviScolaire->observations)" rows="3" />
                </x-form-group>

                <x-form-group label="Bulletin (PDF ou image)" name="bulletin">
                    <input 
                        type="file" 
                        name="bulletin" 
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                    >
                    @if($suiviScolaire->bulletin_path)
                        <p class="mt-1 text-xs text-gray-500">Bulletin actuel: {{ basename($suiviScolaire->bulletin_path) }}</p>
                    @endif
                </x-form-group>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('suivis-scolaires.show', $suiviScolaire) }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Mettre a jour</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
