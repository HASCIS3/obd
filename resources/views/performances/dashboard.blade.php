@section('title', 'Tableau de bord des performances')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tableau de bord des performances</h2>
                <p class="mt-1 text-sm text-gray-500">
                    @if($discipline)
                        {{ $discipline->nom }}
                    @else
                        Vue globale de toutes les disciplines
                    @endif
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <form action="{{ route('performances.dashboard') }}" method="GET" class="flex items-center gap-2">
                    <select name="discipline" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" onchange="this.form.submit()">
                        <option value="">Toutes les disciplines</option>
                        @foreach($disciplines as $disc)
                            <option value="{{ $disc->id }}" {{ $disciplineId == $disc->id ? 'selected' : '' }}>{{ $disc->nom }}</option>
                        @endforeach
                    </select>
                </form>
                <x-button href="{{ route('performances.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle performance
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Matchs -->
            <x-card class="bg-blue-50 border-blue-200">
                <div class="text-center">
                    <div class="text-3xl mb-2">⚽</div>
                    <p class="text-sm font-medium text-blue-600">Matchs joues</p>
                    <p class="text-3xl font-bold text-blue-800">{{ $statsEquipe['matchs']['total'] }}</p>
                    <div class="mt-2 flex justify-center gap-2 text-xs">
                        <span class="text-green-600">{{ $statsEquipe['matchs']['victoires'] }} V</span>
                        <span class="text-red-600">{{ $statsEquipe['matchs']['defaites'] }} D</span>
                        <span class="text-yellow-600">{{ $statsEquipe['matchs']['nuls'] }} N</span>
                    </div>
                </div>
            </x-card>

            <!-- Taux de victoire -->
            <x-card class="bg-green-50 border-green-200">
                <div class="text-center">
                    <div class="text-3xl mb-2">🏆</div>
                    <p class="text-sm font-medium text-green-600">Taux de victoire</p>
                    <p class="text-3xl font-bold text-green-800">{{ $statsEquipe['matchs']['taux_victoire'] }}%</p>
                    <div class="mt-2 text-xs text-green-600">
                        {{ $statsEquipe['matchs']['points_marques'] }} pts marques / {{ $statsEquipe['matchs']['points_encaisses'] }} encaisses
                    </div>
                </div>
            </x-card>

            <!-- Médailles -->
            <x-card class="bg-yellow-50 border-yellow-200">
                <div class="text-center">
                    <div class="text-3xl mb-2">🏅</div>
                    <p class="text-sm font-medium text-yellow-600">Medailles</p>
                    <p class="text-3xl font-bold text-yellow-800">{{ $statsEquipe['competitions']['total_medailles'] }}</p>
                    <div class="mt-2 flex justify-center gap-2 text-xs">
                        <span>🥇 {{ $statsEquipe['competitions']['medailles_or'] }}</span>
                        <span>🥈 {{ $statsEquipe['competitions']['medailles_argent'] }}</span>
                        <span>🥉 {{ $statsEquipe['competitions']['medailles_bronze'] }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Note moyenne -->
            <x-card class="bg-purple-50 border-purple-200">
                <div class="text-center">
                    <div class="text-3xl mb-2">📊</div>
                    <p class="text-sm font-medium text-purple-600">Note moyenne</p>
                    <p class="text-3xl font-bold text-purple-800">{{ $statsEquipe['notes']['moyenne_globale'] }}/10</p>
                    <div class="mt-2 text-xs text-purple-600">
                        {{ $statsEquipe['total_performances'] }} evaluations
                    </div>
                </div>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Dernières performances -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dernieres performances</h3>
                
                @if($dernieresPerformances->count() > 0)
                    <div class="space-y-3">
                        @foreach($dernieresPerformances as $perf)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @if($perf->contexte === 'match')
                                            @if($perf->resultat_match === 'victoire')
                                                <span class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-lg">✓</span>
                                            @elseif($perf->resultat_match === 'defaite')
                                                <span class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-lg">✗</span>
                                            @else
                                                <span class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-lg">=</span>
                                            @endif
                                        @elseif($perf->contexte === 'competition')
                                            @if($perf->medaille)
                                                <span class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-lg">
                                                    @if($perf->medaille === 'or') 🥇 @elseif($perf->medaille === 'argent') 🥈 @else 🥉 @endif
                                                </span>
                                            @else
                                                <span class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-lg">🏆</span>
                                            @endif
                                        @elseif($perf->contexte === 'entrainement')
                                            <span class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-lg">🏃</span>
                                        @else
                                            <span class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-lg">📋</span>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ route('athletes.show', $perf->athlete) }}" class="font-medium text-gray-900 hover:text-primary-600">
                                            {{ $perf->athlete->nom_complet }}
                                        </a>
                                        <p class="text-sm text-gray-500">
                                            {{ $perf->contexte_libelle }}
                                            @if($perf->adversaire) vs {{ $perf->adversaire }} @endif
                                            @if($perf->score_match) ({{ $perf->score_match }}) @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $perf->date_evaluation->format('d/m/Y') }}</p>
                                    @if($perf->note_globale)
                                        <p class="text-sm text-gray-500">Note: {{ $perf->note_globale }}/10</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('performances.index') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                            Voir toutes les performances →
                        </a>
                    </div>
                @else
                    <x-empty-state 
                        title="Aucune performance" 
                        description="Aucune performance enregistree pour le moment."
                    />
                @endif
            </x-card>

            <!-- Top athlètes (si discipline sélectionnée) -->
            @if($discipline && $athletesDiscipline->count() > 0)
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top athletes - {{ $discipline->nom }}</h3>
                    
                    <div class="space-y-3">
                        @foreach($athletesDiscipline as $index => $item)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <a href="{{ route('athletes.show', $item['athlete']) }}" class="font-medium text-gray-900 hover:text-primary-600">
                                            {{ $item['athlete']->nom_complet }}
                                        </a>
                                        <p class="text-sm text-gray-500">
                                            {{ $item['stats']['matchs']['victoires'] }} V / {{ $item['stats']['matchs']['defaites'] }} D
                                            @if($item['stats']['competitions']['total_medailles'] > 0)
                                                | {{ $item['stats']['competitions']['total_medailles'] }} medaille(s)
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($item['stats']['notes']['moyenne_globale'] > 0)
                                        <p class="text-lg font-bold text-primary-600">{{ $item['stats']['notes']['moyenne_globale'] }}/10</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @else
                <!-- Résumé par discipline -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Performances par discipline</h3>
                    
                    @if($disciplines->count() > 0)
                        <div class="space-y-3">
                            @foreach($disciplines as $disc)
                                @php
                                    $statsDiscipline = \App\Models\Performance::statistiquesDiscipline($disc->id);
                                @endphp
                                <a href="{{ route('performances.dashboard', ['discipline' => $disc->id]) }}" 
                                   class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center font-bold">
                                            {{ substr($disc->nom, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $disc->nom }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $statsDiscipline['matchs']['total'] }} matchs |
                                                {{ $statsDiscipline['competitions']['total_medailles'] }} medailles
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold {{ $statsDiscipline['matchs']['taux_victoire'] >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $statsDiscipline['matchs']['taux_victoire'] }}%
                                        </p>
                                        <p class="text-xs text-gray-500">taux victoire</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <x-empty-state 
                            title="Aucune discipline" 
                            description="Aucune discipline active."
                        />
                    @endif
                </x-card>
            @endif
        </div>

        <!-- Légende des contextes -->
        <x-card class="mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Types d'evaluations</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">🏃</span>
                    <span class="text-sm text-gray-600">Entrainement</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">⚽</span>
                    <span class="text-sm text-gray-600">Match</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">🏆</span>
                    <span class="text-sm text-gray-600">Competition</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">📋</span>
                    <span class="text-sm text-gray-600">Test physique</span>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>
