@section('title', 'Modifier performance')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('performances.show', $performance) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier la performance</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $performance->athlete->nom_complet }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('performances.update', $performance) }}" method="POST">
                @csrf
                @method('PUT')

                <x-form-group label="Athlete" name="athlete_id" required>
                    <x-select 
                        name="athlete_id" 
                        :options="$athletes" 
                        :selected="old('athlete_id', $performance->athlete_id)"
                        placeholder="Selectionner un athlete"
                        valueKey="id"
                        labelKey="nom_complet"
                        required
                    />
                </x-form-group>

                <x-form-group label="Discipline" name="discipline_id" required>
                    <x-select 
                        name="discipline_id" 
                        :options="$disciplines" 
                        :selected="old('discipline_id', $performance->discipline_id)"
                        placeholder="Selectionner une discipline"
                        valueKey="id"
                        labelKey="nom"
                        required
                    />
                </x-form-group>

                <x-form-group label="Date d'evaluation" name="date_evaluation" required>
                    <x-input type="date" name="date_evaluation" :value="old('date_evaluation', $performance->date_evaluation->format('Y-m-d'))" required />
                </x-form-group>

                <x-form-group label="Type d'evaluation" name="type_evaluation">
                    <x-input name="type_evaluation" :value="old('type_evaluation', $performance->type_evaluation)" />
                </x-form-group>

                <div class="grid grid-cols-2 gap-4">
                    <x-form-group label="Score" name="score">
                        <x-input type="number" name="score" :value="old('score', $performance->score)" step="0.01" />
                    </x-form-group>

                    <x-form-group label="Unite" name="unite">
                        <x-input name="unite" :value="old('unite', $performance->unite)" />
                    </x-form-group>
                </div>

                <x-form-group label="Competition" name="competition">
                    <x-input name="competition" :value="old('competition', $performance->competition)" />
                </x-form-group>

                <x-form-group label="Classement" name="classement">
                    <x-input type="number" name="classement" :value="old('classement', $performance->classement)" min="1" />
                </x-form-group>

                <x-form-group label="Observations" name="observations">
                    <x-textarea name="observations" :value="old('observations', $performance->observations)" rows="3" />
                </x-form-group>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('performances.show', $performance) }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Mettre a jour</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
