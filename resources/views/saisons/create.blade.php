@section('title', 'Nouvelle Saison')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouvelle Saison</h2>
                <p class="mt-1 text-sm text-gray-500">Créer une nouvelle saison sportive</p>
            </div>
            <x-button href="{{ route('saisons.index') }}" variant="ghost">
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('saisons.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <x-form-group label="Nom de la saison" name="nom" required>
                        <x-input 
                            type="text" 
                            name="nom" 
                            :value="old('nom', $anneeSuggestion . '-' . ($anneeSuggestion + 1))"
                            placeholder="Ex: 2024-2025"
                            required
                        />
                    </x-form-group>

                    <div class="grid grid-cols-2 gap-4">
                        <x-form-group label="Date de début" name="date_debut" required>
                            <x-input 
                                type="date" 
                                name="date_debut" 
                                :value="old('date_debut', $anneeSuggestion . '-09-01')"
                                required
                            />
                        </x-form-group>

                        <x-form-group label="Date de fin" name="date_fin" required>
                            <x-input 
                                type="date" 
                                name="date_fin" 
                                :value="old('date_fin', ($anneeSuggestion + 1) . '-08-31')"
                                required
                            />
                        </x-form-group>
                    </div>

                    <x-form-group label="Description" name="description">
                        <textarea 
                            name="description" 
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Description optionnelle de la saison..."
                        >{{ old('description') }}</textarea>
                    </x-form-group>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button href="{{ route('saisons.index') }}" variant="ghost">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Créer la saison
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
