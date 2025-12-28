@extends('portail-parent.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Calendrier des Evenements</h1>

    <!-- Liste des evenements -->
    <div class="space-y-4">
        @forelse($evenements as $evenement)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="flex">
                    <!-- Date -->
                    <div class="flex-shrink-0 w-20 bg-green-600 text-white text-center py-4">
                        <div class="text-2xl font-bold">{{ $evenement->date_debut->format('d') }}</div>
                        <div class="text-sm uppercase">{{ $evenement->date_debut->format('M') }}</div>
                        <div class="text-xs">{{ $evenement->date_debut->format('Y') }}</div>
                    </div>
                    
                    <!-- Contenu -->
                    <div class="flex-1 p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $evenement->titre }}</h3>
                                @if($evenement->lieu)
                                    <p class="text-sm text-gray-500 flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $evenement->lieu }}
                                    </p>
                                @endif
                            </div>
                            @if($evenement->type)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($evenement->type) }}
                                </span>
                            @endif
                        </div>
                        
                        @if($evenement->description)
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit($evenement->description, 100) }}</p>
                        @endif

                        @if($evenement->heure_debut)
                            <p class="text-xs text-gray-500 mt-2">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $evenement->heure_debut }}
                                @if($evenement->heure_fin) - {{ $evenement->heure_fin }} @endif
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500">Aucun evenement a venir</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
