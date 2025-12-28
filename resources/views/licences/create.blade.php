@section('title', 'Nouvelle Licence')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouvelle Licence</h2>
                <p class="mt-1 text-sm text-gray-500">Créer une nouvelle licence sportive</p>
            </div>
            <x-button href="{{ route('licences.index') }}" variant="ghost">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('licences.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Athlète -->
                    <x-form-group label="Athlète" name="athlete_id" required>
                        <x-select 
                            name="athlete_id" 
                            :options="$athletes->map(fn($a) => ['id' => $a->id, 'name' => $a->nom_complet . ' (' . ($a->age ?? '?') . ' ans)'])->toArray()" 
                            :selected="old('athlete_id')"
                            placeholder="Sélectionner un athlète"
                            required
                        />
                    </x-form-group>

                    <!-- Discipline -->
                    <x-form-group label="Discipline" name="discipline_id" required>
                        <x-select 
                            name="discipline_id" 
                            :options="$disciplines" 
                            :selected="old('discipline_id')"
                            placeholder="Sélectionner une discipline"
                            valueKey="id"
                            labelKey="nom"
                            required
                        />
                    </x-form-group>

                    <!-- Fédération -->
                    <x-form-group label="Fédération" name="federation" required>
                        <x-input 
                            type="text" 
                            name="federation" 
                            :value="old('federation', 'FMJSEP')"
                            placeholder="Ex: FMJSEP"
                            required
                        />
                    </x-form-group>

                    <!-- Type -->
                    <x-form-group label="Type de licence" name="type" required>
                        <x-select 
                            name="type" 
                            :options="[
                                ['id' => 'nationale', 'name' => 'Nationale'],
                                ['id' => 'regionale', 'name' => 'Régionale'],
                                ['id' => 'locale', 'name' => 'Locale'],
                            ]" 
                            :selected="old('type', 'nationale')"
                            required
                        />
                    </x-form-group>

                    <!-- Catégorie -->
                    <x-form-group label="Catégorie" name="categorie">
                        <x-select 
                            name="categorie" 
                            :options="collect($categories)->map(fn($c) => ['id' => $c, 'name' => $c])->toArray()" 
                            :selected="old('categorie')"
                            placeholder="Auto-détection selon l'âge"
                        />
                        <p class="text-xs text-gray-500 mt-1">Laissez vide pour détection automatique</p>
                    </x-form-group>

                    <!-- Saison -->
                    <x-form-group label="Saison" name="saison">
                        <x-input 
                            type="text" 
                            name="saison" 
                            :value="old('saison', $saisonActuelle)"
                            placeholder="Ex: 2024-2025"
                        />
                    </x-form-group>

                    <!-- Date d'émission -->
                    <x-form-group label="Date d'émission" name="date_emission" required>
                        <x-input 
                            type="date" 
                            name="date_emission" 
                            :value="old('date_emission', now()->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <!-- Date d'expiration -->
                    <x-form-group label="Date d'expiration" name="date_expiration" required>
                        <x-input 
                            type="date" 
                            name="date_expiration" 
                            :value="old('date_expiration', now()->addYear()->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <!-- Frais de licence -->
                    <x-form-group label="Frais de licence (FCFA)" name="frais_licence" required>
                        <x-input 
                            type="number" 
                            name="frais_licence" 
                            :value="old('frais_licence', 15000)"
                            min="0"
                            step="500"
                            required
                        />
                    </x-form-group>

                    <!-- Payée -->
                    <x-form-group label="Statut paiement" name="paye">
                        <div class="flex items-center mt-2">
                            <input 
                                type="checkbox" 
                                name="paye" 
                                id="paye" 
                                value="1"
                                {{ old('paye') ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            >
                            <label for="paye" class="ml-2 text-sm text-gray-700">Licence payée</label>
                        </div>
                    </x-form-group>

                    <!-- Document -->
                    <x-form-group label="Document (scan)" name="document" class="md:col-span-2">
                        <input 
                            type="file" 
                            name="document" 
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                        >
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG ou PNG (max 5 Mo)</p>
                    </x-form-group>

                    <!-- Notes -->
                    <x-form-group label="Notes" name="notes" class="md:col-span-2">
                        <textarea 
                            name="notes" 
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Notes additionnelles..."
                        >{{ old('notes') }}</textarea>
                    </x-form-group>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button href="{{ route('licences.index') }}" variant="ghost">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Créer la licence
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
