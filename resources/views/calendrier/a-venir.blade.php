@section('title', 'Événements à venir')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Événements à venir</h2>
                <p class="mt-1 text-sm text-gray-500">Prochains événements planifiés</p>
            </div>
            <x-button href="{{ route('calendrier.index') }}" variant="ghost">
                Voir le calendrier
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Matchs à venir -->
        @if(isset($matchsAVenir) && $matchsAVenir->count() > 0)
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Matchs & Rencontres
            </h3>
            <div class="space-y-4 mb-8">
                @foreach($matchsAVenir as $match)
                    <a href="{{ route('rencontres.show', $match) }}" class="block">
                        <x-card class="hover:shadow-md transition-shadow border-l-4 {{ $match->resultat === 'a_jouer' ? 'border-blue-500' : ($match->resultat === 'victoire' ? 'border-green-500' : ($match->resultat === 'defaite' ? 'border-red-500' : 'border-yellow-500')) }}">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-16 text-center">
                                    <div class="text-3xl font-bold text-primary-600">{{ $match->date_match->format('d') }}</div>
                                    <div class="text-sm text-gray-500 uppercase">{{ $match->date_match->translatedFormat('M') }}</div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $match->discipline?->nom ?? 'Match' }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $match->type_match === 'domicile' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $match->type_match === 'domicile' ? 'Domicile' : 'Extérieur' }}
                                        </span>
                                        @if($match->date_match->isToday())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                                Aujourd'hui
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">OBD vs {{ $match->adversaire }}</h3>
                                    <div class="mt-1 text-sm text-gray-500 space-y-1">
                                        @if($match->heure_match)
                                            <p>
                                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($match->heure_match)->format('H:i') }}
                                            </p>
                                        @endif
                                        @if($match->lieu)
                                            <p>
                                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $match->lieu }}
                                            </p>
                                        @endif
                                        @if($match->type_competition)
                                            <p>
                                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                                </svg>
                                                {{ ucfirst($match->type_competition) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    @if($match->resultat !== 'a_jouer')
                                        <div class="text-2xl font-bold {{ $match->resultat === 'victoire' ? 'text-green-600' : ($match->resultat === 'defaite' ? 'text-red-600' : 'text-yellow-600') }}">
                                            {{ $match->score_obd }} - {{ $match->score_adversaire }}
                                        </div>
                                        <div class="text-sm {{ $match->resultat === 'victoire' ? 'text-green-600' : ($match->resultat === 'defaite' ? 'text-red-600' : 'text-yellow-600') }}">
                                            {{ $match->resultat_libelle }}
                                        </div>
                                    @else
                                        <div class="text-sm text-blue-600 font-medium">À jouer</div>
                                    @endif
                                </div>
                            </div>
                        </x-card>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Activités -->
        @if(isset($activitesAVenir) && $activitesAVenir->count() > 0)
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Activités & Galeries
            </h3>
            <div class="space-y-4 mb-8">
                @foreach($activitesAVenir as $activite)
                    <a href="{{ route('activities.show', $activite) }}" class="block">
                        <x-card class="hover:shadow-md transition-shadow border-l-4 {{ $activite->type === 'galerie' ? 'border-pink-500' : ($activite->type === 'competition' ? 'border-red-500' : ($activite->type === 'tournoi' ? 'border-orange-500' : 'border-purple-500')) }}">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-16 text-center">
                                    <div class="text-3xl font-bold text-primary-600">{{ $activite->debut->format('d') }}</div>
                                    <div class="text-sm text-gray-500 uppercase">{{ $activite->debut->translatedFormat('M') }}</div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $activite->type === 'galerie' ? 'bg-pink-100 text-pink-800' : ($activite->type === 'competition' ? 'bg-red-100 text-red-800' : ($activite->type === 'tournoi' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800')) }}">
                                            {{ $activite->type_label }}
                                        </span>
                                        @if($activite->discipline)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $activite->discipline->nom }}
                                            </span>
                                        @endif
                                        @if($activite->debut->isToday())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse">
                                                Aujourd'hui
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $activite->titre }}</h3>
                                    <div class="mt-1 text-sm text-gray-500 space-y-1">
                                        <p>
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $activite->debut->format('H:i') }}
                                            @if($activite->fin) - {{ $activite->fin->format('H:i') }}@endif
                                        </p>
                                        @if($activite->lieu)
                                            <p>
                                                <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $activite->lieu }}
                                            </p>
                                        @endif
                                    </div>
                                    @if($activite->description)
                                        <p class="mt-2 text-sm text-gray-600">{{ Str::limit($activite->description, 100) }}</p>
                                    @endif
                                </div>
                                @if($activite->image_url)
                                    <div class="flex-shrink-0">
                                        <img src="{{ $activite->image_url }}" alt="{{ $activite->titre }}" class="w-20 h-20 object-cover rounded-lg">
                                    </div>
                                @endif
                            </div>
                        </x-card>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Événements -->
        @if($evenements->count() > 0)
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Événements
            </h3>
            <div class="space-y-4">
                @foreach($evenements as $evenement)
                    <x-card class="hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-16 text-center">
                                <div class="text-3xl font-bold text-primary-600">{{ $evenement->date_debut->format('d') }}</div>
                                <div class="text-sm text-gray-500 uppercase">{{ $evenement->date_debut->translatedFormat('M') }}</div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $evenement->couleur }}20; color: {{ $evenement->couleur }};">
                                        {{ $evenement->type_label }}
                                    </span>
                                    @if($evenement->est_aujourdhui)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Aujourd'hui
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $evenement->titre }}</h3>
                                <div class="mt-1 text-sm text-gray-500 space-y-1">
                                    @if($evenement->heure_debut)
                                        <p>
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $evenement->heure_debut }}
                                            @if($evenement->heure_fin) - {{ $evenement->heure_fin }}@endif
                                        </p>
                                    @endif
                                    @if($evenement->lieu)
                                        <p>
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $evenement->lieu }}
                                        </p>
                                    @endif
                                    @if($evenement->discipline)
                                        <p>
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            {{ $evenement->discipline->nom }}
                                        </p>
                                    @endif
                                </div>
                                @if($evenement->description)
                                    <p class="mt-2 text-sm text-gray-600">{{ Str::limit($evenement->description, 150) }}</p>
                                @endif
                            </div>
                            <div class="flex-shrink-0">
                                <form action="{{ route('calendrier.destroy', $evenement) }}" method="POST" onsubmit="return confirm('Supprimer cet événement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        @endif

        @if($evenements->count() === 0 && (!isset($matchsAVenir) || $matchsAVenir->count() === 0) && (!isset($activitesAVenir) || $activitesAVenir->count() === 0))
            <x-card>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun événement à venir</h3>
                    <p class="mt-1 text-sm text-gray-500">Créez un nouvel événement depuis le calendrier, planifiez un match ou ajoutez une activité.</p>
                    <div class="mt-6 flex justify-center gap-4">
                        <x-button href="{{ route('calendrier.index') }}" variant="primary">
                            Voir le calendrier
                        </x-button>
                        <x-button href="{{ route('activities.index') }}" variant="secondary">
                            Voir les activités
                        </x-button>
                    </div>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
