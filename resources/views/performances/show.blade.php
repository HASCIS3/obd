@section('title', 'Performance')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('performances.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Performance</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $performance->athlete->nom_complet }} - {{ $performance->discipline->nom }}</p>
                </div>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('performances.edit', $performance) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Athlete</dt>
                    <dd class="mt-1">
                        <a href="{{ route('athletes.show', $performance->athlete) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                            {{ $performance->athlete->nom_complet }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Discipline</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $performance->discipline->nom }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Date d'evaluation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $performance->date_evaluation->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Type d'evaluation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $performance->type_evaluation ?: 'Non specifie' }}</dd>
                </div>
                @if($performance->score)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Score</dt>
                    <dd class="mt-1 text-2xl font-bold text-primary-600">{{ $performance->score_formate }}</dd>
                </div>
                @endif
                @if($performance->competition)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Competition</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $performance->competition }}</dd>
                </div>
                @endif
                @if($performance->classement)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Classement</dt>
                    <dd class="mt-1">
                        <x-badge color="secondary" size="lg">{{ $performance->classement }}e place</x-badge>
                    </dd>
                </div>
                @endif
                @if($performance->observations)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Observations</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $performance->observations }}</dd>
                </div>
                @endif
            </dl>

            <div class="mt-6 pt-6 border-t">
                <a href="{{ route('performances.evolution', $performance->athlete) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                    Voir l'evolution de {{ $performance->athlete->prenom }} &rarr;
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>
