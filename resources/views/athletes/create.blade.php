@section('title', 'Nouvel athlete')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('athletes.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouvel athlete</h2>
                <p class="mt-1 text-sm text-gray-500">Ajouter un nouvel athlete au centre sportif</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('athletes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations personnelles -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
                    </div>

                    <x-form-group label="Nom" name="nom" required>
                        <x-input name="nom" :value="old('nom')" required />
                    </x-form-group>

                    <x-form-group label="Prenom" name="prenom" required>
                        <x-input name="prenom" :value="old('prenom')" required />
                    </x-form-group>

                    <x-form-group label="Date de naissance" name="date_naissance">
                        <x-input type="date" name="date_naissance" :value="old('date_naissance')" />
                    </x-form-group>

                    <x-form-group label="Sexe" name="sexe" required>
                        <x-select 
                            name="sexe" 
                            :options="[['id' => 'M', 'name' => 'Masculin'], ['id' => 'F', 'name' => 'Feminin']]"
                            :selected="old('sexe', 'M')"
                            placeholder=""
                        />
                    </x-form-group>

                    <x-form-group label="Telephone" name="telephone">
                        <x-input name="telephone" :value="old('telephone')" placeholder="+223 XX XX XX XX" />
                    </x-form-group>

                    <x-form-group label="Email" name="email">
                        <x-input type="email" name="email" :value="old('email')" />
                    </x-form-group>

                    <x-form-group label="Adresse" name="adresse" class="md:col-span-2">
                        <x-textarea name="adresse" :value="old('adresse')" rows="2" />
                    </x-form-group>

                    <!-- Tuteur -->
                    <div class="md:col-span-2 border-t pt-6 mt-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du tuteur</h3>
                    </div>

                    <x-form-group label="Nom du tuteur" name="nom_tuteur">
                        <x-input name="nom_tuteur" :value="old('nom_tuteur')" />
                    </x-form-group>

                    <x-form-group label="Telephone du tuteur" name="telephone_tuteur">
                        <x-input name="telephone_tuteur" :value="old('telephone_tuteur')" placeholder="+223 XX XX XX XX" />
                    </x-form-group>

                    <!-- Disciplines -->
                    <div class="md:col-span-2 border-t pt-6 mt-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Disciplines</h3>
                    </div>

                    <x-form-group label="Disciplines" name="disciplines" class="md:col-span-2">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($disciplines as $discipline)
                                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer {{ !$discipline->actif ? 'opacity-60 bg-gray-50' : '' }}">
                                    <input 
                                        type="checkbox" 
                                        name="disciplines[]" 
                                        value="{{ $discipline->id }}"
                                        {{ in_array($discipline->id, old('disciplines', [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                    >
                                    <span class="ml-2 text-sm {{ $discipline->actif ? 'text-gray-700' : 'text-gray-400' }}">
                                        {{ $discipline->nom }}
                                        @if(!$discipline->actif)
                                            <span class="text-xs text-red-500">(inactif)</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </x-form-group>

                    <x-form-group label="Date d'inscription" name="date_inscription">
                        <x-input type="date" name="date_inscription" :value="old('date_inscription', date('Y-m-d'))" />
                    </x-form-group>

                    <x-form-group label="Photo" name="photo">
                        <input 
                            type="file" 
                            name="photo" 
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                        >
                    </x-form-group>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('athletes.index') }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Enregistrer</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
