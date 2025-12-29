@section('title', 'Matchs & Rencontres')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Matchs & Rencontres</h2>
                <p class="mt-1 text-sm text-gray-500">Gestion des matchs et performances d'équipe</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('rencontres.statistiques') }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Statistiques
                </x-button>
                @if(auth()->user()->isAdmin())
                <x-button href="{{ route('rencontres.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouveau match
                </x-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500">Total matchs</div>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-4 text-center border-l-4 border-green-500">
                <div class="text-2xl font-bold text-green-600">{{ $stats['victoires'] }}</div>
                <div class="text-sm text-green-600">Victoires</div>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-4 text-center border-l-4 border-red-500">
                <div class="text-2xl font-bold text-red-600">{{ $stats['defaites'] }}</div>
                <div class="text-sm text-red-600">Defaites</div>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-4 text-center border-l-4 border-yellow-500">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['nuls'] }}</div>
                <div class="text-sm text-yellow-600">Nuls</div>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-4 text-center border-l-4 border-blue-500">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['a_venir'] }}</div>
                <div class="text-sm text-blue-600">A venir</div>
            </div>
        </div>

        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('rencontres.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <x-input-label for="discipline">Discipline</x-input-label>
                    <select name="discipline" id="discipline" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">Toutes</option>
                        @foreach($disciplines as $discipline)
                            <option value="{{ $discipline->id }}" {{ request('discipline') == $discipline->id ? 'selected' : '' }}>
                                {{ $discipline->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="resultat">Resultat</x-input-label>
                    <select name="resultat" id="resultat" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">Tous</option>
                        @foreach(\App\Models\Rencontre::resultats() as $key => $label)
                            <option value="{{ $key }}" {{ request('resultat') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="type_competition">Competition</x-input-label>
                    <select name="type_competition" id="type_competition" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">Toutes</option>
                        @foreach(\App\Models\Rencontre::typesCompetition() as $key => $label)
                            <option value="{{ $key }}" {{ request('type_competition') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="saison">Saison</x-input-label>
                    <select name="saison" id="saison" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        <option value="">Toutes</option>
                        @foreach($saisons as $s)
                            <option value="{{ $s }}" {{ request('saison') == $s ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">Filtrer</x-button>
                    <a href="{{ route('rencontres.index') }}" class="px-3 py-2 text-gray-600 hover:text-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </a>
                </div>
            </form>
        </x-card>

        <!-- Liste des matchs -->
        <x-card>
            @if($rencontres->count() > 0)
                <div class="space-y-4">
                    @foreach($rencontres as $rencontre)
                        <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors {{ $rencontre->resultat === 'a_jouer' ? 'border-blue-200 bg-blue-50' : '' }}">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <!-- Info match -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $rencontre->discipline->couleur ?? 'gray' }}-100 text-{{ $rencontre->discipline->couleur ?? 'gray' }}-800">
                                            {{ $rencontre->discipline->nom }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $rencontre->date_match->format('d/m/Y') }}
                                            @if($rencontre->heure_match)
                                                a {{ \Carbon\Carbon::parse($rencontre->heure_match)->format('H:i') }}
                                            @endif
                                        </span>
                                        @if($rencontre->phase)
                                            <span class="text-xs text-gray-400">{{ $rencontre->phase_libelle }}</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Équipes et score -->
                                    <div class="flex items-center gap-4">
                                        <div class="flex-1 text-right">
                                            <span class="font-semibold text-lg {{ $rencontre->type_match === 'domicile' ? 'text-primary-600' : 'text-gray-700' }}">
                                                OBD
                                            </span>
                                            @if($rencontre->type_match === 'domicile')
                                                <span class="text-xs text-gray-400 ml-1">(Dom)</span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex items-center gap-2 px-4 py-2 rounded-lg {{ 
                                            $rencontre->resultat === 'victoire' ? 'bg-green-100' : 
                                            ($rencontre->resultat === 'defaite' ? 'bg-red-100' : 
                                            ($rencontre->resultat === 'nul' ? 'bg-yellow-100' : 'bg-gray-100')) 
                                        }}">
                                            @if($rencontre->resultat === 'a_jouer')
                                                <span class="text-xl font-bold text-gray-500">VS</span>
                                            @else
                                                <span class="text-xl font-bold {{ $rencontre->resultat === 'victoire' ? 'text-green-600' : ($rencontre->resultat === 'defaite' ? 'text-red-600' : 'text-yellow-600') }}">
                                                    {{ $rencontre->score_obd }}
                                                </span>
                                                <span class="text-gray-400">-</span>
                                                <span class="text-xl font-bold {{ $rencontre->resultat === 'defaite' ? 'text-green-600' : ($rencontre->resultat === 'victoire' ? 'text-red-600' : 'text-yellow-600') }}">
                                                    {{ $rencontre->score_adversaire }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="flex-1">
                                            <span class="font-semibold text-lg {{ $rencontre->type_match === 'exterieur' ? 'text-gray-700' : 'text-gray-700' }}">
                                                {{ $rencontre->adversaire }}
                                            </span>
                                            @if($rencontre->type_match === 'exterieur')
                                                <span class="text-xs text-gray-400 ml-1">(Ext)</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Infos supplémentaires -->
                                    <div class="mt-2 flex items-center gap-4 text-sm text-gray-500">
                                        @if($rencontre->nom_competition)
                                            <span>{{ $rencontre->nom_competition }}</span>
                                        @else
                                            <span>{{ $rencontre->type_competition_libelle }}</span>
                                        @endif
                                        @if($rencontre->lieu)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $rencontre->lieu }}
                                            </span>
                                        @endif
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ $rencontre->nb_participants }} joueurs
                                        </span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('rencontres.show', $rencontre) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Details
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('rencontres.participations', $rencontre) }}" class="inline-flex items-center px-3 py-2 border border-primary-300 rounded-lg text-sm font-medium text-primary-700 bg-primary-50 hover:bg-primary-100">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            Joueurs
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $rencontres->links() }}
                </div>
            @else
                <x-empty-state 
                    title="Aucun match enregistre" 
                    description="Commencez par enregistrer un match."
                    :action="route('rencontres.create')"
                    actionText="Nouveau match"
                />
            @endif
        </x-card>
    </div>
</x-app-layout>
