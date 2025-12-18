@section('title', 'Modifier activite')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('activities.show', $activity) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Modifier l'activite</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $activity->titre }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <x-card>
            <form action="{{ route('activities.update', $activity) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form-group label="Type" name="type" required>
                        <x-select name="type" :options="collect($types)->map(fn($label, $key) => ['id' => $key, 'name' => $label])->values()->toArray()" :selected="old('type', $activity->type)" placeholder="" />
                    </x-form-group>

                    <x-form-group label="Discipline" name="discipline_id">
                        <x-select name="discipline_id" :options="$disciplines" :selected="old('discipline_id', $activity->discipline_id)" placeholder="Toutes disciplines" valueKey="id" labelKey="nom" />
                    </x-form-group>

                    <x-form-group label="Titre" name="titre" required class="md:col-span-2">
                        <x-input name="titre" :value="old('titre', $activity->titre)" required />
                    </x-form-group>

                    <x-form-group label="Description" name="description" class="md:col-span-2">
                        <x-textarea name="description" :value="old('description', $activity->description)" rows="4" />
                    </x-form-group>

                    <x-form-group label="Lieu" name="lieu">
                        <x-input name="lieu" :value="old('lieu', $activity->lieu)" />
                    </x-form-group>

                    <x-form-group label="Video (URL YouTube/Vimeo)" name="video_url">
                        <x-input name="video_url" :value="old('video_url', $activity->video_url)" />
                    </x-form-group>

                    <x-form-group label="Date de debut" name="debut" required>
                        <x-input type="datetime-local" name="debut" :value="old('debut', $activity->debut?->format('Y-m-d\TH:i'))" required />
                    </x-form-group>

                    <x-form-group label="Date de fin" name="fin">
                        <x-input type="datetime-local" name="fin" :value="old('fin', $activity->fin?->format('Y-m-d\TH:i'))" />
                    </x-form-group>

                    <x-form-group label="Image/Affiche" name="image" class="md:col-span-2">
                        @if($activity->image_url)
                            <div class="mb-3">
                                <img src="{{ $activity->image_url }}" alt="{{ $activity->titre }}" class="w-32 h-32 object-cover rounded-lg">
                            </div>
                        @endif
                        <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="mt-1 text-xs text-gray-500">Laisser vide pour conserver l'image actuelle</p>
                    </x-form-group>

                    <x-form-group label="Publier" name="publie" class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="publie" value="1" {{ old('publie', $activity->publie) ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Rendre visible aux athletes</span>
                        </label>
                    </x-form-group>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                    <x-button href="{{ route('activities.show', $activity) }}" variant="ghost">Annuler</x-button>
                    <x-button type="submit" variant="primary">Enregistrer</x-button>
                </div>
            </form>
        </x-card>

        <x-card title="Galerie medias">
            <div class="mb-6">
                <form action="{{ route('activities.medias.store', $activity) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de media</label>
                            <select name="type" id="media_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                <option value="photo">Photo</option>
                                <option value="video">Video (URL)</option>
                            </select>
                        </div>
                        <div id="photo_input">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fichier image</label>
                            <input type="file" name="media" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                        <div id="video_input" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL de la video</label>
                            <input type="url" name="url" placeholder="https://youtube.com/watch?v=..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-input name="titre" placeholder="Titre (optionnel)" />
                        <x-input name="description" placeholder="Description (optionnel)" />
                    </div>
                    <x-button type="submit" variant="secondary">Ajouter le media</x-button>
                </form>
            </div>

            @if($activity->medias->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($activity->medias as $media)
                        <div class="relative group">
                            @if($media->isPhoto())
                                <img src="{{ $media->full_url }}" alt="{{ $media->titre }}" class="w-full h-32 object-cover rounded-lg">
                            @else
                                <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif
                            <form action="{{ route('activities.medias.destroy', [$activity, $media]) }}" method="POST" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 bg-danger-500 text-white rounded-full hover:bg-danger-600" onclick="return confirm('Supprimer ce media ?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </form>
                            @if($media->titre)
                                <p class="text-xs text-gray-600 mt-1 truncate">{{ $media->titre }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state title="Aucun media" description="Ajoutez des photos ou videos a cette activite." />
            @endif
        </x-card>

        <x-card title="Zone de danger">
            <form action="{{ route('activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('Supprimer definitivement cette activite ?')">
                @csrf
                @method('DELETE')
                <p class="text-sm text-gray-600 mb-4">La suppression est irreversible. Tous les medias associes seront egalement supprimes.</p>
                <x-button type="submit" variant="danger">Supprimer l'activite</x-button>
            </form>
        </x-card>
    </div>

    <script>
        document.getElementById('media_type').addEventListener('change', function() {
            const photoInput = document.getElementById('photo_input');
            const videoInput = document.getElementById('video_input');
            if (this.value === 'photo') {
                photoInput.classList.remove('hidden');
                videoInput.classList.add('hidden');
            } else {
                photoInput.classList.add('hidden');
                videoInput.classList.remove('hidden');
            }
        });
    </script>
</x-app-layout>
