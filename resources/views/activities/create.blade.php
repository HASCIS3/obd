@section('title', 'Nouvelle activite')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('activities.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Nouvelle activite</h2>
                <p class="mt-1 text-sm text-gray-500">Creer une competition, tournoi, match ou evenement</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-group label="Type" name="type" required>
                        <x-select name="type" :options="collect($types)->map(fn($label, $key) => ['id' => $key, 'name' => $label])->values()->toArray()" :selected="old('type', 'evenement')" placeholder="" />
                    </x-form-group>

                    <x-form-group label="Discipline" name="discipline_id">
                        <x-select name="discipline_id" :options="$disciplines" :selected="old('discipline_id')" placeholder="Toutes disciplines" valueKey="id" labelKey="nom" />
                    </x-form-group>

                    <x-form-group label="Titre" name="titre" required class="md:col-span-2">
                        <x-input name="titre" :value="old('titre')" required placeholder="Ex: Tournoi inter-ecoles 2025" />
                    </x-form-group>

                    <x-form-group label="Description" name="description" class="md:col-span-2">
                        <x-textarea name="description" :value="old('description')" rows="4" placeholder="Details de l'activite..." />
                    </x-form-group>

                    <x-form-group label="Lieu" name="lieu">
                        <x-input name="lieu" :value="old('lieu')" placeholder="Ex: Stade Modibo Keita" />
                    </x-form-group>

                    <x-form-group label="Video (URL YouTube/Vimeo)" name="video_url">
                        <x-input name="video_url" :value="old('video_url')" placeholder="https://youtube.com/watch?v=..." />
                    </x-form-group>

                    <x-form-group label="Date de debut" name="debut" required>
                        <x-input type="datetime-local" name="debut" :value="old('debut')" required />
                    </x-form-group>

                    <x-form-group label="Date de fin" name="fin">
                        <x-input type="datetime-local" name="fin" :value="old('fin')" />
                    </x-form-group>

                    <x-form-group label="Image/Affiche" name="image" class="md:col-span-2">
                        <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="mt-1 text-xs text-gray-500">Image d'affiche pour l'activite (max 2Mo)</p>
                    </x-form-group>

                    <x-form-group label="Publier" name="publie" class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="publie" value="1" {{ old('publie', true) ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Rendre visible aux athletes</span>
                        </label>
                    </x-form-group>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('activities.index') }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Creer l'activite</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
