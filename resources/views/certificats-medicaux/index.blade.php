@section('title', 'Certificats Médicaux')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Certificats Médicaux</h2>
                <p class="mt-1 text-sm text-gray-500">Suivi médical des athlètes</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('certificats-medicaux.expirant-bientot') }}" variant="warning">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Expirant ({{ $stats['expirant_bientot'] }})
                </x-button>
                <x-button href="{{ route('certificats-medicaux.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouveau certificat
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <x-card class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500">Total</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['valides'] }}</div>
                <div class="text-sm text-gray-500">Valides</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ $stats['expires'] }}</div>
                <div class="text-sm text-gray-500">Expirés</div>
            </x-card>
            <x-card class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['expirant_bientot'] }}</div>
                <div class="text-sm text-gray-500">Expirant bientôt</div>
            </x-card>
        </div>

        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('certificats-medicaux.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-input 
                        type="text" 
                        name="search" 
                        placeholder="Rechercher un athlète..." 
                        :value="request('search')"
                    />
                </div>
                <div>
                    <x-select 
                        name="statut" 
                        :options="[
                            ['id' => 'valide', 'name' => 'Valides'],
                            ['id' => 'expire', 'name' => 'Expirés'],
                            ['id' => 'en_attente', 'name' => 'En attente'],
                        ]" 
                        :selected="request('statut')"
                        placeholder="Tous les statuts"
                    />
                </div>
                <div>
                    <x-select 
                        name="type" 
                        :options="[
                            ['id' => 'aptitude', 'name' => 'Aptitude'],
                            ['id' => 'inaptitude_temporaire', 'name' => 'Inaptitude temporaire'],
                            ['id' => 'inaptitude_definitive', 'name' => 'Inaptitude définitive'],
                            ['id' => 'suivi', 'name' => 'Suivi médical'],
                        ]" 
                        :selected="request('type')"
                        placeholder="Tous les types"
                    />
                </div>
                <div class="flex gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">
                        Filtrer
                    </x-button>
                    <x-button href="{{ route('certificats-medicaux.index') }}" variant="ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Liste -->
        <x-card :padding="false">
            @if($certificats->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Athlète</x-th>
                            <x-th>Type</x-th>
                            <x-th>Médecin</x-th>
                            <x-th>Expiration</x-th>
                            <x-th>Aptitude</x-th>
                            <x-th>Statut</x-th>
                            <x-th class="text-right">Actions</x-th>
                        </tr>
                    </x-slot>

                    @foreach($certificats as $certificat)
                        <tr class="hover:bg-gray-50">
                            <x-td>
                                @if($certificat->athlete)
                                    <a href="{{ route('athletes.show', $certificat->athlete) }}" class="text-primary-600 hover:underline">
                                        {{ $certificat->athlete->nom_complet }}
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Athlète supprimé</span>
                                @endif
                            </x-td>
                            <x-td>{{ $certificat->type_label ?? 'N/A' }}</x-td>
                            <x-td>{{ $certificat->medecin }}</x-td>
                            <x-td>
                                <div class="text-sm">
                                    {{ $certificat->date_expiration->format('d/m/Y') }}
                                    @if($certificat->jours_restants <= 30 && $certificat->jours_restants > 0)
                                        <span class="text-yellow-600 text-xs">({{ $certificat->jours_restants }}j)</span>
                                    @elseif($certificat->jours_restants == 0)
                                        <span class="text-red-600 text-xs">(Expiré)</span>
                                    @endif
                                </div>
                            </x-td>
                            <x-td>
                                @if($certificat->apte_competition && $certificat->apte_entrainement)
                                    <x-badge variant="success">Apte</x-badge>
                                @elseif($certificat->apte_entrainement)
                                    <x-badge variant="warning">Entraînement seul</x-badge>
                                @else
                                    <x-badge variant="danger">Inapte</x-badge>
                                @endif
                            </x-td>
                            <x-td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $certificat->statut_badge_class }}">
                                    {{ ucfirst($certificat->statut) }}
                                </span>
                            </x-td>
                            <x-td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('certificats-medicaux.show', $certificat) }}" class="text-gray-600 hover:text-primary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('certificats-medicaux.edit', $certificat) }}" class="text-gray-600 hover:text-primary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t">
                    {{ $certificats->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun certificat</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par créer un certificat médical.</p>
                    <div class="mt-6">
                        <x-button href="{{ route('certificats-medicaux.create') }}" variant="primary">
                            Créer un certificat
                        </x-button>
                    </div>
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
