@section('title', 'Nouvelle Facture')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouvelle Facture</h2>
                <p class="mt-1 text-sm text-gray-500">Créer une facture</p>
            </div>
            <x-button href="{{ route('factures.index') }}" variant="ghost">
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('factures.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-group label="Numéro" name="numero">
                        <div class="mt-1 p-2 bg-gray-100 rounded-md text-gray-700 font-mono">
                            {{ $numero }}
                        </div>
                    </x-form-group>

                    <x-form-group label="Athlète" name="athlete_id" required>
                        <x-select 
                            name="athlete_id" 
                            :options="$athletes->map(fn($a) => ['id' => $a->id, 'name' => $a->nom_complet])->toArray()" 
                            :selected="old('athlete_id', $athleteId)"
                            placeholder="Sélectionner un athlète"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Date d'émission" name="date_emission" required>
                        <x-input 
                            type="date" 
                            name="date_emission" 
                            :value="old('date_emission', now()->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Date d'échéance" name="date_echeance" required>
                        <x-input 
                            type="date" 
                            name="date_echeance" 
                            :value="old('date_echeance', now()->addDays(30)->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Période" name="periode">
                        <x-input 
                            type="text" 
                            name="periode" 
                            :value="old('periode')"
                            placeholder="Ex: Janvier 2025"
                        />
                    </x-form-group>

                    <x-form-group label="Montant HT (FCFA)" name="montant_ht" required>
                        <x-input 
                            type="number" 
                            name="montant_ht" 
                            :value="old('montant_ht')"
                            min="0"
                            step="100"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="TVA (%)" name="tva">
                        <x-input 
                            type="number" 
                            name="tva" 
                            :value="old('tva', 0)"
                            min="0"
                            max="100"
                            step="0.5"
                        />
                        <p class="text-xs text-gray-500 mt-1">0% par défaut (pas de TVA)</p>
                    </x-form-group>

                    <x-form-group label="Description" name="description" class="md:col-span-2">
                        <textarea 
                            name="description" 
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Détails de la facture..."
                        >{{ old('description') }}</textarea>
                    </x-form-group>

                    <x-form-group label="Notes internes" name="notes" class="md:col-span-2">
                        <textarea 
                            name="notes" 
                            rows="2"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Notes internes (non visibles sur la facture)..."
                        >{{ old('notes') }}</textarea>
                    </x-form-group>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button href="{{ route('factures.index') }}" variant="ghost">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Créer la facture
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
