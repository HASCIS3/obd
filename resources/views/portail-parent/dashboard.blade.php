@extends('portail-parent.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- En-tête de bienvenue -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg p-6 mb-6 text-white">
        <h1 class="text-2xl font-bold">Bonjour, {{ $parent->nom_complet }} !</h1>
        <p class="text-green-100 mt-1">Bienvenue sur le portail parent de l'Olympiade de Baco-Djicoroni</p>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $stats['nombre_enfants'] }}</div>
            <div class="text-sm text-gray-500">Enfant(s)</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $stats['presences_mois'] }}</div>
            <div class="text-sm text-gray-500">Presences ce mois</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-red-600">{{ $stats['absences_mois'] }}</div>
            <div class="text-sm text-gray-500">Absences ce mois</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-yellow-600">{{ number_format($stats['paiements_en_attente'], 0, ',', ' ') }}</div>
            <div class="text-sm text-gray-500">FCFA a payer</div>
        </div>
    </div>

    <!-- Mes enfants -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Mes Enfants</h2>
            <a href="{{ route('parent.enfants') }}" class="text-green-600 text-sm hover:underline">Voir tout</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($enfants as $enfant)
                <a href="{{ route('parent.enfants.show', $enfant) }}" class="flex items-center p-4 hover:bg-gray-50 transition">
                    <div class="flex-shrink-0">
                        @if($enfant->photo)
                            <img src="{{ asset('storage/' . $enfant->photo) }}" alt="{{ $enfant->nom_complet }}" class="h-12 w-12 rounded-full object-cover">
                        @else
                            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-bold text-lg">{{ substr($enfant->prenom, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="font-medium text-gray-900">{{ $enfant->nom_complet }}</div>
                        <div class="text-sm text-gray-500">{{ $enfant->disciplines->first()->nom ?? 'Non assigne' }}</div>
                    </div>
                    <div class="text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @empty
                <div class="p-4 text-center text-gray-500">
                    Aucun enfant enregistre
                </div>
            @endforelse
        </div>
    </div>

    <!-- Dernieres presences -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Dernieres Presences</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($dernieresPresences as $presence)
                <div class="flex items-center p-4">
                    <div class="flex-shrink-0">
                        @if($presence->present)
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="font-medium text-gray-900">{{ $presence->athlete->prenom ?? 'Athlete' }}</div>
                        <div class="text-sm text-gray-500">{{ $presence->date->format('d/m/Y') }}</div>
                    </div>
                    <div class="text-sm {{ $presence->present ? 'text-green-600' : 'text-red-600' }}">
                        {{ $presence->present ? 'Present' : 'Absent' }}
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    Aucune presence enregistree
                </div>
            @endforelse
        </div>
    </div>

    <!-- Prochain evenement -->
    @if($prochainEvenement)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Prochain evenement</h3>
                    <div class="mt-1 text-sm text-yellow-700">
                        <p class="font-semibold">{{ $prochainEvenement->titre }}</p>
                        <p>{{ $prochainEvenement->date_debut->format('d/m/Y') }} - {{ $prochainEvenement->lieu ?? 'Lieu a confirmer' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
