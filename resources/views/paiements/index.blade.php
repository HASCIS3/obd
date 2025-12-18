@section('title', 'Paiements')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Paiements</h2>
                <p class="mt-1 text-sm text-gray-500">Gestion des paiements et cotisations</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2 flex-wrap">
                <x-button href="{{ route('paiements.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouveau paiement
                </x-button>
                <x-button href="{{ route('paiements.suivi-annuel') }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Suivi annuel
                </x-button>
                @if(auth()->user()->isAdmin())
                <x-button href="{{ route('paiements.arrieres') }}" variant="outline-danger">
                    Arrieres
                </x-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <x-stat-card 
                title="Encaissements du mois" 
                :value="number_format($stats['total_mois'], 0, ',', ' ') . ' FCFA'"
                color="success"
            />
            <x-stat-card 
                title="Total arrieres" 
                :value="number_format($stats['arrieres'], 0, ',', ' ') . ' FCFA'"
                color="danger"
            />
            <x-stat-card 
                title="Athletes en retard" 
                :value="$stats['nb_impayes']"
                color="warning"
            />
        </div>

        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('paiements.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <x-input type="text" name="search" placeholder="Rechercher athlete..." :value="request('search')" />
                </div>
                <div>
                    <x-select 
                        name="statut" 
                        :options="[
                            ['id' => 'paye', 'name' => 'Paye'],
                            ['id' => 'impaye', 'name' => 'Impaye'],
                            ['id' => 'partiel', 'name' => 'Partiel'],
                        ]" 
                        :selected="request('statut')"
                        placeholder="Tous les statuts"
                    />
                </div>
                <div>
                    <x-select 
                        name="mois" 
                        :options="[
                            ['id' => 1, 'name' => 'Janvier'], ['id' => 2, 'name' => 'Fevrier'],
                            ['id' => 3, 'name' => 'Mars'], ['id' => 4, 'name' => 'Avril'],
                            ['id' => 5, 'name' => 'Mai'], ['id' => 6, 'name' => 'Juin'],
                            ['id' => 7, 'name' => 'Juillet'], ['id' => 8, 'name' => 'Aout'],
                            ['id' => 9, 'name' => 'Septembre'], ['id' => 10, 'name' => 'Octobre'],
                            ['id' => 11, 'name' => 'Novembre'], ['id' => 12, 'name' => 'Decembre'],
                        ]" 
                        :selected="request('mois')"
                        placeholder="Tous les mois"
                    />
                </div>
                <div>
                    <x-input type="number" name="annee" placeholder="Annee" :value="request('annee')" min="2020" max="2100" />
                </div>
                <div class="flex gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">Filtrer</x-button>
                    <x-button href="{{ route('paiements.index') }}" variant="ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Liste des paiements -->
        <x-card :padding="false">
            @if($paiements->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Athlete</x-th>
                            <x-th>Type</x-th>
                            <x-th>Periode</x-th>
                            <x-th>Montant</x-th>
                            <x-th>Paye</x-th>
                            <x-th>Statut</x-th>
                            <x-th class="text-right">Actions</x-th>
                        </tr>
                    </x-slot>

                    @foreach($paiements as $paiement)
                        <tr class="hover:bg-gray-50">
                            <x-td>
                                <a href="{{ route('athletes.show', $paiement->athlete) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                                    {{ $paiement->athlete->nom_complet }}
                                </a>
                            </x-td>
                            <x-td>
                                @if($paiement->type_paiement === 'cotisation')
                                    <x-badge color="primary" size="sm">Cotisation</x-badge>
                                @elseif($paiement->type_paiement === 'inscription')
                                    <x-badge color="success" size="sm">Inscription</x-badge>
                                @else
                                    <x-badge color="warning" size="sm">Equipement</x-badge>
                                @endif
                            </x-td>
                            <x-td>{{ str_pad($paiement->mois, 2, '0', STR_PAD_LEFT) }}/{{ $paiement->annee }}</x-td>
                            <x-td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</x-td>
                            <x-td>{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</x-td>
                            <x-td>
                                @if($paiement->statut === 'paye')
                                    <x-badge color="success">Paye</x-badge>
                                @elseif($paiement->statut === 'partiel')
                                    <x-badge color="warning">Partiel</x-badge>
                                @else
                                    <x-badge color="danger">Impaye</x-badge>
                                @endif
                            </x-td>
                            <x-td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('paiements.show', $paiement) }}" class="text-primary-600 hover:text-primary-900" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('paiements.recu', $paiement) }}" class="text-green-600 hover:text-green-900" title="Telecharger le recu">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('paiements.edit', $paiement) }}" class="text-secondary-600 hover:text-secondary-900" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('paiements.destroy', $paiement) }}" method="POST" class="inline" onsubmit="return confirm('Etes-vous sur de vouloir supprimer ce paiement ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $paiements->links() }}
                </div>
            @else
                <x-empty-state 
                    title="Aucun paiement" 
                    description="Aucun paiement enregistre."
                    :action="route('paiements.create')"
                    actionText="Ajouter un paiement"
                />
            @endif
        </x-card>
    </div>
</x-app-layout>
