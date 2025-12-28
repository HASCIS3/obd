@section('title', 'Gestion des Saisons')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestion des Saisons</h2>
                <p class="mt-1 text-sm text-gray-500">Gérer les saisons sportives</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('saisons.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle saison
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($saisonActive)
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            <strong>Saison active:</strong> {{ $saisonActive->nom }} 
                            ({{ $saisonActive->date_debut->format('d/m/Y') }} - {{ $saisonActive->date_fin->format('d/m/Y') }})
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Aucune saison active.</strong> Veuillez activer une saison.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <x-card :padding="false">
            @if($saisons->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Saison</x-th>
                            <x-th>Période</x-th>
                            <x-th>Durée</x-th>
                            <x-th>Statut</x-th>
                            <x-th class="text-right">Actions</x-th>
                        </tr>
                    </x-slot>

                    @foreach($saisons as $saison)
                        <tr class="hover:bg-gray-50 {{ $saison->active ? 'bg-green-50' : '' }}">
                            <x-td>
                                <span class="font-semibold">{{ $saison->nom }}</span>
                                @if($saison->description)
                                    <p class="text-xs text-gray-500">{{ Str::limit($saison->description, 50) }}</p>
                                @endif
                            </x-td>
                            <x-td>
                                {{ $saison->date_debut->format('d/m/Y') }} - {{ $saison->date_fin->format('d/m/Y') }}
                            </x-td>
                            <x-td>{{ $saison->duree_jours }} jours</x-td>
                            <x-td>
                                @if($saison->active)
                                    <x-badge variant="success">Active</x-badge>
                                @elseif($saison->archivee)
                                    <x-badge variant="secondary">Archivée</x-badge>
                                @elseif($saison->est_en_cours)
                                    <x-badge variant="info">En cours</x-badge>
                                @elseif($saison->est_future)
                                    <x-badge variant="warning">À venir</x-badge>
                                @else
                                    <x-badge variant="secondary">Terminée</x-badge>
                                @endif
                            </x-td>
                            <x-td class="text-right">
                                <div class="flex justify-end gap-2">
                                    @if(!$saison->active && !$saison->archivee)
                                        <form action="{{ route('saisons.activer', $saison) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800" title="Activer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('saisons.edit', $saison) }}" class="text-gray-600 hover:text-primary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if(!$saison->active && !$saison->archivee)
                                        <form action="{{ route('saisons.archiver', $saison) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-800" title="Archiver">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    @if(!$saison->active)
                                        <form action="{{ route('saisons.destroy', $saison) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette saison ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
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
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune saison</h3>
                    <p class="mt-1 text-sm text-gray-500">Créez votre première saison sportive.</p>
                    <div class="mt-6">
                        <x-button href="{{ route('saisons.create') }}" variant="primary">
                            Créer une saison
                        </x-button>
                    </div>
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
