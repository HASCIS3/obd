@section('title', 'Modifier Licence')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier la Licence</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $licence->numero_licence }}</p>
            </div>
            <x-button href="{{ route('licences.show', $licence) }}" variant="ghost">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('licences.update', $licence) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Athlète (lecture seule) -->
                    <x-form-group label="Athlète" name="athlete">
                        <div class="mt-1 p-2 bg-gray-100 rounded-md text-gray-700">
                            {{ $licence->athlete->nom_complet }}
                        </div>
                    </x-form-group>

                    <!-- Discipline (lecture seule) -->
                    <x-form-group label="Discipline" name="discipline">
                        <div class="mt-1 p-2 bg-gray-100 rounded-md text-gray-700">
                            {{ $licence->discipline->nom }}
                        </div>
                    </x-form-group>

                    <!-- Fédération -->
                    <x-form-group label="Fédération" name="federation" required>
                        <x-input 
                            type="text" 
                            name="federation" 
                            :value="old('federation', $licence->federation)"
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
                            :selected="old('type', $licence->type)"
                            required
                        />
                    </x-form-group>

                    <!-- Catégorie -->
                    <x-form-group label="Catégorie" name="categorie">
                        <x-select 
                            name="categorie" 
                            :options="collect($categories)->map(fn($c) => ['id' => $c, 'name' => $c])->toArray()" 
                            :selected="old('categorie', $licence->categorie)"
                            placeholder="Sélectionner une catégorie"
                        />
                    </x-form-group>

                    <!-- Statut -->
                    <x-form-group label="Statut" name="statut" required>
                        <x-select 
                            name="statut" 
                            :options="[
                                ['id' => 'active', 'name' => 'Active'],
                                ['id' => 'expiree', 'name' => 'Expirée'],
                                ['id' => 'suspendue', 'name' => 'Suspendue'],
                                ['id' => 'annulee', 'name' => 'Annulée'],
                            ]" 
                            :selected="old('statut', $licence->statut)"
                            required
                        />
                    </x-form-group>

                    <!-- Saison -->
                    <x-form-group label="Saison" name="saison">
                        <x-input 
                            type="text" 
                            name="saison" 
                            :value="old('saison', $licence->saison)"
                            placeholder="Ex: 2024-2025"
                        />
                    </x-form-group>

                    <!-- Date d'émission -->
                    <x-form-group label="Date d'émission" name="date_emission" required>
                        <x-input 
                            type="date" 
                            name="date_emission" 
                            :value="old('date_emission', $licence->date_emission->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <!-- Date d'expiration -->
                    <x-form-group label="Date d'expiration" name="date_expiration" required>
                        <x-input 
                            type="date" 
                            name="date_expiration" 
                            :value="old('date_expiration', $licence->date_expiration->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <!-- Frais de licence -->
                    <x-form-group label="Frais de licence (FCFA)" name="frais_licence" required>
                        <x-input 
                            type="number" 
                            name="frais_licence" 
                            :value="old('frais_licence', $licence->frais_licence)"
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
                                {{ old('paye', $licence->paye) ? 'checked' : '' }}
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            >
                            <label for="paye" class="ml-2 text-sm text-gray-700">Licence payée</label>
                        </div>
                    </x-form-group>

                    <!-- Document -->
                    <x-form-group label="Document (scan)" name="document" class="md:col-span-2">
                        @if($licence->document)
                            <div class="mb-2">
                                <a href="{{ $licence->document_url }}" target="_blank" class="text-primary-600 hover:underline text-sm">
                                    Voir le document actuel
                                </a>
                            </div>
                        @endif
                        <input 
                            type="file" 
                            name="document" 
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                        >
                        <p class="text-xs text-gray-500 mt-1">Laisser vide pour conserver le document actuel</p>
                    </x-form-group>

                    <!-- Notes -->
                    <x-form-group label="Notes" name="notes" class="md:col-span-2">
                        <textarea 
                            name="notes" 
                            rows="3"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                        >{{ old('notes', $licence->notes) }}</textarea>
                    </x-form-group>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button href="{{ route('licences.show', $licence) }}" variant="ghost">
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
