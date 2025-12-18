@section('title', 'Nouvelle performance')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('performances.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouvelle performance</h2>
                <p class="mt-1 text-sm text-gray-500">Enregistrer une performance sportive</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="performanceForm()">
        <x-card>
            <form action="{{ route('performances.store') }}" method="POST">
                @csrf

                <!-- Informations de base -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                    <x-form-group label="Discipline" name="discipline_id" required>
                        <x-select 
                            name="discipline_id" 
                            :options="$disciplines" 
                            :selected="old('discipline_id', $disciplineId)"
                            placeholder="Selectionner une discipline"
                            valueKey="id"
                            labelKey="nom"
                            required
                        />
                    </x-form-group>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form-group label="Date d'evaluation" name="date_evaluation" required>
                        <x-input type="date" name="date_evaluation" :value="old('date_evaluation', date('Y-m-d'))" required />
                    </x-form-group>

                    <x-form-group label="Contexte" name="contexte" required>
                        <x-select 
                            name="contexte" 
                            x-model="contexte"
                            :options="[
                                ['id' => 'entrainement', 'name' => 'Entrainement'],
                                ['id' => 'match', 'name' => 'Match'],
                                ['id' => 'competition', 'name' => 'Competition'],
                                ['id' => 'test_physique', 'name' => 'Test physique'],
                            ]" 
                            :selected="old('contexte', 'entrainement')"
                            required
                        />
                    </x-form-group>
                </div>

                <x-form-group label="Type d'evaluation" name="type_evaluation">
                    <x-input name="type_evaluation" :value="old('type_evaluation')" placeholder="Ex: Test de vitesse, Endurance, Dribble..." />
                </x-form-group>

                <!-- Section Match -->
                <div x-show="contexte === 'match'" x-cloak class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-blue-800 mb-3">Informations du match</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-group label="Adversaire" name="adversaire">
                            <x-input name="adversaire" :value="old('adversaire')" placeholder="Nom de l'equipe adverse" />
                        </x-form-group>

                        <x-form-group label="Resultat" name="resultat_match">
                            <x-select 
                                name="resultat_match" 
                                :options="[
                                    ['id' => '', 'name' => 'Selectionner'],
                                    ['id' => 'victoire', 'name' => 'Victoire'],
                                    ['id' => 'defaite', 'name' => 'Defaite'],
                                    ['id' => 'nul', 'name' => 'Match nul'],
                                ]" 
                                :selected="old('resultat_match')"
                            />
                        </x-form-group>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <x-form-group label="Points marques" name="points_marques">
                            <x-input type="number" name="points_marques" :value="old('points_marques')" min="0" />
                        </x-form-group>

                        <x-form-group label="Points encaisses" name="points_encaisses">
                            <x-input type="number" name="points_encaisses" :value="old('points_encaisses')" min="0" />
                        </x-form-group>
                    </div>

                    <x-form-group label="Lieu" name="lieu">
                        <x-input name="lieu" :value="old('lieu')" placeholder="Lieu du match" />
                    </x-form-group>
                </div>

                <!-- Section Compétition -->
                <div x-show="contexte === 'competition'" x-cloak class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-yellow-800 mb-3">Informations de la competition</h3>
                    
                    <x-form-group label="Nom de la competition" name="competition">
                        <x-input name="competition" :value="old('competition')" placeholder="Ex: Championnat national, Tournoi regional..." />
                    </x-form-group>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-group label="Classement" name="classement">
                            <x-input type="number" name="classement" :value="old('classement')" min="1" placeholder="Position obtenue" />
                        </x-form-group>

                        <x-form-group label="Medaille" name="medaille">
                            <x-select 
                                name="medaille" 
                                :options="[
                                    ['id' => '', 'name' => 'Aucune medaille'],
                                    ['id' => 'or', 'name' => 'Or'],
                                    ['id' => 'argent', 'name' => 'Argent'],
                                    ['id' => 'bronze', 'name' => 'Bronze'],
                                ]" 
                                :selected="old('medaille')"
                            />
                        </x-form-group>
                    </div>

                    <x-form-group label="Lieu" name="lieu">
                        <x-input name="lieu" :value="old('lieu')" placeholder="Lieu de la competition" />
                    </x-form-group>
                </div>

                <!-- Section Test physique / Score -->
                <div x-show="contexte === 'test_physique' || contexte === 'entrainement'" x-cloak class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-purple-800 mb-3">Mesures et scores</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-group label="Score" name="score">
                            <x-input type="number" name="score" :value="old('score')" step="0.01" />
                        </x-form-group>

                        <x-form-group label="Unite" name="unite">
                            <x-input name="unite" :value="old('unite')" placeholder="Ex: secondes, metres, kg..." />
                        </x-form-group>
                    </div>
                </div>

                <!-- Notes d'évaluation -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-green-800 mb-3">Notes d'evaluation (1 a 10)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-form-group label="Condition physique" name="note_physique">
                            <x-input type="number" name="note_physique" :value="old('note_physique')" min="1" max="10" placeholder="1-10" />
                        </x-form-group>

                        <x-form-group label="Technique" name="note_technique">
                            <x-input type="number" name="note_technique" :value="old('note_technique')" min="1" max="10" placeholder="1-10" />
                        </x-form-group>

                        <x-form-group label="Comportement" name="note_comportement">
                            <x-input type="number" name="note_comportement" :value="old('note_comportement')" min="1" max="10" placeholder="1-10" />
                        </x-form-group>
                    </div>
                    <p class="text-xs text-green-600 mt-2">La note globale sera calculee automatiquement</p>
                </div>

                <x-form-group label="Observations" name="observations">
                    <x-textarea name="observations" :value="old('observations')" rows="3" placeholder="Remarques, points forts, axes d'amelioration..." />
                </x-form-group>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('performances.index') }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Enregistrer</x-button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function performanceForm() {
            return {
                contexte: '{{ old('contexte', 'entrainement') }}'
            }
        }
    </script>
    @endpush
</x-app-layout>
