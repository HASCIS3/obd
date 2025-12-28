@section('title', 'Calendrier')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Calendrier</h2>
                <p class="mt-1 text-sm text-gray-500">Événements et planning</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <x-button href="{{ route('calendrier.a-venir') }}" variant="secondary">
                    À venir
                </x-button>
                <x-button onclick="openCreateModal()" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouvel événement
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtres -->
        <x-card class="mb-6">
            <div class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <x-select 
                        id="filter-type"
                        :options="[
                            ['id' => 'entrainement', 'name' => 'Entraînement'],
                            ['id' => 'competition', 'name' => 'Compétition'],
                            ['id' => 'reunion', 'name' => 'Réunion'],
                            ['id' => 'stage', 'name' => 'Stage'],
                            ['id' => 'autre', 'name' => 'Autre'],
                        ]"
                        placeholder="Tous les types"
                    />
                </div>
                <div class="flex-1 min-w-[200px]">
                    <x-select 
                        id="filter-discipline"
                        :options="$disciplines->map(fn($d) => ['id' => $d->id, 'name' => $d->nom])->toArray()"
                        placeholder="Toutes les disciplines"
                    />
                </div>
            </div>
        </x-card>

        <!-- Légende -->
        <div class="flex flex-wrap gap-4 mb-4">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #14532d;"></span>
                <span class="text-sm">Entraînement</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #dc2626;"></span>
                <span class="text-sm">Compétition</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #2563eb;"></span>
                <span class="text-sm">Réunion</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background-color: #7c3aed;"></span>
                <span class="text-sm">Stage</span>
            </div>
        </div>

        <!-- Calendrier -->
        <x-card>
            <div id="calendar"></div>
        </x-card>
    </div>

    <!-- Modal Création -->
    <div id="createModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeCreateModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Nouvel événement</h3>
                <form id="createForm" method="POST" action="{{ route('calendrier.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Titre *</label>
                            <input type="text" name="titre" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type *</label>
                            <select name="type" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="entrainement">Entraînement</option>
                                <option value="competition">Compétition</option>
                                <option value="reunion">Réunion</option>
                                <option value="stage">Stage</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Discipline</label>
                            <select name="discipline_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="">-- Aucune --</option>
                                @foreach($disciplines as $discipline)
                                    <option value="{{ $discipline->id }}">{{ $discipline->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date début *</label>
                                <input type="date" name="date_debut" id="modal-date-debut" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date fin</label>
                                <input type="date" name="date_fin" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Heure début</label>
                                <input type="time" name="heure_debut" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Heure fin</label>
                                <input type="time" name="heure_fin" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lieu</label>
                            <input type="text" name="lieu" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="toute_journee" value="1" id="toute_journee" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="toute_journee" class="ml-2 text-sm text-gray-700">Toute la journée</label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Détail -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDetailModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 id="detail-titre" class="text-lg font-semibold mb-4"></h3>
                <div class="space-y-3">
                    <p><strong>Type:</strong> <span id="detail-type"></span></p>
                    <p><strong>Date:</strong> <span id="detail-date"></span></p>
                    <p id="detail-lieu-row"><strong>Lieu:</strong> <span id="detail-lieu"></span></p>
                    <p id="detail-discipline-row"><strong>Discipline:</strong> <span id="detail-discipline"></span></p>
                    <p id="detail-description-row"><strong>Description:</strong> <span id="detail-description"></span></p>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeDetailModal()" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Fermer</button>
                    <form id="deleteForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Supprimer cet événement ?')" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        let calendar;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    list: 'Liste'
                },
                events: function(info, successCallback, failureCallback) {
                    const type = document.getElementById('filter-type').value;
                    const discipline = document.getElementById('filter-discipline').value;
                    
                    let url = '{{ route("calendrier.events") }}?start=' + info.startStr + '&end=' + info.endStr;
                    if (type) url += '&type=' + type;
                    if (discipline) url += '&discipline_id=' + discipline;
                    
                    fetch(url)
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(error => failureCallback(error));
                },
                dateClick: function(info) {
                    document.getElementById('modal-date-debut').value = info.dateStr;
                    openCreateModal();
                },
                eventClick: function(info) {
                    showEventDetail(info.event);
                }
            });
            calendar.render();

            // Filtres
            document.getElementById('filter-type').addEventListener('change', () => calendar.refetchEvents());
            document.getElementById('filter-discipline').addEventListener('change', () => calendar.refetchEvents());
        });

        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function showEventDetail(event) {
            document.getElementById('detail-titre').textContent = event.title;
            document.getElementById('detail-type').textContent = event.extendedProps.type_label;
            document.getElementById('detail-date').textContent = event.start.toLocaleDateString('fr-FR');
            
            const lieu = event.extendedProps.lieu;
            document.getElementById('detail-lieu').textContent = lieu || '';
            document.getElementById('detail-lieu-row').style.display = lieu ? 'block' : 'none';
            
            const discipline = event.extendedProps.discipline;
            document.getElementById('detail-discipline').textContent = discipline || '';
            document.getElementById('detail-discipline-row').style.display = discipline ? 'block' : 'none';
            
            const description = event.extendedProps.description;
            document.getElementById('detail-description').textContent = description || '';
            document.getElementById('detail-description-row').style.display = description ? 'block' : 'none';
            
            document.getElementById('deleteForm').action = '/calendrier/evenements/' + event.id;
            document.getElementById('detailModal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
