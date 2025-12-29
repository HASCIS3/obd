@section('title', 'Paiement #' . $paiement->id)

@php
    // Calcul dynamique du total à payer basé sur les frais détaillés
    $totalFraisDetails = 0;
    
    // Ajouter les frais d'inscription si présents
    if ($paiement->frais_inscription) {
        $totalFraisDetails += $paiement->frais_inscription;
    }
    
    // Ajouter les frais d'équipement si présents
    if ($paiement->frais_equipement) {
        $totalFraisDetails += $paiement->frais_equipement;
    }
    
    // Si aucun frais détaillé, utiliser le montant du paiement (cotisation)
    $totalAPayer = $totalFraisDetails > 0 ? $totalFraisDetails : $paiement->montant;
    
    // Calcul du reste à payer et du montant remboursé
    $resteAPayer = max(0, $totalAPayer - $paiement->montant_paye);
    $montantRembourse = max(0, $paiement->montant_paye - $totalAPayer);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between print:hidden">
            <div class="flex items-center">
                <a href="{{ route('paiements.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Paiement #{{ $paiement->id }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $paiement->athlete->nom_complet }}</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimer
                </button>
                <x-button href="{{ route('paiements.recu', $paiement) }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Recu PDF
                </x-button>
                @if(auth()->user()->isAdmin())
                <x-button href="{{ route('paiements.edit', $paiement) }}" variant="secondary">
                    Modifier
                </x-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" id="fiche-paiement">
        <!-- En-tete pour impression -->
        <div class="hidden print:block text-center mb-6 pb-4 border-b-2 border-gray-800">
            <h1 class="text-xl font-bold">CENTRE SPORTIF OBD</h1>
            <h2 class="text-lg font-semibold mt-1">FICHE DE PAIEMENT</h2>
            <p class="text-sm text-gray-600 mt-1">Reference: {{ $paiement->reference ?: 'REF-' . str_pad($paiement->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>

        <x-card>
            <!-- Header avec statut -->
            <div class="flex items-center justify-between pb-3 mb-4 border-b">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $paiement->athlete->nom_complet }}</h3>
                    <p class="text-sm text-gray-500">
                        Disciplines: {{ $paiement->athlete->disciplines->pluck('nom')->join(', ') ?: 'Aucune' }}
                    </p>
                </div>
                <div class="text-right">
                    @if($paiement->statut === 'paye')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800">
                            ✓ PAYE
                        </span>
                    @elseif($paiement->statut === 'partiel')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800">
                            ◐ PARTIEL
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800">
                            ✗ IMPAYE
                        </span>
                    @endif
                </div>
            </div>

            <!-- Informations en 2 colonnes -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mb-4">
                <div class="bg-gray-50 rounded p-2">
                    <span class="text-gray-500 block text-xs">Type</span>
                    <span class="font-medium">
                        @if($paiement->type_paiement === 'cotisation') Cotisation
                        @elseif($paiement->type_paiement === 'inscription') Inscription
                        @else Equipement @endif
                    </span>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    <span class="text-gray-500 block text-xs">Periode</span>
                    <span class="font-medium">{{ \Carbon\Carbon::create($paiement->annee, $paiement->mois, 1)->locale('fr')->isoFormat('MMMM YYYY') }}</span>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    <span class="text-gray-500 block text-xs">Mode</span>
                    <span class="font-medium">{{ \App\Models\Paiement::modesPaiement()[$paiement->mode_paiement] ?? $paiement->mode_paiement }}</span>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    <span class="text-gray-500 block text-xs">Reference</span>
                    <span class="font-medium text-primary-600">{{ $paiement->reference ?: 'REF-' . str_pad($paiement->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="bg-gray-50 rounded p-2">
                    <span class="text-gray-500 block text-xs">Date paiement</span>
                    <span class="font-medium">{{ $paiement->date_paiement?->format('d/m/Y') ?? '-' }}</span>
                </div>
            </div>

            <!-- Detail des frais (si applicable) -->
            @if($paiement->frais_inscription || $paiement->type_equipement)
            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Detail des frais</h4>
                <div class="space-y-1 text-sm">
                    @if($paiement->frais_inscription)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Frais d'inscription</span>
                        <span class="font-medium">{{ number_format($paiement->frais_inscription, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                    @if($paiement->type_equipement)
                    <div class="flex justify-between">
                        <span class="text-gray-600">
                            @switch($paiement->type_equipement)
                                @case('maillot') Maillot (Basket/Volley) @break
                                @case('dobok_enfant') Dobok Enfant @break
                                @case('dobok_junior') Dobok Junior @break
                                @case('dobok_senior') Dobok Senior @break
                                @default Equipement @endswitch
                        </span>
                        <span class="font-medium">{{ number_format($paiement->frais_equipement, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Montants - Section principale -->
            <div class="border-2 border-primary-200 rounded-lg overflow-hidden mb-4">
                <div class="bg-primary-50 px-4 py-2 border-b border-primary-200">
                    <h4 class="font-semibold text-primary-800">Recapitulatif financier</h4>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="text-center p-3 bg-gray-100 rounded-lg">
                            <div class="text-xs text-gray-500 mb-1">Total a payer</div>
                            <div class="text-xl font-bold text-gray-900">{{ number_format($totalAPayer, 0, ',', ' ') }}</div>
                            <div class="text-xs text-gray-500">FCFA</div>
                        </div>
                        <div class="text-center p-3 bg-green-100 rounded-lg">
                            <div class="text-xs text-green-600 mb-1">Montant paye</div>
                            <div class="text-xl font-bold text-green-600">{{ number_format($paiement->montant_paye, 0, ',', ' ') }}</div>
                            <div class="text-xs text-green-600">FCFA</div>
                        </div>
                        <div class="text-center p-3 {{ $resteAPayer > 0 ? 'bg-red-100' : 'bg-green-100' }} rounded-lg">
                            <div class="text-xs {{ $resteAPayer > 0 ? 'text-red-600' : 'text-green-600' }} mb-1">Reste a payer</div>
                            <div class="text-xl font-bold {{ $resteAPayer > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($resteAPayer, 0, ',', ' ') }}</div>
                            <div class="text-xs {{ $resteAPayer > 0 ? 'text-red-600' : 'text-green-600' }}">FCFA</div>
                        </div>
                        <div class="text-center p-3 {{ $montantRembourse > 0 ? 'bg-blue-100' : 'bg-gray-100' }} rounded-lg">
                            <div class="text-xs {{ $montantRembourse > 0 ? 'text-blue-600' : 'text-gray-500' }} mb-1">Montant rembourse</div>
                            <div class="text-xl font-bold {{ $montantRembourse > 0 ? 'text-blue-600' : 'text-gray-400' }}">{{ number_format($montantRembourse, 0, ',', ' ') }}</div>
                            <div class="text-xs {{ $montantRembourse > 0 ? 'text-blue-600' : 'text-gray-500' }}">FCFA</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remarque -->
            @if($paiement->remarque)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                <h4 class="text-sm font-semibold text-yellow-800 mb-1">Remarque</h4>
                <p class="text-sm text-yellow-700">{{ $paiement->remarque }}</p>
            </div>
            @endif

            <!-- Signatures (pour impression) -->
            <div class="grid grid-cols-2 gap-8 mt-6 pt-4 border-t print:mt-8">
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-8">Le Responsable</p>
                    <div class="border-t border-gray-300 pt-1">
                        <p class="text-xs text-gray-400">Signature et cachet</p>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 mb-8">Cachet du Centre</p>
                    <div class="border-t border-gray-300 pt-1">
                        <p class="text-xs text-gray-400">Centre Sportif OBD</p>
                    </div>
                </div>
            </div>

            <!-- Pied de page -->
            <div class="text-center mt-4 pt-3 border-t text-xs text-gray-400">
                <p class="font-medium text-primary-600">Centre Sportif OBD</p>
                <p>Merci de votre confiance. Ce recu fait foi de paiement.</p>
                <p>Document genere le {{ now()->format('d/m/Y') }} a {{ now()->format('H:i') }}</p>
            </div>
        </x-card>
    </div>

    <!-- Styles d'impression -->
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .print\:hidden { display: none !important; }
            .print\:block { display: block !important; }
            nav, header, .print\:hidden { display: none !important; }
            @page { margin: 1cm; size: A4 portrait; }
            #fiche-paiement { max-width: 100% !important; }
        }
    </style>
</x-app-layout>
