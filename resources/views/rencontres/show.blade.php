@section('title', 'Match vs ' . $rencontre->adversaire)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('rencontres.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">OBD vs {{ $rencontre->adversaire }}</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $rencontre->discipline->nom }} - {{ $rencontre->date_match->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                @if($rencontre->isSportIndividuel())
                    <x-button href="{{ route('combats-taekwondo.index', $rencontre) }}" variant="secondary">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Gérer les combats
                    </x-button>
                @else
                    <x-button href="{{ route('rencontres.participations', $rencontre) }}" variant="secondary">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Gérer les joueurs
                    </x-button>
                @endif
                <x-button href="{{ route('rencontres.edit', $rencontre) }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Score principal -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-xl shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <!-- Équipe OBD -->
                <div class="flex-1 text-center">
                    <div class="w-16 h-16 mx-auto bg-white rounded-full flex items-center justify-center mb-2">
                        <img src="{{ asset('images/logo_obd.jpg') }}" alt="OBD" class="w-12 h-12 rounded-full">
                    </div>
                    <h3 class="text-xl font-bold">OBD</h3>
                    <span class="text-sm opacity-75">{{ $rencontre->type_match === 'domicile' ? 'Domicile' : 'Exterieur' }}</span>
                </div>

                <!-- Score -->
                <div class="flex-1 text-center">
                    @if($rencontre->resultat === 'a_jouer')
                        <div class="text-4xl font-bold mb-2">VS</div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20">
                            A jouer
                        </span>
                    @else
                        <div class="flex items-center justify-center gap-4">
                            <span class="text-5xl font-bold">{{ $rencontre->score_obd }}</span>
                            <span class="text-2xl opacity-50">-</span>
                            <span class="text-5xl font-bold">{{ $rencontre->score_adversaire }}</span>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium mt-2
                            {{ $rencontre->resultat === 'victoire' ? 'bg-green-500' : ($rencontre->resultat === 'defaite' ? 'bg-red-500' : 'bg-yellow-500') }}">
                            {{ $rencontre->resultat_libelle }}
                        </span>
                    @endif
                </div>

                <!-- Adversaire -->
                <div class="flex-1 text-center">
                    <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-2">
                        <svg class="w-10 h-10 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold">{{ $rencontre->adversaire }}</h3>
                    <span class="text-sm opacity-75">{{ $rencontre->type_match === 'exterieur' ? 'Domicile' : 'Exterieur' }}</span>
                </div>
            </div>

            <!-- Infos match -->
            <div class="mt-6 pt-4 border-t border-white/20 flex flex-wrap justify-center gap-6 text-sm">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $rencontre->date_match->format('d/m/Y') }}
                    @if($rencontre->heure_match)
                        a {{ \Carbon\Carbon::parse($rencontre->heure_match)->format('H:i') }}
                    @endif
                </span>
                @if($rencontre->lieu)
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    {{ $rencontre->lieu }}
                </span>
                @endif
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ $rencontre->type_competition_libelle }}
                    @if($rencontre->nom_competition)
                        - {{ $rencontre->nom_competition }}
                    @endif
                </span>
                @if($rencontre->phase)
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    {{ $rencontre->phase_libelle }}
                </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Statistiques du match -->
            <div class="lg:col-span-2">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques du match</h3>
                    
                    @if($rencontre->participations->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <div class="text-2xl font-bold text-primary-600">{{ $statsMatch['nb_participants'] }}</div>
                                <div class="text-sm text-gray-500">Joueurs</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $statsMatch['total_points'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Points marques</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $statsMatch['total_passes'] ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Passes decisives</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ number_format($statsMatch['moyenne_note'] ?? 0, 1) }}</div>
                                <div class="text-sm text-gray-500">Note moyenne</div>
                            </div>
                        </div>

                        <!-- Liste des joueurs -->
                        <h4 class="font-medium text-gray-900 mb-3">Composition de l'equipe</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joueur</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Min</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Pts</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Passes</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Reb</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Note</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rencontre->participations->sortByDesc('titulaire') as $participation)
                                        <tr class="{{ $participation->titulaire ? 'bg-green-50' : '' }}">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center">
                                                    @if($participation->athlete)
                                                    <a href="{{ route('athletes.show', $participation->athlete) }}" class="font-medium text-gray-900 hover:text-primary-600">
                                                        {{ $participation->athlete->nom_complet }}
                                                    </a>
                                                    @else
                                                    <span class="text-gray-400">Athlete supprime</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($participation->titulaire)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Titulaire</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">Remplacant</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm">{{ $participation->minutes_jouees ?? '-' }}</td>
                                            <td class="px-4 py-3 text-center text-sm font-medium">{{ $participation->points_marques ?? '-' }}</td>
                                            <td class="px-4 py-3 text-center text-sm">{{ $participation->passes_decisives ?? '-' }}</td>
                                            <td class="px-4 py-3 text-center text-sm">{{ $participation->rebonds ?? '-' }}</td>
                                            <td class="px-4 py-3 text-center">
                                                @if($participation->note_performance)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                        {{ $participation->note_performance >= 7 ? 'bg-green-100 text-green-800' : 
                                                           ($participation->note_performance >= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ number_format($participation->note_performance, 1) }}/10
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
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun joueur enregistre</h3>
                            <p class="mt-1 text-sm text-gray-500">Ajoutez les joueurs qui ont participe a ce match.</p>
                            @if(auth()->user()->isAdmin())
                            <div class="mt-4">
                                <x-button href="{{ route('rencontres.participations', $rencontre) }}" variant="primary">
                                    Ajouter des joueurs
                                </x-button>
                            </div>
                            @endif
                        </div>
                    @endif
                </x-card>

                <!-- Remarques -->
                @if($rencontre->remarques)
                <x-card class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Remarques</h3>
                    <p class="text-gray-600">{{ $rencontre->remarques }}</p>
                </x-card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Meilleur marqueur -->
                @if($meilleurMarqueur && $meilleurMarqueur->points_marques > 0)
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Meilleur marqueur</h3>
                    <div class="text-center">
                        <div class="w-20 h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        @if($meilleurMarqueur->athlete)
                        <h4 class="font-bold text-gray-900">{{ $meilleurMarqueur->athlete->nom_complet }}</h4>
                        <p class="text-3xl font-bold text-primary-600 mt-2">{{ $meilleurMarqueur->points_marques }} pts</p>
                        @endif
                    </div>
                </x-card>
                @endif

                <!-- Historique contre cet adversaire -->
                @if($historique->count() > 0)
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique vs {{ $rencontre->adversaire }}</h3>
                    <div class="space-y-3">
                        @foreach($historique as $match)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm text-gray-500">{{ $match->date_match->format('d/m/Y') }}</div>
                                    <div class="font-medium">{{ $match->score_obd }} - {{ $match->score_adversaire }}</div>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    {{ $match->resultat === 'victoire' ? 'bg-green-100 text-green-800' : 
                                       ($match->resultat === 'defaite' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $match->resultat_libelle }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </x-card>
                @endif

                <!-- Actions admin -->
                @if(auth()->user()->isAdmin())
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-2">
                        <x-button href="{{ route('rencontres.edit', $rencontre) }}" variant="secondary" class="w-full justify-center">
                            Modifier le match
                        </x-button>
                        <form action="{{ route('rencontres.destroy', $rencontre) }}" method="POST" onsubmit="return confirm('Etes-vous sur de vouloir supprimer ce match ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                                Supprimer le match
                            </button>
                        </form>
                    </div>
                </x-card>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
