@section('title', 'Rapport mensuel des presences')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('presences.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Rapport mensuel des presences</h2>
                <p class="mt-1 text-sm text-gray-500">Statistiques par discipline</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('presences.rapport-mensuel') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-group label="Mois" name="mois">
                    <x-select 
                        name="mois" 
                        :options="[
                            ['id' => 1, 'name' => 'Janvier'],
                            ['id' => 2, 'name' => 'Fevrier'],
                            ['id' => 3, 'name' => 'Mars'],
                            ['id' => 4, 'name' => 'Avril'],
                            ['id' => 5, 'name' => 'Mai'],
                            ['id' => 6, 'name' => 'Juin'],
                            ['id' => 7, 'name' => 'Juillet'],
                            ['id' => 8, 'name' => 'Aout'],
                            ['id' => 9, 'name' => 'Septembre'],
                            ['id' => 10, 'name' => 'Octobre'],
                            ['id' => 11, 'name' => 'Novembre'],
                            ['id' => 12, 'name' => 'Decembre'],
                        ]" 
                        :selected="$mois"
                        placeholder=""
                    />
                </x-form-group>

                <x-form-group label="Annee" name="annee">
                    <x-input type="number" name="annee" :value="$annee" min="2020" max="2100" />
                </x-form-group>

                <div class="flex items-end">
                    <x-button type="submit" variant="primary" class="w-full">
                        Generer le rapport
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Rapport -->
        <x-card title="Statistiques par discipline" :padding="false">
            @if(count($stats) > 0)
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Discipline</x-th>
                            <x-th class="text-center">Total</x-th>
                            <x-th class="text-center">Presents</x-th>
                            <x-th class="text-center">Absents</x-th>
                            <x-th class="text-center">Taux</x-th>
                        </tr>
                    </x-slot>

                    @foreach($stats as $stat)
                        <tr>
                            <x-td class="font-medium">{{ $stat['discipline'] }}</x-td>
                            <x-td class="text-center">{{ $stat['total'] }}</x-td>
                            <x-td class="text-center">
                                <span class="text-green-600 font-medium">{{ $stat['presents'] }}</span>
                            </x-td>
                            <x-td class="text-center">
                                <span class="text-danger-600 font-medium">{{ $stat['absents'] }}</span>
                            </x-td>
                            <x-td class="text-center">
                                <div class="flex items-center justify-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $stat['taux'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ $stat['taux'] }}%</span>
                                </div>
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>
            @else
                <x-empty-state title="Aucune donnee" description="Aucune presence enregistree pour cette periode." />
            @endif
        </x-card>
    </div>
</x-app-layout>
