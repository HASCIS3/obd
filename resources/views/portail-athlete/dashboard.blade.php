@extends('portail-athlete.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- En-tête de bienvenue -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center">
            @if($athlete->photo)
                <img src="{{ asset('storage/' . $athlete->photo) }}" alt="{{ $athlete->nom_complet }}" class="h-16 w-16 rounded-full object-cover border-4 border-white">
            @else
                <div class="h-16 w-16 rounded-full bg-white flex items-center justify-center border-4 border-green-200">
                    <span class="text-green-700 font-bold text-2xl">{{ substr($athlete->prenom, 0, 1) }}</span>
                </div>
            @endif
            <div class="ml-4">
                <h1 class="text-2xl font-bold text-white">Salut, {{ $athlete->prenom }} !</h1>
                <p class="text-green-100">{{ $athlete->disciplines->first()->nom ?? 'Olympiade de Baco-Djicoroni' }}</p>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $stats['presences_mois'] }}</div>
            <div class="text-xs text-gray-500">Presences</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-red-600">{{ $stats['absences_mois'] }}</div>
            <div class="text-xs text-gray-500">Absences</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-3xl font-bold text-yellow-600">{{ number_format($stats['paiements_en_attente'], 0, ',', ' ') }}</div>
            <div class="text-xs text-gray-500">FCFA</div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Acces Rapide</h2>
        </div>
        <div class="grid grid-cols-4 gap-2 p-4">
            <a href="{{ route('athlete.presences') }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50">
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <span class="text-xs text-gray-600 text-center">Presences</span>
            </a>
            <a href="{{ route('athlete.suivi-scolaire') }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-xs text-gray-600 text-center">Scolaire</span>
            </a>
            <a href="{{ route('athlete.paiements') }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50">
                <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-xs text-gray-600 text-center">Paiements</span>
            </a>
            <a href="{{ route('athlete.performances') }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-gray-50">
                <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span class="text-xs text-gray-600 text-center">Performances</span>
            </a>
        </div>
    </div>

    <!-- Dernieres presences -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Mes Dernieres Presences</h2>
            <a href="{{ route('athlete.presences') }}" class="text-green-600 text-sm hover:underline">Voir tout</a>
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
                        <div class="font-medium text-gray-900">{{ $presence->date->format('l d F Y') }}</div>
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
