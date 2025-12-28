@section('title', $stageFormation->titre)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $stageFormation->titre }}</h2>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($stageFormation->statut === 'planifie') bg-blue-100 text-blue-800
                        @elseif($stageFormation->statut === 'en_cours') bg-green-100 text-green-800
                        @elseif($stageFormation->statut === 'termine') bg-gray-100 text-gray-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $stageFormation->statut_libelle }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500">Code: {{ $stageFormation->code }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('stages-formation.inscriptions', $stageFormation) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Inscriptions ({{ $stageFormation->nombre_inscrits }})
                </x-button>
                <x-button href="{{ route('stages-formation.edit', $stageFormation) }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques des inscrits -->
        <div class="grid grid-cols-2 sm:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $statsInscrits['total'] }}</p>
                <p class="text-xs text-gray-500">Total inscrits</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-4 text-center">
                <p class="text-2xl font-bold text-blue-700">{{ $statsInscrits['confirmes'] }}</p>
                <p class="text-xs text-blue-600">Confirmés</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4 text-center">
                <p class="text-2xl font-bold text-green-700">{{ $statsInscrits['en_formation'] }}</p>
                <p class="text-xs text-green-600">En formation</p>
            </div>
            <div class="bg-primary-50 rounded-lg shadow-sm border border-primary-200 p-4 text-center">
                <p class="text-2xl font-bold text-primary-700">{{ $statsInscrits['diplomes'] }}</p>
                <p class="text-xs text-primary-600">Diplômés</p>
            </div>
            <div class="bg-red-50 rounded-lg shadow-sm border border-red-200 p-4 text-center">
                <p class="text-2xl font-bold text-red-700">{{ $statsInscrits['echecs'] }}</p>
                <p class="text-xs text-red-600">Échecs</p>
            </div>
            <div class="bg-gray-50 rounded-lg shadow-sm border p-4 text-center">
                <p class="text-2xl font-bold text-gray-700">{{ $statsInscrits['abandons'] }}</p>
                <p class="text-xs text-gray-600">Abandons</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                    
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type de formation</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($stageFormation->type === 'formation_formateurs') bg-purple-100 text-purple-800
                                    @elseif($stageFormation->type === 'recyclage') bg-blue-100 text-blue-800
                                    @elseif($stageFormation->type === 'specialisation') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $stageFormation->type_libelle }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Discipline</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $stageFormation->discipline?->nom ?? 'Toutes disciplines' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dates</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                Du {{ $stageFormation->date_debut->format('d/m/Y') }} au {{ $stageFormation->date_fin->format('d/m/Y') }}
                                <span class="text-gray-500">({{ $stageFormation->duree_jours }} jours / {{ $stageFormation->duree_semaines }} semaines)</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Durée totale</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $stageFormation->duree_heures ?? '-' }} heures</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Lieu</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $stageFormation->lieu }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Organisme</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $stageFormation->organisme }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Places</dt>
                            <dd class="mt-1 text-sm">
                                <span class="{{ $stageFormation->est_complet ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                    {{ $stageFormation->places_restantes }} places restantes
                                </span>
                                <span class="text-gray-500">/ {{ $stageFormation->places_disponibles }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Frais d'inscription</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($stageFormation->frais_inscription > 0)
                                    {{ number_format($stageFormation->frais_inscription, 0, ',', ' ') }} FCFA
                                @else
                                    Gratuit
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @if($stageFormation->description)
                        <div class="mt-6 pt-6 border-t">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                            <dd class="text-sm text-gray-700 whitespace-pre-line">{{ $stageFormation->description }}</dd>
                        </div>
                    @endif
                </x-card>

                @if($stageFormation->objectifs)
                    <x-card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Objectifs de la formation</h3>
                        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $stageFormation->objectifs }}</div>
                    </x-card>
                @endif

                @if($stageFormation->programme)
                    <x-card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Programme</h3>
                        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $stageFormation->programme }}</div>
                    </x-card>
                @endif

                @if($stageFormation->conditions_admission)
                    <x-card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conditions d'admission</h3>
                        <div class="text-sm text-gray-700 whitespace-pre-line">{{ $stageFormation->conditions_admission }}</div>
                    </x-card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Certification -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Certification</h3>
                    <div class="text-center py-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full 
                            @if($stageFormation->type_certification === 'diplome') bg-yellow-100
                            @elseif($stageFormation->type_certification === 'certificat') bg-blue-100
                            @else bg-gray-100
                            @endif mb-3">
                            @if($stageFormation->type_certification === 'diplome')
                                <span class="text-3xl">🎓</span>
                            @elseif($stageFormation->type_certification === 'certificat')
                                <span class="text-3xl">📜</span>
                            @else
                                <span class="text-3xl">📄</span>
                            @endif
                        </div>
                        <p class="font-semibold text-gray-900">{{ $stageFormation->type_certification_libelle }}</p>
                        @if($stageFormation->intitule_certification)
                            <p class="text-sm text-gray-600 mt-1">{{ $stageFormation->intitule_certification }}</p>
                        @endif
                    </div>
                </x-card>

                <!-- Encadreurs -->
                @if($stageFormation->encadreurs && count($stageFormation->encadreurs) > 0)
                    <x-card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Encadreurs / Formateurs</h3>
                        <ul class="space-y-2">
                            @foreach($stageFormation->encadreurs as $encadreur)
                                @if($encadreur)
                                    <li class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $encadreur }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </x-card>
                @endif

                <!-- Actions rapides -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('stages-formation.inscriptions', $stageFormation) }}" 
                           class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <span class="text-sm font-medium text-gray-700">Gérer les inscriptions</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        <a href="{{ route('stages-formation.liste-participants-pdf', $stageFormation) }}" 
                           class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <span class="text-sm font-medium text-gray-700">Liste des participants (PDF)</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </a>
                        @if($stageFormation->nombre_diplomes > 0)
                            <a href="{{ route('stages-formation.diplomes', $stageFormation) }}" 
                               class="flex items-center justify-between p-3 bg-green-50 rounded-lg hover:bg-green-100 transition">
                                <span class="text-sm font-medium text-green-700">Voir les diplômés ({{ $stageFormation->nombre_diplomes }})</span>
                                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </x-card>

                <!-- Informations système -->
                <x-card>
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Informations système</h3>
                    <dl class="text-xs text-gray-500 space-y-1">
                        <div class="flex justify-between">
                            <dt>Créé par</dt>
                            <dd>{{ $stageFormation->createur?->name ?? 'Système' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Créé le</dt>
                            <dd>{{ $stageFormation->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt>Modifié le</dt>
                            <dd>{{ $stageFormation->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </x-card>
            </div>
        </div>

        <!-- Bouton retour -->
        <div class="mt-6">
            <x-button href="{{ route('stages-formation.index') }}" variant="secondary">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à la liste
            </x-button>
        </div>
    </div>
</x-app-layout>
