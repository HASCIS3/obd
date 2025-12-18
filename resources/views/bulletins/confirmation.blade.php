<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OBD - Bulletin soumis</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo_obd.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-8">
    <div class="max-w-lg mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden text-center">
            <div class="bg-green-600 text-white px-6 py-8">
                <div class="w-20 h-20 bg-white rounded-full mx-auto flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold">Bulletin soumis avec succès !</h1>
            </div>

            <div class="p-6">
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Récapitulatif</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p><strong>Athlète :</strong> {{ $suivi->athlete->nom_complet }}</p>
                        <p><strong>Établissement :</strong> {{ $suivi->etablissement }}</p>
                        <p><strong>Classe :</strong> {{ $suivi->classe }}</p>
                        <p><strong>Période :</strong> {{ $suivi->annee_scolaire }}</p>
                        <p><strong>Moyenne :</strong> <span class="{{ $suivi->moyenne_generale >= 10 ? 'text-green-600' : 'text-red-600' }} font-semibold">{{ number_format($suivi->moyenne_generale, 2) }}/20</span></p>
                        @if($suivi->rang)
                        <p><strong>Rang :</strong> {{ $suivi->rang }}e</p>
                        @endif
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-blue-800 text-sm">
                        <strong>Merci !</strong> Les informations ont été transmises au centre sportif OBD. 
                        Un rapport sera envoyé aux parents de l'athlète.
                    </p>
                </div>

                <p class="text-gray-500 text-sm">
                    Vous pouvez fermer cette page.
                </p>
            </div>

            <div class="bg-gray-50 px-6 py-4 text-sm text-gray-500">
                <img src="{{ asset('images/logo_obd.jpg') }}" alt="OBD" class="w-10 h-10 rounded-full mx-auto mb-2">
                <p>Olympiade Baco-Djicoroni</p>
            </div>
        </div>
    </div>
</body>
</html>
