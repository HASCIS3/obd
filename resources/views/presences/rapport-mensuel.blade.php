@section('title', 'Rapport mensuel des presences')

@php
    $moisNoms = [
        1 => 'Janvier', 2 => 'Fevrier', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Aout',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Decembre'
    ];
    $nomMois = $moisNoms[$mois] ?? 'Inconnu';
    $totalGlobal = collect($stats)->sum('total');
    $presentsGlobal = collect($stats)->sum('presents');
    $absentsGlobal = collect($stats)->sum('absents');
    $tauxGlobal = $totalGlobal > 0 ? round(($presentsGlobal / $totalGlobal) * 100, 1) : 0;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('presences.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Rapport mensuel des presences</h2>
                    <p class="mt-1 text-sm text-gray-500">Statistiques par discipline</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimer le rapport
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Zone imprimable -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" id="rapport-content">
        <!-- Filtres (masques a l'impression) -->
        <x-card class="mb-6 print:hidden">
            <form method="GET" action="{{ route('presences.rapport-mensuel') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-form-group label="Mois" name="mois">
                    <x-select 
                        name="mois" 
                        :options="[
                            ['id' => 1, 'name' => 'Janvier'],
                            ['id' => 2, 'name' => 'Fevrier'],
                            ['id' => 3, 'name' => 'Mars'],
                            ['id' => 4, 'name' => 'Avril'],
                            ['id' => 5, 'name' => 'Mai'],
                            ['id' => 6, 'name' => 'Juin'],
                            ['id' => 7, 'name' => 'Juillet'],
                            ['id' => 8, 'name' => 'Aout'],
                            ['id' => 9, 'name' => 'Septembre'],
                            ['id' => 10, 'name' => 'Octobre'],
                            ['id' => 11, 'name' => 'Novembre'],
                            ['id' => 12, 'name' => 'Decembre'],
                        ]" 
                        :selected="$mois"
                        placeholder=""
                    />
                </x-form-group>

                <x-form-group label="Annee" name="annee">
                    <x-input type="number" name="annee" :value="$annee" min="2020" max="2100" />
                </x-form-group>

                <div class="flex items-end">
                    <x-button type="submit" variant="primary" class="w-full">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Charger
                    </x-button>
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="window.print()" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimer
                    </button>
                </div>
            </form>
        </x-card>

        <!-- En-tete du rapport (visible a l'impression) -->
        <div class="hidden print:block mb-8 text-center border-b-2 border-gray-800 pb-4">
            <h1 class="text-2xl font-bold text-gray-900">CENTRE SPORTIF OBD</h1>
            <h2 class="text-xl font-semibold text-gray-700 mt-2">RAPPORT MENSUEL DES PRESENCES</h2>
            <p class="text-lg text-gray-600 mt-1">{{ $nomMois }} {{ $annee }}</p>
            <p class="text-sm text-gray-500 mt-2">Genere le {{ now()->format('d/m/Y a H:i') }}</p>
        </div>

        @if(count($stats) > 0)
            <!-- Resume global -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <x-card class="text-center">
                    <div class="text-3xl font-bold text-gray-900">{{ $totalGlobal }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total pointages</div>
                </x-card>
                <x-card class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $presentsGlobal }}</div>
                    <div class="text-sm text-gray-500 mt-1">Presents</div>
                </x-card>
                <x-card class="text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $absentsGlobal }}</div>
                    <div class="text-sm text-gray-500 mt-1">Absents</div>
                </x-card>
                <x-card class="text-center">
                    <div class="text-3xl font-bold {{ $tauxGlobal >= 80 ? 'text-green-600' : ($tauxGlobal >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $tauxGlobal }}%</div>
                    <div class="text-sm text-gray-500 mt-1">Taux global</div>
                </x-card>
            </div>

            <!-- Tableau par discipline -->
            <x-card title="Statistiques par discipline" class="mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discipline</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Presents</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Absents</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Taux</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider print:hidden">Progression</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($stats as $stat)
                                @if($stat['total'] > 0)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 rounded-full {{ $stat['taux'] >= 80 ? 'bg-green-500' : ($stat['taux'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }} mr-3"></div>
                                            <span class="font-medium text-gray-900">{{ $stat['discipline'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-gray-900 font-semibold">{{ $stat['total'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $stat['presents'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $stat['absents'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-lg font-bold {{ $stat['taux'] >= 80 ? 'text-green-600' : ($stat['taux'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $stat['taux'] }}%
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap print:hidden">
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="h-3 rounded-full {{ $stat['taux'] >= 80 ? 'bg-green-500' : ($stat['taux'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                                 style="width: {{ $stat['taux'] }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr class="font-bold">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900">TOTAL GENERAL</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-900">{{ $totalGlobal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-green-600">{{ $presentsGlobal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-red-600">{{ $absentsGlobal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center {{ $tauxGlobal >= 80 ? 'text-green-600' : ($tauxGlobal >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $tauxGlobal }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap print:hidden"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-card>

            <!-- Legende -->
            <x-card title="Legende" class="mb-6">
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full bg-green-500 mr-2"></div>
                        <span class="text-sm text-gray-600">Excellent (≥ 80%)</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full bg-yellow-500 mr-2"></div>
                        <span class="text-sm text-gray-600">Moyen (50-79%)</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 rounded-full bg-red-500 mr-2"></div>
                        <span class="text-sm text-gray-600">Faible (< 50%)</span>
                    </div>
                </div>
            </x-card>

            <!-- Signature (visible a l'impression) -->
            <div class="hidden print:block mt-12">
                <div class="grid grid-cols-2 gap-8">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-16">Le Directeur</p>
                        <div class="border-t border-gray-400 pt-2">
                            <p class="text-sm text-gray-500">Signature et cachet</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-16">Le Responsable Technique</p>
                        <div class="border-t border-gray-400 pt-2">
                            <p class="text-sm text-gray-500">Signature</p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <x-card>
                <x-empty-state 
                    title="Aucune donnee" 
                    description="Aucune presence enregistree pour {{ $nomMois }} {{ $annee }}."
                    icon="document"
                />
            </x-card>
        @endif
    </div>

    <!-- Styles d'impression -->
    <style>
        @media print {
            body { 
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print\:hidden { display: none !important; }
            .print\:block { display: block !important; }
            nav, header button, .print\:hidden { display: none !important; }
            @page { 
                margin: 1.5cm; 
                size: A4 portrait;
            }
        }
    </style>
</x-app-layout>
