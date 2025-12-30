@section('title', 'Certificat Médical')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Certificat Médical</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $certificat->athlete?->nom_complet ?? 'Athlète supprimé' }}</p>
            </div>
            <div class="flex gap-2">
                <x-button href="{{ route('certificats-medicaux.edit', $certificat) }}" variant="secondary">
                    Modifier
                </x-button>
                <x-button href="{{ route('certificats-medicaux.index') }}" variant="ghost">
                    Retour
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du certificat</h3>
                    
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificat->type_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $certificat->statut_badge_class }}">
                                    {{ ucfirst($certificat->statut) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'examen</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificat->date_examen->format('d/m/Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date d'expiration</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $certificat->date_expiration->format('d/m/Y') }}
                                @if($certificat->jours_restants > 0)
                                    <span class="text-gray-500 text-xs">({{ $certificat->jours_restants }} jours)</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Médecin</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificat->medecin }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Établissement</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificat->etablissement ?? 'Non renseigné' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 pt-4 border-t">
                        <h4 class="text-sm font-medium text-gray-500 mb-3">Aptitude</h4>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                @if($certificat->apte_competition)
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                <span class="text-sm">Compétition</span>
                            </div>
                            <div class="flex items-center">
                                @if($certificat->apte_entrainement)
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                <span class="text-sm">Entraînement</span>
                            </div>
                        </div>
                    </div>

                    @if($certificat->restrictions)
                        <div class="mt-4 pt-4 border-t">
                            <dt class="text-sm font-medium text-gray-500">Restrictions</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificat->restrictions }}</dd>
                        </div>
                    @endif

                    @if($certificat->observations)
                        <div class="mt-4 pt-4 border-t">
                            <dt class="text-sm font-medium text-gray-500">Observations</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $certificat->observations }}</dd>
                        </div>
                    @endif

                    @if($certificat->document)
                        <div class="mt-4 pt-4 border-t">
                            <a href="{{ $certificat->document_url }}" target="_blank" class="inline-flex items-center text-primary-600 hover:underline">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Télécharger le document
                            </a>
                        </div>
                    @endif
                </x-card>

                @if($historique->count() > 0)
                    <x-card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique</h3>
                        <div class="space-y-3">
                            @foreach($historique as $ancien)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="text-sm font-medium">{{ $ancien->type_label }}</span>
                                        <span class="text-gray-500 text-sm ml-2">
                                            {{ $ancien->date_examen->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ancien->statut_badge_class }}">
                                        {{ ucfirst($ancien->statut) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            </div>

            <div class="space-y-6">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Athlète</h3>
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full overflow-hidden bg-gray-200">
                            @if($certificat->athlete->photo_url)
                                <img src="{{ $certificat->athlete->photo_url }}" alt="{{ $certificat->athlete->nom_complet }}" class="h-12 w-12 object-cover">
                            @else
                                <div class="h-12 w-12 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $certificat->athlete->nom_complet }}</div>
                            <div class="text-sm text-gray-500">{{ $certificat->athlete->age }} ans</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-button href="{{ route('athletes.show', $certificat->athlete) }}" variant="secondary" size="sm" class="w-full">
                            Voir le profil
                        </x-button>
                    </div>
                </x-card>

                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <x-button href="{{ route('certificats-medicaux.create', ['athlete_id' => $certificat->athlete_id]) }}" variant="success" class="w-full">
                            Nouveau certificat
                        </x-button>
                        <form action="{{ route('certificats-medicaux.destroy', $certificat) }}" method="POST" onsubmit="return confirm('Supprimer ce certificat ?')">
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="danger" class="w-full">
                                Supprimer
                            </x-button>
                        </form>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
