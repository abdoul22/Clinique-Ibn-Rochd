@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Modifier l'examen #{{ $caisse->numero_entre }}</h1>
        <a href="{{ route('caisses.index') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
        </a>
    </div>

    <form method="POST" action="{{ route(auth()->user()->role->name . '.caisses.update', $caisse) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Colonne de gauche -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Numéro d'entrée
                        *</label>
                    <input type="number" name="numero_entre" id="numero_entre" value="{{ $caisse->numero_entre }}"
                        required min="1"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    <small class="text-gray-500 dark:text-gray-400">Se met à jour automatiquement lors du changement de
                        médecin</small>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Patient *</label>
                    <select name="gestion_patient_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ $caisse->gestion_patient_id == $patient->id ? 'selected' :
                            '' }}>
                            {{ $patient->nom }} {{ $patient->prenom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Médecin *</label>
                    <select name="medecin_id" id="medecin_id" required onchange="updateNumeroEntree()"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}" {{ $caisse->medecin_id == $medecin->id ? 'selected' : '' }}>
                            Dr. {{ $medecin->prenom }} {{ $medecin->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Prescripteur</label>
                    <select name="prescripteur_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        <option value="">Aucun prescripteur</option>
                        @foreach($prescripteurs as $prescripteur)
                        <option value="{{ $prescripteur->id }}" {{ $caisse->prescripteur_id == $prescripteur->id ?
                            'selected' : '' }}>
                            {{ $prescripteur->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Colonne de droite -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Date de l'examen
                        *</label>
                    <input type="date" name="date_examen" value="{{ $caisse->date_examen->format('Y-m-d') }}" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Type d'examen
                        *</label>
                    <select name="examen_id" id="examen_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        onchange="updateTotal()">
                        @foreach($exam_types as $type)
                        @php
                        $service = $type->service;
                        $isPharmacie = $service && ($service->type_service === 'medicament' || $service->type_service === 'PHARMACIE') && $service->pharmacie;
                        @endphp
                        <option value="{{ $type->id }}" data-tarif="{{ $type->tarif }}"
                            data-service-type="{{ $service ? $service->type_service : '' }}"
                            data-is-pharmacie="{{ $isPharmacie ? 'true' : 'false' }}" {{ $caisse->examen_id == $type->id
                            ? 'selected' : '' }}>
                            {{ $type->nom }} - {{ number_format($type->tarif, 2) }} MRU
                            @if($isPharmacie)
                            ({{ $service->pharmacie->nom_medicament }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Total (MRU) *</label>
                    <input type="number" name="total" id="total" step="0.01" value="{{ $caisse->total }}" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                </div>

                {{-- Checkbox : le patient a-t-il une assurance ? --}}
                <div>
                    <label class="text-gray-700 dark:text-gray-200">
                        <input type="checkbox" id="hasAssurance" {{ $caisse->assurance_id ? 'checked' : '' }}> Le
                        patient a une assurance ?
                    </label>
                </div>

                {{-- Champs assurance : nom + couverture --}}
                <div id="assuranceFields" style="display: {{ $caisse->assurance_id ? 'block' : 'none' }};">
                    <label for="assurance_id" class="text-gray-700 dark:text-gray-200">Nom de l'assurance :</label>
                    <select name="assurance_id" id="assurance_id" {{ $caisse->assurance_id ? '' : 'disabled' }}
                        class="form-select bg-white dark:bg-gray-900 text-gray-900 dark:text-white border
                        border-gray-300 dark:border-gray-600">
                        <option value="">-- Sélectionner une assurance --</option>
                        @foreach ($assurances as $assurance)
                        <option value="{{ $assurance->id }}" {{ $caisse->assurance_id == $assurance->id ? 'selected' :
                            '' }}>
                            {{ $assurance->nom }}
                        </option>
                        @endforeach
                    </select>

                    <label for="couverture" class="text-gray-700 dark:text-gray-200 mt-2 block">Couverture (%) :</label>
                    <input type="number" name="couverture" id="couverture" value="{{ $caisse->couverture ?? 0 }}"
                        class="form-input border border-gray-300 dark:border-gray-600 px-2 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        min="0" max="100">
                </div>
            </div>
        </div>

        <div class="border-t pt-4 border-gray-200 dark:border-gray-700">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Caissier *</label>
                <input type="text" name="nom_caissier" value="{{ Auth::user()->name }}" required
                    class="w-full md:w-1/2 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('caisses.index') }}"
                    class="bg-gray-500 dark:bg-gray-700 text-white px-5 py-2 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-800 transition">
                    Annuler
                </a>
                <button type="submit"
                    class="bg-blue-600 dark:bg-blue-700 text-white px-5 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition font-semibold shadow">
                    Mettre à jour
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assuranceToggle = document.getElementById('hasAssurance');
        const assuranceFields = document.getElementById('assuranceFields');
        const assuranceSelect = document.getElementById('assurance_id');
        const couvertureInput = document.getElementById('couverture');

        // Affiche/masque les champs assurance
        assuranceToggle.addEventListener('change', function () {
            if (this.checked) {
                assuranceFields.style.display = 'block';
                assuranceSelect.disabled = false;
                assuranceSelect.required = true;
            } else {
                assuranceFields.style.display = 'none';
                assuranceSelect.disabled = true;
                assuranceSelect.required = false;
                // Réinitialiser les valeurs
                assuranceSelect.value = '';
                couvertureInput.value = '';
            }
        });

        updateTotal(); // Initialiser le total
    });

    function updateTotal() {
        const examenSelect = document.getElementById('examen_id');
        const totalInput = document.getElementById('total');

        if (examenSelect && totalInput) {
            const selectedOption = examenSelect.options[examenSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.tarif) {
                totalInput.value = parseFloat(selectedOption.dataset.tarif).toFixed(2);
            }
        }
    }

    async function updateNumeroEntree() {
        const medecinSelect = document.getElementById('medecin_id');
        const numeroEntreeInput = document.getElementById('numero_entre');

        if (medecinSelect && numeroEntreeInput && medecinSelect.value) {
            try {
                const response = await fetch(`/api/caisses/numero-entree/${medecinSelect.value}`);
                const data = await response.json();

                if (data.numero_entree) {
                    numeroEntreeInput.value = data.numero_entree;

                    // Afficher une notification temporaire
                    const small = numeroEntreeInput.parentElement.querySelector('small');
                    const originalText = small.textContent;
                    small.textContent = `Numéro d'entrée mis à jour: ${data.numero_entree}`;
                    small.style.color = '#10b981'; // text-green-500

                    setTimeout(() => {
                        small.textContent = originalText;
                        small.style.color = '';
                    }, 3000);
                }
            } catch (error) {
                console.error('Erreur lors de la récupération du numéro d\'entrée:', error);
            }
        }
    }
</script>
@endsection