@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('hospitalisations.show', $hospitalisation->id) }}"
                class="mr-4 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Modifier l'Hospitalisation #{{ $hospitalisation->id }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Modifiez les informations de l'hospitalisation</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('hospitalisations.update', $hospitalisation->id) }}"
        class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
        @csrf
        @method('PUT')

        <!-- Informations principales -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Informations Principales
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Patient *
                    </label>
                    <select name="gestion_patient_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        required>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" @if($hospitalisation->gestion_patient_id == $patient->id)
                            selected @endif>{{ $patient->nom }} {{ $patient->prenom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        Médecin *
                    </label>
                    <select name="medecin_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        required>
                        @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}" @if($hospitalisation->medecin_id == $medecin->id) selected
                            @endif>{{ $medecin->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Service *
                    </label>
                    <select name="service_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        required>
                        @foreach($services as $service)
                        <option value="{{ $service->id }}" @if($hospitalisation->service_id == $service->id) selected
                            @endif>{{ $service->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Statut *
                    </label>
                    <select name="statut"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        required>
                        <option value="en cours" @if($hospitalisation->statut == 'en cours') selected @endif>En cours
                        </option>
                        <option value="terminé" @if($hospitalisation->statut == 'terminé') selected @endif>Terminé
                        </option>
                        <option value="annulé" @if($hospitalisation->statut == 'annulé') selected @endif>Annulé</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Dates et motif -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                Période d'Hospitalisation
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Date d'entrée *
                    </label>
                    <input type="date" name="date_entree" value="{{ $hospitalisation->date_entree }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        required>
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Date de sortie
                    </label>
                    <input type="date" name="date_sortie" value="{{ $hospitalisation->date_sortie }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Motif
                    </label>
                    <input type="text" name="motif" value="{{ $hospitalisation->motif }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        placeholder="Motif de l'hospitalisation">
                </div>
            </div>
        </div>

        <!-- Logement et finances -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                </svg>
                Logement et Finances
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Chambre *
                    </label>
                    <select id="chambre-select" name="chambre_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        required>
                        <option value="" class="text-gray-500 dark:text-gray-400">Sélectionner une chambre</option>
                        @foreach($chambres as $chambre)
                        <option value="{{ $chambre->id }}"
                            class="text-gray-900 dark:text-white bg-white dark:bg-gray-900"
                            data-lits="{{ $chambre->lits->count() }}" @if($hospitalisation->lit &&
                            $hospitalisation->lit->chambre_id == $chambre->id) selected @endif>
                            {{ $chambre->nom_complet }} ({{ $chambre->lits->count() }} lits)
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 00-2-2H9a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                        </svg>
                        Lit *
                    </label>
                    <select id="lit-select" name="lit_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        required>
                        <option value="" class="text-gray-500 dark:text-gray-400">Sélectionner un lit</option>
                        @if($hospitalisation->lit)
                        <option value="{{ $hospitalisation->lit->id }}" selected>
                            @if($hospitalisation->lit->chambre)
                            {{ $hospitalisation->lit->chambre->nom }} - Lit {{ $hospitalisation->lit->numero }}
                            @else
                            Chambre supprimée - Lit {{ $hospitalisation->lit->numero }}
                            @endif
                        </option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                        Montant total
                    </label>
                    <input type="number" step="0.01" name="montant_total" value="{{ $hospitalisation->montant_total }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                        placeholder="0.00">
                </div>
            </div>
        </div>

        <!-- Observations -->
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Observations
            </h2>

            <div>
                <label class="block mb-2 font-medium text-gray-700 dark:text-gray-200">Observations médicales</label>
                <textarea name="observation" rows="4"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                    placeholder="Observations et notes médicales...">{{ $hospitalisation->observation }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-4">
            <a href="{{ route('hospitalisations.show', $hospitalisation->id) }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                Annuler
            </a>
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium flex items-center transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const chambreSelect = document.getElementById('chambre-select');
    const litSelect = document.getElementById('lit-select');

    // Données des lits préchargées depuis le serveur
    const litsParChambre = @json($litsParChambre);

    // Récupérer le lit actuel de l'hospitalisation
    const litActuelId = @json($hospitalisation->lit_id ?? null);

    function chargerLits(chambreId, litIdASelectionner = null) {
        // Vider le select des lits
        litSelect.innerHTML = '<option value="" class="text-gray-500 dark:text-gray-400">Sélectionner un lit</option>';

        if (chambreId && litsParChambre[chambreId]) {
            litsParChambre[chambreId].forEach(lit => {
                const option = document.createElement('option');
                option.value = lit.id;
                option.textContent = lit.nom_complet;
                option.className = 'text-gray-900 dark:text-white bg-white dark:bg-gray-900';

                // Sélectionner le lit si c'est celui actuel ou celui spécifié
                if (litIdASelectionner && lit.id == litIdASelectionner) {
                    option.selected = true;
                }

                litSelect.appendChild(option);
            });
        }
    }

    // Écouter les changements de chambre
    chambreSelect.addEventListener('change', function() {
        chargerLits(this.value);
    });

    // Charger les lits de la chambre actuelle au chargement de la page
    if (chambreSelect.value) {
        chargerLits(chambreSelect.value, litActuelId);
    }
});
</script>
@endsection
