@section('title', 'Nouveau Certificat Médical')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouveau Certificat Médical</h2>
                <p class="mt-1 text-sm text-gray-500">Enregistrer un certificat médical</p>
            </div>
            <x-button href="{{ route('certificats-medicaux.index') }}" variant="ghost">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('certificats-medicaux.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-group label="Athlète" name="athlete_id" required>
                        <x-select 
                            name="athlete_id" 
                            :options="$athletes->map(fn($a) => ['id' => $a->id, 'name' => $a->nom_complet])->toArray()" 
                            :selected="old('athlete_id', $athleteId)"
                            placeholder="Sélectionner un athlète"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Type de certificat" name="type" required>
                        <x-select 
                            name="type" 
                            :options="[
                                ['id' => 'aptitude', 'name' => 'Aptitude'],
                                ['id' => 'inaptitude_temporaire', 'name' => 'Inaptitude temporaire'],
                                ['id' => 'inaptitude_definitive', 'name' => 'Inaptitude définitive'],
                                ['id' => 'suivi', 'name' => 'Suivi médical'],
                            ]" 
                            :selected="old('type', 'aptitude')"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Date d'examen" name="date_examen" required>
                        <x-input 
                            type="date" 
                            name="date_examen" 
                            :value="old('date_examen', now()->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Date d'expiration" name="date_expiration" required>
                        <x-input 
                            type="date" 
                            name="date_expiration" 
                            :value="old('date_expiration', now()->addYear()->format('Y-m-d'))"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Médecin" name="medecin" required>
                        <x-input 
                            type="text" 
                            name="medecin" 
                            :value="old('medecin')"
                            placeholder="Dr. Nom du médecin"
                            required
                        />
                    </x-form-group>

                    <x-form-group label="Établissement" name="etablissement">
                        <x-input 
                            type="text" 
                            name="etablissement" 
                            :value="old('etablissement')"
                            placeholder="Hôpital, clinique..."
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
                                    {{ old('apte_competition', true) ? 'checked' : '' }}
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
                                    {{ old('apte_entrainement', true) ? 'checked' : '' }}
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
                            placeholder="Restrictions médicales éventuelles..."
                        >{{ old('restrictions') }}</textarea>
                    </x-form-group>

                    <x-form-group label="Observations" name="observations" class="md:col-span-2">
                        <textarea 
                            name="observations" 
                            rows="2"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Observations du médecin..."
                        >{{ old('observations') }}</textarea>
                    </x-form-group>

                    <x-form-group label="Document (scan)" name="document" class="md:col-span-2">
                        <input 
                            type="file" 
                            name="document" 
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                        >
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG ou PNG (max 5 Mo)</p>
                    </x-form-group>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button href="{{ route('certificats-medicaux.index') }}" variant="ghost">
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
