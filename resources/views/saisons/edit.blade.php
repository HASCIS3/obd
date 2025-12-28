@section('title', 'Modifier Saison')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier la Saison</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $saison->nom }}</p>
            </div>
            <x-button href="{{ route('saisons.index') }}" variant="ghost">
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('saisons.update', $saison) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <x-form-group label="Nom de la saison" name="nom" required>
                        <x-input 
                            type="text" 
                            name="nom" 
                            :value="old('nom', $saison->nom)"
                            required
                        />
                    </x-form-group>

                    <div class="grid grid-cols-2 gap-4">
                        <x-form-group label="Date de début" name="date_debut" required>
                            <x-input 
                                type="date" 
                                name="date_debut" 
                                :value="old('date_debut', $saison->date_debut->format('Y-m-d'))"
                                required
                            />
                        </x-form-group>

                        <x-form-group label="Date de fin" name="date_fin" required>
                            <x-input 
                                type="date" 
                                name="date_fin" 
                                :value="old('date_fin', $saison->date_fin->format('Y-m-d'))"
                                required
                            />
                        </x-form-group>
                    </div>

                    <x-form-group label="Description" name="description">
                        <textarea 
                            name="description" 
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                        >{{ old('description', $saison->description) }}</textarea>
                    </x-form-group>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button href="{{ route('saisons.index') }}" variant="ghost">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Enregistrer
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
