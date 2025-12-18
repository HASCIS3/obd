@section('title', 'Suivi scolaire')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('suivis-scolaires.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Suivi scolaire</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $suiviScolaire->athlete?->nom_complet ?? 'Athlete inconnu' }}</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('suivis-scolaires.edit', ['suivis_scolaire' => $suiviScolaire->id]) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <!-- Resultat -->
            @if($suiviScolaire->moyenne_generale)
            <div class="flex items-center justify-center mb-6 pb-6 border-b">
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-1">Moyenne generale</p>
                    <p class="text-4xl font-bold {{ $suiviScolaire->estSatisfaisant() ? 'text-green-600' : 'text-danger-600' }}">
                        {{ number_format($suiviScolaire->moyenne_generale, 2) }}/20
                    </p>
                    @if($suiviScolaire->estSatisfaisant())
                        <x-badge color="success" class="mt-2">Satisfaisant</x-badge>
                    @else
                        <x-badge color="danger" class="mt-2">Insuffisant</x-badge>
                    @endif
                </div>
            </div>
            @endif

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Athlete</dt>
                    <dd class="mt-1">
                        @if($suiviScolaire->athlete)
                        <a href="{{ route('athletes.show', $suiviScolaire->athlete) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                            {{ $suiviScolaire->athlete->nom_complet }}
                        </a>
                        @else
                        <span class="text-gray-500">Athlete inconnu</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Annee scolaire</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $suiviScolaire->annee_scolaire ?: 'Non renseignee' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Etablissement</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $suiviScolaire->etablissement ?: 'Non renseigne' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Classe</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $suiviScolaire->classe ?: 'Non renseignee' }}</dd>
                </div>
                @if($suiviScolaire->rang)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Rang</dt>
                    <dd class="mt-1">
                        <x-badge color="secondary" size="lg">{{ $suiviScolaire->rang }}e</x-badge>
                    </dd>
                </div>
                @endif
                @if($suiviScolaire->observations)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Observations</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $suiviScolaire->observations }}</dd>
                </div>
                @endif
                @if($suiviScolaire->bulletin_path)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Bulletin</dt>
                    <dd class="mt-1">
                        <a href="{{ Storage::url($suiviScolaire->bulletin_path) }}" target="_blank" class="inline-flex items-center text-primary-600 hover:text-primary-800">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Telecharger le bulletin
                        </a>
                    </dd>
                </div>
                @endif
            </dl>
        </x-card>
    </div>
</x-app-layout>
