@section('title', 'Inscriptions - ' . $stageFormation->titre)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestion des inscriptions</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $stageFormation->titre }} ({{ $stageFormation->code }})</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('stages-formation.show', $stageFormation) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour au stage
                </x-button>
                @if(!$stageFormation->est_complet)
                    <button onclick="openInscriptionModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Inscrire un participant
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Résumé -->
        <div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6">
                    <div>
                        <span class="text-sm text-gray-500">Places</span>
                        <p class="font-semibold {{ $stageFormation->est_complet ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $stageFormation->nombre_inscrits }} / {{ $stageFormation->places_disponibles }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Période</span>
                        <p class="font-semibold text-gray-900">
                            {{ $stageFormation->date_debut->format('d/m/Y') }} - {{ $stageFormation->date_fin->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Statut</span>
                        <p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($stageFormation->statut === 'planifie') bg-blue-100 text-blue-800
                                @elseif($stageFormation->statut === 'en_cours') bg-green-100 text-green-800
                                @elseif($stageFormation->statut === 'termine') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $stageFormation->statut_libelle }}
                            </span>
                        </p>
                    </div>
                </div>
                <a href="{{ route('stages-formation.liste-participants-pdf', $stageFormation) }}" 
                   class="inline-flex items-center text-sm text-primary-600 hover:text-primary-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exporter PDF
                </a>
            </div>
        </div>

        <!-- Liste des inscrits -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            @if($stageFormation->inscriptions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Participant</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Structure</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Statut</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Note</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Certificat</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($stageFormation->inscriptions as $inscription)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $inscription->nom_complet }}</p>
                                            <p class="text-xs text-gray-500">{{ $inscription->fonction ?? '-' }}</p>
                                            @if($inscription->coach)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800 mt-1">
                                                    Coach existant
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <p class="text-gray-900">{{ $inscription->telephone ?? '-' }}</p>
                                        <p class="text-gray-500 text-xs">{{ $inscription->email ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $inscription->structure ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('inscriptions.update', $inscription) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="statut" onchange="this.form.submit()"
                                                    class="text-xs border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500
                                                        @if($inscription->statut === 'diplome') bg-green-50 text-green-800
                                                        @elseif($inscription->statut === 'echec') bg-red-50 text-red-800
                                                        @elseif($inscription->statut === 'abandon') bg-gray-50 text-gray-800
                                                        @elseif($inscription->statut === 'en_formation') bg-blue-50 text-blue-800
                                                        @else bg-yellow-50 text-yellow-800
                                                        @endif">
                                                @foreach(\App\Models\InscriptionStage::STATUTS as $key => $label)
                                                    <option value="{{ $key }}" {{ $inscription->statut == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($inscription->note_finale)
                                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold 
                                                {{ $inscription->note_finale >= 12 ? 'bg-green-100 text-green-700' : ($inscription->note_finale >= 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $inscription->note_finale }}
                                            </span>
                                        @else
                                            <button onclick="openNoteModal({{ $inscription->id }}, '{{ $inscription->nom_complet }}')" 
                                                    class="text-xs text-primary-600 hover:text-primary-800">
                                                Ajouter note
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($inscription->certificat_delivre)
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="text-green-600">✓</span>
                                                <a href="{{ route('inscriptions.certificat-pdf', $inscription) }}" 
                                                   class="text-xs text-primary-600 hover:text-primary-800" title="Télécharger">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        @elseif($inscription->statut === 'diplome' || ($inscription->note_finale && $inscription->note_finale >= 10))
                                            <form action="{{ route('inscriptions.delivrer-certificat', $inscription) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-xs text-green-600 hover:text-green-800">
                                                    Délivrer
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button onclick="openNoteModal({{ $inscription->id }}, '{{ $inscription->nom_complet }}', {{ $inscription->note_finale ?? 'null' }}, '{{ $inscription->appreciation ?? '' }}')" 
                                                    class="text-gray-400 hover:text-blue-600" title="Évaluer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('inscriptions.destroy', $inscription) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Supprimer cette inscription ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600" title="Supprimer">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun participant inscrit</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par inscrire des participants.</p>
                    @if(!$stageFormation->est_complet)
                        <div class="mt-6">
                            <button onclick="openInscriptionModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Inscrire un participant
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Inscription -->
    <div id="inscriptionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeInscriptionModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4">Inscrire un participant</h3>
                <form method="POST" action="{{ route('stages-formation.inscriptions.store', $stageFormation) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom *</label>
                            <input type="text" name="nom" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Prénom *</label>
                            <input type="text" name="prenom" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date de naissance</label>
                            <input type="date" name="date_naissance" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lieu de naissance</label>
                            <input type="text" name="lieu_naissance" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sexe *</label>
                            <select name="sexe" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                            <input type="tel" name="telephone" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fonction</label>
                            <input type="text" name="fonction" placeholder="Ex: Entraîneur" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Structure / Club</label>
                            <input type="text" name="structure" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Niveau d'étude</label>
                            <select name="niveau_etude" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Sélectionner...</option>
                                @foreach(\App\Models\InscriptionStage::NIVEAUX_ETUDE as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Coach existant</label>
                            <select name="coach_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Aucun (nouveau participant)</option>
                                @foreach($coachs as $coach)
                                    <option value="{{ $coach->id }}">{{ $coach->user->name ?? $coach->nom_complet }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Adresse</label>
                            <input type="text" name="adresse" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Expérience</label>
                            <textarea name="experience" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeInscriptionModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700">
                            Inscrire
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Note -->
    <div id="noteModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeNoteModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Évaluation - <span id="noteParticipantName"></span></h3>
                <form id="noteForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="statut" id="noteStatut">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Note finale (sur 20)</label>
                            <input type="number" name="note_finale" id="noteFinale" min="0" max="20" step="0.5"
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Appréciation</label>
                            <textarea name="appreciation" id="noteAppreciation" rows="3"
                                      class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeNoteModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-md hover:bg-primary-700">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openInscriptionModal() {
            document.getElementById('inscriptionModal').classList.remove('hidden');
        }

        function closeInscriptionModal() {
            document.getElementById('inscriptionModal').classList.add('hidden');
        }

        function openNoteModal(id, name, note = null, appreciation = '') {
            document.getElementById('noteParticipantName').textContent = name;
            document.getElementById('noteForm').action = '/inscriptions/' + id;
            document.getElementById('noteFinale').value = note || '';
            document.getElementById('noteAppreciation').value = appreciation || '';
            document.getElementById('noteModal').classList.remove('hidden');
        }

        function closeNoteModal() {
            document.getElementById('noteModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
