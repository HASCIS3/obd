@section('title', 'Modifier paiement')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('paiements.show', $paiement) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier le paiement</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $paiement->athlete->nom_complet }} - {{ str_pad($paiement->mois, 2, '0', STR_PAD_LEFT) }}/{{ $paiement->annee }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('paiements.update', $paiement) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <x-form-group label="Mois" name="mois" required>
                        <x-select 
                            name="mois" 
                            :options="[
                                ['id' => 1, 'name' => 'Janvier'], ['id' => 2, 'name' => 'Fevrier'],
                                ['id' => 3, 'name' => 'Mars'], ['id' => 4, 'name' => 'Avril'],
                                ['id' => 5, 'name' => 'Mai'], ['id' => 6, 'name' => 'Juin'],
                                ['id' => 7, 'name' => 'Juillet'], ['id' => 8, 'name' => 'Aout'],
                                ['id' => 9, 'name' => 'Septembre'], ['id' => 10, 'name' => 'Octobre'],
                                ['id' => 11, 'name' => 'Novembre'], ['id' => 12, 'name' => 'Decembre'],
                            ]" 
                            :selected="old('mois', $paiement->mois)"
                            placeholder=""
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Annee" name="annee" required>
                        <x-input type="number" name="annee" :value="old('annee', $paiement->annee)" min="2020" max="2100" required />
                    </x-form-group>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <x-form-group label="Montant du (FCFA)" name="montant" required>
                        <x-input type="number" name="montant" :value="old('montant', $paiement->montant)" min="0" step="500" required />
                    </x-form-group>

                    <x-form-group label="Montant paye (FCFA)" name="montant_paye" required>
                        <x-input type="number" name="montant_paye" :value="old('montant_paye', $paiement->montant_paye)" min="0" step="500" required />
                    </x-form-group>
                </div>

                <x-form-group label="Mode de paiement" name="mode_paiement" required>
                    <x-select 
                        name="mode_paiement" 
                        :options="[
                            ['id' => 'especes', 'name' => 'Especes'],
                            ['id' => 'virement', 'name' => 'Virement bancaire'],
                            ['id' => 'mobile_money', 'name' => 'Mobile Money'],
                        ]" 
                        :selected="old('mode_paiement', $paiement->mode_paiement)"
                        placeholder=""
                        required
                    />
                </x-form-group>

                <x-form-group label="Date de paiement" name="date_paiement">
                    <x-input type="date" name="date_paiement" :value="old('date_paiement', $paiement->date_paiement?->format('Y-m-d'))" />
                </x-form-group>

                <x-form-group label="Reference" name="reference">
                    <x-input name="reference" :value="old('reference', $paiement->reference)" />
                </x-form-group>

                <x-form-group label="Remarque" name="remarque">
                    <x-textarea name="remarque" :value="old('remarque', $paiement->remarque)" rows="2" />
                </x-form-group>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('paiements.show', $paiement) }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Mettre a jour</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
