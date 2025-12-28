@extends('portail-parent.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Retour -->
    <a href="{{ route('parent.enfants.show', $athlete) }}" class="inline-flex items-center text-green-600 hover:text-green-700 mb-4">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Retour au profil
    </a>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Presences de {{ $athlete->prenom }}</h1>
    <p class="text-gray-500 mb-6">Historique des presences et absences</p>

    <!-- Stats du mois -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-gray-700">{{ $statsMensuelles->total ?? 0 }}</div>
            <div class="text-xs text-gray-500">Total ce mois</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $statsMensuelles->presences ?? 0 }}</div>
            <div class="text-xs text-gray-500">Presences</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $statsMensuelles->absences ?? 0 }}</div>
            <div class="text-xs text-gray-500">Absences</div>
        </div>
    </div>

    <!-- Liste des presences -->
    <div class="bg-white rounded-lg shadow">
        <div class="divide-y divide-gray-100">
            @forelse($presences as $presence)
                <div class="flex items-center p-4">
                    <div class="flex-shrink-0">
                        @if($presence->present)
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        @else
                            <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="font-medium text-gray-900">{{ $presence->date->format('l d F Y') }}</div>
                        @if($presence->motif_absence)
                            <div class="text-sm text-gray-500">{{ $presence->motif_absence }}</div>
                        @endif
                    </div>
                    <div class="text-sm font-medium {{ $presence->present ? 'text-green-600' : 'text-red-600' }}">
                        {{ $presence->present ? 'Present' : 'Absent' }}
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Aucune presence enregistree
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($presences->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $presences->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
