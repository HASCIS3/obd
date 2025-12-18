@section('title', 'Arrieres')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('paiements.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Arrieres de paiement</h2>
                <p class="mt-1 text-sm text-gray-500">Liste des athletes avec des paiements en retard</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($arrieres->count() > 0)
            <div class="space-y-6">
                @foreach($arrieres as $athleteId => $paiements)
                    @php
                        $athlete = $athletes[$athleteId];
                        $totalArrieres = $paiements->sum('montant') - $paiements->sum('montant_paye');
                    @endphp
                    <x-card>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-danger-100 flex items-center justify-center">
                                    <span class="text-danger-600 font-medium">
                                        {{ substr($athlete->prenom, 0, 1) }}{{ substr($athlete->nom, 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('athletes.show', $athlete) }}" class="text-lg font-semibold text-gray-900 hover:text-primary-600">
                                        {{ $athlete->nom_complet }}
                                    </a>
                                    <p class="text-sm text-gray-500">{{ $athlete->telephone ?: 'Pas de telephone' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-danger-600">{{ number_format($totalArrieres, 0, ',', ' ') }} FCFA</p>
                                <p class="text-sm text-gray-500">{{ $paiements->count() }} mois impaye(s)</p>
                            </div>
                        </div>

                        <x-table>
                            <x-slot name="head">
                                <tr>
                                    <x-th>Periode</x-th>
                                    <x-th>Montant du</x-th>
                                    <x-th>Paye</x-th>
                                    <x-th>Reste</x-th>
                                    <x-th>Statut</x-th>
                                    <x-th class="text-right">Action</x-th>
                                </tr>
                            </x-slot>

                            @foreach($paiements as $paiement)
                                <tr>
                                    <x-td>{{ str_pad($paiement->mois, 2, '0', STR_PAD_LEFT) }}/{{ $paiement->annee }}</x-td>
                                    <x-td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</x-td>
                                    <x-td>{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</x-td>
                                    <x-td class="font-medium text-danger-600">{{ number_format($paiement->reste_a_payer, 0, ',', ' ') }} FCFA</x-td>
                                    <x-td>
                                        @if($paiement->statut === 'partiel')
                                            <x-badge color="warning" size="sm">Partiel</x-badge>
                                        @else
                                            <x-badge color="danger" size="sm">Impaye</x-badge>
                                        @endif
                                    </x-td>
                                    <x-td class="text-right">
                                        <a href="{{ route('paiements.edit', $paiement) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                            Regulariser
                                        </a>
                                    </x-td>
                                </tr>
                            @endforeach
                        </x-table>
                    </x-card>
                @endforeach
            </div>
        @else
            <x-card>
                <x-empty-state 
                    title="Aucun arriere" 
                    description="Tous les paiements sont a jour. Felicitations !"
                />
            </x-card>
        @endif
    </div>
</x-app-layout>
