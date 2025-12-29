@section('title', 'Statistiques des matchs')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('rencontres.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Statistiques des matchs</h2>
                <p class="mt-1 text-sm text-gray-500">Analyse des performances de l'equipe</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('rencontres.statistiques') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="discipline">Discipline</x-input-label>
                    <x-select name="discipline" id="discipline">
                        <option value="">Toutes les disciplines</option>
                        @foreach($disciplines as $discipline)
                            <option value="{{ $discipline->id }}" {{ $disciplineId == $discipline->id ? 'selected' : '' }}>
                                {{ $discipline->nom }}
                            </option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input-label for="saison">Saison</x-input-label>
                    <x-select name="saison" id="saison">
                        <option value="">Toutes les saisons</option>
                        @foreach($saisons as $s)
                            <option value="{{ $s }}" {{ $saison == $s ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </x-select>
                </div>
                <div class="flex items-end">
                    <x-button type="submit" variant="primary" class="w-full">Filtrer</x-button>
                </div>
            </form>
        </x-card>

        <!-- Statistiques globales -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <x-card class="text-center">
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total_matchs'] }}</div>
                <div class="text-sm text-gray-500">Matchs joues</div>
            </x-card>
            <x-card class="text-center bg-green-50 border-green-200">
                <div class="text-3xl font-bold text-green-600">{{ $stats['victoires'] }}</div>
                <div class="text-sm text-green-600">Victoires</div>
            </x-card>
            <x-card class="text-center bg-red-50 border-red-200">
                <div class="text-3xl font-bold text-red-600">{{ $stats['defaites'] }}</div>
                <div class="text-sm text-red-600">Defaites</div>
            </x-card>
            <x-card class="text-center bg-yellow-50 border-yellow-200">
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['nuls'] }}</div>
                <div class="text-sm text-yellow-600">Matchs nuls</div>
            </x-card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pourcentage de victoires -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Taux de reussite</h3>
                <div class="flex items-center justify-center">
                    <div class="relative w-40 h-40">
                        <svg class="w-40 h-40 transform -rotate-90">
                            <circle cx="80" cy="80" r="70" stroke="#e5e7eb" stroke-width="12" fill="none" />
                            <circle cx="80" cy="80" r="70" stroke="#16a34a" stroke-width="12" fill="none"
                                stroke-dasharray="{{ 2 * 3.14159 * 70 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 70 * (1 - $stats['pourcentage_victoires'] / 100) }}"
                                stroke-linecap="round" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-3xl font-bold text-gray-900">{{ $stats['pourcentage_victoires'] }}%</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-center text-sm text-gray-500">
                    {{ $stats['victoires'] }} victoires sur {{ $stats['victoires'] + $stats['defaites'] + $stats['nuls'] }} matchs
                </div>
            </x-card>

            <!-- Buts -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bilan des points/buts</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div>
                            <div class="text-sm text-green-600">Points/Buts marques</div>
                            <div class="text-2xl font-bold text-green-700">{{ $stats['buts_marques'] }}</div>
                        </div>
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                        <div>
                            <div class="text-sm text-red-600">Points/Buts encaisses</div>
                            <div class="text-2xl font-bold text-red-700">{{ $stats['buts_encaisses'] }}</div>
                        </div>
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </div>
                    <div class="flex items-center justify-between p-4 {{ $stats['difference_buts'] >= 0 ? 'bg-blue-50' : 'bg-orange-50' }} rounded-lg">
                        <div>
                            <div class="text-sm {{ $stats['difference_buts'] >= 0 ? 'text-blue-600' : 'text-orange-600' }}">Difference</div>
                            <div class="text-2xl font-bold {{ $stats['difference_buts'] >= 0 ? 'text-blue-700' : 'text-orange-700' }}">
                                {{ $stats['difference_buts'] >= 0 ? '+' : '' }}{{ $stats['difference_buts'] }}
                            </div>
                        </div>
                        <svg class="w-10 h-10 {{ $stats['difference_buts'] >= 0 ? 'text-blue-500' : 'text-orange-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Meilleurs joueurs -->
        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Meilleurs joueurs</h3>
            
            @if($meilleursJoueurs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joueur</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Matchs</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Points</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Moyenne pts/match</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note moyenne</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($meilleursJoueurs as $index => $joueur)
                                <tr class="{{ $index < 3 ? 'bg-yellow-50' : '' }}">
                                    <td class="px-4 py-3">
                                        @if($index === 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-400 text-white font-bold">1</span>
                                        @elseif($index === 1)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-white font-bold">2</span>
                                        @elseif($index === 2)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-400 text-white font-bold">3</span>
                                        @else
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-500 font-medium">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($joueur->athlete)
                                            <a href="{{ route('athletes.show', $joueur->athlete) }}" class="font-medium text-gray-900 hover:text-primary-600">
                                                {{ $joueur->athlete->nom_complet }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">Athlete supprime</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $joueur->nb_matchs }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-primary-600">{{ $joueur->total_points ?? 0 }}</td>
                                    <td class="px-4 py-3 text-center">
                                        {{ $joueur->nb_matchs > 0 ? number_format(($joueur->total_points ?? 0) / $joueur->nb_matchs, 1) : 0 }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($joueur->moyenne_note)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                {{ $joueur->moyenne_note >= 7 ? 'bg-green-100 text-green-800' : 
                                                   ($joueur->moyenne_note >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ number_format($joueur->moyenne_note, 1) }}/10
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    Aucune donnee de performance disponible
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>

