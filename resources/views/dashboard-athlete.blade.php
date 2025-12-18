@section('title', 'Mon espace')

@php
    $athlete = auth()->user()->athlete;
    $prochaines = \App\Models\Activity::where('publie', true)
        ->where('debut', '>=', now())
        ->orderBy('debut')
        ->take(3)
        ->get();
    $dernieresGaleries = \App\Models\Activity::where('publie', true)
        ->where('type', 'galerie')
        ->orderBy('debut', 'desc')
        ->take(4)
        ->get();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            @if($athlete?->photo_url)
                <img src="{{ $athlete->photo_url }}" alt="{{ auth()->user()->name }}" class="h-14 w-14 rounded-full object-cover border-2 border-white shadow">
            @else
                <div class="h-14 w-14 rounded-full bg-secondary-500 flex items-center justify-center border-2 border-white shadow">
                    <span class="text-primary-900 font-bold text-xl">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Bienvenue, {{ auth()->user()->name }} !</h2>
                <p class="mt-1 text-sm text-gray-500">
                    @if($athlete)
                        {{ $athlete->disciplines->pluck('nom')->join(', ') ?: 'Aucune discipline' }}
                    @else
                        Espace athlete
                    @endif
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card title="Prochaines activites">
                    @if($prochaines->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($prochaines as $activity)
                                <li class="py-3">
                                    <a href="{{ route('activities.show', $activity) }}" class="flex items-start gap-4 hover:bg-gray-50 rounded-lg p-2 -m-2">
                                        @if($activity->image_url)
                                            <img src="{{ $activity->image_url }}" alt="{{ $activity->titre }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-16 h-16 rounded-lg bg-{{ $activity->type_color }}-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-8 h-8 text-{{ $activity->type_color }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $activity->titre }}</p>
                                                <x-badge color="{{ $activity->type_color }}" size="sm">{{ $activity->type_label }}</x-badge>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $activity->debut?->format('d/m/Y H:i') }}
                                                @if($activity->lieu)
                                                    <span class="ml-2">
                                                        <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        {{ $activity->lieu }}
                                                    </span>
                                                @endif
                                            </p>
                                            @if($activity->description)
                                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ Str::limit($activity->description, 100) }}</p>
                                            @endif
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 pt-4 border-t">
                            <x-button href="{{ route('activities.index') }}" variant="ghost" class="w-full justify-center">
                                Voir toutes les activites
                            </x-button>
                        </div>
                    @else
                        <x-empty-state title="Aucune activite" description="Aucune activite prevue pour le moment." />
                    @endif
                </x-card>

                @if($dernieresGaleries->count() > 0)
                <x-card title="Dernieres galeries">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($dernieresGaleries as $galerie)
                            <a href="{{ route('activities.show', $galerie) }}" class="group relative block">
                                @if($galerie->image_url)
                                    <img src="{{ $galerie->image_url }}" alt="{{ $galerie->titre }}" class="w-full h-24 object-cover rounded-lg group-hover:opacity-90 transition-opacity">
                                @else
                                    <div class="w-full h-24 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-700 mt-1 truncate">{{ $galerie->titre }}</p>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t">
                        <x-button href="{{ route('activities.index', ['type' => 'galerie']) }}" variant="ghost" class="w-full justify-center">
                            Voir toutes les galeries
                        </x-button>
                    </div>
                </x-card>
                @endif
            </div>

            <div class="space-y-6">
                @if($athlete)
                <x-card title="Mon profil">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Nom complet</dt>
                            <dd class="text-sm text-gray-900">{{ $athlete->nom_complet }}</dd>
                        </div>
                        @if($athlete->date_naissance)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Age</dt>
                            <dd class="text-sm text-gray-900">{{ $athlete->age }} ans</dd>
                        </div>
                        @endif
                        @if($athlete->telephone)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Telephone</dt>
                            <dd class="text-sm text-gray-900">{{ $athlete->telephone }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Inscrit depuis</dt>
                            <dd class="text-sm text-gray-900">{{ $athlete->date_inscription?->format('d/m/Y') ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <x-card title="Mes disciplines">
                    @if($athlete->disciplines->count() > 0)
                        <ul class="space-y-2">
                            @foreach($athlete->disciplines as $discipline)
                                <li class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-900">{{ $discipline->nom }}</span>
                                    <x-badge color="primary" size="sm">Inscrit</x-badge>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">Aucune discipline inscrite.</p>
                    @endif
                </x-card>
                @endif

                <x-card title="Acces rapide">
                    <div class="space-y-2">
                        <a href="{{ route('activities.index') }}" class="flex items-center gap-3 p-3 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                            <div class="p-2 bg-primary-500 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Toutes les activites</p>
                                <p class="text-xs text-gray-500">Competitions, tournois, matchs...</p>
                            </div>
                        </a>
                        <a href="{{ route('activities.index', ['type' => 'galerie']) }}" class="flex items-center gap-3 p-3 bg-secondary-50 rounded-lg hover:bg-secondary-100 transition-colors">
                            <div class="p-2 bg-secondary-500 rounded-lg">
                                <svg class="w-5 h-5 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Galeries photos</p>
                                <p class="text-xs text-gray-500">Photos et videos des evenements</p>
                            </div>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="p-2 bg-gray-500 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Mon compte</p>
                                <p class="text-xs text-gray-500">Modifier mon mot de passe</p>
                            </div>
                        </a>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
