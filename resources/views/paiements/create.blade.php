@section('title', 'Nouveau paiement')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('paiements.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouveau paiement</h2>
                <p class="mt-1 text-sm text-gray-500">Enregistrer un paiement</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('paiements.store') }}" method="POST" x-data="paiementForm()">
                @csrf

                <x-form-group label="Athlete" name="athlete_id" required>
                    <x-select 
                        name="athlete_id" 
                        :options="$athletes" 
                        :selected="old('athlete_id', $athleteId)"
                        placeholder="Selectionner un athlete"
                        valueKey="id"
                        labelKey="nom_complet"
                        required
                    />
                </x-form-group>

                <!-- Type de paiement -->
                <x-form-group label="Type de paiement" name="type_paiement" required>
                    <x-select 
                        name="type_paiement" 
                        x-model="typePaiement"
                        @change="updateMontant()"
                        :options="[
                            ['id' => 'cotisation', 'name' => 'Cotisation mensuelle'],
                            ['id' => 'inscription', 'name' => 'Frais d\'inscription'],
                            ['id' => 'equipement', 'name' => 'Equipement uniquement'],
                        ]" 
                        :selected="old('type_paiement', 'cotisation')"
                        placeholder=""
                        required
                    />
                </x-form-group>

                <!-- Section Cotisation mensuelle -->
                <div x-show="typePaiement === 'cotisation'" x-cloak>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-blue-800 mb-3">Cotisation mensuelle</h3>
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
                                    :selected="old('mois', now()->month)"
                                    placeholder=""
                                />
                            </x-form-group>

                            <x-form-group label="Annee" name="annee" required>
                                <x-input type="number" name="annee" :value="old('annee', now()->year)" min="2020" max="2100" />
                            </x-form-group>
                        </div>
                        <x-form-group label="Montant cotisation (FCFA)" name="montant" required>
                            <x-input type="number" name="montant" x-model="montantCotisation" @input="updateMontant()" :value="old('montant', 2000)" min="0" step="500" />
                        </x-form-group>
                    </div>
                </div>

                <!-- Section Inscription -->
                <div x-show="typePaiement === 'inscription'" x-cloak>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-green-800 mb-3">Frais d'inscription</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-group label="Mois" name="mois">
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
                                    :selected="old('mois', now()->month)"
                                    placeholder=""
                                />
                            </x-form-group>

                            <x-form-group label="Annee" name="annee">
                                <x-input type="number" name="annee" :value="old('annee', now()->year)" min="2020" max="2100" />
                            </x-form-group>
                        </div>

                        <x-form-group label="Frais d'inscription (FCFA)" name="frais_inscription" required>
                            <x-input type="number" name="frais_inscription" x-model="fraisInscription" @input="updateMontant()" :value="old('frais_inscription', 5000)" min="0" step="500" placeholder="Ex: 5000" />
                        </x-form-group>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-yellow-800 mb-3">Equipement (optionnel)</h3>
                        
                        <x-form-group label="Type d'equipement" name="type_equipement">
                            <x-select 
                                name="type_equipement" 
                                x-model="typeEquipement"
                                @change="updateMontant()"
                                :options="[
                                    ['id' => '', 'name' => 'Aucun equipement'],
                                    ['id' => 'maillot', 'name' => 'Maillot (Basket/Volley) - 4 000 FCFA'],
                                    ['id' => 'dobok_enfant', 'name' => 'Dobok Enfant (Taekwondo) - 5 000 FCFA'],
                                    ['id' => 'dobok_junior', 'name' => 'Dobok Junior (Taekwondo) - 6 000 à 7 000 FCFA'],
                                    ['id' => 'dobok_senior', 'name' => 'Dobok Senior (Taekwondo) - 8 000 à 10 000 FCFA'],
                                ]" 
                                :selected="old('type_equipement', '')"
                                placeholder=""
                            />
                        </x-form-group>

                        <div x-show="typeEquipement !== ''" x-cloak>
                            <x-form-group label="Prix equipement (FCFA)" name="frais_equipement">
                                <x-input type="number" name="frais_equipement" x-model="fraisEquipement" @input="updateMontant()" :value="old('frais_equipement', 3000)" min="0" step="500" placeholder="Ex: 3000" />
                            </x-form-group>
                        </div>
                    </div>

                    <!-- Champ montant caché pour inscription -->
                    <input type="hidden" name="montant" x-bind:value="montantTotal">
                </div>

                <!-- Section Equipement uniquement -->
                <div x-show="typePaiement === 'equipement'" x-cloak>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-yellow-800 mb-3">Achat d'equipement</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-group label="Mois" name="mois">
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
                                    :selected="old('mois', now()->month)"
                                    placeholder=""
                                />
                            </x-form-group>

                            <x-form-group label="Annee" name="annee">
                                <x-input type="number" name="annee" :value="old('annee', now()->year)" min="2020" max="2100" />
                            </x-form-group>
                        </div>

                        <x-form-group label="Type d'equipement" name="type_equipement" required>
                            <x-select 
                                name="type_equipement" 
                                x-model="typeEquipementSeul"
                                @change="updateMontant()"
                                :options="[
                                    ['id' => 'maillot', 'name' => 'Maillot (Basket/Volley) - 4 000 FCFA'],
                                    ['id' => 'dobok_enfant', 'name' => 'Dobok Enfant (Taekwondo) - 5 000 FCFA'],
                                    ['id' => 'dobok_junior', 'name' => 'Dobok Junior (Taekwondo) - 6 000 à 7 000 FCFA'],
                                    ['id' => 'dobok_senior', 'name' => 'Dobok Senior (Taekwondo) - 8 000 à 10 000 FCFA'],
                                ]" 
                                :selected="old('type_equipement', 'maillot')"
                                placeholder="Selectionner un equipement"
                            />
                        </x-form-group>

                        <x-form-group label="Prix equipement (FCFA)" name="frais_equipement" required>
                            <x-input type="number" name="frais_equipement" x-model="fraisEquipementSeul" @input="updateMontant()" :value="old('frais_equipement', 3000)" min="0" step="500" placeholder="Ex: 3000" />
                        </x-form-group>
                    </div>

                    <!-- Champ montant caché pour équipement -->
                    <input type="hidden" name="montant" x-bind:value="fraisEquipementSeul">
                </div>

                <!-- Récapitulatif du montant total -->
                <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Montant total a payer:</span>
                        <span class="text-2xl font-bold text-primary-600" x-text="formatMontant(montantTotal) + ' FCFA'"></span>
                    </div>
                    <div x-show="typePaiement === 'inscription' && typeEquipement !== ''" class="mt-2 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Frais d'inscription:</span>
                            <span x-text="formatMontant(fraisInscription) + ' FCFA'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Equipement:</span>
                            <span x-text="formatMontant(fraisEquipement) + ' FCFA'"></span>
                        </div>
                    </div>
                </div>

                <x-form-group label="Montant paye (FCFA)" name="montant_paye" required>
                    <x-input type="number" name="montant_paye" :value="old('montant_paye', 0)" min="0" step="500" required />
                </x-form-group>

                <x-form-group label="Mode de paiement" name="mode_paiement" required>
                    <x-select 
                        name="mode_paiement" 
                        :options="[
                            ['id' => 'especes', 'name' => 'Especes'],
                            ['id' => 'virement', 'name' => 'Virement bancaire'],
                            ['id' => 'mobile_money', 'name' => 'Mobile Money'],
                        ]" 
                        :selected="old('mode_paiement', 'especes')"
                        placeholder=""
                        required
                    />
                </x-form-group>

                <x-form-group label="Date de paiement" name="date_paiement">
                    <x-input type="date" name="date_paiement" :value="old('date_paiement', date('Y-m-d'))" />
                </x-form-group>

                <x-form-group label="Reference" name="reference">
                    <x-input name="reference" :value="old('reference')" placeholder="Numero de recu, transaction..." />
                </x-form-group>

                <x-form-group label="Remarque" name="remarque">
                    <x-textarea name="remarque" :value="old('remarque')" rows="2" />
                </x-form-group>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('paiements.index') }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Enregistrer</x-button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function paiementForm() {
            return {
                typePaiement: '{{ old('type_paiement', 'cotisation') }}',
                montantCotisation: {{ old('montant', 2000) }},
                fraisInscription: {{ old('frais_inscription', 2000) }},
                typeEquipement: '{{ old('type_equipement', '') }}',
                fraisEquipement: {{ old('frais_equipement', 4000) }},
                typeEquipementSeul: '{{ old('type_equipement', 'maillot') }}',
                fraisEquipementSeul: {{ old('frais_equipement', 4000) }},
                montantTotal: 0,

                init() {
                    this.updateMontant();
                },

                prixEquipements: {
                    'maillot': 4000,
                    'dobok_enfant': 5000,
                    'dobok_junior': 7000,
                    'dobok_senior': 10000
                },

                updateMontant() {
                    if (this.typePaiement === 'cotisation') {
                        this.montantTotal = parseFloat(this.montantCotisation) || 0;
                    } else if (this.typePaiement === 'inscription') {
                        let total = parseFloat(this.fraisInscription) || 0;
                        if (this.typeEquipement !== '') {
                            // Mettre à jour le prix de l'équipement selon le type sélectionné
                            if (this.prixEquipements[this.typeEquipement]) {
                                this.fraisEquipement = this.prixEquipements[this.typeEquipement];
                            }
                            total += parseFloat(this.fraisEquipement) || 0;
                        }
                        this.montantTotal = total;
                    } else if (this.typePaiement === 'equipement') {
                        // Mettre à jour le prix de l'équipement selon le type sélectionné
                        if (this.prixEquipements[this.typeEquipementSeul]) {
                            this.fraisEquipementSeul = this.prixEquipements[this.typeEquipementSeul];
                        }
                        this.montantTotal = parseFloat(this.fraisEquipementSeul) || 0;
                    }
                },

                formatMontant(montant) {
                    return new Intl.NumberFormat('fr-FR').format(montant);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
