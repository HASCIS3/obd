@section('title', 'Licences Sportives')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Licences Sportives</h2>
                <p class="mt-1 text-sm text-gray-500">Gestion des licences fédérales des athlètes</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('licences.expirant-bientot') }}" variant="warning">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Expirant bientôt ({{ $stats['expirant_bientot'] }})
                </x-button>
                <x-button href="{{ route('licences.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle licence
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <x-card class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500">Total</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['actives'] }}</div>
                <div class="text-sm text-gray-500">Actives</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ $stats['expirees'] }}</div>
                <div class="text-sm text-gray-500">Expirées</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['expirant_bientot'] }}</div>
                <div class="text-sm text-gray-500">Expirant bientôt</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $stats['non_payees'] }}</div>
                <div class="text-sm text-gray-500">Non payées</div>
            </x-card>
        </div>

        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('licences.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <x-input 
                        type="text" 
                        name="search" 
                        placeholder="Rechercher..." 
                        :value="request('search')"
                    />
                </div>
                <div>
                    <x-select 
                        name="discipline_id" 
                        :options="$disciplines" 
                        :selected="request('discipline_id')"
                        placeholder="Toutes les disciplines"
                        valueKey="id"
                        labelKey="nom"
                    />
                </div>
                <div>
                    <x-select 
                        name="statut" 
                        :options="[
                            ['id' => 'active', 'name' => 'Actives'],
                            ['id' => 'expiree', 'name' => 'Expirées'],
                            ['id' => 'suspendue', 'name' => 'Suspendues'],
                            ['id' => 'annulee', 'name' => 'Annulées'],
                        ]" 
                        :selected="request('statut')"
                        placeholder="Tous les statuts"
                    />
                </div>
                <div>
                    <x-select 
                        name="saison" 
                        :options="$saisons->map(fn($s) => ['id' => $s, 'name' => $s])->toArray()" 
                        :selected="request('saison')"
                        placeholder="Toutes les saisons"
                    />
                </div>
                <div class="flex gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">
                        Filtrer
                    </x-button>
                    <x-button href="{{ route('licences.index') }}" variant="ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Actions groupées -->
        <div class="mb-4 flex justify-end">
            <form action="{{ route('licences.verifier-expirations') }}" method="POST">
                @csrf
                <x-button type="submit" variant="secondary" size="sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Vérifier les expirations
                </x-button>
            </form>
        </div>

        <!-- Liste des licences -->
        <x-card :padding="false">
            @if($licences->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>N° Licence</x-th>
                            <x-th>Athlète</x-th>
                            <x-th>Discipline</x-th>
                            <x-th>Catégorie</x-th>
                            <x-th>Expiration</x-th>
                            <x-th>Statut</x-th>
                            <x-th>Paiement</x-th>
                            <x-th class="text-right">Actions</x-th>
                        </tr>
                    </x-slot>

                    @foreach($licences as $licence)
                        <tr class="hover:bg-gray-50">
                            <x-td>
                                <span class="font-mono text-sm">{{ $licence->numero_licence }}</span>
                            </x-td>
                            <x-td>
                                <a href="{{ route('athletes.show', $licence->athlete) }}" class="text-primary-600 hover:underline">
                                    {{ $licence->athlete->nom_complet }}
                                </a>
                            </x-td>
                            <x-td>{{ $licence->discipline->nom }}</x-td>
                            <x-td>
                                <x-badge variant="info">{{ $licence->categorie ?? 'N/A' }}</x-badge>
                            </x-td>
                            <x-td>
                                <div class="text-sm">
                                    {{ $licence->date_expiration->format('d/m/Y') }}
                                    @if($licence->jours_restants <= 30 && $licence->jours_restants > 0)
                                        <span class="text-yellow-600 text-xs">({{ $licence->jours_restants }}j)</span>
                                    @elseif($licence->jours_restants == 0)
                                        <span class="text-red-600 text-xs">(Expirée)</span>
                                    @endif
                                </div>
                            </x-td>
                            <x-td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $licence->statut_badge_class }}">
                                    {{ ucfirst($licence->statut) }}
                                </span>
                            </x-td>
                            <x-td>
                                @if($licence->paye)
                                    <x-badge variant="success">Payée</x-badge>
                                @else
                                    <x-badge variant="danger">{{ number_format($licence->frais_licence, 0, ',', ' ') }} FCFA</x-badge>
                                @endif
                            </x-td>
                            <x-td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('licences.show', $licence) }}" class="text-gray-600 hover:text-primary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('licences.edit', $licence) }}" class="text-gray-600 hover:text-primary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if($licence->statut === 'active' || $licence->statut === 'expiree')
                                        <form action="{{ route('licences.renouveler', $licence) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Renouveler">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t">
                    {{ $licences->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune licence</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle licence.</p>
                    <div class="mt-6">
                        <x-button href="{{ route('licences.create') }}" variant="primary">
                            Créer une licence
                        </x-button>
                    </div>
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
