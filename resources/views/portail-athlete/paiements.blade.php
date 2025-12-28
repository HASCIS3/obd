@extends('portail-athlete.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Mes Paiements</h1>
    <p class="text-gray-500 mb-6">Historique des paiements et arrieres</p>

    <!-- Resume financier -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500 mb-1">Total paye</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($totalPaye, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500 mb-1">Reste a payer</div>
            <div class="text-2xl font-bold {{ $totalDu > 0 ? 'text-red-600' : 'text-gray-600' }}">{{ number_format($totalDu, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    @if($totalDu > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Paiement en attente</h3>
                    <p class="text-sm text-yellow-700 mt-1">Veuillez regulariser votre situation aupres de l'administration.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des paiements -->
    <div class="bg-white rounded-lg shadow">
        <div class="divide-y divide-gray-100">
            @forelse($paiements as $paiement)
                <div class="p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-medium text-gray-900">{{ $paiement->description ?? 'Paiement' }}</div>
                            <div class="text-sm text-gray-500">{{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y') : 'Date non definie' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold {{ $paiement->statut === 'paye' ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $paiement->statut === 'paye' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $paiement->statut === 'paye' ? 'Paye' : 'En attente' }}
                            </span>
                        </div>
                    </div>
                    @if($paiement->mode_paiement)
                        <div class="mt-2 text-xs text-gray-500">
                            Mode: {{ ucfirst($paiement->mode_paiement) }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Aucun paiement enregistre
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($paiements->hasPages())
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $paiements->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
