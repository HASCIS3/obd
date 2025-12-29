@section('title', 'Gestion des Presences')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestion des Presences</h2>
                <p class="mt-1 text-sm text-gray-500">Pointage et suivi des presences des athletes</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('presences.index') }}" variant="outline">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Liste des presences
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Navigation par onglets -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('presences.pointage.quotidien', ['discipline' => $disciplineId]) }}" 
                       class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'quotidien' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="-ml-0.5 mr-2 h-5 w-5 {{ $periode === 'quotidien' ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Quotidien
                    </a>
                    <a href="{{ route('presences.pointage.hebdomadaire', ['discipline' => $disciplineId]) }}" 
                       class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'hebdomadaire' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="-ml-0.5 mr-2 h-5 w-5 {{ $periode === 'hebdomadaire' ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Hebdomadaire
                    </a>
                    <a href="{{ route('presences.pointage.mensuel', ['discipline' => $disciplineId]) }}" 
                       class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'mensuel' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="-ml-0.5 mr-2 h-5 w-5 {{ $periode === 'mensuel' ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Mensuel
                    </a>
                    <a href="{{ route('presences.pointage.annuel', ['discipline' => $disciplineId]) }}" 
                       class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm {{ $periode === 'annuel' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="-ml-0.5 mr-2 h-5 w-5 {{ $periode === 'annuel' ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        Annuel
                    </a>
                </nav>
            </div>
        </div>

        <!-- Selecteur de discipline -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('presences.pointage.' . $periode) }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discipline</label>
                    <x-select 
                        name="discipline" 
                        :options="$disciplines" 
                        :selected="$disciplineId"
                        placeholder="Selectionner une discipline"
                        valueKey="id"
                        labelKey="nom"
                    />
                </div>
                
                @yield('periode-filters')
                
                <div>
                    <x-button type="submit" variant="primary">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Charger
                    </x-button>
                </div>
            </form>
        </x-card>

        <!-- Contenu specifique a la periode -->
        @yield('pointage-content')
    </div>
</x-app-layout>
