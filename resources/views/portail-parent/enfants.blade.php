@extends('portail-parent.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mes Enfants</h1>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($enfants as $enfant)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- Photo et infos principales -->
                <div class="p-4">
                    <div class="flex items-center">
                        @if($enfant->photo)
                            <img src="{{ asset('storage/' . $enfant->photo) }}" alt="{{ $enfant->nom_complet }}" class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-bold text-2xl">{{ substr($enfant->prenom, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $enfant->nom_complet }}</h2>
                            <p class="text-sm text-gray-500">{{ $enfant->disciplines->first()->nom ?? 'Non assigne' }}</p>
                            <p class="text-xs text-gray-400">{{ $enfant->date_naissance ? $enfant->date_naissance->age . ' ans' : '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Statut certificat medical -->
                <div class="px-4 pb-2">
                    @if($enfant->certificatsMedicaux->first() && $enfant->certificatsMedicaux->first()->est_valide)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Certificat medical valide
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Certificat expire ou manquant
                        </span>
                    @endif
                </div>

                <!-- Actions rapides -->
                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <a href="{{ route('parent.enfants.show', $enfant) }}" class="flex flex-col items-center text-gray-600 hover:text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-xs mt-1">Profil</span>
                        </a>
                        <a href="{{ route('parent.presences', $enfant) }}" class="flex flex-col items-center text-gray-600 hover:text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span class="text-xs mt-1">Presences</span>
                        </a>
                        <a href="{{ route('parent.suivi-scolaire', $enfant) }}" class="flex flex-col items-center text-gray-600 hover:text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span class="text-xs mt-1">Scolaire</span>
                        </a>
                        <a href="{{ route('parent.paiements', $enfant) }}" class="flex flex-col items-center text-gray-600 hover:text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs mt-1">Paiements</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Aucun enfant enregistre</h3>
                <p class="mt-2 text-gray-500">Contactez l'administration pour lier vos enfants a votre compte.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
