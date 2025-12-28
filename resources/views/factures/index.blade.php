@section('title', 'Factures')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Factures</h2>
                <p class="mt-1 text-sm text-gray-500">Gestion de la facturation</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('factures.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle facture
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
                <div class="text-2xl font-bold text-blue-600">{{ $stats['emises'] }}</div>
                <div class="text-sm text-gray-500">Émises</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['payees'] }}</div>
                <div class="text-sm text-gray-500">Payées</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['impayees'] }}</div>
                <div class="text-sm text-gray-500">Impayées</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ $stats['en_retard'] }}</div>
                <div class="text-sm text-gray-500">En retard</div>
            </x-card>
        </div>

        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('factures.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        name="statut" 
                        :options="[
                            ['id' => 'brouillon', 'name' => 'Brouillon'],
                            ['id' => 'emise', 'name' => 'Émise'],
                            ['id' => 'payee', 'name' => 'Payée'],
                            ['id' => 'partiellement_payee', 'name' => 'Partiellement payée'],
                            ['id' => 'annulee', 'name' => 'Annulée'],
                        ]" 
                        :selected="request('statut')"
                        placeholder="Tous les statuts"
                    />
                </div>
                <div class="flex gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">
                        Filtrer
                    </x-button>
                    <x-button href="{{ route('factures.index') }}" variant="ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Liste -->
        <x-card :padding="false">
            @if($factures->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>N° Facture</x-th>
                            <x-th>Athlète</x-th>
                            <x-th>Période</x-th>
                            <x-th class="text-right">Montant TTC</x-th>
                            <x-th class="text-right">Payé</x-th>
                            <x-th>Échéance</x-th>
                            <x-th>Statut</x-th>
                            <x-th class="text-right">Actions</x-th>
                        </tr>
                    </x-slot>

                    @foreach($factures as $facture)
                        <tr class="hover:bg-gray-50 {{ $facture->est_en_retard ? 'bg-red-50' : '' }}">
                            <x-td>
                                <span class="font-mono text-sm">{{ $facture->numero }}</span>
                            </x-td>
                            <x-td>
                                <a href="{{ route('athletes.show', $facture->athlete) }}" class="text-primary-600 hover:underline">
                                    {{ $facture->athlete->nom_complet }}
                                </a>
                            </x-td>
                            <x-td>{{ $facture->periode ?? '-' }}</x-td>
                            <x-td class="text-right font-medium">
                                {{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA
                            </x-td>
                            <x-td class="text-right">
                                {{ number_format($facture->montant_paye, 0, ',', ' ') }} FCFA
                            </x-td>
                            <x-td>
                                <span class="{{ $facture->est_en_retard ? 'text-red-600 font-medium' : '' }}">
                                    {{ $facture->date_echeance->format('d/m/Y') }}
                                </span>
                            </x-td>
                            <x-td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $facture->statut_badge_class }}">
                                    {{ $facture->statut_label }}
                                </span>
                            </x-td>
                            <x-td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('factures.show', $facture) }}" class="text-gray-600 hover:text-primary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('factures.pdf', $facture) }}" class="text-gray-600 hover:text-primary-600" title="Télécharger PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                </div>
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t">
                    {{ $factures->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune facture</h3>
                    <div class="mt-6">
                        <x-button href="{{ route('factures.create') }}" variant="primary">
                            Créer une facture
                        </x-button>
                    </div>
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
