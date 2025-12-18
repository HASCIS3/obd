@section('title', 'Nouveau coach')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('coachs.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouveau coach</h2>
                <p class="mt-1 text-sm text-gray-500">Ajouter un nouveau coach au centre sportif</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('coachs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Compte utilisateur -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Compte utilisateur</h3>
                    </div>

                    <x-form-group label="Nom complet" name="name" required>
                        <x-input name="name" :value="old('name')" required />
                    </x-form-group>

                    <x-form-group label="Email" name="email" required>
                        <x-input type="email" name="email" :value="old('email')" required />
                    </x-form-group>

                    <x-form-group label="Mot de passe" name="password" required>
                        <x-password-input name="password" required />
                    </x-form-group>

                    <x-form-group label="Confirmer le mot de passe" name="password_confirmation" required>
                        <x-password-input name="password_confirmation" required />
                    </x-form-group>

                    <!-- Informations du coach -->
                    <div class="md:col-span-2 border-t pt-6 mt-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du coach</h3>
                    </div>

                    <x-form-group label="Telephone" name="telephone">
                        <x-input name="telephone" :value="old('telephone')" placeholder="+223 XX XX XX XX" />
                    </x-form-group>

                    <x-form-group label="Specialite" name="specialite">
                        <x-input name="specialite" :value="old('specialite')" placeholder="Ex: Football, Athletisme..." />
                    </x-form-group>

                    <x-form-group label="Adresse" name="adresse" class="md:col-span-2">
                        <x-textarea name="adresse" :value="old('adresse')" rows="2" />
                    </x-form-group>

                    <x-form-group label="Date d'embauche" name="date_embauche">
                        <x-input type="date" name="date_embauche" :value="old('date_embauche', date('Y-m-d'))" />
                    </x-form-group>

                    <x-form-group label="Photo" name="photo">
                        <input 
                            type="file" 
                            name="photo" 
                            accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                        >
                    </x-form-group>

                    <!-- Disciplines -->
                    <div class="md:col-span-2 border-t pt-6 mt-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Disciplines enseignees</h3>
                    </div>

                    <x-form-group label="Disciplines" name="disciplines" class="md:col-span-2">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach($disciplines as $discipline)
                                <label class="flex items-center p-3 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="disciplines[]" 
                                        value="{{ $discipline->id }}"
                                        {{ in_array($discipline->id, old('disciplines', [])) ? 'checked' : '' }}
                                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                    >
                                    <span class="ml-2 text-sm text-gray-700">{{ $discipline->nom }}</span>
                                </label>
                            @endforeach
                        </div>
                    </x-form-group>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('coachs.index') }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Enregistrer</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
