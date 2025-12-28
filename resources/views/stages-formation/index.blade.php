@section('title', 'Stages de Formation')

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Stages de Formation</h2>
                <p class="mt-1 text-sm text-gray-500">Gestion des formations pour entraîneurs et encadreurs</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('stages-formation.create') }}" variant="primary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nouveau stage
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border p-4">
                <p class="text-sm text-gray-500">Total stages</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-4">
                <p class="text-sm text-blue-600">Planifiés</p>
                <p class="text-2xl font-bold text-blue-700">{{ $stats['planifies'] }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4">
                <p class="text-sm text-green-600">En cours</p>
                <p class="text-2xl font-bold text-green-700">{{ $stats['en_cours'] }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg shadow-sm border p-4">
                <p class="text-sm text-gray-600">Terminés</p>
                <p class="text-2xl font-bold text-gray-700">{{ $stats['termines'] }}</p>
            </div>
            <div class="bg-primary-50 rounded-lg shadow-sm border border-primary-200 p-4">
                <p class="text-sm text-primary-600">Diplômés</p>
                <p class="text-2xl font-bold text-primary-700">{{ $stats['total_diplomes'] }}</p>
            </div>
        </div>

        <!-- Filtres -->
        <x-card class="mb-6">
            <form method="GET" action="{{ route('stages-formation.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher..." 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="w-48">
                    <select name="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Tous les types</option>
                        @foreach(\App\Models\StageFormation::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-48">
                    <select name="statut" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\StageFormation::STATUTS as $key => $label)
                            <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-48">
                    <select name="discipline_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Toutes disciplines</option>
                        @foreach($disciplines as $discipline)
                            <option value="{{ $discipline->id }}" {{ request('discipline_id') == $discipline->id ? 'selected' : '' }}>{{ $discipline->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <x-button type="submit" variant="secondary">Filtrer</x-button>
                <a href="{{ route('stages-formation.index') }}" class="inline-flex items-center px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                    Réinitialiser
                </a>
            </form>
        </x-card>

        <!-- Liste des stages -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            @if($stages->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Stage</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Lieu</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Inscrits</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Statut</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($stages as $stage)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div>
                                            <a href="{{ route('stages-formation.show', $stage) }}" class="font-medium text-gray-900 hover:text-primary-600">
                                                {{ $stage->titre }}
                                            </a>
                                            <p class="text-xs text-gray-500">{{ $stage->code }}</p>
                                            @if($stage->discipline)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                                    {{ $stage->discipline->nom }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($stage->type === 'formation_formateurs') bg-purple-100 text-purple-800
                                            @elseif($stage->type === 'recyclage') bg-blue-100 text-blue-800
                                            @elseif($stage->type === 'specialisation') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $stage->type_libelle }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <p class="text-gray-900">{{ $stage->date_debut->format('d/m/Y') }}</p>
                                        <p class="text-gray-500">au {{ $stage->date_fin->format('d/m/Y') }}</p>
                                        <p class="text-xs text-gray-400">{{ $stage->duree_jours }} jours</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $stage->lieu }}
                                        <p class="text-xs text-gray-400">{{ $stage->organisme }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-lg font-semibold {{ $stage->est_complet ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $stage->nombre_inscrits }}
                                        </span>
                                        <span class="text-gray-400">/{{ $stage->places_disponibles }}</span>
                                        @if($stage->nombre_diplomes > 0)
                                            <p class="text-xs text-green-600">{{ $stage->nombre_diplomes }} diplômé(s)</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($stage->statut === 'planifie') bg-blue-100 text-blue-800
                                            @elseif($stage->statut === 'en_cours') bg-green-100 text-green-800
                                            @elseif($stage->statut === 'termine') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $stage->statut_libelle }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('stages-formation.show', $stage) }}" class="text-gray-400 hover:text-primary-600" title="Voir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('stages-formation.inscriptions', $stage) }}" class="text-gray-400 hover:text-green-600" title="Inscriptions">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('stages-formation.edit', $stage) }}" class="text-gray-400 hover:text-blue-600" title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t">
                    {{ $stages->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun stage de formation</h3>
                    <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau stage.</p>
                    <div class="mt-6">
                        <x-button href="{{ route('stages-formation.create') }}" variant="primary">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Créer un stage
                        </x-button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
