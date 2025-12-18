@section('title', 'Saisir les presences')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('presences.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Saisir les presences</h2>
                <p class="mt-1 text-sm text-gray-500">Enregistrer les presences des athletes</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Selection de la discipline et date -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('presences.create') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-group label="Date" name="date">
                    <x-input type="date" name="date" :value="$date" />
                </x-form-group>

                <x-form-group label="Discipline" name="discipline">
                    <x-select 
                        name="discipline" 
                        :options="$disciplines" 
                        :selected="$disciplineId"
                        placeholder="Selectionner une discipline"
                        valueKey="id"
                        labelKey="nom"
                    />
                </x-form-group>

                <div class="flex items-end">
                    <x-button type="submit" variant="primary" class="w-full">
                        Charger les athletes
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Formulaire de presences -->
        @if($disciplineId && $athletes->count() > 0)
            <x-card title="Liste des athletes">
                <form action="{{ route('presences.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <input type="hidden" name="discipline_id" value="{{ $disciplineId }}">

                    <div class="space-y-4">
                        @foreach($athletes as $index => $athlete)
                            <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                                <div class="flex items-center flex-1">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-primary-600 font-medium text-sm">
                                            {{ substr($athlete->prenom, 0, 1) }}{{ substr($athlete->nom, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $athlete->nom_complet }}</p>
                                        <p class="text-xs text-gray-500">{{ $athlete->telephone ?: 'Pas de telephone' }}</p>
                                    </div>
                                </div>

                                <input type="hidden" name="presences[{{ $index }}][athlete_id]" value="{{ $athlete->id }}">

                                <div class="flex items-center gap-6">
                                    <div class="flex items-center gap-4">
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="presences[{{ $index }}][present]" 
                                                value="1"
                                                {{ ($existingPresences[$athlete->id] ?? null) === true ? 'checked' : '' }}
                                                {{ !isset($existingPresences[$athlete->id]) ? 'checked' : '' }}
                                                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Present</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input 
                                                type="radio" 
                                                name="presences[{{ $index }}][present]" 
                                                value="0"
                                                {{ ($existingPresences[$athlete->id] ?? null) === false ? 'checked' : '' }}
                                                class="h-4 w-4 text-danger-600 focus:ring-danger-500 border-gray-300"
                                            >
                                            <span class="ml-2 text-sm text-gray-700">Absent</span>
                                        </label>
                                    </div>

                                    <input 
                                        type="text" 
                                        name="presences[{{ $index }}][remarque]" 
                                        placeholder="Remarque..."
                                        class="w-40 text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                    >
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                        <x-button href="{{ route('presences.index') }}" variant="ghost">Annuler</x-button>
                        <x-button type="submit" variant="primary">Enregistrer les presences</x-button>
                    </div>
                </form>
            </x-card>
        @elseif($disciplineId)
            <x-card>
                <x-empty-state 
                    title="Aucun athlete" 
                    description="Aucun athlete inscrit a cette discipline."
                />
            </x-card>
        @else
            <x-card>
                <x-empty-state 
                    title="Selectionnez une discipline" 
                    description="Choisissez une discipline et une date pour afficher les athletes."
                />
            </x-card>
        @endif
    </div>
</x-app-layout>
