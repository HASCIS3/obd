<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    🥋 Feuille de Combat - Taekwondo
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $rencontre->adversaire }} 
                    @if($combat->categorie_poids) • <span class="font-semibold">{{ $combat->categorie_poids }}</span> @endif
                    @if($combat->categorie_age) • {{ ucfirst($combat->categorie_age) }} @endif
                </p>
            </div>
            <x-button href="{{ route('combats-taekwondo.index', $rencontre) }}" variant="secondary">
                ← Retour aux combats
            </x-button>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen bg-gradient-to-br from-slate-800 via-slate-900 to-gray-900" x-data="combatTaekwondo()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- SCORE BOARD PRINCIPAL -->
            <div class="bg-gray-900 rounded-2xl shadow-2xl overflow-hidden mb-8">
                <!-- Barre supérieure avec chrono -->
                <div class="bg-gradient-to-r from-gray-800 via-gray-900 to-gray-800 px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <select x-model="statut" @change="updateStatut()" 
                            class="bg-gray-700 text-white border-0 rounded-lg px-4 py-2 text-sm font-semibold focus:ring-2 focus:ring-yellow-500">
                            <option value="a_jouer">⏳ À jouer</option>
                            <option value="en_cours">🔴 En cours</option>
                            <option value="termine">✅ Terminé</option>
                        </select>
                    </div>
                    
                    <!-- Chronomètre central -->
                    <div class="flex flex-col items-center">
                        <div class="text-6xl font-mono font-black text-yellow-400 tracking-wider" x-text="formatTime(chrono)"></div>
                        <div class="text-gray-400 text-sm mt-1">Round <span x-text="roundActuel === 'golden' ? 'Golden' : roundActuel" class="text-yellow-400 font-bold"></span></div>
                    </div>
                    
                    <div class="flex gap-2">
                        <button @click="startChrono()" x-show="!chronoRunning" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg text-sm font-bold transition shadow-lg">
                            ▶ GO
                        </button>
                        <button @click="pauseChrono()" x-show="chronoRunning" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-400 text-gray-900 rounded-lg text-sm font-bold transition shadow-lg">
                            ⏸ STOP
                        </button>
                        <button @click="resetChrono()" class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white rounded-lg text-sm font-bold transition">
                            ↺
                        </button>
                    </div>
                </div>

                <!-- Zone des scores -->
                <div class="grid grid-cols-3 gap-0">
                    <!-- ROUGE -->
                    <div class="p-6 text-center border-4 border-red-500" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                        <div class="text-xs uppercase tracking-widest mb-2 font-bold" style="color: #fca5a5;">🔴 HONG (Rouge)</div>
                        <div class="text-8xl font-black drop-shadow-lg" style="color: #ffffff;" x-text="scoreRouge"></div>
                        <div class="mt-4 rounded-lg p-3" style="background: rgba(127, 29, 29, 0.7);">
                            <div class="font-bold text-lg uppercase tracking-wide" style="color: #ffffff;">{{ $combat->nom_rouge ?? 'Rouge' }}</div>
                            <div class="text-sm font-medium" style="color: #fca5a5;">{{ $combat->club_rouge ?? 'OBD' }}</div>
                        </div>
                    </div>

                    <!-- VS Central -->
                    <div class="flex flex-col items-center justify-center p-4" style="background: #111827;">
                        <div class="text-3xl font-black mb-4" style="color: #6b7280;">VS</div>
                        <div class="flex gap-2 mb-4">
                            <template x-for="r in ['1', '2', '3']" :key="r">
                                <button @click="roundActuel = r" 
                                    :class="roundActuel === r ? 'ring-2 ring-yellow-300' : ''"
                                    :style="roundActuel === r ? 'background: #eab308; color: #111827;' : 'background: #374151; color: #9ca3af;'"
                                    class="w-10 h-10 rounded-full font-bold text-lg transition hover:opacity-80">
                                    <span x-text="r"></span>
                                </button>
                            </template>
                            <button @click="roundActuel = 'golden'" 
                                :class="roundActuel === 'golden' ? 'ring-2 ring-yellow-300' : ''"
                                :style="roundActuel === 'golden' ? 'background: #eab308; color: #111827;' : 'background: #374151; color: #9ca3af;'"
                                class="w-10 h-10 rounded-full font-bold text-sm transition hover:opacity-80">
                                G
                            </button>
                        </div>
                        <button @click="nextRound()" class="px-4 py-2 rounded-lg text-xs font-bold transition hover:opacity-80" style="background: #374151; color: #ffffff;">
                            Round suivant →
                        </button>
                    </div>

                    <!-- BLEU -->
                    <div class="p-6 text-center border-4 border-blue-500" style="background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);">
                        <div class="text-xs uppercase tracking-widest mb-2 font-bold" style="color: #93c5fd;">🔵 CHUNG (Bleu)</div>
                        <div class="text-8xl font-black drop-shadow-lg" style="color: #ffffff;" x-text="scoreBleu"></div>
                        <div class="mt-4 rounded-lg p-3" style="background: rgba(30, 58, 138, 0.7);">
                            <div class="font-bold text-lg uppercase tracking-wide" style="color: #ffffff;">{{ $combat->nom_bleu ?? 'Bleu' }}</div>
                            <div class="text-sm font-medium" style="color: #93c5fd;">{{ $combat->club_bleu ?? 'Adversaire' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Alerte victoire automatique -->
                <div x-show="Math.abs(scoreRouge - scoreBleu) >= 20" x-cloak
                     class="bg-yellow-500 text-gray-900 px-6 py-3 text-center font-black text-lg animate-pulse">
                    ⚠️ ÉCART DE 20 POINTS - VICTOIRE AUTOMATIQUE ! ⚠️
                </div>
            </div>

            <!-- TABLE DE SCORING -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <!-- En-tête de la table -->
                <div class="bg-gray-800 text-white px-6 py-4">
                    <h3 class="text-lg font-bold">📊 Tableau de Score - Round <span x-text="roundActuel === 'golden' ? 'Golden' : roundActuel" class="text-yellow-400"></span></h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider">Technique</th>
                                <th class="px-4 py-4 text-center text-sm font-bold text-gray-700 uppercase tracking-wider w-20">Pts</th>
                                <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider bg-red-600 w-40">🔴 ROUGE</th>
                                <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider bg-blue-600 w-40">🔵 BLEU</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Coup de poing au tronc -->
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">👊</span>
                                        <div>
                                            <div class="font-semibold text-gray-900">Poing tronc</div>
                                            <div class="text-xs text-gray-500">Jirugi</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-800 font-bold">1</span>
                                </td>
                                <td class="px-6 py-4 bg-red-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('rouge', 'poing_tronc')" class="w-10 h-10 bg-red-200 hover:bg-red-300 rounded-lg text-red-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-red-700" x-text="rounds[roundActuel]?.rouge?.poing_tronc || 0"></span>
                                        <button @click="increment('rouge', 'poing_tronc')" class="w-10 h-10 bg-red-600 hover:bg-red-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-blue-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('bleu', 'poing_tronc')" class="w-10 h-10 bg-blue-200 hover:bg-blue-300 rounded-lg text-blue-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-blue-700" x-text="rounds[roundActuel]?.bleu?.poing_tronc || 0"></span>
                                        <button @click="increment('bleu', 'poing_tronc')" class="w-10 h-10 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Coup de pied au tronc -->
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🦵</span>
                                        <div>
                                            <div class="font-semibold text-gray-900">Pied tronc</div>
                                            <div class="text-xs text-gray-500">Momtong Chagi</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-200 text-green-800 font-bold">2</span>
                                </td>
                                <td class="px-6 py-4 bg-red-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('rouge', 'pied_tronc')" class="w-10 h-10 bg-red-200 hover:bg-red-300 rounded-lg text-red-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-red-700" x-text="rounds[roundActuel]?.rouge?.pied_tronc || 0"></span>
                                        <button @click="increment('rouge', 'pied_tronc')" class="w-10 h-10 bg-red-600 hover:bg-red-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-blue-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('bleu', 'pied_tronc')" class="w-10 h-10 bg-blue-200 hover:bg-blue-300 rounded-lg text-blue-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-blue-700" x-text="rounds[roundActuel]?.bleu?.pied_tronc || 0"></span>
                                        <button @click="increment('bleu', 'pied_tronc')" class="w-10 h-10 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Coup de pied rotatif au tronc -->
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🌀</span>
                                        <div>
                                            <div class="font-semibold text-gray-900">Pied rotatif tronc</div>
                                            <div class="text-xs text-gray-500">Dwi Chagi / Dollyeo Chagi</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-200 text-purple-800 font-bold">4</span>
                                </td>
                                <td class="px-6 py-4 bg-red-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('rouge', 'pied_rotatif_tronc')" class="w-10 h-10 bg-red-200 hover:bg-red-300 rounded-lg text-red-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-red-700" x-text="rounds[roundActuel]?.rouge?.pied_rotatif_tronc || 0"></span>
                                        <button @click="increment('rouge', 'pied_rotatif_tronc')" class="w-10 h-10 bg-red-600 hover:bg-red-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-blue-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('bleu', 'pied_rotatif_tronc')" class="w-10 h-10 bg-blue-200 hover:bg-blue-300 rounded-lg text-blue-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-blue-700" x-text="rounds[roundActuel]?.bleu?.pied_rotatif_tronc || 0"></span>
                                        <button @click="increment('bleu', 'pied_rotatif_tronc')" class="w-10 h-10 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Coup de pied à la tête -->
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🎯</span>
                                        <div>
                                            <div class="font-semibold text-gray-900">Pied tête</div>
                                            <div class="text-xs text-gray-500">Olgul Chagi</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-200 text-orange-800 font-bold">3</span>
                                </td>
                                <td class="px-6 py-4 bg-red-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('rouge', 'pied_tete')" class="w-10 h-10 bg-red-200 hover:bg-red-300 rounded-lg text-red-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-red-700" x-text="rounds[roundActuel]?.rouge?.pied_tete || 0"></span>
                                        <button @click="increment('rouge', 'pied_tete')" class="w-10 h-10 bg-red-600 hover:bg-red-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-blue-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('bleu', 'pied_tete')" class="w-10 h-10 bg-blue-200 hover:bg-blue-300 rounded-lg text-blue-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-blue-700" x-text="rounds[roundActuel]?.bleu?.pied_tete || 0"></span>
                                        <button @click="increment('bleu', 'pied_tete')" class="w-10 h-10 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Coup de pied rotatif à la tête -->
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">💫</span>
                                        <div>
                                            <div class="font-semibold text-gray-900">Pied rotatif tête</div>
                                            <div class="text-xs text-gray-500">Dwi Huryeo Chagi</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-300 text-yellow-800 font-bold">5</span>
                                </td>
                                <td class="px-6 py-4 bg-red-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('rouge', 'pied_rotatif_tete')" class="w-10 h-10 bg-red-200 hover:bg-red-300 rounded-lg text-red-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-red-700" x-text="rounds[roundActuel]?.rouge?.pied_rotatif_tete || 0"></span>
                                        <button @click="increment('rouge', 'pied_rotatif_tete')" class="w-10 h-10 bg-red-600 hover:bg-red-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-blue-50">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('bleu', 'pied_rotatif_tete')" class="w-10 h-10 bg-blue-200 hover:bg-blue-300 rounded-lg text-blue-700 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-blue-700" x-text="rounds[roundActuel]?.bleu?.pied_rotatif_tete || 0"></span>
                                        <button @click="increment('bleu', 'pied_rotatif_tete')" class="w-10 h-10 bg-blue-600 hover:bg-blue-500 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Kyong-go (Avertissement) -->
                            <tr class="bg-yellow-100 hover:bg-yellow-200 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🟡</span>
                                        <div>
                                            <div class="font-semibold text-gray-900">Kyong-go</div>
                                            <div class="text-xs text-yellow-700 font-medium">Avertissement (2 = 1 Gam-jeom)</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500 text-white font-bold text-xs">½</span>
                                </td>
                                <td class="px-6 py-4 bg-red-100">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('rouge', 'kyonggo')" class="w-10 h-10 bg-red-300 hover:bg-red-400 rounded-lg text-red-800 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-red-800" x-text="rounds[roundActuel]?.rouge?.kyonggo || 0"></span>
                                        <button @click="increment('rouge', 'kyonggo')" class="w-10 h-10 bg-red-700 hover:bg-red-600 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-blue-100">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('bleu', 'kyonggo')" class="w-10 h-10 bg-blue-300 hover:bg-blue-400 rounded-lg text-blue-800 font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-blue-800" x-text="rounds[roundActuel]?.bleu?.kyonggo || 0"></span>
                                        <button @click="increment('bleu', 'kyonggo')" class="w-10 h-10 bg-blue-700 hover:bg-blue-600 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Gam-jeom (Pénalité directe) -->
                            <tr class="bg-red-200 hover:bg-red-300 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🔴</span>
                                        <div>
                                            <div class="font-semibold text-gray-900">Gam-jeom</div>
                                            <div class="text-xs text-red-700 font-medium">Pénalité directe (+1 pt adversaire)</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-600 text-white font-bold">-1</span>
                                </td>
                                <td class="px-6 py-4 bg-red-200">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('rouge', 'gamjeom')" class="w-10 h-10 bg-red-400 hover:bg-red-500 rounded-lg text-white font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-red-900" x-text="rounds[roundActuel]?.rouge?.gamjeom || 0"></span>
                                        <button @click="increment('rouge', 'gamjeom')" class="w-10 h-10 bg-red-800 hover:bg-red-700 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-blue-200">
                                    <div class="flex items-center justify-center gap-3">
                                        <button @click="decrement('bleu', 'gamjeom')" class="w-10 h-10 bg-blue-400 hover:bg-blue-500 rounded-lg text-white font-bold text-xl transition shadow">−</button>
                                        <span class="w-12 text-center font-black text-2xl text-blue-900" x-text="rounds[roundActuel]?.bleu?.gamjeom || 0"></span>
                                        <button @click="increment('bleu', 'gamjeom')" class="w-10 h-10 bg-blue-800 hover:bg-blue-700 rounded-lg text-white font-bold text-xl transition shadow">+</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <!-- Score du round -->
                        <tfoot>
                            <tr class="bg-gray-800">
                                <td class="px-6 py-4 text-white font-bold text-lg" colspan="2">
                                    SCORE ROUND <span x-text="roundActuel === 'golden' ? 'GOLDEN' : roundActuel" class="text-yellow-400"></span>
                                </td>
                                <td class="px-6 py-4 text-center bg-red-700">
                                    <span class="text-4xl font-black text-white" x-text="getScoreRound('rouge')"></span>
                                </td>
                                <td class="px-6 py-4 text-center bg-blue-700">
                                    <span class="text-4xl font-black text-white" x-text="getScoreRound('bleu')"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

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
            const defaultRounds = {
                '1': { rouge: {poing_tronc: 0, pied_tronc: 0, pied_rotatif_tronc: 0, pied_tete: 0, pied_rotatif_tete: 0, kyonggo: 0, gamjeom: 0}, bleu: {poing_tronc: 0, pied_tronc: 0, pied_rotatif_tronc: 0, pied_tete: 0, pied_rotatif_tete: 0, kyonggo: 0, gamjeom: 0} },
                '2': { rouge: {poing_tronc: 0, pied_tronc: 0, pied_rotatif_tronc: 0, pied_tete: 0, pied_rotatif_tete: 0, kyonggo: 0, gamjeom: 0}, bleu: {poing_tronc: 0, pied_tronc: 0, pied_rotatif_tronc: 0, pied_tete: 0, pied_rotatif_tete: 0, kyonggo: 0, gamjeom: 0} },
                '3': { rouge: {poing_tronc: 0, pied_tronc: 0, pied_rotatif_tronc: 0, pied_tete: 0, pied_rotatif_tete: 0, kyonggo: 0, gamjeom: 0}, bleu: {poing_tronc: 0, pied_tronc: 0, pied_rotatif_tronc: 0, pied_tete: 0, pied_rotatif_tete: 0, kyonggo: 0, gamjeom: 0} },
                'golden': { rouge: {poing_tronc: 0, pied_tronc: 0, pied_rotatif_tronc: 0, pied_tete: 0, pied_rotatif_tete: 0, kyonggo: 0, gamjeom: 0}, bleu: {poing_tronc: 0, pied_tronc: 0, pied_rotatif_tronc: 0, pied_tete: 0, pied_rotatif_tete: 0, kyonggo: 0, gamjeom: 0} }
            };
            
            let savedRounds = @json($combat->rounds);
            if (!savedRounds || Object.keys(savedRounds).length === 0) {
                savedRounds = defaultRounds;
            }
            
            return {
                rounds: savedRounds,
                roundActuel: '{{ $combat->round_actuel ?? 1 }}',
                statut: '{{ $combat->statut }}',
                scoreRouge: {{ $combat->score_rouge ?? 0 }},
                scoreBleu: {{ $combat->score_bleu ?? 0 }},
                chrono: 120,
                chronoRunning: false,
                chronoInterval: null,
                showTerminerModal: false,

                init() {
                    // S'assurer que roundActuel est une string
                    this.roundActuel = String(this.roundActuel);
                    if (this.roundActuel === '4') this.roundActuel = 'golden';
                    this.calculateTotalScores();
                },

                increment(combattant, action) {
                    if (!this.rounds[this.roundActuel]) return;
                    this.rounds[this.roundActuel][combattant][action]++;
                    this.calculateTotalScores();
                },

                decrement(combattant, action) {
                    if (!this.rounds[this.roundActuel]) return;
                    if (this.rounds[this.roundActuel][combattant][action] > 0) {
                        this.rounds[this.roundActuel][combattant][action]--;
                        this.calculateTotalScores();
                    }
                },

                getScoreRound(combattant) {
                    if (!this.rounds[this.roundActuel] || !this.rounds[this.roundActuel][combattant]) return 0;
                    const r = this.rounds[this.roundActuel][combattant];
                    let score = (r.poing_tronc * 1) + (r.pied_tronc * 2) + (r.pied_rotatif_tronc * 4) + (r.pied_tete * 3) + (r.pied_rotatif_tete * 5);
                    
                    // Ajouter les points des pénalités adversaires (2 kyonggo = 1 pt, 1 gamjeom = 1 pt)
                    const adversaire = combattant === 'rouge' ? 'bleu' : 'rouge';
                    const advData = this.rounds[this.roundActuel][adversaire];
                    score += Math.floor((advData.kyonggo || 0) / 2) + (advData.gamjeom || 0);
                    
                    return score;
                },

                calculateTotalScores() {
                    let totalRouge = 0;
                    let totalBleu = 0;

                    for (const [key, round] of Object.entries(this.rounds)) {
                        // Points techniques
                        const rougeScore = (round.rouge.poing_tronc * 1) + (round.rouge.pied_tronc * 2) + (round.rouge.pied_rotatif_tronc * 4) + (round.rouge.pied_tete * 3) + (round.rouge.pied_rotatif_tete * 5);
                        const bleuScore = (round.bleu.poing_tronc * 1) + (round.bleu.pied_tronc * 2) + (round.bleu.pied_rotatif_tronc * 4) + (round.bleu.pied_tete * 3) + (round.bleu.pied_rotatif_tete * 5);
                        
                        // Points des pénalités adversaires (2 kyonggo = 1 pt, 1 gamjeom = 1 pt)
                        const penalitesRouge = Math.floor((round.bleu.kyonggo || 0) / 2) + (round.bleu.gamjeom || 0);
                        const penalitesBleu = Math.floor((round.rouge.kyonggo || 0) / 2) + (round.rouge.gamjeom || 0);
                        
                        totalRouge += rougeScore + penalitesRouge;
                        totalBleu += bleuScore + penalitesBleu;
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
                    const currentRound = parseInt(this.roundActuel);
                    if (!isNaN(currentRound) && currentRound < 3) {
                        this.roundActuel = String(currentRound + 1);
                    } else if (this.roundActuel === '3') {
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
