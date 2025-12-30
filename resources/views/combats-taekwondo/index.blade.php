<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Combats Taekwondo
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $rencontre->adversaire }} - {{ $rencontre->date_match->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <x-button href="{{ route('rencontres.show', $rencontre) }}" variant="secondary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour
                </x-button>
                <x-button href="{{ route('combats-taekwondo.create', $rencontre) }}" variant="primary">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau combat
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($combats->count() > 0)
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($combats as $combat)
                        <x-card class="hover:shadow-lg transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $combat->statut === 'termine' ? 'bg-green-100 text-green-800' : ($combat->statut === 'en_cours' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $combat->statut_label }}
                                </span>
                                @if($combat->categorie_poids)
                                    <span class="text-xs text-gray-500">{{ $combat->categorie_poids }}</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between mb-4">
                                <div class="text-center flex-1">
                                    <div class="w-12 h-12 mx-auto bg-red-500 rounded-full flex items-center justify-center mb-2">
                                        <span class="text-white font-bold text-lg">{{ $combat->score_rouge }}</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $combat->nom_rouge_complet }}</p>
                                    @if($combat->club_rouge)
                                        <p class="text-xs text-gray-500">{{ $combat->club_rouge }}</p>
                                    @endif
                                </div>

                                <div class="px-4 text-gray-400 font-bold">VS</div>

                                <div class="text-center flex-1">
                                    <div class="w-12 h-12 mx-auto bg-blue-500 rounded-full flex items-center justify-center mb-2">
                                        <span class="text-white font-bold text-lg">{{ $combat->score_bleu }}</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $combat->nom_bleu_complet }}</p>
                                    @if($combat->club_bleu)
                                        <p class="text-xs text-gray-500">{{ $combat->club_bleu }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($combat->statut === 'termine' && $combat->vainqueur !== 'non_determine')
                                <div class="text-center py-2 rounded-lg {{ $combat->vainqueur === 'rouge' ? 'bg-red-50 text-red-700' : ($combat->vainqueur === 'bleu' ? 'bg-blue-50 text-blue-700' : 'bg-gray-50 text-gray-700') }}">
                                    <span class="text-sm font-medium">
                                        🏆 {{ $combat->vainqueur_label }}
                                    </span>
                                </div>
                            @endif

                            <div class="mt-4 flex gap-2">
                                <x-button href="{{ route('combats-taekwondo.saisie', [$rencontre, $combat]) }}" variant="primary" class="flex-1 justify-center text-sm">
                                    {{ $combat->statut === 'termine' ? 'Voir' : 'Saisir' }}
                                </x-button>
                                <form action="{{ route('combats-taekwondo.destroy', [$rencontre, $combat]) }}" method="POST" onsubmit="return confirm('Supprimer ce combat ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-md">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun combat</h3>
                        <p class="mt-1 text-sm text-gray-500">Créez un nouveau combat pour cette compétition.</p>
                        <div class="mt-6">
                            <x-button href="{{ route('combats-taekwondo.create', $rencontre) }}" variant="primary">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Nouveau combat
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>
