@section('title', 'Presences - ' . $athlete->nom_complet)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('athletes.show', $athlete) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Presences de {{ $athlete->nom_complet }}</h2>
                <p class="mt-1 text-sm text-gray-500">Historique et statistiques de presence</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
            <x-stat-card title="Total" :value="$stats['total']" color="info" />
            <x-stat-card title="Presents" :value="$stats['presents']" color="success" />
            <x-stat-card title="Absents" :value="$stats['absents']" color="danger" />
            <x-stat-card title="Taux de presence" :value="$stats['taux'] . '%'" color="primary" />
        </div>

        <!-- Historique -->
        <x-card title="Historique des presences" :padding="false">
            @if($presences->count() > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Date</x-th>
                            <x-th>Discipline</x-th>
                            <x-th>Statut</x-th>
                            <x-th>Remarque</x-th>
                        </tr>
                    </x-slot>

                    @foreach($presences as $presence)
                        <tr>
                            <x-td>{{ $presence->date->format('d/m/Y') }}</x-td>
                            <x-td>{{ $presence->discipline->nom }}</x-td>
                            <x-td>
                                @if($presence->present)
                                    <x-badge color="success">Present</x-badge>
                                @else
                                    <x-badge color="danger">Absent</x-badge>
                                @endif
                            </x-td>
                            <x-td>{{ $presence->remarque ?: '-' }}</x-td>
                        </tr>
                    @endforeach
                </x-table>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $presences->links() }}
                </div>
            @else
                <x-empty-state title="Aucune presence" description="Aucune presence enregistree pour cet athlete." />
            @endif
        </x-card>
    </div>
</x-app-layout>
