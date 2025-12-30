<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nouveau Combat Taekwondo
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $rencontre->adversaire }} - {{ $rencontre->date_match->format('d/m/Y') }}
                </p>
            </div>
            <x-button href="{{ route('combats-taekwondo.index', $rencontre) }}" variant="secondary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('combats-taekwondo.store', $rencontre) }}" method="POST">
                @csrf

                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Configuration du combat</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="categorie_poids" value="Catégorie de poids" />
                            <select name="categorie_poids" id="categorie_poids" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="">-- Sélectionner --</option>
                                <option value="-54kg">-54 kg</option>
                                <option value="-58kg">-58 kg</option>
                                <option value="-63kg">-63 kg</option>
                                <option value="-68kg">-68 kg</option>
                                <option value="-74kg">-74 kg</option>
                                <option value="-80kg">-80 kg</option>
                                <option value="-87kg">-87 kg</option>
                                <option value="+87kg">+87 kg</option>
                                <option value="-46kg">-46 kg (F)</option>
                                <option value="-49kg">-49 kg (F)</option>
                                <option value="-53kg">-53 kg (F)</option>
                                <option value="-57kg">-57 kg (F)</option>
                                <option value="-62kg">-62 kg (F)</option>
                                <option value="-67kg">-67 kg (F)</option>
                                <option value="+67kg">+67 kg (F)</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="categorie_age" value="Catégorie d'âge" />
                            <select name="categorie_age" id="categorie_age" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="">-- Sélectionner --</option>
                                <option value="cadet">Cadet (12-14 ans)</option>
                                <option value="junior">Junior (15-17 ans)</option>
                                <option value="senior">Senior (18+ ans)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Combattant Rouge -->
                        <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">🔴</span>
                                </div>
                                <h4 class="text-lg font-bold text-red-700">Combattant Rouge</h4>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="athlete_rouge_id" value="Athlète OBD (optionnel)" />
                                    <select name="athlete_rouge_id" id="athlete_rouge_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                                        <option value="">-- Sélectionner un athlète --</option>
                                        @foreach($athletes as $athlete)
                                            <option value="{{ $athlete->id }}">{{ $athlete->nom_complet }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="nom_rouge" value="Nom du combattant" />
                                    <x-text-input type="text" name="nom_rouge" id="nom_rouge" class="mt-1 block w-full" placeholder="Nom complet" />
                                </div>
                                <div>
                                    <x-input-label for="club_rouge" value="Club / Équipe" />
                                    <x-text-input type="text" name="club_rouge" id="club_rouge" class="mt-1 block w-full" value="OBD" placeholder="Club" />
                                </div>
                            </div>
                        </div>

                        <!-- Combattant Bleu -->
                        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold">🔵</span>
                                </div>
                                <h4 class="text-lg font-bold text-blue-700">Combattant Bleu</h4>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="athlete_bleu_id" value="Athlète OBD (optionnel)" />
                                    <select name="athlete_bleu_id" id="athlete_bleu_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">-- Sélectionner un athlète --</option>
                                        @foreach($athletes as $athlete)
                                            <option value="{{ $athlete->id }}">{{ $athlete->nom_complet }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="nom_bleu" value="Nom du combattant" />
                                    <x-text-input type="text" name="nom_bleu" id="nom_bleu" class="mt-1 block w-full" placeholder="Nom complet" />
                                </div>
                                <div>
                                    <x-input-label for="club_bleu" value="Club / Équipe" />
                                    <x-text-input type="text" name="club_bleu" id="club_bleu" class="mt-1 block w-full" placeholder="Club adverse" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <x-button type="button" variant="secondary" onclick="window.history.back()">
                            Annuler
                        </x-button>
                        <x-button type="submit" variant="primary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Créer le combat
                        </x-button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>
</x-app-layout>
