@extends('portail-parent.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Retour -->
    <a href="{{ route('parent.enfants') }}" class="inline-flex items-center text-green-600 hover:text-green-700 mb-4">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Retour
    </a>

    <!-- Profil de l'enfant -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
            <div class="flex items-center">
                @if($athlete->photo)
                    <img src="{{ asset('storage/' . $athlete->photo) }}" alt="{{ $athlete->nom_complet }}" class="h-20 w-20 rounded-full object-cover border-4 border-white">
                @else
                    <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center border-4 border-green-200">
                        <span class="text-green-600 font-bold text-3xl">{{ substr($athlete->prenom, 0, 1) }}</span>
                    </div>
                @endif
                <div class="ml-4 text-white">
                    <h1 class="text-2xl font-bold">{{ $athlete->nom_complet }}</h1>
                    <p class="text-green-100">{{ $athlete->disciplines->first()->nom ?? 'Non assigne' }}</p>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Date de naissance</span>
                    <p class="font-medium">{{ $athlete->date_naissance ? $athlete->date_naissance->format('d/m/Y') : 'Non renseigne' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Age</span>
                    <p class="font-medium">{{ $athlete->date_naissance ? $athlete->date_naissance->age . ' ans' : '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Discipline</span>
                    <p class="font-medium">{{ $athlete->disciplines->first()->nom ?? 'Non assigne' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Categorie</span>
                    <p class="font-medium">{{ $athlete->categorie ?? 'Non definie' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques du mois -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['presences_mois'] }}</div>
            <div class="text-xs text-gray-500">Presences</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['absences_mois'] }}</div>
            <div class="text-xs text-gray-500">Absences</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['presences_total'] }}</div>
            <div class="text-xs text-gray-500">Total</div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="divide-y divide-gray-100">
            <a href="{{ route('parent.presences', $athlete) }}" class="flex items-center p-4 hover:bg-gray-50">
                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <div class="font-medium text-gray-900">Presences</div>
                    <div class="text-sm text-gray-500">Historique des presences et absences</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('parent.suivi-scolaire', $athlete) }}" class="flex items-center p-4 hover:bg-gray-50">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <div class="font-medium text-gray-900">Suivi Scolaire</div>
                    <div class="text-sm text-gray-500">Notes et bulletins scolaires</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('parent.paiements', $athlete) }}" class="flex items-center p-4 hover:bg-gray-50">
                <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <div class="font-medium text-gray-900">Paiements</div>
                    <div class="text-sm text-gray-500">Historique des paiements et arrieres</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="{{ route('parent.performances', $athlete) }}" class="flex items-center p-4 hover:bg-gray-50">
                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <div class="font-medium text-gray-900">Performances</div>
                    <div class="text-sm text-gray-500">Evaluations et resultats sportifs</div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection
