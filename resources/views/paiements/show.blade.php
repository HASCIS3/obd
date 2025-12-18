@section('title', 'Paiement #' . $paiement->id)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
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
                <x-button href="{{ route('paiements.recu', $paiement) }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Télécharger le reçu
                </x-button>
                @if(auth()->user()->isAdmin())
                <x-button href="{{ route('paiements.edit', $paiement) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <!-- Statut -->
            <div class="flex items-center justify-between pb-4 mb-4 border-b">
                <span class="text-lg font-medium text-gray-900">Statut du paiement</span>
                @if($paiement->statut === 'paye')
                    <x-badge color="success" size="lg">Paye</x-badge>
                @elseif($paiement->statut === 'partiel')
                    <x-badge color="warning" size="lg">Partiel</x-badge>
                @else
                    <x-badge color="danger" size="lg">Impaye</x-badge>
                @endif
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Athlete</dt>
                    <dd class="mt-1">
                        <a href="{{ route('athletes.show', $paiement->athlete) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                            {{ $paiement->athlete->nom_complet }}
                        </a>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Type de paiement</dt>
                    <dd class="mt-1">
                        @if($paiement->type_paiement === 'cotisation')
                            <x-badge color="primary">Cotisation mensuelle</x-badge>
                        @elseif($paiement->type_paiement === 'inscription')
                            <x-badge color="success">Frais d'inscription</x-badge>
                        @else
                            <x-badge color="warning">Equipement</x-badge>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Periode</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ str_pad($paiement->mois, 2, '0', STR_PAD_LEFT) }}/{{ $paiement->annee }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Mode de paiement</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ \App\Models\Paiement::modesPaiement()[$paiement->mode_paiement] ?? $paiement->mode_paiement }}</dd>
                </div>
            </dl>

            <!-- Détails des frais -->
            @if($paiement->type_paiement === 'inscription' || $paiement->type_paiement === 'equipement')
            <div class="mt-6 pt-4 border-t">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Détail des frais</h3>
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    @if($paiement->frais_inscription)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Frais d'inscription</span>
                        <span class="font-semibold">{{ number_format($paiement->frais_inscription, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                    @if($paiement->type_equipement)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">
                            @switch($paiement->type_equipement)
                                @case('maillot')
                                    Maillot (Basket/Volley) - 4 000 FCFA
                                    @break
                                @case('dobok_enfant')
                                    Dobok Enfant (Taekwondo)
                                    @break
                                @case('dobok_junior')
                                    Dobok Junior (Taekwondo)
                                    @break
                                @case('dobok_senior')
                                    Dobok Senior (Taekwondo)
                                    @break
                                @default
                                    Dobok (Taekwondo)
                            @endswitch
                        </span>
                        <span class="font-semibold">{{ number_format($paiement->frais_equipement, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="font-semibold text-gray-900">Total</span>
                        <span class="font-bold text-lg text-primary-600">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Montants -->
            <div class="mt-6 pt-4 border-t">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Montants</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <dt class="text-sm font-medium text-gray-500">Montant dû</dt>
                        <dd class="mt-1 text-xl font-bold text-gray-900">{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <dt class="text-sm font-medium text-green-600">Montant payé</dt>
                        <dd class="mt-1 text-xl font-bold text-green-600">{{ number_format($paiement->montant_paye, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    @if($paiement->reste_a_payer > 0)
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <dt class="text-sm font-medium text-red-600">Reste à payer</dt>
                        <dd class="mt-1 text-xl font-bold text-red-600">{{ number_format($paiement->reste_a_payer, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    @else
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <dt class="text-sm font-medium text-green-600">Reste à payer</dt>
                        <dd class="mt-1 text-xl font-bold text-green-600">0 FCFA</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Autres informations -->
            <div class="mt-6 pt-4 border-t">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de paiement</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paiement->date_paiement?->format('d/m/Y') ?? 'Non renseignée' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Référence</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paiement->reference ?: 'Aucune' }}</dd>
                    </div>
                    @if($paiement->remarque)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Remarque</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $paiement->remarque }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </x-card>
    </div>
</x-app-layout>
