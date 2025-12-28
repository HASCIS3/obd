@section('title', 'Certificats Expirant Bientôt')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Certificats Expirant Bientôt</h2>
                <p class="mt-1 text-sm text-gray-500">Certificats expirant dans les 30 prochains jours</p>
            </div>
            <x-button href="{{ route('certificats-medicaux.index') }}" variant="ghost">
                Retour
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($certificats->count() > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>{{ $certificats->count() }} certificat(s)</strong> expirent dans les 30 prochains jours.
                        </p>
                    </div>
                </div>
            </div>

            <x-card :padding="false">
                <x-table>
                    <x-slot name="head">
                        <tr>
                            <x-th>Athlète</x-th>
                            <x-th>Type</x-th>
                            <x-th>Médecin</x-th>
                            <x-th>Expiration</x-th>
                            <x-th>Jours restants</x-th>
                            <x-th class="text-right">Actions</x-th>
                        </tr>
                    </x-slot>

                    @foreach($certificats as $certificat)
                        <tr class="hover:bg-gray-50 {{ $certificat->jours_restants <= 7 ? 'bg-red-50' : ($certificat->jours_restants <= 15 ? 'bg-yellow-50' : '') }}">
                            <x-td>
                                <a href="{{ route('athletes.show', $certificat->athlete) }}" class="text-primary-600 hover:underline">
                                    {{ $certificat->athlete->nom_complet }}
                                </a>
                            </x-td>
                            <x-td>{{ $certificat->type_label }}</x-td>
                            <x-td>{{ $certificat->medecin }}</x-td>
                            <x-td>{{ $certificat->date_expiration->format('d/m/Y') }}</x-td>
                            <x-td>
                                <span class="font-bold {{ $certificat->jours_restants <= 7 ? 'text-red-600' : ($certificat->jours_restants <= 15 ? 'text-yellow-600' : 'text-gray-900') }}">
                                    {{ $certificat->jours_restants }} jour(s)
                                </span>
                            </x-td>
                            <x-td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('certificats-medicaux.show', $certificat) }}" class="text-gray-600 hover:text-primary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('certificats-medicaux.create', ['athlete_id' => $certificat->athlete_id]) }}" class="text-green-600 hover:text-green-800" title="Nouveau certificat">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </a>
                                </div>
                            </x-td>
                        </tr>
                    @endforeach
                </x-table>
            </x-card>
        @else
            <x-card>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tous les certificats sont à jour</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucun certificat n'expire dans les 30 prochains jours.</p>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
