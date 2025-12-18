@section('title', 'Rapport mensuel')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Rapport mensuel</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $rapport['periode']['libelle'] }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <form method="GET" action="{{ route('rapport.mensuel') }}" class="flex gap-2">
                    <x-select 
                        name="mois" 
                        :options="[
                            ['id' => 1, 'name' => 'Janvier'], ['id' => 2, 'name' => 'Fevrier'],
                            ['id' => 3, 'name' => 'Mars'], ['id' => 4, 'name' => 'Avril'],
                            ['id' => 5, 'name' => 'Mai'], ['id' => 6, 'name' => 'Juin'],
                            ['id' => 7, 'name' => 'Juillet'], ['id' => 8, 'name' => 'Aout'],
                            ['id' => 9, 'name' => 'Septembre'], ['id' => 10, 'name' => 'Octobre'],
                            ['id' => 11, 'name' => 'Novembre'], ['id' => 12, 'name' => 'Decembre'],
                        ]" 
                        :selected="$mois"
                    />
                    <x-input type="number" name="annee" :value="$annee" min="2020" max="2100" class="w-24" />
                    <x-button type="submit" variant="primary">Generer</x-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-stat-card 
                title="Athletes actifs" 
                :value="$rapport['athletes']['total_actifs']"
                color="primary"
            />
            <x-stat-card 
                title="Nouveaux inscrits" 
                :value="$rapport['athletes']['nouveaux']"
                color="success"
            />
            <x-stat-card 
                title="Taux de presence" 
                :value="$rapport['presences']['taux'] . '%'"
                color="info"
            />
            <x-stat-card 
                title="Taux de recouvrement" 
                :value="$rapport['paiements']['taux_recouvrement'] . '%'"
                color="secondary"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Presences -->
            <x-card title="Presences">
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total enregistrements</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $rapport['presences']['total'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Taux de presence</dt>
                        <dd class="mt-1 text-2xl font-semibold text-primary-600">{{ $rapport['presences']['taux'] }}%</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Presents</dt>
                        <dd class="mt-1 text-xl font-semibold text-green-600">{{ $rapport['presences']['presents'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Absents</dt>
                        <dd class="mt-1 text-xl font-semibold text-danger-600">{{ $rapport['presences']['absents'] }}</dd>
                    </div>
                </dl>

                <!-- Barre de progression -->
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-500 mb-1">
                        <span>Presence</span>
                        <span>{{ $rapport['presences']['taux'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-primary-500 h-3 rounded-full" style="width: {{ $rapport['presences']['taux'] }}%"></div>
                    </div>
                </div>
            </x-card>

            <!-- Paiements -->
            <x-card title="Paiements">
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total du</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">{{ number_format($rapport['paiements']['total_du'], 0, ',', ' ') }} FCFA</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total paye</dt>
                        <dd class="mt-1 text-xl font-semibold text-green-600">{{ number_format($rapport['paiements']['total_paye'], 0, ',', ' ') }} FCFA</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Arrieres</dt>
                        <dd class="mt-1 text-xl font-semibold text-danger-600">{{ number_format($rapport['paiements']['total_arrieres'], 0, ',', ' ') }} FCFA</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Taux de recouvrement</dt>
                        <dd class="mt-1 text-xl font-semibold text-primary-600">{{ $rapport['paiements']['taux_recouvrement'] }}%</dd>
                    </div>
                </dl>

                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between text-sm">
                        <span class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            Payes: {{ $rapport['paiements']['nb_payes'] }}
                        </span>
                        <span class="flex items-center">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                            Partiels: {{ $rapport['paiements']['nb_partiels'] }}
                        </span>
                        <span class="flex items-center">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            Impayes: {{ $rapport['paiements']['nb_impayes'] }}
                        </span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Performances -->
        <x-card title="Performances sportives" class="mt-6">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-3xl font-bold text-primary-600">{{ $rapport['performances']['total'] }}</p>
                    <p class="text-sm text-gray-500">Evaluations</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-secondary-600">{{ $rapport['performances']['en_competition'] }}</p>
                    <p class="text-sm text-gray-500">Competitions</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-yellow-600">{{ $rapport['performances']['podiums'] }}</p>
                    <p class="text-sm text-gray-500">Podiums</p>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>
