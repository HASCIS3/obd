@section('title', 'Evolution - ' . $athlete->nom_complet)

@php
    $stats = \App\Models\Performance::statistiquesAthlete($athlete->id, $disciplineId);
    $performancesAvecNote = $performances->whereNotNull('note_globale');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('athletes.show', $athlete) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Evolution de {{ $athlete->nom_complet }}</h2>
                    <p class="mt-1 text-sm text-gray-500">Suivi des performances dans le temps</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('performances.create', ['athlete' => $athlete->id]) }}" variant="primary" size="sm">
                    <svg class="-ml-1 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle performance
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtre par discipline -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('performances.evolution', $athlete) }}" class="flex gap-4 items-end">
                <x-form-group label="Discipline" name="discipline" class="flex-1 mb-0">
                    <x-select 
                        name="discipline" 
                        :options="$disciplines" 
                        :selected="$disciplineId"
                        placeholder="Toutes les disciplines"
                        valueKey="id"
                        labelKey="nom"
                    />
                </x-form-group>
                <x-button type="submit" variant="primary">Filtrer</x-button>
            </form>
        </x-card>

        <!-- Statistiques de l'athlète -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <x-card class="bg-blue-50 border-blue-200">
                <div class="text-center">
                    <p class="text-xs font-medium text-blue-600">Matchs</p>
                    <p class="text-xl font-bold text-blue-800">{{ $stats['matchs']['total'] }}</p>
                    <p class="text-xs text-blue-600">{{ $stats['matchs']['victoires'] }}V / {{ $stats['matchs']['defaites'] }}D</p>
                </div>
            </x-card>

            <x-card class="bg-green-50 border-green-200">
                <div class="text-center">
                    <p class="text-xs font-medium text-green-600">Victoires</p>
                    <p class="text-xl font-bold text-green-800">{{ $stats['matchs']['victoires'] }}</p>
                    @php
                        $tauxVictoire = $stats['matchs']['total'] > 0 ? round(($stats['matchs']['victoires'] / $stats['matchs']['total']) * 100) : 0;
                    @endphp
                    <p class="text-xs text-green-600">{{ $tauxVictoire }}%</p>
                </div>
            </x-card>

            <x-card class="bg-yellow-50 border-yellow-200">
                <div class="text-center">
                    <p class="text-xs font-medium text-yellow-600">Medailles</p>
                    <p class="text-xl font-bold text-yellow-800">
                        {{ $stats['competitions']['medailles_or'] + $stats['competitions']['medailles_argent'] + $stats['competitions']['medailles_bronze'] }}
                    </p>
                    <p class="text-xs">🥇{{ $stats['competitions']['medailles_or'] }} 🥈{{ $stats['competitions']['medailles_argent'] }} 🥉{{ $stats['competitions']['medailles_bronze'] }}</p>
                </div>
            </x-card>

            <x-card class="bg-purple-50 border-purple-200">
                <div class="text-center">
                    <p class="text-xs font-medium text-purple-600">Note physique</p>
                    <p class="text-xl font-bold text-purple-800">{{ $stats['notes']['moyenne_physique'] }}/10</p>
                </div>
            </x-card>

            <x-card class="bg-indigo-50 border-indigo-200">
                <div class="text-center">
                    <p class="text-xs font-medium text-indigo-600">Note technique</p>
                    <p class="text-xl font-bold text-indigo-800">{{ $stats['notes']['moyenne_technique'] }}/10</p>
                </div>
            </x-card>

            <x-card class="bg-pink-50 border-pink-200">
                <div class="text-center">
                    <p class="text-xs font-medium text-pink-600">Note globale</p>
                    <p class="text-xl font-bold text-pink-800">{{ $stats['notes']['moyenne_globale'] }}/10</p>
                </div>
            </x-card>
        </div>

        @if($performances->count() > 0)
            <!-- Graphiques d'evolution -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Graphique des notes -->
                <x-card title="Evolution des notes">
                    <div class="h-64">
                        <canvas id="notesChart"></canvas>
                    </div>
                </x-card>

                <!-- Graphique des résultats de matchs -->
                <x-card title="Resultats des matchs">
                    <div class="h-64">
                        <canvas id="matchsChart"></canvas>
                    </div>
                </x-card>
            </div>

            <!-- Liste des performances -->
            <x-card title="Historique des performances" :padding="false">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contexte</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resultat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progression</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($performances as $index => $performance)
                                @php
                                    $prevPerf = $performances->get($index - 1);
                                    $progression = null;
                                    if ($prevPerf && $performance->note_globale && $prevPerf->note_globale) {
                                        $progression = $performance->note_globale - $prevPerf->note_globale;
                                    }
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $performance->date_evaluation->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">
                                        @if($performance->contexte === 'match')
                                            <x-badge color="info">Match</x-badge>
                                        @elseif($performance->contexte === 'competition')
                                            <x-badge color="warning">Competition</x-badge>
                                        @elseif($performance->contexte === 'entrainement')
                                            <x-badge color="gray">Entrainement</x-badge>
                                        @else
                                            <x-badge color="purple">Test</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        @if($performance->adversaire)
                                            vs {{ $performance->adversaire }}
                                        @elseif($performance->competition)
                                            {{ $performance->competition }}
                                        @else
                                            {{ $performance->type_evaluation ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($performance->resultat_match)
                                            @if($performance->resultat_match === 'victoire')
                                                <span class="text-green-600 font-semibold">✓ Victoire</span>
                                            @elseif($performance->resultat_match === 'defaite')
                                                <span class="text-red-600 font-semibold">✗ Defaite</span>
                                            @else
                                                <span class="text-yellow-600 font-semibold">= Nul</span>
                                            @endif
                                            @if($performance->score_match)
                                                <span class="text-gray-500 text-sm">({{ $performance->score_match }})</span>
                                            @endif
                                        @elseif($performance->medaille)
                                            @if($performance->medaille === 'or')🥇 Or
                                            @elseif($performance->medaille === 'argent')🥈 Argent
                                            @else🥉 Bronze
                                            @endif
                                        @elseif($performance->classement)
                                            {{ $performance->classement }}e place
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($performance->note_globale)
                                            <span class="font-semibold {{ $performance->note_globale >= 7 ? 'text-green-600' : ($performance->note_globale >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $performance->note_globale }}/10
                                            </span>
                                            <div class="text-xs text-gray-400">
                                                P:{{ $performance->note_physique ?? '-' }} 
                                                T:{{ $performance->note_technique ?? '-' }} 
                                                C:{{ $performance->note_comportement ?? '-' }}
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($progression !== null)
                                            @if($progression > 0)
                                                <span class="text-green-600 font-semibold">↑ +{{ number_format($progression, 1) }}</span>
                                            @elseif($progression < 0)
                                                <span class="text-red-600 font-semibold">↓ {{ number_format($progression, 1) }}</span>
                                            @else
                                                <span class="text-gray-500">=</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Conseils d'amélioration -->
            <x-card title="Axes d'amelioration" class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($stats['notes']['moyenne_physique'] < 7)
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                            <h4 class="font-semibold text-red-800 mb-2">💪 Condition physique</h4>
                            <p class="text-sm text-red-600">Note actuelle: {{ $stats['notes']['moyenne_physique'] }}/10</p>
                            <p class="text-xs text-red-500 mt-2">Recommandation: Intensifier les seances d'entrainement physique, travail cardio et endurance.</p>
                        </div>
                    @else
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">💪 Condition physique</h4>
                            <p class="text-sm text-green-600">Note actuelle: {{ $stats['notes']['moyenne_physique'] }}/10</p>
                            <p class="text-xs text-green-500 mt-2">Excellent niveau! Maintenir le rythme actuel.</p>
                        </div>
                    @endif

                    @if($stats['notes']['moyenne_technique'] < 7)
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                            <h4 class="font-semibold text-red-800 mb-2">🎯 Technique</h4>
                            <p class="text-sm text-red-600">Note actuelle: {{ $stats['notes']['moyenne_technique'] }}/10</p>
                            <p class="text-xs text-red-500 mt-2">Recommandation: Plus de repetitions techniques, travail individuel avec le coach.</p>
                        </div>
                    @else
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">🎯 Technique</h4>
                            <p class="text-sm text-green-600">Note actuelle: {{ $stats['notes']['moyenne_technique'] }}/10</p>
                            <p class="text-xs text-green-500 mt-2">Tres bon niveau technique!</p>
                        </div>
                    @endif

                    @if($stats['notes']['moyenne_comportement'] < 7)
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                            <h4 class="font-semibold text-red-800 mb-2">🧠 Comportement</h4>
                            <p class="text-sm text-red-600">Note actuelle: {{ $stats['notes']['moyenne_comportement'] }}/10</p>
                            <p class="text-xs text-red-500 mt-2">Recommandation: Travail sur la discipline, l'esprit d'equipe et la concentration.</p>
                        </div>
                    @else
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <h4 class="font-semibold text-green-800 mb-2">🧠 Comportement</h4>
                            <p class="text-sm text-green-600">Note actuelle: {{ $stats['notes']['moyenne_comportement'] }}/10</p>
                            <p class="text-xs text-green-500 mt-2">Excellent comportement et discipline!</p>
                        </div>
                    @endif
                </div>
            </x-card>
        @else
            <x-card>
                <x-empty-state 
                    title="Aucune performance" 
                    description="Aucune performance enregistree pour cet athlete. Ajoutez des performances pour voir l'evolution."
                >
                    <x-button href="{{ route('performances.create', ['athlete' => $athlete->id]) }}" variant="primary">
                        Ajouter une performance
                    </x-button>
                </x-empty-state>
            </x-card>
        @endif
    </div>

    @if($performances->count() > 0)
    @push('scripts')
    <script>
        // Graphique des notes
        const notesCtx = document.getElementById('notesChart').getContext('2d');
        const performancesAvecNote = @json($performancesAvecNote->values());
        
        new Chart(notesCtx, {
            type: 'line',
            data: {
                labels: performancesAvecNote.map(p => new Date(p.date_evaluation).toLocaleDateString('fr-FR')),
                datasets: [
                    {
                        label: 'Note globale',
                        data: performancesAvecNote.map(p => p.note_globale),
                        borderColor: '#14532d',
                        backgroundColor: 'rgba(20, 83, 45, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Physique',
                        data: performancesAvecNote.map(p => p.note_physique),
                        borderColor: '#7c3aed',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Technique',
                        data: performancesAvecNote.map(p => p.note_technique),
                        borderColor: '#0891b2',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 10,
                        ticks: {
                            stepSize: 2
                        }
                    }
                }
            }
        });

        // Graphique des matchs
        const matchsCtx = document.getElementById('matchsChart').getContext('2d');
        new Chart(matchsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Victoires', 'Defaites', 'Nuls'],
                datasets: [{
                    data: [{{ $stats['matchs']['victoires'] }}, {{ $stats['matchs']['defaites'] }}, {{ $stats['matchs']['nuls'] }}],
                    backgroundColor: ['#16a34a', '#dc2626', '#eab308'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
    @endpush
    @endif
</x-app-layout>
