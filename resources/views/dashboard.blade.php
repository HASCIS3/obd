@section('title', 'Dashboard')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    Tableau de bord
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Bienvenue, <span class="font-semibold text-primary-600">{{ auth()->user()->name }}</span> ! Vue d'ensemble de votre centre sportif.
                </p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center gap-3">
                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ now()->translatedFormat('l d F Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Accès rapides -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('athletes.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Nouvel athlète
                </a>
                <a href="{{ route('presences.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition border shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Saisir présences
                </a>
                <a href="{{ route('paiements.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition border shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Enregistrer paiement
                </a>
                <a href="{{ route('performances.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition border shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Ajouter performance
                </a>
                <a href="{{ route('calendrier.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition border shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Calendrier
                </a>
                <a href="{{ route('rencontres.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition border shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Rencontres
                </a>
                <a href="{{ route('activities.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition border shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Activités
                </a>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary-100 text-sm font-medium">Athlètes actifs</p>
                        <p class="text-3xl font-bold mt-1">{{ $stats['total_athletes'] }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('athletes.index') }}" class="mt-3 inline-flex items-center text-xs text-primary-100 hover:text-white">
                    Voir tous <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="bg-secondary-500 rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary-900 text-sm font-medium">Coachs</p>
                        <p class="text-3xl font-bold mt-1 text-secondary-900">{{ $stats['total_coachs'] }}</p>
                    </div>
                    <div class="bg-secondary-600 rounded-full p-3">
                        <svg class="h-6 w-6 text-secondary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('coachs.index') }}" class="mt-3 inline-flex items-center text-xs text-secondary-800 hover:text-secondary-900 font-medium">
                    Gérer <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">Disciplines</p>
                        <p class="text-3xl font-bold mt-1">{{ $stats['total_disciplines'] }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-3">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('disciplines.index') }}" class="mt-3 inline-flex items-center text-xs text-blue-100 hover:text-white">
                    Voir <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="bg-danger-500 rounded-xl p-5 text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-danger-50 text-sm font-medium">Arriérés</p>
                        <p class="text-2xl font-bold mt-1">{{ number_format($stats['arrieres_total'], 0, ',', ' ') }}</p>
                        <p class="text-xs text-danger-100">FCFA</p>
                    </div>
                    <div class="bg-danger-600 rounded-full p-3">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <a href="{{ route('paiements.arrieres') }}" class="mt-3 inline-flex items-center text-xs text-danger-100 hover:text-white font-medium">
                    Détails <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <!-- Graphiques et listes -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Graphique des presences -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Présences du mois</h3>
                    <a href="{{ route('presences.index') }}" class="text-xs text-primary-600 hover:underline">Détails</a>
                </div>
                <div class="h-48">
                    <canvas id="presencesChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                    <div class="bg-green-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-green-600">{{ $stats['presences_mois'] }}</p>
                        <p class="text-xs text-green-700">Présents</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-3">
                        <p class="text-2xl font-bold text-red-600">{{ $stats['absences_mois'] }}</p>
                        <p class="text-xs text-red-700">Absents</p>
                    </div>
                </div>
            </div>

            <!-- Performances sportives -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Performances</h3>
                    <a href="{{ route('performances.index') }}" class="text-xs text-primary-600 hover:underline">Voir tout</a>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">⚽</span>
                            <div>
                                <p class="font-semibold text-blue-800">{{ $statsPerformance['matchs']['total'] }} matchs</p>
                                <p class="text-xs text-blue-600">
                                    <span class="text-green-600">{{ $statsPerformance['matchs']['victoires'] }}V</span> · 
                                    <span class="text-red-600">{{ $statsPerformance['matchs']['defaites'] }}D</span> · 
                                    <span class="text-yellow-600">{{ $statsPerformance['matchs']['nuls'] }}N</span>
                                </p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-blue-700">{{ $statsPerformance['matchs']['taux_victoire'] }}%</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">🏅</span>
                            <div>
                                <p class="font-semibold text-yellow-800">Médailles</p>
                                <p class="text-xs text-yellow-600">Compétitions</p>
                            </div>
                        </div>
                        <div class="flex gap-1">
                            <span class="bg-yellow-200 px-2 py-1 rounded text-sm">🥇{{ $statsPerformance['competitions']['medailles_or'] }}</span>
                            <span class="bg-gray-200 px-2 py-1 rounded text-sm">🥈{{ $statsPerformance['competitions']['medailles_argent'] }}</span>
                            <span class="bg-orange-200 px-2 py-1 rounded text-sm">🥉{{ $statsPerformance['competitions']['medailles_bronze'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">📊</span>
                            <div>
                                <p class="font-semibold text-purple-800">Note moyenne</p>
                                <p class="text-xs text-purple-600">Évaluations</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-purple-700">{{ $statsPerformance['note_moyenne'] }}/10</span>
                    </div>
                </div>
            </div>

            <!-- Derniers athletes inscrits -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Nouveaux athlètes</h3>
                    <a href="{{ route('athletes.index') }}" class="text-xs text-primary-600 hover:underline">Voir tous</a>
                </div>
                @if($derniersAthletes->count() > 0)
                    <ul class="space-y-3">
                        @foreach($derniersAthletes->take(5) as $athlete)
                            <li class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg transition">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden bg-gray-200">
                                        @if($athlete->photo_url)
                                            <img src="{{ $athlete->photo_url }}" alt="{{ $athlete->nom_complet }}" class="h-10 w-10 object-cover">
                                        @else
                                            <div class="h-10 w-10 flex items-center justify-center bg-primary-100 text-primary-600 font-semibold">
                                                {{ substr($athlete->prenom, 0, 1) }}{{ substr($athlete->nom, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $athlete->nom_complet }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $athlete->disciplines->pluck('nom')->first() ?: 'Aucune discipline' }}
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('athletes.show', $athlete) }}" class="text-primary-600 hover:text-primary-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="mt-2 text-sm">Aucun athlète inscrit</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section Finances et Activités récentes -->
        @if(auth()->user()->isAdmin())
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Paiements recents -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Paiements récents
                    </h3>
                    <a href="{{ route('paiements.index') }}" class="text-xs text-primary-600 hover:underline">Voir tous</a>
                </div>
                @if($paiementsRecents->count() > 0)
                    <ul class="space-y-3">
                        @foreach($paiementsRecents->take(5) as $paiement)
                            <li class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $paiement->athlete->nom_complet }}</p>
                                    <p class="text-xs text-gray-500">{{ $paiement->date_paiement?->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-green-600">
                                        +{{ number_format($paiement->montant_paye, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-xs text-gray-500">FCFA</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="mt-2 text-sm">Aucun paiement récent</p>
                    </div>
                @endif
            </div>

            <!-- Athletes avec arrieres -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Arriérés de paiement
                    </h3>
                    <a href="{{ route('paiements.arrieres') }}" class="text-xs text-red-600 hover:underline">Voir tous</a>
                </div>
                @if($athletesArrieres->count() > 0)
                    <ul class="space-y-3">
                        @foreach($athletesArrieres->take(5) as $athlete)
                            @php
                                $totalArrieres = $athlete->paiements->sum('montant') - $athlete->paiements->sum('montant_paye');
                            @endphp
                            <li class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-9 w-9 rounded-full overflow-hidden ring-2 ring-red-200">
                                        @if($athlete->photo_url)
                                            <img src="{{ $athlete->photo_url }}" alt="{{ $athlete->nom_complet }}" class="h-9 w-9 object-cover">
                                        @else
                                            <div class="h-9 w-9 flex items-center justify-center bg-red-100 text-red-600 font-semibold text-sm">
                                                {{ substr($athlete->prenom, 0, 1) }}{{ substr($athlete->nom, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $athlete->nom_complet }}</p>
                                        <p class="text-xs text-gray-500">{{ $athlete->paiements->count() }} mois impayé(s)</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-red-600">
                                        {{ number_format($totalArrieres, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-xs text-gray-500">FCFA</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8">
                        <div class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="mt-2 text-sm text-green-600 font-medium">Tous les paiements sont à jour !</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Section Rencontres et Activités -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Rencontres sportives -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="text-2xl mr-2">⚔️</span>
                        Rencontres sportives
                    </h3>
                    <a href="{{ route('rencontres.index') }}" class="text-xs text-primary-600 hover:underline">Voir toutes</a>
                </div>
                
                <!-- Stats rencontres -->
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <div class="bg-gray-50 rounded-lg p-2 text-center">
                        <p class="text-xl font-bold text-gray-700">{{ $statsRencontres['total'] }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-2 text-center">
                        <p class="text-xl font-bold text-green-600">{{ $statsRencontres['victoires'] }}</p>
                        <p class="text-xs text-green-700">Victoires</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-2 text-center">
                        <p class="text-xl font-bold text-red-600">{{ $statsRencontres['defaites'] }}</p>
                        <p class="text-xs text-red-700">Défaites</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-2 text-center">
                        <p class="text-xl font-bold text-yellow-600">{{ $statsRencontres['nuls'] }}</p>
                        <p class="text-xs text-yellow-700">Nuls</p>
                    </div>
                </div>

                <!-- Prochaines rencontres -->
                @if($prochainesRencontres->count() > 0)
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">📅 Prochaines rencontres</h4>
                    <ul class="space-y-2">
                        @foreach($prochainesRencontres->take(3) as $rencontre)
                            <li class="flex items-center justify-between p-2 bg-blue-50 rounded-lg">
                                <div class="flex items-center">
                                    <span class="text-lg mr-2">{{ $rencontre->discipline->icone ?? '🏆' }}</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">vs {{ $rencontre->adversaire }}</p>
                                        <p class="text-xs text-gray-500">{{ $rencontre->discipline->nom ?? 'Sport' }} • {{ $rencontre->lieu ?? 'Lieu TBD' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-blue-600">{{ \Carbon\Carbon::parse($rencontre->date_match)->format('d/m') }}</p>
                                    <p class="text-xs text-gray-500">{{ $rencontre->heure_match ? \Carbon\Carbon::parse($rencontre->heure_match)->format('H:i') : '' }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4 text-gray-500">
                        <p class="text-sm">Aucune rencontre à venir</p>
                        <a href="{{ route('rencontres.create') }}" class="text-xs text-primary-600 hover:underline">+ Planifier une rencontre</a>
                    </div>
                @endif

                <!-- Derniers résultats -->
                @if($dernieresRencontres->count() > 0)
                    <h4 class="text-sm font-semibold text-gray-700 mt-4 mb-2">📊 Derniers résultats</h4>
                    <ul class="space-y-2">
                        @foreach($dernieresRencontres->take(3) as $rencontre)
                            <li class="flex items-center justify-between p-2 rounded-lg {{ $rencontre->resultat === 'victoire' ? 'bg-green-50' : ($rencontre->resultat === 'defaite' ? 'bg-red-50' : 'bg-gray-50') }}">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">vs {{ $rencontre->adversaire }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($rencontre->date_match)->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-right">
                                    @if($rencontre->score_obd !== null && $rencontre->score_adversaire !== null)
                                        <p class="text-sm font-bold {{ $rencontre->resultat === 'victoire' ? 'text-green-600' : ($rencontre->resultat === 'defaite' ? 'text-red-600' : 'text-yellow-600') }}">
                                            {{ $rencontre->score_obd }} - {{ $rencontre->score_adversaire }}
                                        </p>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $rencontre->resultat === 'victoire' ? 'bg-green-100 text-green-800' : ($rencontre->resultat === 'defaite' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($rencontre->resultat ?? 'En attente') }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Activités -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="text-2xl mr-2">📆</span>
                        Activités & Événements
                    </h3>
                    <a href="{{ route('activities.index') }}" class="text-xs text-primary-600 hover:underline">Voir toutes</a>
                </div>

                <!-- Stats activités -->
                <div class="grid grid-cols-3 gap-2 mb-4">
                    <div class="bg-indigo-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ $statsActivites['total'] }}</p>
                        <p class="text-xs text-indigo-700">Total</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $statsActivites['a_venir'] }}</p>
                        <p class="text-xs text-green-700">À venir</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-orange-600">{{ $statsActivites['ce_mois'] }}</p>
                        <p class="text-xs text-orange-700">Ce mois</p>
                    </div>
                </div>

                <!-- Prochaines activités -->
                @if($prochainesActivites->count() > 0)
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">🗓️ Prochaines activités</h4>
                    <ul class="space-y-2">
                        @foreach($prochainesActivites->take(4) as $activite)
                            <li class="flex items-center justify-between p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border-l-4 border-indigo-500">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $activite->titre }}</p>
                                    <p class="text-xs text-gray-500">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium" style="background-color: {{ $activite->couleur_type }}20; color: {{ $activite->couleur_type }};">
                                            {{ $activite->libelle_type }}
                                        </span>
                                        @if($activite->lieu) • {{ $activite->lieu }} @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold text-indigo-600">{{ \Carbon\Carbon::parse($activite->debut)->format('d/m') }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($activite->debut)->format('H:i') }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm">Aucune activité à venir</p>
                        <a href="{{ route('activities.create') }}" class="text-xs text-primary-600 hover:underline">+ Créer une activité</a>
                    </div>
                @endif

                <!-- Combats Taekwondo si existants -->
                @if($statsCombats['total'] > 0)
                    <div class="mt-4 p-3 bg-gradient-to-r from-red-100 via-white to-blue-100 rounded-lg border">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <span class="text-lg mr-2">🥋</span> Combats Taekwondo
                        </h4>
                        <div class="flex items-center justify-between">
                            <div class="text-center">
                                <p class="text-lg font-bold text-red-600">{{ $statsCombats['victoires_rouge'] }}</p>
                                <p class="text-xs text-gray-500">Hong (Rouge)</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-black text-gray-400">{{ $statsCombats['total'] }}</p>
                                <p class="text-xs text-gray-500">Combats</p>
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-bold text-blue-600">{{ $statsCombats['victoires_bleu'] }}</p>
                                <p class="text-xs text-gray-500">Chung (Bleu)</p>
                            </div>
                        </div>
                        @if($statsCombats['en_cours'] > 0)
                            <p class="text-center mt-2 text-xs text-orange-600 font-medium">
                                🔴 {{ $statsCombats['en_cours'] }} combat(s) en cours
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Dernières performances -->
        <div class="bg-white rounded-xl shadow-sm border mb-8">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Dernières performances
                    </h3>
                    <a href="{{ route('performances.index') }}" class="text-xs text-primary-600 hover:underline">Voir toutes</a>
                </div>
            </div>
            @if($dernieresPerformances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Athlète</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contexte</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Détails</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($dernieresPerformances->take(5) as $perf)
                                @if($perf->athlete)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('athletes.show', $perf->athlete) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600">
                                            {{ $perf->athlete->nom_complet }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($perf->contexte === 'match')
                                            @if($perf->resultat_match === 'victoire')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Victoire</span>
                                            @elseif($perf->resultat_match === 'defaite')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">✗ Défaite</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">= Nul</span>
                                            @endif
                                        @elseif($perf->contexte === 'competition')
                                            @if($perf->medaille)
                                                <span class="text-lg">
                                                    @if($perf->medaille === 'or')🥇
                                                    @elseif($perf->medaille === 'argent')🥈
                                                    @else🥉
                                                    @endif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Compétition</span>
                                            @endif
                                        @elseif($perf->contexte === 'entrainement')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Entraînement</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Test</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if($perf->adversaire)
                                            vs {{ $perf->adversaire }}
                                            @if($perf->score_match) <span class="font-medium">({{ $perf->score_match }})</span> @endif
                                        @elseif($perf->competition)
                                            {{ $perf->competition }}
                                        @else
                                            {{ $perf->type_evaluation ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $perf->date_evaluation->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($perf->note_globale)
                                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold {{ $perf->note_globale >= 7 ? 'bg-green-100 text-green-700' : ($perf->note_globale >= 5 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $perf->note_globale }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune performance enregistrée</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par ajouter des performances.</p>
                    <div class="mt-4">
                        <a href="{{ route('performances.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Ajouter une performance
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Graphique des presences
        const presencesCtx = document.getElementById('presencesChart').getContext('2d');
        new Chart(presencesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Presents', 'Absents'],
                datasets: [{
                    data: [{{ $stats['presences_mois'] }}, {{ $stats['absences_mois'] }}],
                    backgroundColor: ['#14532d', '#CE1126'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
