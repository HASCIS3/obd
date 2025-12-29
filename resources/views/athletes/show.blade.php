@section('title', $athlete->nom_complet)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('athletes.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="flex-shrink-0 h-12 w-12 rounded-full overflow-hidden mr-4">
                    @if($athlete->photo_url)
                        <img src="{{ $athlete->photo_url }}" alt="{{ $athlete->nom_complet }}" class="h-12 w-12 object-cover">
                    @else
                        <img src="{{ asset('images/default-avatar.svg') }}" alt="{{ $athlete->nom_complet }}" class="h-12 w-12 object-cover">
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $athlete->nom_complet }}</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Inscrit le {{ $athlete->date_inscription?->format('d/m/Y') ?? 'N/A' }}
                    </p>
                </div>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('athletes.edit', $athlete) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>

                @if($athlete->user)
                    <x-button variant="ghost" disabled>
                        Compte cree
                    </x-button>
                @else
                    <x-button href="{{ route('athletes.account.create', $athlete) }}" variant="primary">
                        Creer compte
                    </x-button>
                @endif
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations personnelles -->
                <x-card title="Informations personnelles">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $athlete->nom_complet }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sexe</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $athlete->sexe === 'M' ? 'Masculin' : 'Feminin' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date de naissance</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $athlete->date_naissance?->format('d/m/Y') ?? 'Non renseignee' }}
                                @if($athlete->date_naissance)
                                    <span class="text-gray-500">({{ $athlete->age }} ans)</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                @if($athlete->actif)
                                    <x-badge color="success">Actif</x-badge>
                                @else
                                    <x-badge color="gray">Inactif</x-badge>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telephone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $athlete->telephone ?: 'Non renseigne' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $athlete->email ?: 'Non renseigne' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $athlete->adresse ?: 'Non renseignee' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <!-- Tuteur -->
                @if($athlete->nom_tuteur || $athlete->telephone_tuteur)
                <x-card title="Tuteur">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nom du tuteur</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $athlete->nom_tuteur ?: 'Non renseigne' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telephone du tuteur</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $athlete->telephone_tuteur ?: 'Non renseigne' }}</dd>
                        </div>
                    </dl>
                </x-card>
                @endif

                <!-- Presences recentes -->
                <x-card title="Presences recentes">
                    @if($athlete->presences->count() > 0)
                        <x-table>
                            <x-slot name="head">
                                <tr>
                                    <x-th>Date</x-th>
                                    <x-th>Discipline</x-th>
                                    <x-th>Statut</x-th>
                                </tr>
                            </x-slot>
                            @foreach($athlete->presences as $presence)
                                <tr>
                                    <x-td>{{ $presence->date->format('d/m/Y') }}</x-td>
                                    <x-td>{{ $presence->discipline->nom }}</x-td>
                                    <x-td>
                                        @if($presence->present)
                                            <x-badge color="success">Present</x-badge>
                                        @else
                                            <x-badge color="danger">Absent</x-badge>
                                        @endif
                                    </x-td>
                                </tr>
                            @endforeach
                        </x-table>
                        <div class="mt-4">
                            <a href="{{ route('presences.athlete', $athlete) }}" class="text-sm text-primary-600 hover:text-primary-800">
                                Voir toutes les presences &rarr;
                            </a>
                        </div>
                    @else
                        <x-empty-state title="Aucune presence" description="Les presences apparaitront ici." />
                    @endif
                </x-card>
            </div>

            <!-- Colonne laterale -->
            <div class="space-y-6">
                <!-- Disciplines -->
                <x-card title="Disciplines">
                    @if($athlete->disciplines->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($athlete->disciplines as $discipline)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $discipline->nom }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($discipline->tarif_mensuel, 0, ',', ' ') }} FCFA/mois</p>
                                    </div>
                                    <x-badge color="primary" size="sm">Inscrit</x-badge>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-empty-state title="Aucune discipline" description="Cet athlete n'est inscrit a aucune discipline." />
                    @endif
                </x-card>

                <!-- Paiements -->
                @if(auth()->user()->isAdmin())
                <x-card title="Paiements">
                    @if($athlete->paiements->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($athlete->paiements->take(5) as $paiement)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-900">{{ $paiement->mois }}/{{ $paiement->annee }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                    @if($paiement->statut === 'paye')
                                        <x-badge color="success" size="sm">Paye</x-badge>
                                    @elseif($paiement->statut === 'partiel')
                                        <x-badge color="warning" size="sm">Partiel</x-badge>
                                    @else
                                        <x-badge color="danger" size="sm">Impaye</x-badge>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @if($athlete->arrieres > 0)
                            <div class="mt-4 p-3 bg-danger-50 rounded-lg">
                                <p class="text-sm font-medium text-danger-800">
                                    Arrieres: {{ number_format($athlete->arrieres, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        @endif
                    @else
                        <x-empty-state title="Aucun paiement" description="Les paiements apparaitront ici." />
                    @endif
                </x-card>
                @endif

                <!-- Certificat Medical -->
                <x-card title="Certificat Medical">
                    @php
                        $certificat = $athlete->certificatsMedicaux()->latest('date_examen')->first();
                    @endphp
                    @if($certificat)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Statut</span>
                                @if($certificat->statut === 'valide')
                                    <x-badge color="success">Valide</x-badge>
                                @elseif($certificat->statut === 'expire')
                                    <x-badge color="danger">Expire</x-badge>
                                @else
                                    <x-badge color="warning">En attente</x-badge>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Type</span>
                                <span class="text-sm text-gray-900">{{ $certificat->type_label }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Date examen</span>
                                <span class="text-sm text-gray-900">{{ $certificat->date_examen->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Expiration</span>
                                <span class="text-sm text-gray-900">
                                    {{ $certificat->date_expiration->format('d/m/Y') }}
                                    @if($certificat->jours_restants > 0)
                                        <span class="text-xs text-gray-500">({{ $certificat->jours_restants }}j)</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Medecin</span>
                                <span class="text-sm text-gray-900">{{ $certificat->medecin }}</span>
                            </div>
                            <div class="pt-3 border-t flex gap-2">
                                <div class="flex items-center">
                                    @if($certificat->apte_competition)
                                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    <span class="text-xs">Competition</span>
                                </div>
                                <div class="flex items-center">
                                    @if($certificat->apte_entrainement)
                                        <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                    <span class="text-xs">Entrainement</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('certificats-medicaux.show', $certificat) }}" class="text-sm text-primary-600 hover:text-primary-800">
                                    Voir le certificat complet &rarr;
                                </a>
                            </div>
                        </div>
                    @else
                        <x-empty-state title="Aucun certificat" description="Aucun certificat medical enregistre." />
                        <div class="mt-3">
                            <x-button href="{{ route('certificats-medicaux.create', ['athlete_id' => $athlete->id]) }}" variant="primary" size="sm" class="w-full">
                                Ajouter un certificat
                            </x-button>
                        </div>
                    @endif
                </x-card>

                <!-- Performances -->
                <x-card title="Performances recentes">
                    @if($athlete->performances->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($athlete->performances as $performance)
                                <li class="py-3">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900">{{ $performance->discipline->nom }}</p>
                                        <span class="text-sm text-gray-500">{{ $performance->date_evaluation->format('d/m/Y') }}</span>
                                    </div>
                                    @if($performance->score)
                                        <p class="text-xs text-gray-600 mt-1">Score: {{ $performance->score_formate }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-empty-state title="Aucune performance" description="Les performances apparaitront ici." />
                    @endif
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
