@section('title', 'Suivi annuel des paiements')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('paiements.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Suivi annuel {{ $annee }}</h2>
                    <p class="mt-1 text-sm text-gray-500">Cotisation: {{ number_format($cotisationMensuelle, 0, ',', ' ') }} FCFA/mois × 12 = {{ number_format($totalAnnuel, 0, ',', ' ') }} FCFA/an</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0">
                <form action="{{ route('paiements.suivi-annuel') }}" method="GET" class="flex items-center gap-2">
                    <select name="annee" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        @for($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $annee == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <x-button type="submit" variant="secondary">Afficher</x-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques globales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-card class="bg-blue-50 border-blue-200">
                <div class="text-center">
                    <p class="text-sm font-medium text-blue-600">Total attendu</p>
                    <p class="text-2xl font-bold text-blue-800">{{ number_format($stats['total_attendu'], 0, ',', ' ') }} FCFA</p>
                    <p class="text-xs text-blue-500">{{ $suiviAthletes->count() }} athletes × {{ number_format($totalAnnuel, 0, ',', ' ') }}</p>
                </div>
            </x-card>
            <x-card class="bg-green-50 border-green-200">
                <div class="text-center">
                    <p class="text-sm font-medium text-green-600">Total recu</p>
                    <p class="text-2xl font-bold text-green-800">{{ number_format($stats['total_recu'], 0, ',', ' ') }} FCFA</p>
                    <p class="text-xs text-green-500">{{ $stats['athletes_a_jour'] }} athlete(s) a jour</p>
                </div>
            </x-card>
            <x-card class="bg-red-50 border-red-200">
                <div class="text-center">
                    <p class="text-sm font-medium text-red-600">Total arrieres</p>
                    <p class="text-2xl font-bold text-red-800">{{ number_format($stats['total_arrieres'], 0, ',', ' ') }} FCFA</p>
                    <p class="text-xs text-red-500">{{ $stats['athletes_en_cours'] + $stats['athletes_aucun'] }} athlete(s) en retard</p>
                </div>
            </x-card>
            <x-card class="bg-yellow-50 border-yellow-200">
                <div class="text-center">
                    <p class="text-sm font-medium text-yellow-600">Taux de recouvrement</p>
                    <p class="text-2xl font-bold text-yellow-800">
                        {{ $stats['total_attendu'] > 0 ? round(($stats['total_recu'] / $stats['total_attendu']) * 100, 1) : 0 }}%
                    </p>
                    <p class="text-xs text-yellow-500">{{ $stats['athletes_en_cours'] }} en cours de paiement</p>
                </div>
            </x-card>
        </div>

        <!-- Liste des athlètes -->
        <x-card :padding="false">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Suivi par athlete</h3>
            </div>
            
            @if($suiviAthletes->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Athlete</x-th>
                            <x-th class="text-center">Mois payes</x-th>
                            <x-th class="text-right">Total paye</x-th>
                            <x-th class="text-right">Reste a payer</x-th>
                            <x-th class="text-center">Progression</x-th>
                            <x-th>Statut</x-th>
                        </tr>
                    </x-slot>

                    @foreach($suiviAthletes as $suivi)
                        <tr class="hover:bg-gray-50">
                            <x-td>
                                <a href="{{ route('athletes.show', $suivi['athlete']) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                                    {{ $suivi['athlete']->nom_complet }}
                                </a>
                            </x-td>
                            <x-td class="text-center">
                                <div class="flex justify-center gap-1 flex-wrap">
                                    @php
                                        $moisNoms = ['J', 'F', 'M', 'A', 'M', 'J', 'J', 'A', 'S', 'O', 'N', 'D'];
                                    @endphp
                                    @for($m = 1; $m <= 12; $m++)
                                        @if(in_array($m, $suivi['mois_payes']))
                                            <span class="w-6 h-6 rounded-full bg-green-500 text-white text-xs flex items-center justify-center" title="{{ \App\Models\Paiement::mois()[$m] }} - Paye">
                                                {{ $moisNoms[$m-1] }}
                                            </span>
                                        @elseif(in_array($m, $suivi['mois_partiels']))
                                            <span class="w-6 h-6 rounded-full bg-yellow-500 text-white text-xs flex items-center justify-center" title="{{ \App\Models\Paiement::mois()[$m] }} - Partiel">
                                                {{ $moisNoms[$m-1] }}
                                            </span>
                                        @else
                                            <span class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 text-xs flex items-center justify-center" title="{{ \App\Models\Paiement::mois()[$m] }} - Non paye">
                                                {{ $moisNoms[$m-1] }}
                                            </span>
                                        @endif
                                    @endfor
                                </div>
                            </x-td>
                            <x-td class="text-right font-semibold text-green-600">
                                {{ number_format($suivi['total_paye'], 0, ',', ' ') }} FCFA
                            </x-td>
                            <x-td class="text-right font-semibold {{ $suivi['reste_a_payer'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($suivi['reste_a_payer'], 0, ',', ' ') }} FCFA
                            </x-td>
                            <x-td>
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div class="h-4 rounded-full {{ $suivi['pourcentage'] == 100 ? 'bg-green-500' : ($suivi['pourcentage'] > 0 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                         style="width: {{ $suivi['pourcentage'] }}%">
                                    </div>
                                </div>
                                <p class="text-xs text-center text-gray-500 mt-1">{{ $suivi['pourcentage'] }}%</p>
                            </x-td>
                            <x-td>
                                @if($suivi['statut'] === 'complet')
                                    <x-badge color="success">A jour</x-badge>
                                @elseif($suivi['statut'] === 'en_cours')
                                    <x-badge color="warning">En cours</x-badge>
                                @else
                                    <x-badge color="danger">Aucun paiement</x-badge>
                                @endif
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>
            @else
                <x-empty-state 
                    title="Aucun athlete" 
                    description="Aucun athlete actif trouve."
                />
            @endif
        </x-card>
    </div>
</x-app-layout>
