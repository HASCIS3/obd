@extends('presences.pointage.index')

@section('periode-filters')
    <div class="flex-1 min-w-[120px]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Annee</label>
        <x-select 
            name="annee" 
            :options="$anneeOptions" 
            :selected="$annee"
        />
    </div>
    
    <div class="flex items-end gap-2">
        <a href="{{ route('presences.pointage.annuel', ['annee' => $annee - 1, 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Annee precedente">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <a href="{{ route('presences.pointage.annuel', ['annee' => now()->year, 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
            Cette annee
        </a>
        <a href="{{ route('presences.pointage.annuel', ['annee' => $annee + 1, 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Annee suivante">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
@endsection

@section('pointage-content')
    @if($disciplineId && $athletes->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Tableau annuel -->
            <div class="lg:col-span-3">
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Bilan annuel {{ $annee }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $selectedDiscipline->nom ?? 'Discipline' }} - {{ $athletes->count() }} athletes</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('presences.rapport-mensuel', ['annee' => $annee]) }}" class="text-sm text-primary-600 hover:text-primary-800">
                                Voir rapport detaille →
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 min-w-[180px]">Athlete</th>
                                    @foreach($moisLabels as $moisNum => $moisNom)
                                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[60px]">
                                            {{ $moisNom }}
                                        </th>
                                    @endforeach
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">Total</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">Taux</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($athletes as $athlete)
                                    @php
                                        $stats = $athleteStats[$athlete->id] ?? ['mois' => [], 'presents' => 0, 'total' => 0, 'taux' => 0];
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap sticky left-0 bg-white z-10">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full overflow-hidden bg-primary-100 flex items-center justify-center">
                                                    @if($athlete->photo_url)
                                                        <img src="{{ $athlete->photo_url }}" alt="{{ $athlete->nom_complet }}" class="h-8 w-8 object-cover">
                                                    @else
                                                        <span class="text-primary-600 font-medium text-xs">
                                                            {{ substr($athlete->prenom, 0, 1) }}{{ substr($athlete->nom, 0, 1) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="ml-2">
                                                    <a href="{{ route('athletes.show', $athlete) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600">
                                                        {{ $athlete->nom_complet }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($moisLabels as $moisNum => $moisNom)
                                            @php
                                                $moisStats = $stats['mois'][$moisNum] ?? ['presents' => 0, 'total' => 0, 'taux' => 0];
                                            @endphp
                                            <td class="px-2 py-3 text-center">
                                                @if($moisStats['total'] > 0)
                                                    <a href="{{ route('presences.pointage.mensuel', ['mois' => $moisNum, 'annee' => $annee, 'discipline' => $disciplineId]) }}"
                                                       class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                                        {{ $moisStats['taux'] >= 80 ? 'bg-green-100 text-green-800 hover:bg-green-200' : ($moisStats['taux'] >= 50 ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-red-100 text-red-800 hover:bg-red-200') }}">
                                                        {{ $moisStats['taux'] }}%
                                                    </a>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-3 text-center bg-gray-50">
                                            <span class="font-semibold text-gray-900">{{ $stats['presents'] }}/{{ $stats['total'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center bg-gray-50">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $stats['taux'] >= 80 ? 'bg-green-100 text-green-800' : ($stats['taux'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $stats['taux'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100">
                                <tr>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 sticky left-0 bg-gray-100 z-10">Moyenne</td>
                                    @foreach($moisLabels as $moisNum => $moisNom)
                                        @php
                                            $moisTotal = $statsParMois[$moisNum] ?? ['taux' => 0];
                                        @endphp
                                        <td class="px-2 py-3 text-center">
                                            @if($moisTotal['taux'] > 0)
                                                <span class="text-xs font-medium {{ $moisTotal['taux'] >= 80 ? 'text-green-700' : ($moisTotal['taux'] >= 50 ? 'text-yellow-700' : 'text-red-700') }}">
                                                    {{ $moisTotal['taux'] }}%
                                                </span>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 text-center font-semibold text-gray-900">{{ $statsGlobales['presents'] }}/{{ $statsGlobales['total'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                            {{ $statsGlobales['taux'] >= 80 ? 'bg-green-200 text-green-800' : ($statsGlobales['taux'] >= 50 ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                            {{ $statsGlobales['taux'] }}%
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </x-card>

                <!-- Graphique d'evolution annuelle -->
                <x-card title="Evolution mensuelle du taux de presence" class="mt-6">
                    <div class="h-72">
                        <canvas id="evolutionAnnuelleChart"></canvas>
                    </div>
                </x-card>
            </div>

            <!-- Statistiques annuelles -->
            <div class="lg:col-span-1 space-y-6">
                <x-card title="Bilan {{ $annee }}">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Mois actifs</span>
                            <span class="text-lg font-bold text-gray-900">{{ $statsGlobales['mois_actifs'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total seances</span>
                            <span class="text-lg font-bold text-primary-600">{{ $statsGlobales['total'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Presents</span>
                            <span class="text-lg font-bold text-green-600">{{ $statsGlobales['presents'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Absents</span>
                            <span class="text-lg font-bold text-red-600">{{ $statsGlobales['absents'] }}</span>
                        </div>
                    </div>
                </x-card>

                <x-card title="Taux annuel">
                    <div class="text-center">
                        <div class="text-5xl font-bold {{ $statsGlobales['taux'] >= 80 ? 'text-green-600' : ($statsGlobales['taux'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $statsGlobales['taux'] }}%
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Moyenne annuelle</p>
                        <div class="mt-4 h-3 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $statsGlobales['taux'] >= 80 ? 'bg-green-500' : ($statsGlobales['taux'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $statsGlobales['taux'] }}%"></div>
                        </div>
                    </div>
                </x-card>

                <x-card title="Meilleurs athletes">
                    <div class="space-y-3">
                        @foreach($topAthletes->take(5) as $index => $top)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($index === 0)
                                        <span class="text-xl mr-2">🥇</span>
                                    @elseif($index === 1)
                                        <span class="text-xl mr-2">🥈</span>
                                    @elseif($index === 2)
                                        <span class="text-xl mr-2">🥉</span>
                                    @else
                                        <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold mr-2">
                                            {{ $index + 1 }}
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-900 truncate max-w-[100px]">{{ $top['nom'] }}</span>
                                </div>
                                <span class="text-sm font-semibold {{ $top['taux'] >= 80 ? 'text-green-600' : ($top['taux'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $top['taux'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <x-card title="Comparaison">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600">Meilleur mois</span>
                            <div class="text-right">
                                <span class="text-sm font-semibold text-green-600">{{ $meilleurMois['nom'] ?? '-' }}</span>
                                <span class="text-xs text-gray-500 block">{{ $meilleurMois['taux'] ?? 0 }}%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600">Pire mois</span>
                            <div class="text-right">
                                <span class="text-sm font-semibold text-red-600">{{ $pireMois['nom'] ?? '-' }}</span>
                                <span class="text-xs text-gray-500 block">{{ $pireMois['taux'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    @elseif($disciplineId)
        <x-card>
            <x-empty-state 
                title="Aucun athlete" 
                description="Aucun athlete inscrit a cette discipline."
            />
        </x-card>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Selectionnez une discipline</h3>
                <p class="mt-2 text-sm text-gray-500">Choisissez une discipline pour voir le bilan annuel.</p>
            </div>
        </x-card>
    @endif

    @if($disciplineId && $athletes->count() > 0)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('evolutionAnnuelleChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_values($moisLabels)) !!},
                datasets: [{
                    label: 'Taux de presence (%)',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: {!! json_encode($chartColors) !!},
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
    @endif
@endsection
