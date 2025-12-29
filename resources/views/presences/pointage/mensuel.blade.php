@extends('presences.pointage.index')

@section('periode-filters')
    <div class="flex-1 min-w-[150px]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Mois</label>
        <x-select 
            name="mois" 
            :options="$moisOptions" 
            :selected="$mois"
        />
    </div>
    
    <div class="flex-1 min-w-[100px]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Annee</label>
        <x-select 
            name="annee" 
            :options="$anneeOptions" 
            :selected="$annee"
        />
    </div>
    
    <div class="flex items-end gap-2">
        <a href="{{ route('presences.pointage.mensuel', ['mois' => $moisPrecedent['mois'], 'annee' => $moisPrecedent['annee'], 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Mois precedent">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <a href="{{ route('presences.pointage.mensuel', ['mois' => now()->month, 'annee' => now()->year, 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
            Ce mois
        </a>
        <a href="{{ route('presences.pointage.mensuel', ['mois' => $moisSuivant['mois'], 'annee' => $moisSuivant['annee'], 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Mois suivant">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
@endsection

@section('pointage-content')
    @if($disciplineId && $athletes->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Tableau mensuel -->
            <div class="lg:col-span-3">
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $nomMois }} {{ $annee }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $selectedDiscipline->nom ?? 'Discipline' }} - {{ $athletes->count() }} athletes</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 min-w-[180px]">Athlete</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">S1</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">S2</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">S3</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">S4</th>
                                    @if($nbSemaines > 4)
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">S5</th>
                                    @endif
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">Presents</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">Absents</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">Taux</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($athletes as $athlete)
                                    @php
                                        $stats = $athleteStats[$athlete->id] ?? ['semaines' => [], 'presents' => 0, 'absents' => 0, 'total' => 0, 'taux' => 0];
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
                                        @for($s = 1; $s <= $nbSemaines; $s++)
                                            @php
                                                $semaine = $stats['semaines'][$s] ?? ['presents' => 0, 'total' => 0, 'taux' => 0];
                                            @endphp
                                            <td class="px-3 py-3 text-center">
                                                <div class="flex flex-col items-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                        {{ $semaine['taux'] >= 80 ? 'bg-green-100 text-green-800' : ($semaine['taux'] >= 50 ? 'bg-yellow-100 text-yellow-800' : ($semaine['total'] > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-500')) }}">
                                                        {{ $semaine['presents'] }}/{{ $semaine['total'] }}
                                                    </span>
                                                </div>
                                            </td>
                                        @endfor
                                        <td class="px-4 py-3 text-center bg-gray-50">
                                            <span class="font-semibold text-green-600">{{ $stats['presents'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center bg-gray-50">
                                            <span class="font-semibold text-red-600">{{ $stats['absents'] }}</span>
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
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 sticky left-0 bg-gray-100 z-10">Total</td>
                                    @for($s = 1; $s <= $nbSemaines; $s++)
                                        @php
                                            $semaineTotale = $statsParSemaine[$s] ?? ['presents' => 0, 'total' => 0, 'taux' => 0];
                                        @endphp
                                        <td class="px-3 py-3 text-center">
                                            <span class="text-xs font-medium text-gray-700">{{ $semaineTotale['taux'] }}%</span>
                                        </td>
                                    @endfor
                                    <td class="px-4 py-3 text-center font-semibold text-green-600">{{ $statsGlobales['presents'] }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-red-600">{{ $statsGlobales['absents'] }}</td>
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

                <!-- Graphique d'evolution -->
                <x-card title="Evolution du taux de presence" class="mt-6">
                    <div class="h-64">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </x-card>
            </div>

            <!-- Statistiques du mois -->
            <div class="lg:col-span-1 space-y-6">
                <x-card title="Resume du mois">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Jours de seance</span>
                            <span class="text-lg font-bold text-gray-900">{{ $statsGlobales['jours_seance'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total pointages</span>
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

                <x-card title="Taux de presence">
                    <div class="text-center">
                        <div class="text-4xl font-bold {{ $statsGlobales['taux'] >= 80 ? 'text-green-600' : ($statsGlobales['taux'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $statsGlobales['taux'] }}%
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $nomMois }} {{ $annee }}</p>
                        <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $statsGlobales['taux'] >= 80 ? 'bg-green-500' : ($statsGlobales['taux'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $statsGlobales['taux'] }}%"></div>
                        </div>
                    </div>
                </x-card>

                <x-card title="Top 5 assidus">
                    <div class="space-y-3">
                        @foreach($topAthletes->take(5) as $index => $top)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-6 h-6 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-500')) }} flex items-center justify-center text-xs font-bold mr-2">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-sm text-gray-900 truncate max-w-[100px]">{{ $top['nom'] }}</span>
                                </div>
                                <span class="text-sm font-semibold {{ $top['taux'] >= 80 ? 'text-green-600' : ($top['taux'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $top['taux'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <x-card title="Athletes en difficulte">
                    <div class="space-y-3">
                        @forelse($athletesEnDifficulte->take(5) as $athlete)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-900 truncate max-w-[120px]">{{ $athlete['nom'] }}</span>
                                <span class="text-sm font-semibold text-red-600">{{ $athlete['taux'] }}%</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center">Aucun athlete en difficulte</p>
                        @endforelse
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Selectionnez une discipline</h3>
                <p class="mt-2 text-sm text-gray-500">Choisissez une discipline pour voir le suivi mensuel.</p>
            </div>
        </x-card>
    @endif

    @if($disciplineId && $athletes->count() > 0)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('evolutionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Taux de presence (%)',
                    data: {!! json_encode($chartData) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.3,
                    fill: true
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
