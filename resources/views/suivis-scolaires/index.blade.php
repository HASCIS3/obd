@section('title', 'Suivi scolaire')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Suivi scolaire</h2>
                <p class="mt-1 text-sm text-gray-500">Suivi des resultats scolaires des athletes</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('suivis-scolaires.dashboard') }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Dashboard Sport/Etudes
                </x-button>
                <x-button href="{{ route('suivis-scolaires.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouveau suivi
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('suivis-scolaires.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-input type="text" name="search" placeholder="Rechercher athlete..." :value="request('search')" />
                </div>
                <div>
                    <x-select 
                        name="annee_scolaire" 
                        :options="$anneesScolaires->map(fn($a) => ['id' => $a, 'name' => $a])" 
                        :selected="request('annee_scolaire')"
                        placeholder="Toutes les annees"
                    />
                </div>
                <div>
                    <x-select 
                        name="satisfaisant" 
                        :options="[['id' => '1', 'name' => 'Satisfaisant (>=10)'], ['id' => '0', 'name' => 'Insuffisant (<10)']]" 
                        :selected="request('satisfaisant')"
                        placeholder="Tous les resultats"
                    />
                </div>
                <div class="flex gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">Filtrer</x-button>
                    <x-button href="{{ route('suivis-scolaires.index') }}" variant="ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Liste -->
        <x-card :padding="false">
            @if($suivis->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Athlete</x-th>
                            <x-th>Etablissement</x-th>
                            <x-th>Classe</x-th>
                            <x-th>Annee</x-th>
                            <x-th>Moyenne</x-th>
                            <x-th>Rang</x-th>
                            <x-th class="text-right">Actions</x-th>
                        </tr>
                    </x-slot>

                    @foreach($suivis as $suivi)
                        <tr class="hover:bg-gray-50">
                            <x-td>
                                <a href="{{ route('athletes.show', $suivi->athlete) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                                    {{ $suivi->athlete->nom_complet }}
                                </a>
                            </x-td>
                            <x-td>{{ $suivi->etablissement ?: '-' }}</x-td>
                            <x-td>{{ $suivi->classe ?: '-' }}</x-td>
                            <x-td>{{ $suivi->annee_scolaire ?: '-' }}</x-td>
                            <x-td>
                                @if($suivi->moyenne_generale)
                                    <span class="font-medium {{ $suivi->estSatisfaisant() ? 'text-green-600' : 'text-danger-600' }}">
                                        {{ number_format($suivi->moyenne_generale, 2) }}/20
                                    </span>
                                @else
                                    -
                                @endif
                            </x-td>
                            <x-td>
                                @if($suivi->rang)
                                    <x-badge color="secondary">{{ $suivi->rang }}e</x-badge>
                                @else
                                    -
                                @endif
                            </x-td>
                            <x-td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('suivis-scolaires.show', $suivi) }}" class="text-primary-600 hover:text-primary-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('suivis-scolaires.edit', $suivi) }}" class="text-secondary-600 hover:text-secondary-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $suivis->links() }}
                </div>
            @else
                <x-empty-state 
                    title="Aucun suivi scolaire" 
                    description="Commencez par ajouter un suivi scolaire."
                    :action="route('suivis-scolaires.create')"
                    actionText="Ajouter un suivi"
                />
            @endif
        </x-card>
    </div>
</x-app-layout>
