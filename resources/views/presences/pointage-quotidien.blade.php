@section('title', 'Pointage Quotidien')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Pointage Quotidien</h2>
                <p class="mt-1 text-sm text-gray-500">Enregistrement des presences - {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('presences.rapport-mensuel') }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Rapport Mensuel
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Selection Date et Discipline -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('presences.pointage') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-form-group label="Date du pointage" name="date">
                    <x-input type="date" name="date" :value="$date" class="font-semibold" />
                </x-form-group>

                <x-form-group label="Discipline" name="discipline">
                    <x-select 
                        name="discipline" 
                        :options="$disciplines" 
                        :selected="$disciplineId"
                        placeholder="Toutes les disciplines"
                        valueKey="id"
                        labelKey="nom"
                    />
                </x-form-group>

                <div class="flex items-end gap-2">
                    <x-button type="submit" variant="primary" class="flex-1">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Charger
                    </x-button>
                </div>

                <div class="flex items-end gap-2">
                    <!-- Navigation rapide -->
                    <a href="{{ route('presences.pointage', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
                       class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Jour precedent">
                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <a href="{{ route('presences.pointage', ['date' => now()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
                       class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
                        Aujourd'hui
                    </a>
                    <a href="{{ route('presences.pointage', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
                       class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Jour suivant">
                        <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </form>
        </x-card>

        @if($disciplineId && $athletes->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Colonne principale - Liste des athletes -->
                <div class="lg:col-span-3">
                    <x-card>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $selectedDiscipline->nom ?? 'Athletes' }}
                                <span class="text-sm font-normal text-gray-500">({{ $athletes->count() }} athletes)</span>
                            </h3>
                            <div class="flex gap-2">
                                <button type="button" onclick="setAllPresent()" class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200">
                                    Tous presents
                                </button>
                                <button type="button" onclick="setAllAbsent()" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">
                                    Tous absents
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('presences.store') }}" method="POST" id="pointageForm">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="discipline_id" value="{{ $disciplineId }}">

                            <!-- En-tete du tableau -->
                            <div class="hidden md:grid md:grid-cols-12 gap-4 px-4 py-2 bg-gray-50 rounded-t-lg text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="col-span-4">Athlete</div>
                                <div class="col-span-2 text-center">Stats Semaine</div>
                                <div class="col-span-2 text-center">Stats Mois</div>
                                <div class="col-span-2 text-center">Statut</div>
                                <div class="col-span-2">Remarque</div>
                            </div>

                            <div class="divide-y divide-gray-200">
                                @foreach($athletes as $index => $athlete)
                                    @php
                                        $weekStats = $athleteStats[$athlete->id]['week'] ?? ['presents' => 0, 'total' => 0, 'taux' => 0];
                                        $monthStats = $athleteStats[$athlete->id]['month'] ?? ['presents' => 0, 'total' => 0, 'taux' => 0];
                                        $isPresent = $existingPresences[$athlete->id] ?? null;
                                    @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 px-4 py-4 hover:bg-gray-50 items-center">
                                        <!-- Info Athlete -->
                                        <div class="col-span-4 flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full overflow-hidden bg-primary-100 flex items-center justify-center">
                                                @if($athlete->photo_url)
                                                    <img src="{{ $athlete->photo_url }}" alt="{{ $athlete->nom_complet }}" class="h-10 w-10 object-cover">
                                                @else
                                                    <span class="text-primary-600 font-medium text-sm">
                                                        {{ substr($athlete->prenom, 0, 1) }}{{ substr($athlete->nom, 0, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <a href="{{ route('athletes.show', $athlete) }}" class="text-sm font-medium text-gray-900 hover:text-primary-600">
                                                    {{ $athlete->nom_complet }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $athlete->telephone ?: 'Pas de telephone' }}</p>
                                            </div>
                                        </div>

                                        <!-- Stats Semaine -->
                                        <div class="col-span-2 text-center">
                                            <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium 
                                                {{ $weekStats['taux'] >= 80 ? 'bg-green-100 text-green-800' : ($weekStats['taux'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $weekStats['presents'] }}/{{ $weekStats['total'] }}
                                                <span class="ml-1 text-xs">({{ $weekStats['taux'] }}%)</span>
                                            </div>
                                            <p class="text-xs text-gray-400 mt-1">Cette semaine</p>
                                        </div>

                                        <!-- Stats Mois -->
                                        <div class="col-span-2 text-center">
                                            <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium 
                                                {{ $monthStats['taux'] >= 80 ? 'bg-green-100 text-green-800' : ($monthStats['taux'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ $monthStats['presents'] }}/{{ $monthStats['total'] }}
                                                <span class="ml-1 text-xs">({{ $monthStats['taux'] }}%)</span>
                                            </div>
                                            <p class="text-xs text-gray-400 mt-1">Ce mois</p>
                                        </div>

                                        <!-- Statut Presence -->
                                        <div class="col-span-2">
                                            <input type="hidden" name="presences[{{ $index }}][athlete_id]" value="{{ $athlete->id }}">
                                            <div class="flex justify-center gap-2">
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" 
                                                           name="presences[{{ $index }}][present]" 
                                                           value="1"
                                                           {{ $isPresent === true ? 'checked' : '' }}
                                                           {{ $isPresent === null ? 'checked' : '' }}
                                                           class="sr-only peer presence-radio">
                                                    <div class="w-12 h-12 rounded-lg border-2 border-gray-200 flex items-center justify-center
                                                                peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                                                        <svg class="w-6 h-6 text-gray-400 peer-checked:text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-500 block text-center mt-1">Present</span>
                                                </label>
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" 
                                                           name="presences[{{ $index }}][present]" 
                                                           value="0"
                                                           {{ $isPresent === false ? 'checked' : '' }}
                                                           class="sr-only peer presence-radio">
                                                    <div class="w-12 h-12 rounded-lg border-2 border-gray-200 flex items-center justify-center
                                                                peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                                        <svg class="w-6 h-6 text-gray-400 peer-checked:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </div>
                                                    <span class="text-xs text-gray-500 block text-center mt-1">Absent</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Remarque -->
                                        <div class="col-span-2">
                                            <input type="text" 
                                                   name="presences[{{ $index }}][remarque]" 
                                                   placeholder="Motif..."
                                                   value="{{ $existingRemarks[$athlete->id] ?? '' }}"
                                                   class="w-full text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-between items-center mt-6 pt-6 border-t">
                                <div class="text-sm text-gray-500">
                                    <span class="font-medium text-gray-900">{{ $athletes->count() }}</span> athletes a pointer
                                </div>
                                <div class="flex gap-3">
                                    <x-button href="{{ route('presences.index') }}" variant="ghost">Annuler</x-button>
                                    <x-button type="submit" variant="primary">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Enregistrer le pointage
                                    </x-button>
                                </div>
                            </div>
                        </form>
                    </x-card>
                </div>

                <!-- Colonne laterale - Statistiques -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Resume du jour -->
                    <x-card title="Resume du jour">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Total athletes</span>
                                <span class="text-lg font-bold text-gray-900">{{ $athletes->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Deja pointes</span>
                                <span class="text-lg font-bold text-primary-600">{{ count($existingPresences) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Presents</span>
                                <span class="text-lg font-bold text-green-600">{{ collect($existingPresences)->filter(fn($p) => $p === true)->count() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Absents</span>
                                <span class="text-lg font-bold text-red-600">{{ collect($existingPresences)->filter(fn($p) => $p === false)->count() }}</span>
                            </div>
                        </div>
                    </x-card>

                    <!-- Stats de la semaine -->
                    <x-card title="Cette semaine">
                        @php
                            $weekTotal = collect($athleteStats)->sum(fn($s) => $s['week']['total'] ?? 0);
                            $weekPresents = collect($athleteStats)->sum(fn($s) => $s['week']['presents'] ?? 0);
                            $weekTaux = $weekTotal > 0 ? round(($weekPresents / $weekTotal) * 100, 1) : 0;
                        @endphp
                        <div class="text-center">
                            <div class="text-3xl font-bold {{ $weekTaux >= 80 ? 'text-green-600' : ($weekTaux >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $weekTaux }}%
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Taux de presence</p>
                            <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $weekTaux >= 80 ? 'bg-green-500' : ($weekTaux >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                     style="width: {{ $weekTaux }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">{{ $weekPresents }} presents / {{ $weekTotal }} seances</p>
                        </div>
                    </x-card>

                    <!-- Stats du mois -->
                    <x-card title="Ce mois">
                        @php
                            $monthTotal = collect($athleteStats)->sum(fn($s) => $s['month']['total'] ?? 0);
                            $monthPresents = collect($athleteStats)->sum(fn($s) => $s['month']['presents'] ?? 0);
                            $monthTaux = $monthTotal > 0 ? round(($monthPresents / $monthTotal) * 100, 1) : 0;
                        @endphp
                        <div class="text-center">
                            <div class="text-3xl font-bold {{ $monthTaux >= 80 ? 'text-green-600' : ($monthTaux >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $monthTaux }}%
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Taux de presence</p>
                            <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $monthTaux >= 80 ? 'bg-green-500' : ($monthTaux >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                     style="width: {{ $monthTaux }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">{{ $monthPresents }} presents / {{ $monthTotal }} seances</p>
                        </div>
                    </x-card>

                    <!-- Legende -->
                    <x-card title="Legende">
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                <span class="text-gray-600">≥ 80% : Excellent</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                                <span class="text-gray-600">50-79% : A ameliorer</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                <span class="text-gray-600">< 50% : Critique</span>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        @elseif($disciplineId)
            <x-card>
                <x-empty-state 
                    title="Aucun athlete" 
                    description="Aucun athlete inscrit a cette discipline."
                />
            </x-card>
        @else
            <x-card>
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Selectionnez une discipline</h3>
                    <p class="mt-2 text-sm text-gray-500">Choisissez une discipline pour commencer le pointage quotidien.</p>
                </div>
            </x-card>
        @endif
    </div>

    @push('scripts')
    <script>
        function setAllPresent() {
            document.querySelectorAll('input[type="radio"][value="1"]').forEach(radio => {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            });
        }

        function setAllAbsent() {
            document.querySelectorAll('input[type="radio"][value="0"]').forEach(radio => {
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            });
        }

        // Style dynamique pour les boutons radio
        document.querySelectorAll('.presence-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const container = this.closest('label').querySelector('div');
                const svg = container.querySelector('svg');
                
                if (this.value === '1' && this.checked) {
                    container.classList.remove('border-gray-200', 'border-red-500', 'bg-red-50');
                    container.classList.add('border-green-500', 'bg-green-50');
                    svg.classList.remove('text-gray-400', 'text-red-600');
                    svg.classList.add('text-green-600');
                } else if (this.value === '0' && this.checked) {
                    container.classList.remove('border-gray-200', 'border-green-500', 'bg-green-50');
                    container.classList.add('border-red-500', 'bg-red-50');
                    svg.classList.remove('text-gray-400', 'text-green-600');
                    svg.classList.add('text-red-600');
                }
            });
            
            // Appliquer le style initial
            if (radio.checked) {
                radio.dispatchEvent(new Event('change'));
            }
        });
    </script>
    @endpush
</x-app-layout>
