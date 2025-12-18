@section('title', 'Nouveau suivi scolaire')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('suivis-scolaires.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouveau suivi scolaire</h2>
                <p class="mt-1 text-sm text-gray-500">Enregistrer les resultats scolaires d'un athlete</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('suivis-scolaires.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <x-form-group label="Athlete" name="athlete_id" required>
                    <x-select 
                        name="athlete_id" 
                        :options="$athletes" 
                        :selected="old('athlete_id', $athleteId)"
                        placeholder="Selectionner un athlete"
                        valueKey="id"
                        labelKey="nom_complet"
                        required
                    />
                </x-form-group>

                <x-form-group label="Etablissement" name="etablissement">
                    <x-input name="etablissement" :value="old('etablissement')" placeholder="Nom de l'ecole" />
                </x-form-group>

                <div class="grid grid-cols-2 gap-4">
                    <x-form-group label="Classe" name="classe">
                        <x-input name="classe" :value="old('classe')" placeholder="Ex: 6eme, 3eme..." />
                    </x-form-group>

                    <x-form-group label="Annee scolaire" name="annee_scolaire">
                        <x-input name="annee_scolaire" :value="old('annee_scolaire', date('Y') . '-' . (date('Y') + 1))" placeholder="Ex: 2024-2025" />
                    </x-form-group>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-form-group label="Moyenne generale" name="moyenne_generale">
                        <x-input type="number" name="moyenne_generale" :value="old('moyenne_generale')" min="0" max="20" step="0.01" placeholder="/20" />
                    </x-form-group>

                    <x-form-group label="Rang" name="rang">
                        <x-input type="number" name="rang" :value="old('rang')" min="1" placeholder="Position dans la classe" />
                    </x-form-group>
                </div>

                <x-form-group label="Observations" name="observations">
                    <x-textarea name="observations" :value="old('observations')" rows="3" placeholder="Commentaires sur les resultats..." />
                </x-form-group>

                <x-form-group label="Bulletin (PDF ou image)" name="bulletin">
                    <input 
                        type="file" 
                        name="bulletin" 
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                    >
                </x-form-group>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('suivis-scolaires.index') }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Enregistrer</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
