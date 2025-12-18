@section('title', 'Disciplines')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Disciplines</h2>
                <p class="mt-1 text-sm text-gray-500">Gestion des disciplines sportives</p>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('disciplines.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle discipline
                </x-button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Grille des disciplines -->
        @if($disciplines->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($disciplines as $discipline)
                    <x-card class="hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $discipline->nom }}</h3>
                                <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                                    {{ $discipline->description ?: 'Aucune description' }}
                                </p>
                            </div>
                            @if($discipline->actif)
                                <x-badge color="success" size="sm">Actif</x-badge>
                            @else
                                <x-badge color="gray" size="sm">Inactif</x-badge>
                            @endif
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <dl class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <dt class="text-xs text-gray-500">Athletes</dt>
                                    <dd class="text-lg font-semibold text-primary-600">{{ $discipline->athletes_count }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Coachs</dt>
                                    <dd class="text-lg font-semibold text-secondary-600">{{ $discipline->coachs_count }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs text-gray-500">Tarif</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ number_format($discipline->tarif_mensuel, 0, ',', ' ') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
                            <a href="{{ route('disciplines.show', $discipline) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                Voir les details &rarr;
                            </a>
                            @if(auth()->user()->isAdmin())
                            <div class="flex gap-2">
                                <a href="{{ route('disciplines.edit', $discipline) }}" class="text-gray-400 hover:text-gray-600" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('disciplines.destroy', $discipline) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer la discipline {{ $discipline->nom }} ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-danger-600" title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </x-card>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $disciplines->links() }}
            </div>
        @else
            <x-card>
                <x-empty-state 
                    title="Aucune discipline" 
                    description="Commencez par ajouter une discipline sportive."
                    :action="auth()->user()->isAdmin() ? route('disciplines.create') : null"
                    actionText="Ajouter une discipline"
                />
            </x-card>
        @endif
    </div>
</x-app-layout>
