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

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Performances de {{ $athlete->prenom }}</h1>
    <p class="text-gray-500 mb-6">Evaluations et resultats sportifs</p>

    <!-- Liste des performances -->
    <div class="space-y-4">
        @forelse($performances as $performance)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $performance->type_evaluation ?? 'Evaluation' }}</h3>
                            <p class="text-sm text-gray-500">{{ $performance->date_evaluation ? $performance->date_evaluation->format('d/m/Y') : '' }}</p>
                        </div>
                        @if($performance->note)
                            <div class="text-right">
                                <div class="text-2xl font-bold {{ $performance->note >= 14 ? 'text-green-600' : ($performance->note >= 10 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($performance->note, 1) }}/20
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($performance->discipline)
                        <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-2">
                            {{ $performance->discipline }}
                        </div>
                    @endif

                    @if($performance->observations)
                        <div class="mt-3 p-3 bg-gray-50 rounded text-sm text-gray-700">
                            {{ $performance->observations }}
                        </div>
                    @endif

                    @if($performance->points_forts)
                        <div class="mt-2">
                            <span class="text-xs font-medium text-green-600">Points forts:</span>
                            <p class="text-sm text-gray-600">{{ $performance->points_forts }}</p>
                        </div>
                    @endif

                    @if($performance->points_ameliorer)
                        <div class="mt-2">
                            <span class="text-xs font-medium text-orange-600">A ameliorer:</span>
                            <p class="text-sm text-gray-600">{{ $performance->points_ameliorer }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <p class="text-gray-500">Aucune performance enregistree</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($performances->hasPages())
        <div class="mt-6">
            {{ $performances->links() }}
        </div>
    @endif
</div>
@endsection
