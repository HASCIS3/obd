@extends('portail-athlete.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mon Profil</h1>

    <!-- Photo et infos principales -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-600 to-green-700 p-6">
            <div class="flex items-center">
                @if($athlete->photo)
                    <img src="{{ asset('storage/' . $athlete->photo) }}" alt="{{ $athlete->nom_complet }}" class="h-20 w-20 rounded-full object-cover border-4 border-white">
                @else
                    <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center border-4 border-green-200">
                        <span class="text-green-700 font-bold text-3xl">{{ substr($athlete->prenom, 0, 1) }}</span>
                    </div>
                @endif
                <div class="ml-4 text-white">
                    <h2 class="text-2xl font-bold">{{ $athlete->nom_complet }}</h2>
                    <p class="text-green-100">{{ $athlete->disciplines->first()->nom ?? 'Olympiade de Baco-Djicoroni' }}</p>
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
                    <span class="text-gray-500">Sexe</span>
                    <p class="font-medium">{{ $athlete->sexe == 'M' ? 'Masculin' : 'Feminin' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Telephone</span>
                    <p class="font-medium">{{ $athlete->telephone ?? 'Non renseigne' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Email</span>
                    <p class="font-medium">{{ $athlete->email ?? 'Non renseigne' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Adresse</span>
                    <p class="font-medium">{{ $athlete->adresse ?? 'Non renseigne' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Date d'inscription</span>
                    <p class="font-medium">{{ $athlete->date_inscription ? $athlete->date_inscription->format('d/m/Y') : 'Non renseigne' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Statut</span>
                    <p class="font-medium">
                        @if($athlete->actif)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Actif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Inactif</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Disciplines -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Mes Disciplines</h3>
        </div>
        <div class="p-4">
            @if($athlete->disciplines->count() > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($athlete->disciplines as $discipline)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            {{ $discipline->nom }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucune discipline assignee</p>
            @endif
        </div>
    </div>

    <!-- Certificat medical -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Certificat Medical</h3>
        </div>
        <div class="p-4">
            @php
                $certificat = $athlete->certificatsMedicaux->first();
            @endphp
            @if($certificat)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Date d'expiration: <span class="font-medium">{{ $certificat->date_expiration ? $certificat->date_expiration->format('d/m/Y') : 'Non definie' }}</span></p>
                        <p class="text-sm text-gray-600 mt-1">Statut: 
                            @if($certificat->est_valide)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Valide</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Expire</span>
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucun certificat medical enregistre</p>
            @endif
        </div>
    </div>

    <!-- Contact tuteur -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Contact Tuteur</h3>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Nom du tuteur</span>
                    <p class="font-medium">{{ $athlete->nom_tuteur ?? 'Non renseigne' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Telephone tuteur</span>
                    <p class="font-medium">{{ $athlete->telephone_tuteur ?? 'Non renseigne' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
