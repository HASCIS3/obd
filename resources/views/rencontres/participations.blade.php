@section('title', 'Gestion des joueurs')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('rencontres.show', $rencontre) }}" class="text-gray-400 hover:text-gray-600 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestion des joueurs</h2>
                <p class="mt-1 text-sm text-gray-500">
                    OBD vs {{ $rencontre->adversaire }} - {{ $rencontre->date_match->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="{{ route('rencontres.participations.store', $rencontre) }}" method="POST" id="participationsForm">
            @csrf

            <!-- Info match -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-lg p-4 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm opacity-75">{{ $rencontre->discipline->nom }}</span>
                        <h3 class="text-xl font-bold">OBD vs {{ $rencontre->adversaire }}</h3>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold">{{ $rencontre->score_formate }}</div>
                        <span class="text-sm opacity-75">{{ $rencontre->resultat_libelle }}</span>
                    </div>
                </div>
            </div>

            <!-- Liste des athlètes disponibles -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Selectionner les joueurs</h3>
                    <div class="text-sm text-gray-500">
                        <span id="selectedCount">0</span> joueur(s) selectionne(s)
                    </div>
                </div>

                @if($athletes->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joueur</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Titulaire</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Minutes</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Points</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Passes</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Rebonds</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">Fautes</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Note /10</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($athletes as $index => $athlete)
                                    @php
                                        $participation = $rencontre->participations->firstWhere('athlete_id', $athlete->id);
                                        $isSelected = in_array($athlete->id, $participantsIds);
                                    @endphp
                                    <tr class="athlete-row {{ $isSelected ? 'bg-green-50' : '' }}" data-athlete-id="{{ $athlete->id }}">
                                        <td class="px-4 py-3">
                                            <input type="checkbox" 
                                                   name="selected_athletes[]" 
                                                   value="{{ $athlete->id }}" 
                                                   class="athlete-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                                   {{ $isSelected ? 'checked' : '' }}
                                                   data-index="{{ $index }}">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-600">{{ substr($athlete->prenom, 0, 1) }}{{ substr($athlete->nom, 0, 1) }}</span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="font-medium text-gray-900">{{ $athlete->nom_complet }}</div>
                                                    <div class="text-sm text-gray-500">{{ $athlete->categorie }}</div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="participations[{{ $index }}][athlete_id]" value="{{ $athlete->id }}" class="participation-field" disabled>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" 
                                                   name="participations[{{ $index }}][titulaire]" 
                                                   value="1"
                                                   class="participation-field rounded border-gray-300 text-green-600 focus:ring-green-500"
                                                   {{ $participation?->titulaire ? 'checked' : '' }}
                                                   disabled>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                   name="participations[{{ $index }}][minutes_jouees]" 
                                                   value="{{ $participation?->minutes_jouees }}"
                                                   class="participation-field w-16 text-center text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                                   min="0" max="120"
                                                   disabled>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                   name="participations[{{ $index }}][points_marques]" 
                                                   value="{{ $participation?->points_marques }}"
                                                   class="participation-field w-16 text-center text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                                   min="0"
                                                   disabled>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                   name="participations[{{ $index }}][passes_decisives]" 
                                                   value="{{ $participation?->passes_decisives }}"
                                                   class="participation-field w-16 text-center text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                                   min="0"
                                                   disabled>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                   name="participations[{{ $index }}][rebonds]" 
                                                   value="{{ $participation?->rebonds }}"
                                                   class="participation-field w-16 text-center text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                                   min="0"
                                                   disabled>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                   name="participations[{{ $index }}][fautes]" 
                                                   value="{{ $participation?->fautes }}"
                                                   class="participation-field w-16 text-center text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                                   min="0" max="5"
                                                   disabled>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                   name="participations[{{ $index }}][note_performance]" 
                                                   value="{{ $participation?->note_performance }}"
                                                   class="participation-field w-16 text-center text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
                                                   min="0" max="10" step="0.5"
                                                   disabled>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Boutons -->
                    <div class="mt-6 flex justify-end gap-4">
                        <x-button type="button" variant="secondary" onclick="window.history.back()">
                            Annuler
                        </x-button>
                        <x-button type="submit" variant="primary">
                            Enregistrer les participations
                        </x-button>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun athlete disponible</h3>
                        <p class="mt-1 text-sm text-gray-500">Il n'y a pas d'athletes inscrits dans cette discipline.</p>
                    </div>
                @endif
            </x-card>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const athleteCheckboxes = document.querySelectorAll('.athlete-checkbox');
            const selectedCountSpan = document.getElementById('selectedCount');

            function updateSelectedCount() {
                const count = document.querySelectorAll('.athlete-checkbox:checked').length;
                selectedCountSpan.textContent = count;
            }

            function toggleRowFields(checkbox) {
                const row = checkbox.closest('tr');
                const fields = row.querySelectorAll('.participation-field');
                
                if (checkbox.checked) {
                    row.classList.add('bg-green-50');
                    fields.forEach(field => field.disabled = false);
                } else {
                    row.classList.remove('bg-green-50');
                    fields.forEach(field => field.disabled = true);
                }
            }

            // Select all
            selectAllCheckbox.addEventListener('change', function() {
                athleteCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                    toggleRowFields(checkbox);
                });
                updateSelectedCount();
            });

            // Individual checkbox
            athleteCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    toggleRowFields(this);
                    updateSelectedCount();
                    
                    // Update select all checkbox
                    const allChecked = document.querySelectorAll('.athlete-checkbox:checked').length === athleteCheckboxes.length;
                    selectAllCheckbox.checked = allChecked;
                });
            });

            // Initialize
            athleteCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    toggleRowFields(checkbox);
                }
            });
            updateSelectedCount();
        });
    </script>
    @endpush
</x-app-layout>

