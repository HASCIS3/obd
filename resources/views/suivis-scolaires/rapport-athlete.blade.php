@section('title', 'Rapport ' . $athlete->nom_complet)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center">
                @if($athlete->photo_url)
                    <img src="{{ $athlete->photo_url }}" class="w-16 h-16 rounded-full object-cover mr-4 border-2 border-primary-500">
                @else
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center mr-4">
                        <span class="text-primary-600 font-bold text-2xl">{{ substr($athlete->prenom, 0, 1) }}</span>
                    </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $athlete->nom_complet }}</h2>
                    <p class="text-sm text-gray-500">{{ $athlete->categorie_age }} • {{ $athlete->age }} ans • {{ $athlete->disciplinesActives->pluck('nom')->join(', ') ?: 'Aucune discipline' }}</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('suivis-scolaires.rapport-parent', $athlete->id) }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Rapport parent
                </x-button>
                <x-button href="{{ route('suivis-scolaires.dashboard') }}" variant="ghost">
                    ← Retour
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Indicateurs cles -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <x-card class="text-center">
                <div class="text-4xl mb-2">📊</div>
                <p class="text-2xl font-bold {{ $analyse['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $analyse['moyenne'] ? number_format($analyse['moyenne'], 2) . '/20' : 'N/A' }}
                </p>
                <p class="text-sm text-gray-500">Moyenne actuelle</p>
            </x-card>

            <x-card class="text-center">
                <div class="text-4xl mb-2">✅</div>
                <p class="text-2xl font-bold {{ $analyse['taux_presence'] >= 80 ? 'text-green-600' : ($analyse['taux_presence'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ number_format($analyse['taux_presence'], 0) }}%
                </p>
                <p class="text-sm text-gray-500">Taux de presence</p>
            </x-card>

            <x-card class="text-center">
                <div class="text-4xl mb-2">🏃</div>
                <p class="text-2xl font-bold text-primary-600">{{ $analyse['nb_seances'] }}</p>
                <p class="text-sm text-gray-500">Seances ce mois</p>
            </x-card>

            <x-card class="text-center">
                <div class="text-4xl mb-2">
                    @if($analyse['tendance'] === 'hausse') 📈
                    @elseif($analyse['tendance'] === 'baisse') 📉
                    @else ➡️
                    @endif
                </div>
                <p class="text-2xl font-bold {{ $analyse['tendance'] === 'hausse' ? 'text-green-600' : ($analyse['tendance'] === 'baisse' ? 'text-red-600' : 'text-gray-600') }}">
                    {{ ucfirst($analyse['tendance']) }}
                </p>
                <p class="text-sm text-gray-500">Tendance scolaire</p>
            </x-card>

            <x-card class="text-center">
                <div class="text-4xl mb-2">⚖️</div>
                <x-badge color="{{ $analyse['equilibre_color'] }}" class="text-lg px-3 py-1">{{ $analyse['equilibre'] }}</x-badge>
                <p class="text-sm text-gray-500 mt-2">Equilibre Sport/Etudes</p>
            </x-card>
        </div>

        <!-- Analyse detaillee -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Graphique evolution -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Evolution sur 6 mois</h3>
                <div class="h-64">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </x-card>

            <!-- Recommandations -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">💡 Analyse et recommandations</h3>
                <div class="space-y-4">
                    @foreach($recommandations as $reco)
                    <div class="flex items-start p-3 rounded-lg {{ $reco['type'] === 'success' ? 'bg-green-50' : ($reco['type'] === 'warning' ? 'bg-yellow-50' : 'bg-red-50') }}">
                        <span class="text-2xl mr-3">{{ $reco['icon'] }}</span>
                        <div>
                            <p class="font-medium {{ $reco['type'] === 'success' ? 'text-green-800' : ($reco['type'] === 'warning' ? 'text-yellow-800' : 'text-red-800') }}">
                                {{ $reco['titre'] }}
                            </p>
                            <p class="text-sm {{ $reco['type'] === 'success' ? 'text-green-600' : ($reco['type'] === 'warning' ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $reco['message'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <!-- Correlation detaillee -->
        <x-card class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔬 Analyse de correlation Sport/Etudes</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 mb-2">Intensite d'entrainement</p>
                    <div class="flex justify-center mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="text-2xl {{ $i <= $analyse['intensite'] ? 'text-primary-500' : 'text-gray-300' }}">●</span>
                        @endfor
                    </div>
                    <p class="text-sm font-medium">{{ $analyse['intensite_label'] }}</p>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 mb-2">Impact sur les etudes</p>
                    <div class="text-4xl mb-2">
                        @if($analyse['impact'] === 'positif') 👍
                        @elseif($analyse['impact'] === 'negatif') 👎
                        @else 👌
                        @endif
                    </div>
                    <p class="text-sm font-medium {{ $analyse['impact'] === 'positif' ? 'text-green-600' : ($analyse['impact'] === 'negatif' ? 'text-red-600' : 'text-gray-600') }}">
                        {{ ucfirst($analyse['impact']) }}
                    </p>
                </div>

                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 mb-2">Charge recommandee</p>
                    <p class="text-2xl font-bold text-primary-600 mb-1">{{ $analyse['charge_recommandee'] }}</p>
                    <p class="text-sm text-gray-600">seances/semaine</p>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Conclusion :</strong> {{ $analyse['conclusion'] }}
                </p>
            </div>
        </x-card>

        <!-- Historique des suivis scolaires -->
        <x-card :padding="false" class="mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">📚 Historique scolaire</h3>
            </div>
            
            @if($suivis->count() > 0)
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-th>Annee</x-th>
                        <x-th>Etablissement</x-th>
                        <x-th>Classe</x-th>
                        <x-th>Moyenne</x-th>
                        <x-th>Rang</x-th>
                        <x-th>Niveau</x-th>
                    </tr>
                </x-slot>

                @foreach($suivis as $suivi)
                <tr class="hover:bg-gray-50">
                    <x-td class="font-medium">{{ $suivi->annee_scolaire ?: '-' }}</x-td>
                    <x-td>{{ $suivi->etablissement ?: '-' }}</x-td>
                    <x-td>{{ $suivi->classe ?: '-' }}</x-td>
                    <x-td>
                        <span class="font-semibold {{ $suivi->estSatisfaisant() ? 'text-green-600' : 'text-red-600' }}">
                            {{ $suivi->moyenne_formatee }}
                        </span>
                    </x-td>
                    <x-td>{{ $suivi->rang_formate ?: '-' }}</x-td>
                    <x-td>
                        <x-badge color="{{ $suivi->niveau_couleur }}">{{ $suivi->niveau }}</x-badge>
                    </x-td>
                </tr>
                @endforeach
            </x-table>
            @else
            <div class="p-8 text-center text-gray-500">
                Aucun suivi scolaire enregistre
            </div>
            @endif
        </x-card>

        <!-- Historique des presences recentes -->
        <x-card :padding="false">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">🏃 Presences recentes (30 derniers jours)</h3>
                <span class="text-sm text-gray-500">{{ $presences->where('present', true)->count() }}/{{ $presences->count() }} presences</span>
            </div>
            
            <div class="p-6">
                <div class="flex flex-wrap gap-1">
                    @foreach($presences as $presence)
                    <div class="w-8 h-8 rounded {{ $presence->present ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center text-white text-xs font-medium" title="{{ $presence->date->format('d/m') }} - {{ $presence->present ? 'Present' : 'Absent' }}">
                        {{ $presence->date->format('d') }}
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <span class="flex items-center"><span class="w-3 h-3 bg-green-500 rounded mr-1"></span> Present</span>
                    <span class="flex items-center"><span class="w-3 h-3 bg-red-500 rounded mr-1"></span> Absent</span>
                </div>
            </div>
        </x-card>
    </div>

    @push('scripts')
    <script>
        const evolutionData = @json($evolutionData);

        new Chart(document.getElementById('evolutionChart'), {
            type: 'line',
            data: {
                labels: evolutionData.labels,
                datasets: [
                    {
                        label: 'Presence (%)',
                        data: evolutionData.presence,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Moyenne (/20)',
                        data: evolutionData.moyenne,
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { min: 0, max: 100, position: 'left' },
                    y1: { min: 0, max: 20, position: 'right', grid: { drawOnChartArea: false } }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
