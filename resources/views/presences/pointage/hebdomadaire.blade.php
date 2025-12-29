@extends('presences.pointage.index')

@section('periode-filters')
    <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Semaine du</label>
        <x-input type="date" name="date" :value="$startOfWeek->format('Y-m-d')" />
    </div>
    
    <div class="flex items-end gap-2">
        <a href="{{ route('presences.pointage.hebdomadaire', ['date' => $startOfWeek->copy()->subWeek()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Semaine precedente">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <a href="{{ route('presences.pointage.hebdomadaire', ['date' => now()->startOfWeek()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
            Cette semaine
        </a>
        <a href="{{ route('presences.pointage.hebdomadaire', ['date' => $startOfWeek->copy()->addWeek()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Semaine suivante">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
@endsection

@section('pointage-content')
    @if($disciplineId && $athletes->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Tableau hebdomadaire -->
            <div class="lg:col-span-3">
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Semaine du {{ $startOfWeek->locale('fr')->isoFormat('D MMMM') }} au {{ $endOfWeek->locale('fr')->isoFormat('D MMMM YYYY') }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $selectedDiscipline->nom ?? 'Discipline' }} - {{ $athletes->count() }} athletes</p>
                        </div>
                        <div class="flex gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Semaine {{ $startOfWeek->weekOfYear }}
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10">Athlete</th>
                                    @foreach($joursSemaine as $jour)
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[80px]">
                                            <div>{{ $jour['nom'] }}</div>
                                            <div class="text-gray-400 font-normal">{{ $jour['date']->format('d/m') }}</div>
                                        </th>
                                    @endforeach
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">Total</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-100">Taux</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($athletes as $athlete)
                                    @php
                                        $stats = $athleteStats[$athlete->id] ?? ['presences' => [], 'total' => 0, 'presents' => 0, 'taux' => 0];
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
                                        @foreach($joursSemaine as $jour)
                                            @php
                                                $dateKey = $jour['date']->format('Y-m-d');
                                                $presence = $stats['presences'][$dateKey] ?? null;
                                            @endphp
                                            <td class="px-3 py-3 text-center">
                                                @if($presence === true)
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-600">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </span>
                                                @elseif($presence === false)
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                        </svg>
                                                    </span>
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
                        </table>
                    </div>

                    <div class="mt-4 pt-4 border-t flex justify-between items-center">
                        <p class="text-sm text-gray-500">
                            Cliquez sur un jour pour effectuer le pointage quotidien
                        </p>
                        <div class="flex gap-2">
                            @foreach($joursSemaine as $jour)
                                <a href="{{ route('presences.pointage.quotidien', ['date' => $jour['date']->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
                                   class="px-2 py-1 text-xs font-medium text-primary-600 hover:text-primary-800 hover:bg-primary-50 rounded">
                                    {{ $jour['nom_court'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Statistiques de la semaine -->
            <div class="lg:col-span-1 space-y-6">
                <x-card title="Resume de la semaine">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total seances</span>
                            <span class="text-lg font-bold text-gray-900">{{ $statsGlobales['total_seances'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total pointages</span>
                            <span class="text-lg font-bold text-primary-600">{{ $statsGlobales['total_pointages'] }}</span>
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
                        <p class="text-sm text-gray-500 mt-1">Cette semaine</p>
                        <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $statsGlobales['taux'] >= 80 ? 'bg-green-500' : ($statsGlobales['taux'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $statsGlobales['taux'] }}%"></div>
                        </div>
                    </div>
                </x-card>

                <x-card title="Top 3 assidus">
                    <div class="space-y-3">
                        @foreach($topAthletes as $index => $top)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span class="w-6 h-6 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-600') }} flex items-center justify-center text-xs font-bold mr-2">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-sm text-gray-900 truncate max-w-[120px]">{{ $top['nom'] }}</span>
                                </div>
                                <span class="text-sm font-semibold {{ $top['taux'] >= 80 ? 'text-green-600' : 'text-yellow-600' }}">{{ $top['taux'] }}%</span>
                            </div>
                        @endforeach
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Selectionnez une discipline</h3>
                <p class="mt-2 text-sm text-gray-500">Choisissez une discipline pour voir le suivi hebdomadaire.</p>
            </div>
        </x-card>
    @endif
@endsection
