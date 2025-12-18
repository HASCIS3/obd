@section('title', $discipline->nom)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('disciplines.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $discipline->nom }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $discipline->description ?: 'Discipline sportive' }}</p>
                </div>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('disciplines.edit', $discipline) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <x-stat-card 
                title="Athletes inscrits" 
                :value="$stats['total_athletes']"
                color="primary"
            />
            <x-stat-card 
                title="Coachs" 
                :value="$stats['total_coachs']"
                color="secondary"
            />
            <x-stat-card 
                title="Presences ce mois" 
                :value="$stats['presences_mois']"
                color="success"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Athletes -->
            <x-card title="Athletes inscrits">
                @if($discipline->athletes->count() > 0)
                    <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                        @foreach($discipline->athletes as $athlete)
                            <li class="py-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-primary-600 font-medium text-xs">
                                            {{ substr($athlete->prenom, 0, 1) }}{{ substr($athlete->nom, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $athlete->nom_complet }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('athletes.show', $athlete) }}" class="text-primary-600 hover:text-primary-800 text-sm">
                                    Voir
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <x-empty-state title="Aucun athlete" description="Aucun athlete inscrit a cette discipline." />
                @endif
            </x-card>

            <!-- Coachs -->
            <x-card title="Coachs">
                @if($discipline->coachs->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($discipline->coachs as $coach)
                            <li class="py-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-secondary-100 flex items-center justify-center">
                                        <span class="text-secondary-700 font-medium text-xs">
                                            {{ substr($coach->user->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $coach->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $coach->user->email }}</p>
                                    </div>
                                </div>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('coachs.show', $coach) }}" class="text-primary-600 hover:text-primary-800 text-sm">
                                    Voir
                                </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <x-empty-state title="Aucun coach" description="Aucun coach n'enseigne cette discipline." />
                @endif
            </x-card>
        </div>

        <!-- Informations -->
        <x-card title="Informations" class="mt-6">
            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tarif mensuel</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ number_format($discipline->tarif_mensuel, 0, ',', ' ') }} FCFA</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                    <dd class="mt-1">
                        @if($discipline->actif)
                            <x-badge color="success">Active</x-badge>
                        @else
                            <x-badge color="gray">Inactive</x-badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date de creation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $discipline->created_at->format('d/m/Y') }}</dd>
                </div>
            </dl>
        </x-card>
    </div>
</x-app-layout>
