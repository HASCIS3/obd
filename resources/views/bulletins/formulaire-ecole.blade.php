<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OBD - Soumission de bulletin</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo_obd.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="max-w-2xl mx-auto px-4">
        <!-- En-tête -->
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo_obd.jpg') }}" alt="OBD" class="w-20 h-20 rounded-full mx-auto mb-4 shadow-lg">
            <h1 class="text-2xl font-bold text-gray-900">Olympiade Baco-Djicoroni</h1>
            <p class="text-gray-600">Centre Sportif d'Excellence</p>
        </div>

        <!-- Carte principale -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-green-600 text-white px-6 py-4">
                <h2 class="text-xl font-semibold">📄 Soumission de bulletin scolaire</h2>
                <p class="text-green-100 text-sm mt-1">Pour l'athlète : <strong>{{ $athlete->nom_complet }}</strong></p>
            </div>

            <form action="{{ route('bulletin.soumettre', $token) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Informations de l'établissement -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">🏫 Informations de l'établissement</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'établissement *</label>
                            <input type="text" name="etablissement" value="{{ old('etablissement') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ex: Lycée Technique de Bamako">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Classe *</label>
                            <input type="text" name="classe" value="{{ old('classe') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ex: 3ème A">
                        </div>
                    </div>
                </div>

                <!-- Période -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📅 Période</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Année scolaire *</label>
                            <select name="annee_scolaire" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Sélectionner...</option>
                                <option value="2024-2025" {{ old('annee_scolaire') == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                                <option value="2025-2026" {{ old('annee_scolaire') == '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Trimestre *</label>
                            <select name="trimestre" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Sélectionner...</option>
                                <option value="1er Trimestre" {{ old('trimestre') == '1er Trimestre' ? 'selected' : '' }}>1er Trimestre</option>
                                <option value="2ème Trimestre" {{ old('trimestre') == '2ème Trimestre' ? 'selected' : '' }}>2ème Trimestre</option>
                                <option value="3ème Trimestre" {{ old('trimestre') == '3ème Trimestre' ? 'selected' : '' }}>3ème Trimestre</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Résultats -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Résultats scolaires</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Moyenne générale *</label>
                            <input type="number" name="moyenne_generale" value="{{ old('moyenne_generale') }}" required
                                step="0.01" min="0" max="20"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ex: 12.50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rang</label>
                            <input type="number" name="rang" value="{{ old('rang') }}"
                                min="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ex: 5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Effectif classe</label>
                            <input type="number" name="effectif_classe" value="{{ old('effectif_classe') }}"
                                min="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ex: 45">
                        </div>
                    </div>
                </div>

                <!-- Photo du bulletin -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">📷 Photo du bulletin *</h3>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-500 transition-colors">
                        <input type="file" name="bulletin_photo" id="bulletin_photo" accept="image/*" required
                            class="hidden" onchange="previewImage(this)">
                        <label for="bulletin_photo" class="cursor-pointer">
                            <div id="preview-container" class="hidden mb-4">
                                <img id="preview-image" class="max-h-48 mx-auto rounded-lg shadow">
                            </div>
                            <div id="upload-placeholder">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-600">Cliquez pour télécharger la photo du bulletin</p>
                                <p class="text-sm text-gray-400 mt-1">JPG, JPEG ou PNG (max 5 Mo)</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Observations -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">💬 Observations</h3>
                    <textarea name="observations" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Observations de l'enseignant sur le comportement, les progrès, etc.">{{ old('observations') }}</textarea>
                </div>

                <!-- Contact enseignant -->
                <div class="border-b pb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">👨‍🏫 Contact enseignant (optionnel)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'enseignant</label>
                            <input type="text" name="nom_enseignant" value="{{ old('nom_enseignant') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ex: M. Diallo">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email_enseignant" value="{{ old('email_enseignant') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="Ex: enseignant@ecole.ml">
                        </div>
                    </div>
                </div>

                <!-- Bouton de soumission -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition-colors flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Soumettre le bulletin
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-sm text-gray-500">
            <p>Olympiade Baco-Djicoroni - Centre Sportif d'Excellence</p>
            <p class="mt-1">Ce formulaire est sécurisé et les données sont confidentielles.</p>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview-image');
            const previewContainer = document.getElementById('preview-container');
            const placeholder = document.getElementById('upload-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>
