@section('title', $coach->user->name)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('coachs.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div class="flex-shrink-0 h-12 w-12 rounded-full overflow-hidden mr-4">
                    @if($coach->photo_url)
                        <img src="{{ $coach->photo_url }}" alt="{{ $coach->user->name }}" class="h-12 w-12 object-cover">
                    @else
                        <img src="{{ asset('images/default-avatar.svg') }}" alt="{{ $coach->user->name }}" class="h-12 w-12 object-cover">
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $coach->user->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Coach depuis {{ $coach->date_embauche?->format('d/m/Y') ?? 'N/A' }}
                    </p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('coachs.edit', $coach) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations -->
                <x-card title="Informations">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $coach->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $coach->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telephone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $coach->telephone ?: 'Non renseigne' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Specialite</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $coach->specialite ?: 'Non renseignee' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'embauche</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $coach->date_embauche?->format('d/m/Y') ?? 'Non renseignee' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                @if($coach->actif)
                                    <x-badge color="success">Actif</x-badge>
                                @else
                                    <x-badge color="gray">Inactif</x-badge>
                                @endif
                            </dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $coach->adresse ?: 'Non renseignee' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <!-- Presences enregistrees -->
                <x-card title="Presences enregistrees recemment">
                    @if($coach->presences->count() > 0)
                        <x-table>
                            <x-slot name="head">
                                <tr>
                                    <x-th>Date</x-th>
                                    <x-th>Discipline</x-th>
                                    <x-th>Athlete</x-th>
                                    <x-th>Statut</x-th>
                                </tr>
                            </x-slot>
                            @foreach($coach->presences as $presence)
                                <tr>
                                    <x-td>{{ $presence->date->format('d/m/Y') }}</x-td>
                                    <x-td>{{ $presence->discipline->nom }}</x-td>
                                    <x-td>{{ $presence->athlete->nom_complet }}</x-td>
                                    <x-td>
                                        @if($presence->present)
                                            <x-badge color="success" size="sm">Present</x-badge>
                                        @else
                                            <x-badge color="danger" size="sm">Absent</x-badge>
                                        @endif
                                    </x-td>
                                </tr>
                            @endforeach
                        </x-table>
                    @else
                        <x-empty-state title="Aucune presence" description="Ce coach n'a pas encore enregistre de presences." />
                    @endif
                </x-card>
            </div>

            <!-- Colonne laterale -->
            <div class="space-y-6">
                <!-- Disciplines -->
                <x-card title="Disciplines enseignees">
                    @if($coach->disciplines->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($coach->disciplines as $discipline)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $discipline->nom }}</p>
                                    </div>
                                    <a href="{{ route('disciplines.show', $discipline) }}" class="text-primary-600 hover:text-primary-800 text-sm">
                                        Voir
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <x-empty-state title="Aucune discipline" description="Ce coach n'enseigne aucune discipline." />
                    @endif
                </x-card>

                <!-- Statistiques -->
                <x-card title="Statistiques">
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Disciplines</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $coach->disciplines->count() }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Presences enregistrees</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $coach->presences->count() }}</dd>
                        </div>
                    </dl>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
