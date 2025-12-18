@section('title', 'Dashboard')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Tableau de bord
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Bienvenue, {{ auth()->user()->name }} ! Voici un apercu de votre centre sportif.
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <span class="text-sm text-gray-500">{{ now()->format('l d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques principales -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <x-stat-card 
                title="Athletes actifs" 
                :value="$stats['total_athletes']"
                color="primary"
            >
                <x-slot name="icon">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Coachs" 
                :value="$stats['total_coachs']"
                color="secondary"
            >
                <x-slot name="icon">
                    <svg class="h-6 w-6 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Disciplines" 
                :value="$stats['total_disciplines']"
                color="info"
            >
                <x-slot name="icon">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Arrieres (FCFA)" 
                :value="number_format($stats['arrieres_total'], 0, ',', ' ')"
                color="danger"
            >
                <x-slot name="icon">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot>
            </x-stat-card>
        </div>

        <!-- Graphiques et listes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Graphique des presences -->
            <x-card title="Presences du mois">
                <div class="h-64">
                    <canvas id="presencesChart"></canvas>
                </div>
            </x-card>

            <!-- Derniers athletes inscrits -->
            <x-card title="Derniers athletes inscrits">
                @if($derniersAthletes->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($derniersAthletes as $athlete)
                            <li class="py-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        @if($athlete->photo_url)
                                            <img src="{{ $athlete->photo_url }}" alt="{{ $athlete->nom_complet }}" class="h-10 w-10 object-cover">
                                        @else
                                            <img src="{{ asset('images/default-avatar.svg') }}" alt="{{ $athlete->nom_complet }}" class="h-10 w-10 object-cover">
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $athlete->nom_complet }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $athlete->disciplines->pluck('nom')->join(', ') ?: 'Aucune discipline' }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('athletes.show', $athlete) }}" class="text-primary-600 hover:text-primary-800 text-sm">
                                    Voir
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <x-empty-state 
                        title="Aucun athlete" 
                        description="Commencez par ajouter des athletes."
                    />
                @endif
            </x-card>
        </div>

        <!-- Section Performances -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Performances sportives</h3>
            <a href="{{ route('performances.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                Voir tout &rarr;
            </a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-8">
            <x-card class="bg-blue-50 border-blue-200">
                <div class="text-center">
                    <div class="text-2xl mb-1">⚽</div>
                    <p class="text-xs font-medium text-blue-600">Matchs joues</p>
                    <p class="text-2xl font-bold text-blue-800">{{ $statsPerformance['matchs']['total'] }}</p>
                    <div class="mt-1 flex justify-center gap-1 text-xs">
                        <span class="text-green-600">{{ $statsPerformance['matchs']['victoires'] }}V</span>
                        <span class="text-red-600">{{ $statsPerformance['matchs']['defaites'] }}D</span>
                        <span class="text-yellow-600">{{ $statsPerformance['matchs']['nuls'] }}N</span>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-green-50 border-green-200">
                <div class="text-center">
                    <div class="text-2xl mb-1">🏆</div>
                    <p class="text-xs font-medium text-green-600">Taux de victoire</p>
                    <p class="text-2xl font-bold text-green-800">{{ $statsPerformance['matchs']['taux_victoire'] }}%</p>
                </div>
            </x-card>

            <x-card class="bg-yellow-50 border-yellow-200">
                <div class="text-center">
                    <div class="text-2xl mb-1">🏅</div>
                    <p class="text-xs font-medium text-yellow-600">Medailles</p>
                    <div class="flex justify-center gap-2 mt-1">
                        <span class="text-sm">🥇{{ $statsPerformance['competitions']['medailles_or'] }}</span>
                        <span class="text-sm">🥈{{ $statsPerformance['competitions']['medailles_argent'] }}</span>
                        <span class="text-sm">🥉{{ $statsPerformance['competitions']['medailles_bronze'] }}</span>
                    </div>
                </div>
            </x-card>

            <x-card class="bg-purple-50 border-purple-200">
                <div class="text-center">
                    <div class="text-2xl mb-1">📊</div>
                    <p class="text-xs font-medium text-purple-600">Note moyenne</p>
                    <p class="text-2xl font-bold text-purple-800">{{ $statsPerformance['note_moyenne'] }}/10</p>
                </div>
            </x-card>
        </div>

        <!-- Dernières performances -->
        <x-card title="Dernieres performances" class="mb-8">
            @if($dernieresPerformances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Athlete</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contexte</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($dernieresPerformances as $perf)
                                <tr>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('athletes.show', $perf->athlete) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600">
                                            {{ $perf->athlete->nom_complet }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">
                                        @if($perf->contexte === 'match')
                                            @if($perf->resultat_match === 'victoire')
                                                <x-badge color="success">Victoire</x-badge>
                                            @elseif($perf->resultat_match === 'defaite')
                                                <x-badge color="danger">Defaite</x-badge>
                                            @else
                                                <x-badge color="warning">Nul</x-badge>
                                            @endif
                                        @elseif($perf->contexte === 'competition')
                                            @if($perf->medaille)
                                                <span>
                                                    @if($perf->medaille === 'or')🥇
                                                    @elseif($perf->medaille === 'argent')🥈
                                                    @else🥉
                                                    @endif
                                                </span>
                                            @else
                                                <x-badge color="info">Competition</x-badge>
                                            @endif
                                        @elseif($perf->contexte === 'entrainement')
                                            <x-badge color="gray">Entrainement</x-badge>
                                        @else
                                            <x-badge color="purple">Test</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-500">
                                        @if($perf->adversaire)
                                            vs {{ $perf->adversaire }}
                                            @if($perf->score_match) ({{ $perf->score_match }}) @endif
                                        @elseif($perf->competition)
                                            {{ $perf->competition }}
                                        @else
                                            {{ $perf->type_evaluation ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $perf->date_evaluation->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 text-sm font-medium {{ $perf->note_globale >= 7 ? 'text-green-600' : ($perf->note_globale >= 5 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $perf->note_globale ? $perf->note_globale.'/10' : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('performances.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                        Voir toutes les performances &rarr;
                    </a>
                </div>
            @else
                <x-empty-state 
                    title="Aucune performance" 
                    description="Les performances apparaitront ici."
                >
                    <x-button href="{{ route('performances.create') }}" variant="primary" size="sm">
                        Ajouter une performance
                    </x-button>
                </x-empty-state>
            @endif
        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Paiements recents -->
            @if(auth()->user()->isAdmin())
            <x-card title="Paiements recents">
                @if($paiementsRecents->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($paiementsRecents as $paiement)
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $paiement->athlete->nom_complet }}</p>
                                    <p class="text-xs text-gray-500">{{ $paiement->date_paiement?->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-green-600">
                                        {{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA
                                    </p>
                                    <x-badge color="success" size="sm">Paye</x-badge>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('paiements.index') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                            Voir tous les paiements &rarr;
                        </a>
                    </div>
                @else
                    <x-empty-state 
                        title="Aucun paiement" 
                        description="Les paiements recents apparaitront ici."
                    />
                @endif
            </x-card>

            <!-- Athletes avec arrieres -->
            <x-card title="Athletes avec arrieres">
                @if($athletesArrieres->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($athletesArrieres as $athlete)
                            @php
                                $totalArrieres = $athlete->paiements->sum('montant') - $athlete->paiements->sum('montant_paye');
                            @endphp
                            <li class="py-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden ring-2 ring-danger-200">
                                        @if($athlete->photo_url)
                                            <img src="{{ $athlete->photo_url }}" alt="{{ $athlete->nom_complet }}" class="h-10 w-10 object-cover">
                                        @else
                                            <img src="{{ asset('images/default-avatar.svg') }}" alt="{{ $athlete->nom_complet }}" class="h-10 w-10 object-cover">
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $athlete->nom_complet }}</p>
                                        <p class="text-xs text-gray-500">{{ $athlete->paiements->count() }} mois impaye(s)</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-danger-600">
                                        {{ number_format($totalArrieres, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="{{ route('paiements.arrieres') }}" class="text-sm text-danger-600 hover:text-danger-800 font-medium">
                            Voir tous les arrieres &rarr;
                        </a>
                    </div>
                @else
                    <x-empty-state 
                        title="Aucun arriere" 
                        description="Tous les paiements sont a jour !"
                    />
                @endif
            </x-card>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Graphique des presences
        const presencesCtx = document.getElementById('presencesChart').getContext('2d');
        new Chart(presencesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Presents', 'Absents'],
                datasets: [{
                    data: [{{ $stats['presences_mois'] }}, {{ $stats['absences_mois'] }}],
                    backgroundColor: ['#14532d', '#CE1126'],
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
</x-app-layout>
