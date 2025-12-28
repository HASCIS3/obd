@section('title', 'Modifier Facture')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier la Facture</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $facture->numero }}</p>
            </div>
            <x-button href="{{ route('factures.show', $facture) }}" variant="ghost">
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('factures.update', $facture) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-group label="Numéro" name="numero">
                        <div class="mt-1 p-2 bg-gray-100 rounded-md text-gray-700 font-mono">
                            {{ $facture->numero }}
                        </div>
                    </x-form-group>

                    <x-form-group label="Athlète" name="athlete_id">
                        <div class="mt-1 p-2 bg-gray-100 rounded-md text-gray-700">
                            {{ $facture->athlete->nom_complet }}
                        </div>
                    </x-form-group>

                    <x-form-group label="Date d'émission" name="date_emission" required>
                        <x-input 
                            type="date" 
                            name="date_emission" 
                            :value="old('date_emission', $facture->date_emission->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Date d'échéance" name="date_echeance" required>
                        <x-input 
                            type="date" 
                            name="date_echeance" 
                            :value="old('date_echeance', $facture->date_echeance->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Période" name="periode">
                        <x-input 
                            type="text" 
                            name="periode" 
                            :value="old('periode', $facture->periode)"
                            placeholder="Ex: Janvier 2025"
                        />
                    </x-form-group>

                    <x-form-group label="Montant HT (FCFA)" name="montant_ht" required>
                        <x-input 
                            type="number" 
                            name="montant_ht" 
                            :value="old('montant_ht', $facture->montant_ht)"
                            min="0"
                            step="100"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="TVA (%)" name="tva">
                        <x-input 
                            type="number" 
                            name="tva" 
                            :value="old('tva', $facture->tva)"
                            min="0"
                            max="100"
                            step="0.5"
                        />
                    </x-form-group>

                    <x-form-group label="Description" name="description" class="md:col-span-2">
                        <textarea 
                            name="description" 
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                        >{{ old('description', $facture->description) }}</textarea>
                    </x-form-group>

                    <x-form-group label="Notes internes" name="notes" class="md:col-span-2">
                        <textarea 
                            name="notes" 
                            rows="2"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                        >{{ old('notes', $facture->notes) }}</textarea>
                    </x-form-group>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button href="{{ route('factures.show', $facture) }}" variant="ghost">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Enregistrer
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
