@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Ajouter un nouvel examen</h1>
        <a href="{{ route('caisses.index') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
        </a>
    </div>

    <form method="POST" action="{{ route(auth()->user()->role->name . '.caisses.store') }}" id="formFacture">
        @csrf
        @if ($errors->any())
        <div
            class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-2 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Colonne de gauche -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Numéro d'entrée
                    </label>
                    <input type="text" value="{{ $numero_prevu }}" disabled
                        class="w-full font-bold bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-500 dark:text-gray-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Numéro de téléphone
                        *</label>
                    <input type="text" id="patient_phone" name="patient_phone"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        placeholder="Saisir le numéro de téléphone du patient">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Patient *</label>
                    <select name="gestion_patient_id" id="patient_select" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        <option value="">Sélectionner un patient</option>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" data-phone="{{ $patient->phone }}">{{ $patient->first_name }}
                            {{ $patient->last_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Médecin *</label>
                    <select name="medecin_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        <option value="">Sélectionner un médecin</option>
                        @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Prescripteur</label>
                    <select name="prescripteur_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        <option value="">Sélectionner un prescripteur</option>
                        @foreach($prescripteurs as $prescripteur)
                        <option value="{{ $prescripteur->id }}">{{ $prescripteur->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Colonne de droite -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Date de l'examen
                        *</label>
                    <input type="date" name="date_examen" required value="{{ date('Y-m-d') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Type d'examen
                        *</label>
                    <select name="examen_id" id="examen_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        onchange="updateTotal()">
                        <option value="">Sélectionner un type d'examen</option>
                        @foreach($exam_types as $type)
                        <option value="{{ $type->id }}" data-tarif="{{ $type->tarif }}">
                            {{ $type->nom }} - {{ number_format($type->tarif, 2) }} MRU
                        </option>
                        @endforeach
                    </select>
                </div>
                {{-- Checkbox : le patient a-t-il une assurance ? --}}
                <div>
                    <label class="text-gray-700 dark:text-gray-200">
                        <input type="checkbox" id="hasAssurance"> Le patient a une assurance ?
                    </label>
                </div>

                {{-- Champs assurance : nom + couverture --}}
                <div id="assuranceFields" style="display: none;">
                    <label for="assurance_id" class="text-gray-700 dark:text-gray-200">Nom de l'assurance :</label>
                    <select name="assurance_id" id="assurance_id"
                        class="form-select bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
                        @foreach ($assurances as $assurance)
                        <option value="{{ $assurance->id }}">{{ $assurance->nom }}</option>
                        @endforeach
                    </select>

                    <label for="couverture" class="text-gray-700 dark:text-gray-200">Couverture (%) :</label>
                    <input type="number" name="couverture" id="couverture"
                        class="form-input border border-b-black px-2 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600"
                        min="0" max="100">
                </div>

                {{-- Mode de paiement : visible sauf si assurance 100% --}}
                <div id="modePaiement">
                    <label for="type" class="text-gray-700 dark:text-gray-200">Mode de paiement :</label>
                    <select name="type" id="type"
                        class="form-select bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
                        <option value="especes">Espèces</option>
                        <option value="bankily">Bankily</option>
                        <option value="masrivi">Masrivi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Total (MRU) *</label>
                    <input type="text" id="display_total" readonly
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="hidden" name="total" id="total" value="" required>
                </div>
            </div>
        </div>

        <div class="border-t pt-4 border-gray-200 dark:border-gray-700">
            <div class="text-gray-900 dark:text-white">Caissier *</div>
            <div class="mb-4">
                <div class="w-full md:w-1/2 bg-gray-200 dark:bg-gray-900 py-4 mt-2 pl-2 ">
                    <strong class="text-gray-900 dark:text-white">{{ Auth::user()->name }}</strong>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('caisses.index') }}"
                    class="bg-gray-500 dark:bg-gray-700 text-white px-5 py-2 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-800 transition">
                    Annuler
                </a>
                <button type="submit"
                    class="bg-blue-600 dark:bg-blue-700 text-white px-5 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition font-semibold shadow">
                    Enregistrer
                </button>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const assuranceToggle = document.getElementById('hasAssurance');
        const assuranceFields = document.getElementById('assuranceFields');
        const couvertureField = document.getElementById('couverture');
        const modePaiementField = document.getElementById('modePaiement');

        // Affiche/masque les champs assurance
        assuranceToggle.addEventListener('change', function () {
            if (this.checked) {
                assuranceFields.style.display = 'block';
            } else {
                assuranceFields.style.display = 'none';
                modePaiementField.style.display = 'block'; // réaffiche toujours
            }
        });

        // Affiche/masque le mode de paiement selon la couverture
        couvertureField.addEventListener('input', function () {
            const couverture = parseFloat(this.value);
            if (!isNaN(couverture) && couverture === 100) {
                modePaiementField.style.display = 'none';
            } else {
                modePaiementField.style.display = 'block';
            }
        });

        // Synchronisation patient <-> téléphone
        const patientSelect = document.getElementById('patient_select');
        const phoneInput = document.getElementById('patient_phone');
        const patientOptions = Array.from(patientSelect.options);
        // Quand on choisit un patient, remplir le téléphone
        patientSelect.addEventListener('change', function() {
            const selected = patientSelect.options[patientSelect.selectedIndex];
            phoneInput.value = selected.getAttribute('data-phone') || '';
        });
        // Quand on tape un numéro, sélectionner le patient correspondant
        phoneInput.addEventListener('input', function() {
            const val = phoneInput.value.trim();
            const found = patientOptions.find(opt => opt.getAttribute('data-phone') === val);
            if (found) {
                patientSelect.value = found.value;
            }
        });
    });
</script>
<script>
    function updateTotal() {
        const select = document.getElementById('examen_id');
        const selectedOption = select.options[select.selectedIndex];
        const tarif = selectedOption.getAttribute('data-tarif');
        if (tarif) {
            document.getElementById('display_total').value = tarif;
            document.getElementById('total').value = tarif;
        } else {
            document.getElementById('display_total').value = '';
            document.getElementById('total').value = '';
        }
    }
</script>
<script>
    document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('profile-dropdown');
            const button = dropdown?.previousElementSibling;
            if (dropdown && !dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
</script>

@endsection
