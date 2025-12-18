@section('title', 'Athletes')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Athletes</h2>
                <p class="mt-1 text-sm text-gray-500">Gestion des athletes du centre sportif</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('athletes.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvel athlete
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('athletes.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <x-input 
                        type="text" 
                        name="search" 
                        placeholder="Rechercher..." 
                        :value="request('search')"
                    />
                </div>
                <div>
                    <x-select 
                        name="discipline" 
                        :options="$disciplines" 
                        :selected="request('discipline')"
                        placeholder="Toutes les disciplines"
                        valueKey="id"
                        labelKey="nom"
                    />
                </div>
                <div>
                    <x-select 
                        name="actif" 
                        :options="[['id' => '1', 'name' => 'Actifs'], ['id' => '0', 'name' => 'Inactifs']]" 
                        :selected="request('actif')"
                        placeholder="Tous les statuts"
                    />
                </div>
                <div class="flex gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrer
                    </x-button>
                    <x-button href="{{ route('athletes.index') }}" variant="ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Liste des athletes -->
        <x-card :padding="false">
            @if($athletes->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Athlete</x-th>
                            <x-th>Contact</x-th>
                            <x-th>Disciplines</x-th>
                            <x-th>Statut</x-th>
                            <x-th class="text-right">Actions</x-th>
                        </tr>
                    </x-slot>

                    @foreach($athletes as $athlete)
                        <tr class="hover:bg-gray-50">
                            <x-td>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden">
                                        @if($athlete->photo_url)
                                            <img src="{{ $athlete->photo_url }}" alt="{{ $athlete->nom_complet }}" class="h-10 w-10 object-cover">
                                        @else
                                            <img src="{{ asset('images/default-avatar.svg') }}" alt="{{ $athlete->nom_complet }}" class="h-10 w-10 object-cover">
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $athlete->nom_complet }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $athlete->sexe === 'M' ? 'Homme' : 'Femme' }}
                                            @if($athlete->date_naissance)
                                                - {{ $athlete->age }} ans
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </x-td>
                            <x-td>
                                <div class="text-sm text-gray-900">{{ $athlete->telephone ?: '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $athlete->email ?: '-' }}</div>
                            </x-td>
                            <x-td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse($athlete->disciplines as $discipline)
                                        <x-badge color="primary" size="sm">{{ $discipline->nom }}</x-badge>
                                    @empty
                                        <span class="text-gray-400 text-sm">Aucune</span>
                                    @endforelse
                                </div>
                            </x-td>
                            <x-td>
                                @if($athlete->actif)
                                    <x-badge color="success">Actif</x-badge>
                                @else
                                    <x-badge color="gray">Inactif</x-badge>
                                @endif
                            </x-td>
                            <x-td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('athletes.show', $athlete) }}" class="text-primary-600 hover:text-primary-900" title="Voir">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('athletes.edit', $athlete) }}" class="text-secondary-600 hover:text-secondary-900" title="Modifier">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('athletes.destroy', $athlete) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet athlete ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger-600 hover:text-danger-900" title="Supprimer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $athletes->links() }}
                </div>
            @else
                <x-empty-state 
                    title="Aucun athlete trouve" 
                    description="Commencez par ajouter un athlete au centre."
                    :action="route('athletes.create')"
                    actionText="Ajouter un athlete"
                />
            @endif
        </x-card>
    </div>
</x-app-layout>
