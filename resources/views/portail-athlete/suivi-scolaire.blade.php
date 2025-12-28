@extends('portail-athlete.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Mon Suivi Scolaire</h1>
    <p class="text-gray-500 mb-6">Notes et bulletins scolaires</p>

    <!-- Liste des suivis -->
    <div class="space-y-4">
        @forelse($suivis as $suivi)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $suivi->periode ?? 'Periode non definie' }}</h3>
                            <p class="text-sm text-gray-500">{{ $suivi->annee_scolaire ?? '' }}</p>
                        </div>
                        @if($suivi->moyenne_generale)
                            <div class="text-right">
                                <div class="text-2xl font-bold {{ $suivi->moyenne_generale >= 10 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($suivi->moyenne_generale, 2) }}/20
                                </div>
                                <div class="text-xs text-gray-500">Moyenne</div>
                            </div>
                        @endif
                    </div>

                    @if($suivi->etablissement)
                        <div class="text-sm text-gray-600 mb-2">
                            <span class="font-medium">Etablissement:</span> {{ $suivi->etablissement }}
                        </div>
                    @endif

                    @if($suivi->classe)
                        <div class="text-sm text-gray-600 mb-2">
                            <span class="font-medium">Classe:</span> {{ $suivi->classe }}
                        </div>
                    @endif

                    @if($suivi->observations)
                        <div class="mt-3 p-3 bg-gray-50 rounded text-sm text-gray-700">
                            <span class="font-medium">Observations:</span> {{ $suivi->observations }}
                        </div>
                    @endif

                    @if($suivi->bulletin_path)
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $suivi->bulletin_path) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Telecharger le bulletin
                            </a>
                        </div>
                    @endif
                </div>
                <div class="px-4 py-2 bg-gray-50 text-xs text-gray-500">
                    Ajoute le {{ $suivi->created_at->format('d/m/Y') }}
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="text-gray-500">Aucun suivi scolaire enregistre</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($suivis->hasPages())
        <div class="mt-6">
            {{ $suivis->links() }}
        </div>
    @endif
</div>
@endsection
