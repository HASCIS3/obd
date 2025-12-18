@section('title', 'Presences')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Presences</h2>
                <p class="mt-1 text-sm text-gray-500">Suivi des presences des athletes</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('presences.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Saisir presences
                </x-button>
                <x-button href="{{ route('presences.rapport-mensuel') }}" variant="outline">
                    Rapport mensuel
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('presences.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-input type="date" name="date" :value="request('date', now()->format('Y-m-d'))" />
                </div>
                <div>
                    <x-select 
                        name="discipline" 
                        :options="$disciplines" 
                        :selected="request('discipline')"
                        placeholder="Toutes les disciplines"
                        valueKey="id"
                        labelKey="nom"
                    />
                </div>
                <div>
                    <x-select 
                        name="present" 
                        :options="[['id' => '1', 'name' => 'Presents'], ['id' => '0', 'name' => 'Absents']]" 
                        :selected="request('present')"
                        placeholder="Tous les statuts"
                    />
                </div>
                <div class="flex gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">Filtrer</x-button>
                    <x-button href="{{ route('presences.index') }}" variant="ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Liste des presences -->
        <x-card :padding="false">
            @if($presences->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Date</x-th>
                            <x-th>Athlete</x-th>
                            <x-th>Discipline</x-th>
                            <x-th>Coach</x-th>
                            <x-th>Statut</x-th>
                            <x-th>Remarque</x-th>
                        </tr>
                    </x-slot>

                    @foreach($presences as $presence)
                        <tr class="hover:bg-gray-50">
                            <x-td>{{ $presence->date->format('d/m/Y') }}</x-td>
                            <x-td>
                                <a href="{{ route('athletes.show', $presence->athlete) }}" class="text-primary-600 hover:text-primary-800">
                                    {{ $presence->athlete->nom_complet }}
                                </a>
                            </x-td>
                            <x-td>{{ $presence->discipline->nom }}</x-td>
                            <x-td>{{ $presence->coach?->user->name ?? '-' }}</x-td>
                            <x-td>
                                @if($presence->present)
                                    <x-badge color="success">Present</x-badge>
                                @else
                                    <x-badge color="danger">Absent</x-badge>
                                @endif
                            </x-td>
                            <x-td class="max-w-xs truncate">{{ $presence->remarque ?: '-' }}</x-td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $presences->links() }}
                </div>
            @else
                <x-empty-state 
                    title="Aucune presence" 
                    description="Aucune presence enregistree pour cette date."
                    :action="route('presences.create')"
                    actionText="Saisir des presences"
                />
            @endif
        </x-card>
    </div>
</x-app-layout>
