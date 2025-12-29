@extends('presences.pointage.index')

@section('periode-filters')
    <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
        <x-input type="date" name="date" :value="$date" />
    </div>
    
    <div class="flex items-end gap-2">
        <a href="{{ route('presences.pointage.quotidien', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Jour precedent">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <a href="{{ route('presences.pointage.quotidien', ['date' => now()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
            Aujourd'hui
        </a>
        <a href="{{ route('presences.pointage.quotidien', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d'), 'discipline' => $disciplineId]) }}" 
           class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50" title="Jour suivant">
            <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
@endsection

@section('pointage-content')
    @if($disciplineId && $athletes->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Liste des athletes -->
            <div class="lg:col-span-3">
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Pointage du {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                            </h3>
                            <p class="text-sm text-gray-500">{{ $selectedDiscipline->nom ?? 'Discipline' }} - {{ $athletes->count() }} athletes</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="setAllPresent()" class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Tous presents
                            </button>
                            <button type="button" onclick="setAllAbsent()" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-md hover:bg-red-200">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tous absents
                            </button>
                        </div>
                    </div>

                    <form action="{{ route('presences.store') }}" method="POST" id="pointageForm">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">
                        <input type="hidden" name="discipline_id" value="{{ $disciplineId }}">

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Athlete</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Semaine</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarque</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($athletes as $index => $athlete)
                                        @php
                                            $weekStats = $athleteStats[$athlete->id]['week'] ?? ['presents' => 0, 'total' => 0, 'taux' => 0];
                                            $monthStats = $athleteStats[$athlete->id]['month'] ?? ['presents' => 0, 'total' => 0, 'taux' => 0];
                                            $isPresent = $existingPresences[$athlete->id] ?? null;
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
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
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $weekStats['taux'] >= 80 ? 'bg-green-100 text-green-800' : ($weekStats['taux'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $weekStats['presents'] }}/{{ $weekStats['total'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    {{ $monthStats['taux'] >= 80 ? 'bg-green-100 text-green-800' : ($monthStats['taux'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ $monthStats['presents'] }}/{{ $monthStats['total'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <input type="hidden" name="presences[{{ $index }}][athlete_id]" value="{{ $athlete->id }}">
                                                <div class="flex justify-center gap-3">
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="radio" 
                                                               name="presences[{{ $index }}][present]" 
                                                               value="1"
                                                               {{ $isPresent === true || $isPresent === null ? 'checked' : '' }}
                                                               class="w-5 h-5 text-green-600 border-gray-300 focus:ring-green-500">
                                                        <span class="ml-2 text-sm text-gray-700">Present</span>
                                                    </label>
                                                    <label class="flex items-center cursor-pointer">
                                                        <input type="radio" 
                                                               name="presences[{{ $index }}][present]" 
                                                               value="0"
                                                               {{ $isPresent === false ? 'checked' : '' }}
                                                               class="w-5 h-5 text-red-600 border-gray-300 focus:ring-red-500">
                                                        <span class="ml-2 text-sm text-gray-700">Absent</span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <input type="text" 
                                                       name="presences[{{ $index }}][remarque]" 
                                                       placeholder="Motif..."
                                                       value="{{ $existingRemarks[$athlete->id] ?? '' }}"
                                                       class="w-full text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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

            <!-- Statistiques du jour -->
            <div class="lg:col-span-1 space-y-6">
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

                <x-card title="Taux de presence">
                    @php
                        $totalPointes = count($existingPresences);
                        $totalPresents = collect($existingPresences)->filter(fn($p) => $p === true)->count();
                        $tauxJour = $totalPointes > 0 ? round(($totalPresents / $totalPointes) * 100) : 0;
                    @endphp
                    <div class="text-center">
                        <div class="text-4xl font-bold {{ $tauxJour >= 80 ? 'text-green-600' : ($tauxJour >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $tauxJour }}%
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Aujourd'hui</p>
                        <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $tauxJour >= 80 ? 'bg-green-500' : ($tauxJour >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                 style="width: {{ $tauxJour }}%"></div>
                        </div>
                    </div>
                </x-card>

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Selectionnez une discipline</h3>
                <p class="mt-2 text-sm text-gray-500">Choisissez une discipline pour commencer le pointage quotidien.</p>
            </div>
        </x-card>
    @endif

    @push('scripts')
    <script>
        function setAllPresent() {
            document.querySelectorAll('input[type="radio"][value="1"]').forEach(radio => {
                radio.checked = true;
            });
        }

        function setAllAbsent() {
            document.querySelectorAll('input[type="radio"][value="0"]').forEach(radio => {
                radio.checked = true;
            });
        }
    </script>
    @endpush
@endsection
