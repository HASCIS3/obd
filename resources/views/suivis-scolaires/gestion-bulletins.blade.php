@section('title', 'Gestion des bulletins')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">📬 Gestion des bulletins</h2>
                <p class="mt-1 text-sm text-gray-500">Liens de soumission pour les ecoles et rapports pour les parents</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('suivis-scolaires.dashboard') }}" variant="ghost">
                    ← Retour au dashboard
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <!-- Instructions -->
        <x-card class="mb-6 bg-blue-50 border-blue-200">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">📋 Comment ca marche ?</h3>
            <div class="text-sm text-blue-700 space-y-2">
                <p><strong>1. Generer un lien</strong> : Cliquez sur "Generer lien" pour creer un lien unique pour chaque athlete.</p>
                <p><strong>2. Partager avec l'ecole</strong> : Envoyez ce lien a l'etablissement scolaire de l'athlete.</p>
                <p><strong>3. L'ecole soumet le bulletin</strong> : L'enseignant remplit le formulaire avec la photo du bulletin.</p>
                <p><strong>4. Envoyer le rapport</strong> : Telechargez ou envoyez le rapport aux parents.</p>
            </div>
        </x-card>

        <!-- Liste des athletes -->
        <x-card :padding="false">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">🏃 Athletes et liens de soumission</h3>
            </div>
            
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-th>Athlete</x-th>
                        <x-th>Tuteur</x-th>
                        <x-th>Dernier bulletin</x-th>
                        <x-th>Lien ecole</x-th>
                        <x-th class="text-right">Actions</x-th>
                    </tr>
                </x-slot>

                @forelse($athletes as $athlete)
                <tr class="hover:bg-gray-50">
                    <x-td>
                        <div class="flex items-center">
                            @if($athlete->photo_url)
                                <img src="{{ $athlete->photo_url }}" class="w-10 h-10 rounded-full object-cover mr-3">
                            @else
                                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center mr-3">
                                    <span class="text-primary-600 font-semibold">{{ substr($athlete->prenom, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $athlete->nom_complet }}</p>
                                <p class="text-xs text-gray-500">{{ $athlete->categorie_age }}</p>
                            </div>
                        </div>
                    </x-td>
                    <x-td>
                        @if($athlete->nom_tuteur)
                            <p class="text-sm">{{ $athlete->nom_tuteur }}</p>
                            @if($athlete->telephone_tuteur)
                                <p class="text-xs text-gray-500">{{ $athlete->telephone_tuteur }}</p>
                            @endif
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </x-td>
                    <x-td>
                        @php $dernierSuivi = $athlete->suivisScolaires->sortByDesc('created_at')->first(); @endphp
                        @if($dernierSuivi)
                            <div>
                                <span class="font-medium {{ $dernierSuivi->estPassable() ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($dernierSuivi->moyenne_generale, 2) }}/20
                                </span>
                                <p class="text-xs text-gray-500">{{ $dernierSuivi->annee_scolaire }}</p>
                            </div>
                        @else
                            <span class="text-gray-400">Aucun</span>
                        @endif
                    </x-td>
                    <x-td>
                        @if($athlete->bulletin_token)
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Actif
                                </span>
                                <button onclick="copyLink('{{ route('bulletin.formulaire', $athlete->bulletin_token) }}')" 
                                    class="text-primary-600 hover:text-primary-800 text-xs underline">
                                    Copier
                                </button>
                            </div>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                Non genere
                            </span>
                        @endif
                    </x-td>
                    <x-td class="text-right">
                        <div class="flex justify-end gap-2">
                            @if($athlete->bulletin_token)
                                <!-- Voir le lien -->
                                <button onclick="showLink('{{ $athlete->nom_complet }}', '{{ route('bulletin.formulaire', $athlete->bulletin_token) }}')"
                                    class="text-blue-600 hover:text-blue-800" title="Voir le lien">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                    </svg>
                                </button>
                                <!-- Regenerer -->
                                <form action="{{ route('bulletin.regenerer-lien', $athlete) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Regenerer le lien">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <!-- Generer -->
                                <form action="{{ route('bulletin.generer-lien', $athlete) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Generer un lien">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Telecharger PDF -->
                            <a href="{{ route('bulletin.rapport-pdf', $athlete) }}" class="text-purple-600 hover:text-purple-800" title="Telecharger rapport PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </a>
                            
                            <!-- Voir rapport -->
                            <a href="{{ route('suivis-scolaires.rapport-parent', $athlete) }}" class="text-primary-600 hover:text-primary-800" title="Voir rapport">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </div>
                    </x-td>
                </tr>
                @empty
                <tr>
                    <x-td colspan="5" class="text-center py-8 text-gray-500">
                        Aucun athlete actif
                    </x-td>
                </tr>
                @endforelse
            </x-table>
        </x-card>
    </div>

    <!-- Modal pour afficher le lien -->
    <div id="linkModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 overflow-hidden">
            <div class="bg-primary-600 text-white px-6 py-4">
                <h3 class="text-lg font-semibold">🔗 Lien de soumission</h3>
                <p class="text-primary-200 text-sm" id="modalAthleteName"></p>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 mb-4">Partagez ce lien avec l'etablissement scolaire :</p>
                <div class="bg-gray-100 p-3 rounded-lg break-all text-sm font-mono" id="modalLink"></div>
                <div class="mt-4 flex gap-2">
                    <button onclick="copyModalLink()" class="flex-1 bg-primary-600 text-white py-2 px-4 rounded-lg hover:bg-primary-700">
                        📋 Copier le lien
                    </button>
                    <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentLink = '';

        function showLink(athleteName, link) {
            currentLink = link;
            document.getElementById('modalAthleteName').textContent = athleteName;
            document.getElementById('modalLink').textContent = link;
            document.getElementById('linkModal').classList.remove('hidden');
            document.getElementById('linkModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('linkModal').classList.add('hidden');
            document.getElementById('linkModal').classList.remove('flex');
        }

        function copyLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                alert('Lien copie !');
            });
        }

        function copyModalLink() {
            navigator.clipboard.writeText(currentLink).then(() => {
                alert('Lien copie !');
                closeModal();
            });
        }

        // Fermer le modal en cliquant en dehors
        document.getElementById('linkModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
    @endpush
</x-app-layout>
