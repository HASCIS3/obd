@extends('portail-parent.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mon Profil</h1>

    <form action="{{ route('parent.profil.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Informations personnelles -->
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations personnelles</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                        <input type="text" value="{{ auth()->user()->name }}" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-500">
                        <p class="text-xs text-gray-500 mt-1">Contactez l'administration pour modifier votre nom</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-500">
                    </div>

                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">Telephone principal</label>
                        <input type="tel" name="telephone" id="telephone" value="{{ old('telephone', $parent->telephone) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                        @error('telephone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telephone_secondaire" class="block text-sm font-medium text-gray-700 mb-1">Telephone secondaire</label>
                        <input type="tel" name="telephone_secondaire" id="telephone_secondaire" value="{{ old('telephone_secondaire', $parent->telephone_secondaire) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $parent->adresse) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>

            <!-- Preferences de notification -->
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Preferences de notification</h2>
                
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="hidden" name="recevoir_notifications" value="0">
                        <input type="checkbox" name="recevoir_notifications" value="1" {{ $parent->recevoir_notifications ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Recevoir les notifications par email</span>
                    </label>

                    <label class="flex items-center">
                        <input type="hidden" name="recevoir_sms" value="0">
                        <input type="checkbox" name="recevoir_sms" value="1" {{ $parent->recevoir_sms ? 'checked' : '' }} class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Recevoir les notifications par SMS</span>
                    </label>
                </div>
            </div>

            <!-- Bouton de sauvegarde -->
            <div class="p-4 bg-gray-50">
                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 font-medium">
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>

    <!-- Informations sur les enfants lies -->
    <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Enfants lies a ce compte</h2>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($parent->athletes as $enfant)
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center">
                        @if($enfant->photo)
                            <img src="{{ asset('storage/' . $enfant->photo) }}" alt="{{ $enfant->nom_complet }}" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-bold">{{ substr($enfant->prenom, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="ml-3">
                            <div class="font-medium text-gray-900">{{ $enfant->nom_complet }}</div>
                            <div class="text-sm text-gray-500">{{ $enfant->disciplines->first()->nom ?? 'Non assigne' }}</div>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $enfant->pivot->lien ?? 'Tuteur' }}</span>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    Aucun enfant lie a ce compte
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
