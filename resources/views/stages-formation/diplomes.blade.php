@section('title', 'Diplômés - ' . $stageFormation->titre)

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Diplômés</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $stageFormation->titre }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <x-button href="{{ route('stages-formation.show', $stageFormation) }}" variant="secondary">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour au stage
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Résumé -->
        <div class="bg-green-50 rounded-xl shadow-sm border border-green-200 p-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="bg-green-100 rounded-full p-4">
                    <span class="text-4xl">🎓</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-green-800">{{ $diplomes->count() }} diplômé(s)</h3>
                    <p class="text-green-600">{{ $stageFormation->type_certification_libelle }} - {{ $stageFormation->intitule_certification ?? $stageFormation->titre }}</p>
                </div>
            </div>
        </div>

        <!-- Liste des diplômés -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            @if($diplomes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">N°</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Diplômé</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">N° Certificat</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Note</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date délivrance</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($diplomes as $index => $diplome)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $diplome->nom_complet }}</p>
                                            <p class="text-xs text-gray-500">{{ $diplome->structure ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm text-primary-600">{{ $diplome->numero_certificat }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($diplome->note_finale)
                                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-sm font-bold 
                                                {{ $diplome->note_finale >= 14 ? 'bg-green-100 text-green-700' : ($diplome->note_finale >= 12 ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ $diplome->note_finale }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $diplome->date_delivrance?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('inscriptions.certificat-pdf', $diplome) }}" 
                                           class="inline-flex items-center px-3 py-1 bg-primary-100 text-primary-700 text-sm font-medium rounded-lg hover:bg-primary-200 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Télécharger
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun diplômé</h3>
                    <p class="mt-1 text-sm text-gray-500">Les participants diplômés apparaîtront ici.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
