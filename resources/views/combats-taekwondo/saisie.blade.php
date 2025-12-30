<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Combat Taekwondo (Kyorugi)
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $rencontre->adversaire }} 
                    @if($combat->categorie_poids) • {{ $combat->categorie_poids }} @endif
                    @if($combat->categorie_age) • {{ ucfirst($combat->categorie_age) }} @endif
                </p>
            </div>
            <div class="flex gap-2">
                <x-button href="{{ route('combats-taekwondo.index', $rencontre) }}" variant="secondary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-4" x-data="combatTaekwondo()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- En-tête avec statut et chronomètre -->
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-xl shadow-xl p-6 mb-6">
                <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                    <!-- Statut -->
                    <div class="flex items-center gap-4">
                        <select x-model="statut" @change="updateStatut()" 
                            class="bg-gray-700 text-white border-gray-600 rounded-lg px-4 py-2 font-semibold focus:ring-2 focus:ring-yellow-500">
                            <option value="a_jouer">⏳ À jouer</option>
                            <option value="en_cours">🔴 En cours</option>
                            <option value="termine">✅ Terminé</option>
                        </select>
                        <span class="text-white text-sm">Round <span x-text="roundActuel" class="font-bold text-yellow-400"></span>/3</span>
                    </div>

                    <!-- Chronomètre -->
                    <div class="text-center">
                        <div class="text-5xl font-mono font-bold text-white" x-text="formatTime(chrono)"></div>
                        <div class="flex gap-2 mt-2">
                            <button @click="startChrono()" x-show="!chronoRunning" class="px-4 py-1 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">
                                ▶ Démarrer
                            </button>
                            <button @click="pauseChrono()" x-show="chronoRunning" class="px-4 py-1 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm font-medium transition">
                                ⏸ Pause
                            </button>
                            <button @click="resetChrono()" class="px-4 py-1 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm font-medium transition">
                                ↺ Reset
                            </button>
                            <button @click="nextRound()" class="px-4 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                                Round suivant →
                            </button>
                        </div>
                    </div>

                    <!-- Scores totaux -->
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-4xl font-bold text-white" x-text="scoreRouge"></span>
                            </div>
                            <p class="text-white text-sm mt-1 font-medium">Rouge</p>
                        </div>
                        <div class="text-3xl text-gray-400 font-bold">VS</div>
                        <div class="text-center">
                            <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center shadow-lg">
                                <span class="text-4xl font-bold text-white" x-text="scoreBleu"></span>
                            </div>
                            <p class="text-white text-sm mt-1 font-medium">Bleu</p>
                        </div>
                    </div>
                </div>

                <!-- Alerte victoire automatique -->
                <div x-show="Math.abs(scoreRouge - scoreBleu) >= 20" 
                     class="mt-4 bg-yellow-500 text-yellow-900 px-4 py-2 rounded-lg text-center font-bold animate-pulse">
                    ⚠️ ÉCART DE 20 POINTS - VICTOIRE AUTOMATIQUE !
                </div>
            </div>

            <!-- Combattants -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Combattant Rouge -->
                <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-xl shadow-xl p-6 text-white">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-3xl">🔴</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">{{ $combat->nom_rouge_complet }}</h3>
                            @if($combat->club_rouge)
                                <p class="text-red-200">{{ $combat->club_rouge }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Combattant Bleu -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-xl p-6 text-white">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-3xl">🔵</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">{{ $combat->nom_bleu_complet }}</h3>
                            @if($combat->club_bleu)
                                <p class="text-blue-200">{{ $combat->club_bleu }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sélection du round -->
            <div class="flex justify-center gap-2 mb-6">
                <template x-for="r in [1, 2, 3, 'golden']" :key="r">
                    <button @click="roundActuel = r" 
                        :class="roundActuel === r ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                        class="px-6 py-2 rounded-lg font-semibold shadow transition">
                        <span x-text="r === 'golden' ? 'Golden Round' : 'Round ' + r"></span>
                    </button>
                </template>
            </div>

            <!-- Table de notation -->
            <x-card class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Points</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-red-600 bg-red-50">🔴 Rouge</th>
                                <th class="px-4 py-3 text-center text-sm font-semibold text-blue-600 bg-blue-50">🔵 Bleu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Coup de poing au tronc -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">👊</span>
                                        Coup de poing au tronc
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">1 pt</span>
                                </td>
                                <td class="px-4 py-3 bg-red-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('rouge', 'poing_tronc')" class="w-8 h-8 bg-red-200 hover:bg-red-300 rounded-full text-red-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].rouge.poing_tronc"></span>
                                        <button @click="increment('rouge', 'poing_tronc')" class="w-8 h-8 bg-red-500 hover:bg-red-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 bg-blue-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('bleu', 'poing_tronc')" class="w-8 h-8 bg-blue-200 hover:bg-blue-300 rounded-full text-blue-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].bleu.poing_tronc"></span>
                                        <button @click="increment('bleu', 'poing_tronc')" class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Coup de pied au tronc -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🦵</span>
                                        Coup de pied au tronc
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">2 pts</span>
                                </td>
                                <td class="px-4 py-3 bg-red-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('rouge', 'pied_tronc')" class="w-8 h-8 bg-red-200 hover:bg-red-300 rounded-full text-red-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].rouge.pied_tronc"></span>
                                        <button @click="increment('rouge', 'pied_tronc')" class="w-8 h-8 bg-red-500 hover:bg-red-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 bg-blue-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('bleu', 'pied_tronc')" class="w-8 h-8 bg-blue-200 hover:bg-blue-300 rounded-full text-blue-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].bleu.pied_tronc"></span>
                                        <button @click="increment('bleu', 'pied_tronc')" class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Coup de pied rotatif au tronc -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🌀</span>
                                        Coup de pied rotatif au tronc
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">4 pts</span>
                                </td>
                                <td class="px-4 py-3 bg-red-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('rouge', 'pied_rotatif_tronc')" class="w-8 h-8 bg-red-200 hover:bg-red-300 rounded-full text-red-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].rouge.pied_rotatif_tronc"></span>
                                        <button @click="increment('rouge', 'pied_rotatif_tronc')" class="w-8 h-8 bg-red-500 hover:bg-red-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 bg-blue-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('bleu', 'pied_rotatif_tronc')" class="w-8 h-8 bg-blue-200 hover:bg-blue-300 rounded-full text-blue-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].bleu.pied_rotatif_tronc"></span>
                                        <button @click="increment('bleu', 'pied_rotatif_tronc')" class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Coup de pied à la tête -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🎯</span>
                                        Coup de pied à la tête
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">3 pts</span>
                                </td>
                                <td class="px-4 py-3 bg-red-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('rouge', 'pied_tete')" class="w-8 h-8 bg-red-200 hover:bg-red-300 rounded-full text-red-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].rouge.pied_tete"></span>
                                        <button @click="increment('rouge', 'pied_tete')" class="w-8 h-8 bg-red-500 hover:bg-red-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 bg-blue-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('bleu', 'pied_tete')" class="w-8 h-8 bg-blue-200 hover:bg-blue-300 rounded-full text-blue-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].bleu.pied_tete"></span>
                                        <button @click="increment('bleu', 'pied_tete')" class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Coup de pied rotatif à la tête -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">💫</span>
                                        Coup de pied rotatif à la tête
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">5 pts</span>
                                </td>
                                <td class="px-4 py-3 bg-red-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('rouge', 'pied_rotatif_tete')" class="w-8 h-8 bg-red-200 hover:bg-red-300 rounded-full text-red-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].rouge.pied_rotatif_tete"></span>
                                        <button @click="increment('rouge', 'pied_rotatif_tete')" class="w-8 h-8 bg-red-500 hover:bg-red-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 bg-blue-50">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('bleu', 'pied_rotatif_tete')" class="w-8 h-8 bg-blue-200 hover:bg-blue-300 rounded-full text-blue-700 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg" x-text="rounds[roundActuel].bleu.pied_rotatif_tete"></span>
                                        <button @click="increment('bleu', 'pied_rotatif_tete')" class="w-8 h-8 bg-blue-500 hover:bg-blue-600 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Gam-jeom (Pénalités) -->
                            <tr class="bg-yellow-50 hover:bg-yellow-100">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">⚠️</span>
                                        Gam-jeom (Pénalité)
                                        <span class="text-xs text-gray-500">(+1 pt adversaire)</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">-1</span>
                                </td>
                                <td class="px-4 py-3 bg-red-100">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('rouge', 'gamjeom')" class="w-8 h-8 bg-red-300 hover:bg-red-400 rounded-full text-red-800 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg text-red-700" x-text="rounds[roundActuel].rouge.gamjeom"></span>
                                        <button @click="increment('rouge', 'gamjeom')" class="w-8 h-8 bg-red-600 hover:bg-red-700 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                                <td class="px-4 py-3 bg-blue-100">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="decrement('bleu', 'gamjeom')" class="w-8 h-8 bg-blue-300 hover:bg-blue-400 rounded-full text-blue-800 font-bold transition">−</button>
                                        <span class="w-10 text-center font-bold text-lg text-blue-700" x-text="rounds[roundActuel].bleu.gamjeom"></span>
                                        <button @click="increment('bleu', 'gamjeom')" class="w-8 h-8 bg-blue-600 hover:bg-blue-700 rounded-full text-white font-bold transition">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Score du round -->
                            <tr class="bg-gray-200 font-bold">
                                <td class="px-4 py-3 text-sm text-gray-900" colspan="2">
                                    Score du Round <span x-text="roundActuel === 'golden' ? 'Golden' : roundActuel"></span>
                                </td>
                                <td class="px-4 py-3 text-center bg-red-200">
                                    <span class="text-2xl text-red-700" x-text="getScoreRound('rouge')"></span>
                                </td>
                                <td class="px-4 py-3 text-center bg-blue-200">
                                    <span class="text-2xl text-blue-700" x-text="getScoreRound('bleu')"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Actions finales -->
            <div class="mt-6 flex flex-wrap justify-center gap-4">
                <x-button type="button" variant="secondary" onclick="window.history.back()">
                    Annuler
                </x-button>
                <button @click="saveScores()" class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Enregistrer le combat
                </button>
                <button @click="showTerminerModal = true" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Valider le résultat
                </button>
            </div>
        </div>

        <!-- Modal Terminer -->
        <div x-show="showTerminerModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showTerminerModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showTerminerModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showTerminerModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form action="{{ route('combats-taekwondo.terminer', [$rencontre, $combat]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Valider le résultat du combat</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vainqueur</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer hover:bg-red-50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                        <input type="radio" name="vainqueur" value="rouge" class="sr-only" required>
                                        <span class="text-center">
                                            <span class="block text-2xl">🔴</span>
                                            <span class="text-sm font-medium">Rouge</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-gray-500 has-[:checked]:bg-gray-50">
                                        <input type="radio" name="vainqueur" value="nul" class="sr-only">
                                        <span class="text-center">
                                            <span class="block text-2xl">⚖️</span>
                                            <span class="text-sm font-medium">Nul</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center justify-center p-3 border-2 rounded-lg cursor-pointer hover:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                        <input type="radio" name="vainqueur" value="bleu" class="sr-only">
                                        <span class="text-center">
                                            <span class="block text-2xl">🔵</span>
                                            <span class="text-sm font-medium">Bleu</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type de victoire</label>
                                <select name="type_victoire" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                    <option value="points">Aux points</option>
                                    <option value="ecart_20">Écart de 20 points</option>
                                    <option value="ko">KO / RSC</option>
                                    <option value="disqualification">Disqualification</option>
                                    <option value="abandon">Abandon</option>
                                    <option value="decision_arbitre">Décision de l'arbitre</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Remarques (optionnel)</label>
                                <textarea name="remarques" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Notes sur le combat..."></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showTerminerModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                Valider
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function combatTaekwondo() {
            return {
                rounds: @json($combat->rounds ?? \App\Models\CombatTaekwondo::getDefaultRounds()),
                roundActuel: {{ $combat->round_actuel ?? 1 }},
                statut: '{{ $combat->statut }}',
                scoreRouge: {{ $combat->score_rouge ?? 0 }},
                scoreBleu: {{ $combat->score_bleu ?? 0 }},
                chrono: 120,
                chronoRunning: false,
                chronoInterval: null,
                showTerminerModal: false,

                init() {
                    this.calculateTotalScores();
                },

                increment(combattant, action) {
                    this.rounds[this.roundActuel][combattant][action]++;
                    this.calculateTotalScores();
                },

                decrement(combattant, action) {
                    if (this.rounds[this.roundActuel][combattant][action] > 0) {
                        this.rounds[this.roundActuel][combattant][action]--;
                        this.calculateTotalScores();
                    }
                },

                getScoreRound(combattant) {
                    const r = this.rounds[this.roundActuel][combattant];
                    let score = (r.poing_tronc * 1) + (r.pied_tronc * 2) + (r.pied_rotatif_tronc * 4) + (r.pied_tete * 3) + (r.pied_rotatif_tete * 5);
                    
                    // Ajouter les gam-jeom de l'adversaire
                    const adversaire = combattant === 'rouge' ? 'bleu' : 'rouge';
                    score += this.rounds[this.roundActuel][adversaire].gamjeom;
                    
                    return score;
                },

                calculateTotalScores() {
                    let totalRouge = 0;
                    let totalBleu = 0;

                    for (const [key, round] of Object.entries(this.rounds)) {
                        const rougeScore = (round.rouge.poing_tronc * 1) + (round.rouge.pied_tronc * 2) + (round.rouge.pied_rotatif_tronc * 4) + (round.rouge.pied_tete * 3) + (round.rouge.pied_rotatif_tete * 5);
                        const bleuScore = (round.bleu.poing_tronc * 1) + (round.bleu.pied_tronc * 2) + (round.bleu.pied_rotatif_tronc * 4) + (round.bleu.pied_tete * 3) + (round.bleu.pied_rotatif_tete * 5);
                        
                        totalRouge += rougeScore + round.bleu.gamjeom;
                        totalBleu += bleuScore + round.rouge.gamjeom;
                    }

                    this.scoreRouge = totalRouge;
                    this.scoreBleu = totalBleu;
                },

                formatTime(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins}:${secs.toString().padStart(2, '0')}`;
                },

                startChrono() {
                    this.chronoRunning = true;
                    this.chronoInterval = setInterval(() => {
                        if (this.chrono > 0) {
                            this.chrono--;
                        } else {
                            this.pauseChrono();
                        }
                    }, 1000);
                },

                pauseChrono() {
                    this.chronoRunning = false;
                    clearInterval(this.chronoInterval);
                },

                resetChrono() {
                    this.pauseChrono();
                    this.chrono = 120;
                },

                nextRound() {
                    if (this.roundActuel < 3) {
                        this.roundActuel++;
                    } else if (this.roundActuel === 3) {
                        this.roundActuel = 'golden';
                    }
                    this.resetChrono();
                },

                updateStatut() {
                    // Auto-save when status changes
                    this.saveScores();
                },

                async saveScores() {
                    try {
                        const response = await fetch('{{ route("combats-taekwondo.update-scores", [$rencontre, $combat]) }}', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                rounds: this.rounds,
                                round_actuel: this.roundActuel === 'golden' ? 4 : this.roundActuel,
                                statut: this.statut,
                            }),
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.scoreRouge = data.score_rouge;
                            this.scoreBleu = data.score_bleu;
                        }
                    } catch (error) {
                        console.error('Erreur lors de la sauvegarde:', error);
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
