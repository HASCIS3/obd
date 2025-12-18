@section('title', 'Rapport Parent - ' . $athlete->nom_complet)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">📄 Rapport pour les parents</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $athlete->nom_complet }} - {{ now()->format('F Y') }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimer / PDF
                </button>
                <x-button href="{{ route('suivis-scolaires.rapport-athlete', $athlete->id) }}" variant="ghost">
                    ← Retour
                </x-button>
            </div>
        </div>
    </x-slot>

    <!-- Zone imprimable -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 print:px-0 print:max-w-full">
        <!-- En-tete du rapport -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6 print:shadow-none print:border">
            <div class="bg-primary-700 text-white p-6 print:bg-primary-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img src="{{ asset('images/logo_obd.jpg') }}" alt="OBD" class="w-16 h-16 rounded-full border-2 border-white mr-4">
                        <div>
                            <h1 class="text-2xl font-bold">Olympiade Baco-Djicoroni</h1>
                            <p class="text-primary-200">Centre Sportif d'Excellence</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-primary-200">Rapport genere le</p>
                        <p class="font-semibold">{{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-center mb-6">
                    @if($athlete->photo_url)
                        <img src="{{ $athlete->photo_url }}" class="w-20 h-20 rounded-full object-cover mr-4 border-2 border-gray-200">
                    @else
                        <div class="w-20 h-20 rounded-full bg-primary-100 flex items-center justify-center mr-4">
                            <span class="text-primary-600 font-bold text-3xl">{{ substr($athlete->prenom, 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $athlete->nom_complet }}</h2>
                        <p class="text-gray-500">{{ $athlete->age }} ans • {{ $athlete->categorie_age }}</p>
                        <p class="text-gray-500">Disciplines : {{ $athlete->disciplinesActives->pluck('nom')->join(', ') ?: 'Aucune' }}</p>
                    </div>
                </div>

                <!-- Message personnalise -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <p class="text-blue-800">
                        <strong>Cher(e) {{ $athlete->nom_tuteur ?: 'Parent' }},</strong><br>
                        Voici le rapport mensuel de suivi de votre enfant {{ $athlete->prenom }} au sein de notre centre sportif. 
                        Ce rapport presente l'equilibre entre sa pratique sportive et ses resultats scolaires.
                    </p>
                </div>

                <!-- Resume en un coup d'oeil -->
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">📊 Resume du mois</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold {{ $analyse['taux_presence'] >= 80 ? 'text-green-600' : ($analyse['taux_presence'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($analyse['taux_presence'], 0) }}%
                        </p>
                        <p class="text-sm text-gray-500">Assiduite</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold text-primary-600">{{ $analyse['nb_seances'] }}</p>
                        <p class="text-sm text-gray-500">Seances</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold {{ $analyse['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $analyse['moyenne'] ? number_format($analyse['moyenne'], 1) : 'N/A' }}
                        </p>
                        <p class="text-sm text-gray-500">Moyenne /20</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold">
                            @if($analyse['tendance'] === 'hausse') 📈
                            @elseif($analyse['tendance'] === 'baisse') 📉
                            @else ➡️
                            @endif
                        </p>
                        <p class="text-sm text-gray-500">Tendance</p>
                    </div>
                </div>

                <!-- Evaluation de l'equilibre -->
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">⚖️ Equilibre Sport / Etudes</h3>
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Niveau d'equilibre</span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $analyse['equilibre_color'] === 'success' ? 'bg-green-100 text-green-800' : ($analyse['equilibre_color'] === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $analyse['equilibre'] }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="h-4 rounded-full {{ $analyse['equilibre_color'] === 'success' ? 'bg-green-500' : ($analyse['equilibre_color'] === 'warning' ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $analyse['equilibre_score'] }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">{{ $analyse['equilibre_description'] }}</p>
                </div>

                <!-- Observations du coach -->
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">👨‍🏫 Observations et conseils</h3>
                <div class="space-y-3 mb-6">
                    @foreach($observations as $obs)
                    <div class="flex items-start">
                        <span class="text-xl mr-3">{{ $obs['icon'] }}</span>
                        <div>
                            <p class="font-medium text-gray-900">{{ $obs['titre'] }}</p>
                            <p class="text-sm text-gray-600">{{ $obs['message'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Calendrier des presences -->
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">📅 Calendrier des presences</h3>
                <div class="mb-6">
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach($presences as $presence)
                        <div class="w-6 h-6 rounded text-xs flex items-center justify-center {{ $presence->present ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}" title="{{ $presence->date->format('d/m/Y') }}">
                            {{ $presence->date->format('d') }}
                        </div>
                        @endforeach
                    </div>
                    <div class="flex gap-4 text-xs text-gray-500">
                        <span class="flex items-center"><span class="w-3 h-3 bg-green-500 rounded mr-1"></span> Present ({{ $presences->where('present', true)->count() }})</span>
                        <span class="flex items-center"><span class="w-3 h-3 bg-red-500 rounded mr-1"></span> Absent ({{ $presences->where('present', false)->count() }})</span>
                    </div>
                </div>

                <!-- Recommandations pour les parents -->
                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">💡 Recommandations</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <ul class="space-y-2 text-sm text-yellow-800">
                        @foreach($recommandationsParent as $reco)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $reco }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Prochain rendez-vous -->
                <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                    <h4 class="font-semibold text-primary-800 mb-2">📞 Contact</h4>
                    <p class="text-sm text-primary-700">
                        Pour toute question concernant ce rapport ou le suivi de votre enfant, 
                        n'hesitez pas a nous contacter ou a venir nous rencontrer au centre.
                    </p>
                    <p class="text-sm text-primary-700 mt-2">
                        <strong>Horaires d'entrainement :</strong> Selon le planning de la discipline
                    </p>
                </div>
            </div>

            <!-- Pied de page -->
            <div class="bg-gray-100 px-6 py-4 text-center text-sm text-gray-500">
                <p>Olympiade Baco-Djicoroni - Centre Sportif d'Excellence</p>
                <p>Ce rapport est genere automatiquement. Pour plus d'informations, contactez le centre.</p>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            body { background: white !important; }
            .print\:shadow-none { box-shadow: none !important; }
            .print\:border { border: 1px solid #e5e7eb !important; }
            .print\:px-0 { padding-left: 0 !important; padding-right: 0 !important; }
            .print\:max-w-full { max-width: 100% !important; }
            nav, header button, .no-print { display: none !important; }
        }
    </style>
    @endpush
</x-app-layout>
