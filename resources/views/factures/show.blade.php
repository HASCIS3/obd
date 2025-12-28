@section('title', 'Facture ' . $facture->numero)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Facture {{ $facture->numero }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $facture->athlete->nom_complet }}</p>
            </div>
            <div class="flex gap-2">
                <x-button href="{{ route('factures.pdf', $facture) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    PDF
                </x-button>
                <x-button href="{{ route('factures.index') }}" variant="ghost">
                    Retour
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Détails facture -->
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <div class="flex items-center justify-between mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $facture->statut_badge_class }}">
                            {{ $facture->statut_label }}
                        </span>
                        @if($facture->est_en_retard)
                            <span class="text-red-600 text-sm font-medium">⚠️ En retard</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Athlète</h4>
                            <p class="mt-1">
                                <a href="{{ route('athletes.show', $facture->athlete) }}" class="text-primary-600 hover:underline">
                                    {{ $facture->athlete->nom_complet }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Période</h4>
                            <p class="mt-1">{{ $facture->periode ?? '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Date d'émission</h4>
                            <p class="mt-1">{{ $facture->date_emission->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Date d'échéance</h4>
                            <p class="mt-1 {{ $facture->est_en_retard ? 'text-red-600 font-medium' : '' }}">
                                {{ $facture->date_echeance->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    @if($facture->description)
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="text-sm font-medium text-gray-500">Description</h4>
                            <p class="mt-1 text-gray-700">{{ $facture->description }}</p>
                        </div>
                    @endif
                </x-card>

                <!-- Montants -->
                <x-card>
                    <h3 class="text-lg font-semibold mb-4">Montants</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Montant HT</span>
                            <span>{{ number_format($facture->montant_ht, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if($facture->tva > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">TVA ({{ $facture->tva }}%)</span>
                                <span>{{ number_format($facture->montant_ttc - $facture->montant_ht, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-semibold text-lg border-t pt-3">
                            <span>Montant TTC</span>
                            <span>{{ number_format($facture->montant_ttc, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>Montant payé</span>
                            <span>{{ number_format($facture->montant_paye, 0, ',', ' ') }} FCFA</span>
                        </div>
                        @if($facture->reste_a_payer > 0)
                            <div class="flex justify-between text-red-600 font-semibold">
                                <span>Reste à payer</span>
                                <span>{{ number_format($facture->reste_a_payer, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>

            <!-- Actions -->
            <div class="space-y-6">
                <x-card>
                    <h3 class="text-lg font-semibold mb-4">Actions</h3>
                    <div class="space-y-3">
                        @if($facture->statut === 'brouillon')
                            <form action="{{ route('factures.emettre', $facture) }}" method="POST">
                                @csrf
                                <x-button type="submit" variant="primary" class="w-full">
                                    Émettre la facture
                                </x-button>
                            </form>
                            <x-button href="{{ route('factures.edit', $facture) }}" variant="secondary" class="w-full">
                                Modifier
                            </x-button>
                        @endif

                        @if(in_array($facture->statut, ['emise', 'partiellement_payee']))
                            <form action="{{ route('factures.paiement', $facture) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Enregistrer un paiement</label>
                                    <x-input 
                                        type="number" 
                                        name="montant" 
                                        placeholder="Montant en FCFA"
                                        min="0"
                                        max="{{ $facture->reste_a_payer }}"
                                        step="100"
                                        required
                                    />
                                </div>
                                <x-button type="submit" variant="success" class="w-full">
                                    Enregistrer le paiement
                                </x-button>
                            </form>

                            <form action="{{ route('factures.annuler', $facture) }}" method="POST" onsubmit="return confirm('Annuler cette facture ?')">
                                @csrf
                                <x-button type="submit" variant="danger" class="w-full">
                                    Annuler la facture
                                </x-button>
                            </form>
                        @endif

                        @if(in_array($facture->statut, ['brouillon', 'annulee']))
                            <form action="{{ route('factures.destroy', $facture) }}" method="POST" onsubmit="return confirm('Supprimer cette facture ?')">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="danger" class="w-full">
                                    Supprimer
                                </x-button>
                            </form>
                        @endif
                    </div>
                </x-card>

                @if($facture->notes)
                    <x-card>
                        <h3 class="text-lg font-semibold mb-2">Notes internes</h3>
                        <p class="text-sm text-gray-600">{{ $facture->notes }}</p>
                    </x-card>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
