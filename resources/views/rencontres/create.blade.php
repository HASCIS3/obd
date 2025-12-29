@section('title', 'Nouveau match')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('rencontres.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouveau match</h2>
                <p class="mt-1 text-sm text-gray-500">Enregistrer une nouvelle rencontre sportive</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('rencontres.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Discipline -->
                    <div>
                        <x-input-label for="discipline_id">Discipline *</x-input-label>
                        <select name="discipline_id" id="discipline_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Selectionnez une discipline</option>
                            @foreach($disciplines as $discipline)
                                <option value="{{ $discipline->id }}" {{ old('discipline_id') == $discipline->id ? 'selected' : '' }}>
                                    {{ $discipline->nom }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('discipline_id')" class="mt-2" />
                    </div>

                    <!-- Adversaire -->
                    <div>
                        <x-input-label for="adversaire" required>Adversaire</x-input-label>
                        <x-input type="text" name="adversaire" id="adversaire" value="{{ old('adversaire') }}" placeholder="Ex: Equipe Senou" required />
                        <x-input-error :messages="$errors->get('adversaire')" class="mt-2" />
                    </div>

                    <!-- Date du match -->
                    <div>
                        <x-input-label for="date_match" required>Date du match</x-input-label>
                        <x-input type="date" name="date_match" id="date_match" value="{{ old('date_match', now()->format('Y-m-d')) }}" required />
                        <x-input-error :messages="$errors->get('date_match')" class="mt-2" />
                    </div>

                    <!-- Heure du match -->
                    <div>
                        <x-input-label for="heure_match">Heure du match</x-input-label>
                        <x-input type="time" name="heure_match" id="heure_match" value="{{ old('heure_match') }}" />
                        <x-input-error :messages="$errors->get('heure_match')" class="mt-2" />
                    </div>

                    <!-- Type de match -->
                    <div>
                        <x-input-label for="type_match" required>Type de match</x-input-label>
                        <select name="type_match" id="type_match" required class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            @foreach(\App\Models\Rencontre::typesMatch() as $key => $label)
                                <option value="{{ $key }}" {{ old('type_match', 'domicile') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('type_match')" class="mt-2" />
                    </div>

                    <!-- Lieu -->
                    <div>
                        <x-input-label for="lieu">Lieu</x-input-label>
                        <x-input type="text" name="lieu" id="lieu" value="{{ old('lieu') }}" placeholder="Ex: Terrain OBD" />
                        <x-input-error :messages="$errors->get('lieu')" class="mt-2" />
                    </div>

                    <!-- Type de compétition -->
                    <div>
                        <x-input-label for="type_competition" required>Type de competition</x-input-label>
                        <select name="type_competition" id="type_competition" required class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            @foreach(\App\Models\Rencontre::typesCompetition() as $key => $label)
                                <option value="{{ $key }}" {{ old('type_competition', 'amical') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('type_competition')" class="mt-2" />
                    </div>

                    <!-- Nom de la compétition -->
                    <div>
                        <x-input-label for="nom_competition">Nom de la competition</x-input-label>
                        <x-input type="text" name="nom_competition" id="nom_competition" value="{{ old('nom_competition') }}" placeholder="Ex: Championnat regional 2025" />
                        <x-input-error :messages="$errors->get('nom_competition')" class="mt-2" />
                    </div>

                    <!-- Saison -->
                    <div>
                        <x-input-label for="saison">Saison</x-input-label>
                        <x-input type="text" name="saison" id="saison" value="{{ old('saison', $saison) }}" placeholder="Ex: 2024-2025" />
                        <x-input-error :messages="$errors->get('saison')" class="mt-2" />
                    </div>

                    <!-- Phase -->
                    <div>
                        <x-input-label for="phase">Phase</x-input-label>
                        <select name="phase" id="phase" class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Selectionnez une phase</option>
                            @foreach(\App\Models\Rencontre::phases() as $key => $label)
                                <option value="{{ $key }}" {{ old('phase') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('phase')" class="mt-2" />
                    </div>
                </div>

                <!-- Section Résultat -->
                <div class="mt-8 pt-6 border-t">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Resultat du match</h3>
                    <p class="text-sm text-gray-500 mb-4">Laissez vide si le match n'a pas encore ete joue</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Résultat -->
                        <div>
                            <x-input-label for="resultat" required>Statut</x-input-label>
                            <select name="resultat" id="resultat" required class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                @foreach(\App\Models\Rencontre::resultats() as $key => $label)
                                    <option value="{{ $key }}" {{ old('resultat', 'a_jouer') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('resultat')" class="mt-2" />
                        </div>

                        <!-- Score OBD -->
                        <div>
                            <x-input-label for="score_obd">Score OBD</x-input-label>
                            <x-input type="number" name="score_obd" id="score_obd" value="{{ old('score_obd') }}" min="0" placeholder="0" />
                            <x-input-error :messages="$errors->get('score_obd')" class="mt-2" />
                        </div>

                        <!-- Score Adversaire -->
                        <div>
                            <x-input-label for="score_adversaire">Score Adversaire</x-input-label>
                            <x-input type="number" name="score_adversaire" id="score_adversaire" value="{{ old('score_adversaire') }}" min="0" placeholder="0" />
                            <x-input-error :messages="$errors->get('score_adversaire')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Remarques -->
                <div class="mt-6">
                    <x-input-label for="remarques">Remarques</x-input-label>
                    <x-textarea name="remarques" id="remarques" rows="3" placeholder="Notes sur le match...">{{ old('remarques') }}</x-textarea>
                    <x-input-error :messages="$errors->get('remarques')" class="mt-2" />
                </div>

                <!-- Boutons -->
                <div class="mt-8 flex justify-end gap-4">
                    <x-button type="button" variant="secondary" onclick="window.history.back()">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Enregistrer le match
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>

