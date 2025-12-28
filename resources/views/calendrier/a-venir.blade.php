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
        @if($evenements->count() > 0)
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
        @else
            <x-card>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun événement à venir</h3>
                    <p class="mt-1 text-sm text-gray-500">Créez un nouvel événement depuis le calendrier.</p>
                    <div class="mt-6">
                        <x-button href="{{ route('calendrier.index') }}" variant="primary">
                            Voir le calendrier
                        </x-button>
                    </div>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
