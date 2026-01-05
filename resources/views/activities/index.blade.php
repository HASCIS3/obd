@section('title', 'Activites')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Activites</h2>
                <p class="mt-1 text-sm text-gray-500">Competitions, tournois, matchs et evenements du centre.</p>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('activities.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvelle activite
                </x-button>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-card class="mb-6">
            <form method="GET" action="{{ route('activities.index') }}" class="flex flex-wrap gap-2">
                <a href="{{ route('activities.index') }}" class="px-3 py-1.5 text-sm rounded-full {{ !$type ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Tous
                </a>
                @foreach($types as $key => $label)
                    <a href="{{ route('activities.index', ['type' => $key]) }}" class="px-3 py-1.5 text-sm rounded-full {{ $type === $key ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </form>
        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card title="A venir">
                @if($aVenir->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($aVenir as $activity)
                            <li class="py-3">
                                <a href="{{ route('activities.show', $activity) }}" class="block hover:bg-gray-50 rounded-lg p-2 -m-2">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3">
                                            @if($activity->image_url)
                                                <img src="{{ $activity->image_url }}" alt="{{ $activity->titre }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $activity->titre }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $activity->debut?->format('d/m/Y H:i') }}
                                                    @if($activity->lieu)
                                                        - {{ $activity->lieu }}
                                                    @endif
                                                </p>
                                                @if($activity->discipline)
                                                    <p class="text-xs text-primary-600 mt-1">{{ $activity->discipline->nom }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <x-badge color="{{ $activity->type_color }}" size="sm">{{ $activity->type_label }}</x-badge>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        {{ $aVenir->links() }}
                    </div>
                @else
                    <x-empty-state title="Aucune activite" description="Aucune activite a venir pour le moment." />
                @endif
            </x-card>

            <x-card title="Precedentes">
                @if($precedentes->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($precedentes as $item)
                            <li class="py-3">
                                @php
                                    $isRencontre = isset($item->is_rencontre) && $item->is_rencontre;
                                    $url = $isRencontre ? route('rencontres.show', $item->id) : route('activities.show', $item);
                                @endphp
                                <a href="{{ $url }}" class="block hover:bg-gray-50 rounded-lg p-2 -m-2">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3">
                                            @if($isRencontre)
                                                {{-- Icône pour les matchs avec résultat --}}
                                                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0 {{ $item->resultat === 'victoire' ? 'bg-green-100' : ($item->resultat === 'defaite' ? 'bg-red-100' : 'bg-yellow-100') }}">
                                                    <span class="text-2xl">{{ $item->discipline?->icone ?? '⚽' }}</span>
                                                </div>
                                            @elseif(isset($item->image_url) && $item->image_url)
                                                <img src="{{ $item->image_url }}" alt="{{ $item->titre }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $item->titre }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $item->debut instanceof \Carbon\Carbon ? $item->debut->format('d/m/Y') : \Carbon\Carbon::parse($item->debut)->format('d/m/Y') }}
                                                    @if($item->lieu)
                                                        - {{ $item->lieu }}
                                                    @endif
                                                </p>
                                                @if($item->discipline)
                                                    <p class="text-xs text-primary-600 mt-1">{{ $item->discipline->nom }}</p>
                                                @endif
                                                @if($isRencontre && $item->score_obd !== null)
                                                    <p class="text-xs font-bold mt-1 {{ $item->resultat === 'victoire' ? 'text-green-600' : ($item->resultat === 'defaite' ? 'text-red-600' : 'text-yellow-600') }}">
                                                        Score: {{ $item->score_obd }} - {{ $item->score_adversaire }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($isRencontre)
                                            <x-badge color="{{ $item->resultat === 'victoire' ? 'success' : ($item->resultat === 'defaite' ? 'danger' : 'warning') }}" size="sm">
                                                {{ ucfirst($item->resultat) }}
                                            </x-badge>
                                        @else
                                            <x-badge color="{{ $item->type_color ?? 'gray' }}" size="sm">{{ $item->type_label ?? ucfirst($item->type) }}</x-badge>
                                        @endif
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        {{ $precedentes->links() }}
                    </div>
                @else
                    <x-empty-state title="Aucune activite" description="Aucune activite precedente." />
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
