@section('title', $activity->titre)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                <a href="{{ route('activities.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $activity->titre }}</h2>
                        <x-badge color="{{ $activity->type_color }}">{{ $activity->type_label }}</x-badge>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $activity->debut?->format('d/m/Y H:i') }}
                        @if($activity->fin)
                            - {{ $activity->fin?->format('H:i') }}
                        @endif
                    </p>
                </div>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('activities.edit', $activity) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </x-button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($activity->image_url)
        <div class="rounded-xl overflow-hidden shadow-lg mb-6">
            <img src="{{ $activity->image_url }}" alt="{{ $activity->titre }}" class="w-full h-64 md:h-80 object-cover">
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-2 rounded-lg bg-{{ $activity->type_color }}-100">
                            @switch($activity->type)
                                @case('competition')
                                    <svg class="w-5 h-5 text-{{ $activity->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    @break
                                @case('tournoi')
                                    <svg class="w-5 h-5 text-{{ $activity->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    @break
                                @case('match')
                                    <svg class="w-5 h-5 text-{{ $activity->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @break
                                @case('galerie')
                                    <svg class="w-5 h-5 text-{{ $activity->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    @break
                                @default
                                    <svg class="w-5 h-5 text-{{ $activity->type_color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                            @endswitch
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                    </div>
                    @if($activity->description)
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $activity->description }}</p>
                        </div>
                    @else
                        <p class="text-gray-500 italic">Aucune description disponible.</p>
                    @endif
                </x-card>

                @if($activity->video_url)
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-2 rounded-lg bg-danger-100">
                            <svg class="w-5 h-5 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Video</h3>
                    </div>
                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100">
                        @php
                            $embedUrl = $activity->video_url;
                            if (str_contains($embedUrl, 'youtube.com/watch')) {
                                preg_match('/v=([^&]+)/', $embedUrl, $matches);
                                $embedUrl = isset($matches[1]) ? "https://www.youtube.com/embed/{$matches[1]}" : $embedUrl;
                            } elseif (str_contains($embedUrl, 'youtu.be/')) {
                                $videoId = substr(parse_url($embedUrl, PHP_URL_PATH), 1);
                                $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                            }
                        @endphp
                        <iframe src="{{ $embedUrl }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                    </div>
                </x-card>
                @endif

                @if($activity->photos->count() > 0)
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-2 rounded-lg bg-secondary-100">
                            <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Galerie photos</h3>
                        <span class="text-sm text-gray-500">({{ $activity->photos->count() }} photos)</span>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($activity->photos as $photo)
                            <a href="{{ $photo->full_url }}" target="_blank" class="group block relative overflow-hidden rounded-lg">
                                <img src="{{ $photo->full_url }}" alt="{{ $photo->titre ?? $activity->titre }}" class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors"></div>
                            </a>
                        @endforeach
                    </div>
                </x-card>
                @endif

                @if($activity->videos->count() > 0)
                <x-card>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-2 rounded-lg bg-danger-100">
                            <svg class="w-5 h-5 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Videos</h3>
                        <span class="text-sm text-gray-500">({{ $activity->videos->count() }} videos)</span>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($activity->videos as $video)
                            <div>
                                @if($video->titre)
                                    <p class="text-sm font-medium text-gray-700 mb-2">{{ $video->titre }}</p>
                                @endif
                                <div class="aspect-video rounded-lg overflow-hidden bg-gray-100">
                                    <iframe src="{{ $video->embed_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
                @endif
            </div>

            <div class="space-y-6">
                @if($activity->createur)
                <x-card>
                    <div class="text-center">
                        <div class="relative inline-block">
                            @if($activity->createur->photo_url)
                                <img src="{{ $activity->createur->photo_url }}" alt="{{ $activity->createur->name }}" class="w-24 h-24 rounded-full object-cover mx-auto border-4 border-primary-100 shadow-lg">
                            @else
                                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center mx-auto border-4 border-primary-100 shadow-lg">
                                    <span class="text-white font-bold text-3xl">{{ substr($activity->createur->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 p-1.5 bg-white rounded-full shadow">
                                <div class="p-1 bg-primary-500 rounded-full">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <h4 class="mt-4 text-lg font-semibold text-gray-900">{{ $activity->createur->name }}</h4>
                        <p class="text-sm text-primary-600 font-medium">Organisateur</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $activity->createur->email }}</p>
                    </div>
                </x-card>
                @endif

                <x-card>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Informations</h4>
                    <dl class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-primary-50 rounded-lg flex-shrink-0">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Date & Heure</dt>
                                <dd class="text-sm text-gray-900 mt-0.5">
                                    {{ $activity->debut?->translatedFormat('l d F Y') }}<br>
                                    <span class="text-primary-600 font-semibold">{{ $activity->debut?->format('H:i') }}</span>
                                    @if($activity->fin)
                                        - <span class="text-primary-600 font-semibold">{{ $activity->fin->format('H:i') }}</span>
                                    @endif
                                </dd>
                            </div>
                        </div>

                        @if($activity->lieu)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-danger-50 rounded-lg flex-shrink-0">
                                <svg class="w-4 h-4 text-danger-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Lieu</dt>
                                <dd class="text-sm text-gray-900 mt-0.5">{{ $activity->lieu }}</dd>
                            </div>
                        </div>
                        @endif

                        @if($activity->discipline)
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-secondary-50 rounded-lg flex-shrink-0">
                                <svg class="w-4 h-4 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Discipline</dt>
                                <dd class="text-sm text-gray-900 mt-0.5">{{ $activity->discipline->nom }}</dd>
                            </div>
                        </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-gray-50 rounded-lg flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Publie le</dt>
                                <dd class="text-sm text-gray-900 mt-0.5">{{ $activity->created_at->format('d/m/Y') }}</dd>
                            </div>
                        </div>
                    </dl>
                </x-card>

                @if($activity->isUpcoming())
                <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl p-6 text-white text-center shadow-lg">
                    <div class="text-4xl mb-2">🎯</div>
                    <h4 class="font-bold text-lg">Evenement a venir !</h4>
                    <p class="text-primary-100 text-sm mt-1">
                        Dans {{ $activity->debut->diffForHumans(null, true) }}
                    </p>
                </div>
                @else
                <div class="bg-gradient-to-br from-gray-500 to-gray-700 rounded-xl p-6 text-white text-center shadow-lg">
                    <div class="text-4xl mb-2">✅</div>
                    <h4 class="font-bold text-lg">Evenement termine</h4>
                    <p class="text-gray-200 text-sm mt-1">
                        Il y a {{ $activity->debut->diffForHumans(null, true) }}
                    </p>
                </div>
                @endif

                <x-card>
                    <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Partager</h4>
                    <div class="flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="flex-1 p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">
                            <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($activity->titre . ' - ' . request()->url()) }}" target="_blank" class="flex-1 p-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-center">
                            <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                        <button onclick="navigator.clipboard.writeText('{{ request()->url() }}'); alert('Lien copie !')" class="flex-1 p-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-center">
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
