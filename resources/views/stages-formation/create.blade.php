@section('title', 'Nouveau Stage de Formation')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouveau Stage de Formation</h2>
                <p class="mt-1 text-sm text-gray-500">Créer un nouveau stage pour les entraîneurs</p>
            </div>
            <x-button href="{{ route('stages-formation.index') }}" variant="secondary">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('stages-formation.store') }}">
            @csrf

            <x-card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations générales</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="titre" class="block text-sm font-medium text-gray-700">Titre du stage *</label>
                        <input type="text" name="titre" id="titre" value="{{ old('titre') }}" required
                               placeholder="Ex: Formation des Formateurs en Football"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('titre')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type de formation *</label>
                        <select name="type" id="type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            @foreach(\App\Models\StageFormation::TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="discipline_id" class="block text-sm font-medium text-gray-700">Discipline</label>
                        <select name="discipline_id" id="discipline_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Toutes disciplines</option>
                            @foreach($disciplines as $discipline)
                                <option value="{{ $discipline->id }}" {{ old('discipline_id') == $discipline->id ? 'selected' : '' }}>
                                    {{ $discipline->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('discipline_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <x-card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Dates et lieu</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700">Date de début *</label>
                        <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut') }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('date_debut')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de fin *</label>
                        <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin') }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('date_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="lieu" class="block text-sm font-medium text-gray-700">Lieu *</label>
                        <input type="text" name="lieu" id="lieu" value="{{ old('lieu') }}" required
                               placeholder="Ex: INJS Bamako"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('lieu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="organisme" class="block text-sm font-medium text-gray-700">Organisme organisateur *</label>
                        <input type="text" name="organisme" id="organisme" value="{{ old('organisme', 'Institut National de la Jeunesse et des Sports (INJS)') }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('organisme')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duree_heures" class="block text-sm font-medium text-gray-700">Durée totale (heures)</label>
                        <input type="number" name="duree_heures" id="duree_heures" value="{{ old('duree_heures') }}" min="1"
                               placeholder="Ex: 80"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('duree_heures')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="places_disponibles" class="block text-sm font-medium text-gray-700">Places disponibles *</label>
                        <input type="number" name="places_disponibles" id="places_disponibles" value="{{ old('places_disponibles', 30) }}" required min="1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('places_disponibles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="frais_inscription" class="block text-sm font-medium text-gray-700">Frais d'inscription (FCFA)</label>
                        <input type="number" name="frais_inscription" id="frais_inscription" value="{{ old('frais_inscription', 0) }}" min="0" step="100"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('frais_inscription')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <x-card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Certification</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="type_certification" class="block text-sm font-medium text-gray-700">Type de certification *</label>
                        <select name="type_certification" id="type_certification" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            @foreach(\App\Models\StageFormation::TYPES_CERTIFICATION as $key => $label)
                                <option value="{{ $key }}" {{ old('type_certification', 'certificat') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type_certification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="intitule_certification" class="block text-sm font-medium text-gray-700">Intitulé de la certification</label>
                        <input type="text" name="intitule_certification" id="intitule_certification" value="{{ old('intitule_certification') }}"
                               placeholder="Ex: Certificat de Formation des Formateurs"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('intitule_certification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <x-card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Programme et objectifs</h3>
                
                <div class="space-y-6">
                    <div>
                        <label for="objectifs" class="block text-sm font-medium text-gray-700">Objectifs de la formation</label>
                        <textarea name="objectifs" id="objectifs" rows="4"
                                  placeholder="Décrivez les objectifs pédagogiques de cette formation..."
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">{{ old('objectifs') }}</textarea>
                        @error('objectifs')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="programme" class="block text-sm font-medium text-gray-700">Contenu du programme</label>
                        <textarea name="programme" id="programme" rows="6"
                                  placeholder="Détaillez le contenu du programme de formation..."
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">{{ old('programme') }}</textarea>
                        @error('programme')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="conditions_admission" class="block text-sm font-medium text-gray-700">Conditions d'admission</label>
                        <textarea name="conditions_admission" id="conditions_admission" rows="3"
                                  placeholder="Ex: Être entraîneur en activité, avoir au moins 2 ans d'expérience..."
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">{{ old('conditions_admission') }}</textarea>
                        @error('conditions_admission')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <x-card class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Encadreurs / Formateurs</h3>
                
                <div id="encadreurs-container">
                    <div class="encadreur-row flex gap-2 mb-2">
                        <input type="text" name="encadreurs[]" placeholder="Nom de l'encadreur"
                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <button type="button" onclick="removeEncadreur(this)" class="px-3 py-2 text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" onclick="addEncadreur()" class="mt-2 inline-flex items-center text-sm text-primary-600 hover:text-primary-800">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Ajouter un encadreur
                </button>
            </x-card>

            <div class="flex justify-end gap-4">
                <x-button href="{{ route('stages-formation.index') }}" variant="secondary">Annuler</x-button>
                <x-button type="submit" variant="primary">Créer le stage</x-button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function addEncadreur() {
            const container = document.getElementById('encadreurs-container');
            const row = document.createElement('div');
            row.className = 'encadreur-row flex gap-2 mb-2';
            row.innerHTML = `
                <input type="text" name="encadreurs[]" placeholder="Nom de l'encadreur"
                       class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                <button type="button" onclick="removeEncadreur(this)" class="px-3 py-2 text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            `;
            container.appendChild(row);
        }

        function removeEncadreur(button) {
            const container = document.getElementById('encadreurs-container');
            if (container.children.length > 1) {
                button.closest('.encadreur-row').remove();
            }
        }
    </script>
    @endpush
</x-app-layout>
