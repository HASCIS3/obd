@section('title', 'Modifier Certificat Médical')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier le Certificat</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $certificat->athlete->nom_complet }}</p>
            </div>
            <x-button href="{{ route('certificats-medicaux.show', $certificat) }}" variant="ghost">
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('certificats-medicaux.update', $certificat) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-group label="Athlète" name="athlete">
                        <div class="mt-1 p-2 bg-gray-100 rounded-md text-gray-700">
                            {{ $certificat->athlete->nom_complet }}
                        </div>
                    </x-form-group>

                    <x-form-group label="Type" name="type" required>
                        <x-select 
                            name="type" 
                            :options="[
                                ['id' => 'aptitude', 'name' => 'Aptitude'],
                                ['id' => 'inaptitude_temporaire', 'name' => 'Inaptitude temporaire'],
                                ['id' => 'inaptitude_definitive', 'name' => 'Inaptitude définitive'],
                                ['id' => 'suivi', 'name' => 'Suivi médical'],
                            ]" 
                            :selected="old('type', $certificat->type)"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Statut" name="statut" required>
                        <x-select 
                            name="statut" 
                            :options="[
                                ['id' => 'valide', 'name' => 'Valide'],
                                ['id' => 'expire', 'name' => 'Expiré'],
                                ['id' => 'en_attente', 'name' => 'En attente'],
                            ]" 
                            :selected="old('statut', $certificat->statut)"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Date d'examen" name="date_examen" required>
                        <x-input 
                            type="date" 
                            name="date_examen" 
                            :value="old('date_examen', $certificat->date_examen->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Date d'expiration" name="date_expiration" required>
                        <x-input 
                            type="date" 
                            name="date_expiration" 
                            :value="old('date_expiration', $certificat->date_expiration->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Médecin" name="medecin" required>
                        <x-input 
                            type="text" 
                            name="medecin" 
                            :value="old('medecin', $certificat->medecin)"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Établissement" name="etablissement">
                        <x-input 
                            type="text" 
                            name="etablissement" 
                            :value="old('etablissement', $certificat->etablissement)"
                        />
                    </x-form-group>

                    <x-form-group label="Aptitude" name="aptitude" class="md:col-span-2">
                        <div class="flex gap-6 mt-2">
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="apte_competition" 
                                    id="apte_competition" 
                                    value="1"
                                    {{ old('apte_competition', $certificat->apte_competition) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                >
                                <label for="apte_competition" class="ml-2 text-sm text-gray-700">Apte à la compétition</label>
                            </div>
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    name="apte_entrainement" 
                                    id="apte_entrainement" 
                                    value="1"
                                    {{ old('apte_entrainement', $certificat->apte_entrainement) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                >
                                <label for="apte_entrainement" class="ml-2 text-sm text-gray-700">Apte à l'entraînement</label>
                            </div>
                        </div>
                    </x-form-group>

                    <x-form-group label="Restrictions" name="restrictions" class="md:col-span-2">
                        <textarea 
                            name="restrictions" 
                            rows="2"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                        >{{ old('restrictions', $certificat->restrictions) }}</textarea>
                    </x-form-group>

                    <x-form-group label="Observations" name="observations" class="md:col-span-2">
                        <textarea 
                            name="observations" 
                            rows="2"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                        >{{ old('observations', $certificat->observations) }}</textarea>
                    </x-form-group>

                    <x-form-group label="Document" name="document" class="md:col-span-2">
                        @if($certificat->document)
                            <div class="mb-2">
                                <a href="{{ $certificat->document_url }}" target="_blank" class="text-primary-600 hover:underline text-sm">
                                    Voir le document actuel
                                </a>
                            </div>
                        @endif
                        <input 
                            type="file" 
                            name="document" 
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                        >
                    </x-form-group>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button href="{{ route('certificats-medicaux.show', $certificat) }}" variant="ghost">
                        Annuler
                    </x-button>
                    <x-button type="submit" variant="primary">
                        Enregistrer
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
