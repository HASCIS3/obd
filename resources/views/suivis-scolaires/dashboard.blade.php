@section('title', 'Tableau de bord Suivi')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">📊 Tableau de bord Sport & Etudes</h2>
                <p class="mt-1 text-sm text-gray-500">Analyse de la correlation entre pratique sportive et performance scolaire</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('suivis-scolaires.gestion-bulletins') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                    Gestion bulletins
                </x-button>
                <x-button href="{{ route('suivis-scolaires.index') }}" variant="ghost">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Liste des suivis
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques globales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <x-card class="bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Athletes suivis</p>
                        <p class="text-3xl font-bold">{{ $stats['total_athletes'] }}</p>
                    </div>
                    <div class="text-4xl opacity-80">👥</div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-green-500 to-green-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Moyenne generale</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['moyenne_globale'], 2) }}/20</p>
                    </div>
                    <div class="text-4xl opacity-80">📚</div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-purple-500 to-purple-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Taux presence moyen</p>
                        <p class="text-3xl font-bold">{{ number_format($stats['taux_presence_moyen'], 1) }}%</p>
                    </div>
                    <div class="text-4xl opacity-80">✅</div>
                </div>
            </x-card>

            <x-card class="bg-gradient-to-br from-orange-500 to-orange-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm">Correlation</p>
                        <p class="text-3xl font-bold">{{ $stats['correlation'] }}</p>
                    </div>
                    <div class="text-4xl opacity-80">📈</div>
                </div>
            </x-card>
        </div>

        <!-- Alertes et recommandations -->
        @if(count($alertes) > 0)
        <x-card class="mb-8 border-l-4 border-warning-500 bg-warning-50">
            <h3 class="text-lg font-semibold text-warning-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Alertes et recommandations ({{ count($alertes) }})
            </h3>
            <div class="space-y-3">
                @foreach($alertes as $alerte)
                <div class="flex items-start p-3 bg-white rounded-lg shadow-sm">
                    <span class="text-2xl mr-3">{{ $alerte['icon'] }}</span>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $alerte['athlete'] }}</p>
                        <p class="text-sm text-gray-600">{{ $alerte['message'] }}</p>
                        <p class="text-xs text-{{ $alerte['type'] }}-600 mt-1">{{ $alerte['recommandation'] }}</p>
                    </div>
                    <a href="{{ route('suivis-scolaires.rapport-athlete', $alerte['athlete_id']) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                        Voir rapport →
                    </a>
                </div>
                @endforeach
            </div>
        </x-card>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Graphique correlation -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Correlation Presence vs Moyenne scolaire</h3>
                <div class="h-80">
                    <canvas id="correlationChart"></canvas>
                </div>
                <p class="text-xs text-gray-500 mt-2 text-center">Chaque point represente un athlete. Plus le point est en haut a droite, meilleure est la correlation.</p>
            </x-card>

            <!-- Repartition par niveau -->
            <x-card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Repartition par niveau scolaire</h3>
                <div class="h-80">
                    <canvas id="niveauChart"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Evolution mensuelle -->
        <x-card class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Evolution mensuelle (Presence vs Resultats)</h3>
            <div class="h-80">
                <canvas id="evolutionChart"></canvas>
            </div>
        </x-card>

        <!-- Tableau des athletes avec indicateurs -->
        <x-card :padding="false">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">🏃 Suivi individuel des athletes</h3>
                <p class="text-sm text-gray-500">Cliquez sur un athlete pour voir son rapport detaille et l'envoyer aux parents</p>
            </div>
            
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-th>Athlete</x-th>
                        <x-th>Presence</x-th>
                        <x-th>Moyenne</x-th>
                        <x-th>Tendance</x-th>
                        <x-th>Equilibre</x-th>
                        <x-th>Statut</x-th>
                        <x-th class="text-right">Actions</x-th>
                    </tr>
                </x-slot>

                @forelse($athletesAnalyse as $analyse)
                <tr class="hover:bg-gray-50">
                    <x-td>
                        <div class="flex items-center">
                            @if($analyse['athlete']->photo_url)
                                <img src="{{ $analyse['athlete']->photo_url }}" class="w-10 h-10 rounded-full object-cover mr-3">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                    <span class="text-primary-600 font-semibold">{{ substr($analyse['athlete']->prenom, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $analyse['athlete']->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $analyse['athlete']->categorie_age }} • {{ $analyse['athlete']->disciplinesActives->pluck('nom')->join(', ') ?: 'Aucune discipline' }}</p>
                            </div>
                        </div>
                    </x-td>
                    <x-td>
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="h-2 rounded-full {{ $analyse['taux_presence'] >= 80 ? 'bg-green-500' : ($analyse['taux_presence'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $analyse['taux_presence'] }}%"></div>
                            </div>
                            <span class="text-sm font-medium">{{ number_format($analyse['taux_presence'], 0) }}%</span>
                        </div>
                    </x-td>
                    <x-td>
                        @if($analyse['moyenne'])
                            <span class="font-semibold {{ $analyse['moyenne'] >= 14 ? 'text-green-600' : ($analyse['moyenne'] >= 10 ? 'text-blue-600' : ($analyse['moyenne'] >= 8 ? 'text-yellow-600' : 'text-red-600')) }}">
                                {{ number_format($analyse['moyenne'], 2) }}/20
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </x-td>
                    <x-td>
                        @if($analyse['tendance'] === 'hausse')
                            <span class="inline-flex items-center text-green-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Hausse
                            </span>
                        @elseif($analyse['tendance'] === 'baisse')
                            <span class="inline-flex items-center text-red-600">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" />
                                </svg>
                                Baisse
                            </span>
                        @else
                            <span class="inline-flex items-center text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                </svg>
                                Stable
                            </span>
                        @endif
                    </x-td>
                    <x-td>
                        <x-badge color="{{ $analyse['equilibre_color'] }}">{{ $analyse['equilibre'] }}</x-badge>
                    </x-td>
                    <x-td>
                        @if($analyse['alerte'])
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ⚠️ Attention
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✅ OK
                            </span>
                        @endif
                    </x-td>
                    <x-td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('suivis-scolaires.rapport-athlete', $analyse['athlete']->id) }}" class="text-primary-600 hover:text-primary-800" title="Voir rapport">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </a>
                            <a href="{{ route('suivis-scolaires.rapport-parent', $analyse['athlete']->id) }}" class="text-green-600 hover:text-green-800" title="Rapport parent">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </a>
                        </div>
                    </x-td>
                </tr>
                @empty
                <tr>
                    <x-td colspan="7" class="text-center py-8 text-gray-500">
                        Aucun athlete avec suivi scolaire
                    </x-td>
                </tr>
                @endforelse
            </x-table>
        </x-card>
    </div>

    @push('scripts')
    <script>
        // Donnees pour les graphiques
        const correlationData = @json($correlationData);
        const niveauData = @json($niveauData);
        const evolutionData = @json($evolutionData);

        // Graphique de correlation (scatter)
        new Chart(document.getElementById('correlationChart'), {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Athletes',
                    data: correlationData,
                    backgroundColor: correlationData.map(d => {
                        if (d.y >= 14) return 'rgba(34, 197, 94, 0.7)';
                        if (d.y >= 10) return 'rgba(59, 130, 246, 0.7)';
                        if (d.y >= 8) return 'rgba(234, 179, 8, 0.7)';
                        return 'rgba(239, 68, 68, 0.7)';
                    }),
                    borderColor: correlationData.map(d => {
                        if (d.y >= 14) return 'rgb(34, 197, 94)';
                        if (d.y >= 10) return 'rgb(59, 130, 246)';
                        if (d.y >= 8) return 'rgb(234, 179, 8)';
                        return 'rgb(239, 68, 68)';
                    }),
                    borderWidth: 2,
                    pointRadius: 8,
                    pointHoverRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw.name + ': Presence ' + context.raw.x + '%, Moyenne ' + context.raw.y + '/20';
                            }
                        }
                    },
                    legend: { display: false }
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Taux de presence (%)' },
                        min: 0,
                        max: 100
                    },
                    y: {
                        title: { display: true, text: 'Moyenne scolaire (/20)' },
                        min: 0,
                        max: 20
                    }
                }
            }
        });

        // Graphique de repartition par niveau
        new Chart(document.getElementById('niveauChart'), {
            type: 'doughnut',
            data: {
                labels: ['Excellent (>=17)', 'Très bien (14-17)', 'Satisfaisant (12-14)', 'Passable (10-12)', 'Insuffisant (<10)'],
                datasets: [{
                    data: [niveauData.excellent, niveauData.tresBien, niveauData.satisfaisant, niveauData.passable, niveauData.insuffisant],
                    backgroundColor: ['#22c55e', '#3b82f6', '#6b7280', '#eab308', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Graphique d'evolution mensuelle
        new Chart(document.getElementById('evolutionChart'), {
            type: 'line',
            data: {
                labels: evolutionData.labels,
                datasets: [
                    {
                        label: 'Taux de presence (%)',
                        data: evolutionData.presence,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Moyenne scolaire (/20)',
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
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Presence (%)' },
                        min: 0,
                        max: 100
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Moyenne (/20)' },
                        min: 0,
                        max: 20,
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
