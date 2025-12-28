@section('title', 'Licence ' . $licence->numero_licence)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Licence {{ $licence->numero_licence }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $licence->athlete->nom_complet }} - {{ $licence->discipline->nom }}</p>
            </div>
            <div class="flex gap-2">
                <x-button href="{{ route('licences.edit', $licence) }}" variant="secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
                <x-button href="{{ route('licences.index') }}" variant="ghost">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de la licence</h3>
                    
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Numéro de licence</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $licence->numero_licence }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fédération</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $licence->federation }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($licence->type) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                            <dd class="mt-1">
                                <x-badge variant="info">{{ $licence->categorie ?? 'Non définie' }}</x-badge>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Saison</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $licence->saison ?? 'Non définie' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $licence->statut_badge_class }}">
                                    {{ ucfirst($licence->statut) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'émission</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $licence->date_emission->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'expiration</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $licence->date_expiration->format('d/m/Y') }}
                                @if($licence->jours_restants > 0 && $licence->jours_restants <= 30)
                                    <span class="text-yellow-600 text-xs">({{ $licence->jours_restants }} jours restants)</span>
                                @elseif($licence->jours_restants == 0)
                                    <span class="text-red-600 text-xs">(Expirée)</span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @if($licence->notes)
                        <div class="mt-4 pt-4 border-t">
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $licence->notes }}</dd>
                        </div>
                    @endif

                    @if($licence->document)
                        <div class="mt-4 pt-4 border-t">
                            <dt class="text-sm font-medium text-gray-500 mb-2">Document</dt>
                            <a href="{{ $licence->document_url }}" target="_blank" class="inline-flex items-center text-primary-600 hover:underline">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Télécharger le document
                            </a>
                        </div>
                    @endif
                </x-card>

                <!-- Historique des licences -->
                @if($historique->count() > 0)
                    <x-card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des licences</h3>
                        <div class="space-y-3">
                            @foreach($historique as $ancienne)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="font-mono text-sm">{{ $ancienne->numero_licence }}</span>
                                        <span class="text-gray-500 text-sm ml-2">
                                            {{ $ancienne->date_emission->format('d/m/Y') }} - {{ $ancienne->date_expiration->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ancienne->statut_badge_class }}">
                                        {{ ucfirst($ancienne->statut) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Athlète -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Athlète</h3>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full overflow-hidden">
                            @if($licence->athlete->photo_url)
                                <img src="{{ $licence->athlete->photo_url }}" alt="{{ $licence->athlete->nom_complet }}" class="h-12 w-12 object-cover">
                            @else
                                <div class="h-12 w-12 bg-gray-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $licence->athlete->nom_complet }}</div>
                            <div class="text-sm text-gray-500">{{ $licence->athlete->age }} ans - {{ $licence->athlete->categorie_age }}</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-button href="{{ route('athletes.show', $licence->athlete) }}" variant="secondary" size="sm" class="w-full">
                            Voir le profil
                        </x-button>
                    </div>
                </x-card>

                <!-- Paiement -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Paiement</h3>
                    <div class="text-center">
                        <div class="text-2xl font-bold {{ $licence->paye ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($licence->frais_licence, 0, ',', ' ') }} FCFA
                        </div>
                        <div class="mt-2">
                            @if($licence->paye)
                                <x-badge variant="success">Payée</x-badge>
                            @else
                                <x-badge variant="danger">Non payée</x-badge>
                            @endif
                        </div>
                    </div>
                </x-card>

                <!-- Actions -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        @if($licence->statut === 'active' || $licence->statut === 'expiree')
                            <form action="{{ route('licences.renouveler', $licence) }}" method="POST">
                                @csrf
                                <x-button type="submit" variant="success" class="w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Renouveler
                                </x-button>
                            </form>
                        @endif

                        <form action="{{ route('licences.destroy', $licence) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette licence ?')">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="danger" class="w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Supprimer
                            </x-button>
                        </form>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
