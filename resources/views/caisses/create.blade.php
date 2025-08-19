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
        @if($fromRdv)
        <input type="hidden" name="from_rdv" value="{{ $fromRdv->id }}">
        @endif
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

        @if($fromRdv)
        <div
            class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-6">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <div>
                    <strong>Paiement depuis un rendez-vous</strong>
                    <p class="text-sm mt-1">
                        Vous êtes en train de créer une facture pour le rendez-vous du patient
                        <strong>{{ $prefilledPatient->first_name }} {{ $prefilledPatient->last_name }}</strong>
                        avec le Dr. <strong>{{ $prefilledMedecin->nom }}</strong>.
                        Les informations patient et médecin sont pré-remplies et ne peuvent pas être modifiées.
                    </p>
                </div>
            </div>
        </div>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Colonne de gauche -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Numéro d'entrée
                        @if($fromRdv)
                        <span class="text-green-600 text-xs">(Depuis le rendez-vous)</span>
                        @endif
                    </label>
                    <input type="text" id="numero_entree_display" name="numero_entree" value="{{ $numero_prevu }}"
                        readonly
                        class="w-full font-bold bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Numéro de téléphone *
                        @if($fromRdv)
                        <span class="text-green-600 text-xs">(Pré-rempli depuis le rendez-vous)</span>
                        @endif
                    </label>
                    <input type="text" id="patient_phone" name="patient_phone"
                        value="{{ $fromRdv && $prefilledPatient ? $prefilledPatient->phone : '' }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $fromRdv ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : 'bg-white dark:bg-gray-900' }} text-gray-900 dark:text-white"
                        placeholder="{{ $fromRdv ? '' : 'Saisir le numéro de téléphone du patient' }}" {{ $fromRdv
                        ? 'disabled' : '' }}>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Patient *
                        @if($fromRdv)
                        <span class="text-green-600 text-xs">(Pré-rempli depuis le rendez-vous)</span>
                        @endif
                    </label>
                    <select name="gestion_patient_id" id="patient_select" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white {{ $fromRdv ? 'bg-gray-100 dark:bg-gray-700 cursor-not-allowed' : '' }}"
                        {{ $fromRdv ? 'disabled' : '' }}>
                        <option value="">Sélectionner un patient</option>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" data-phone="{{ $patient->phone }}" {{ $fromRdv &&
                            $prefilledPatient && $patient->id == $prefilledPatient->id ? 'selected' : '' }}>
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </option>
                        @endforeach
                    </select>
                    @if($fromRdv && $prefilledPatient)
                    <input type="hidden" name="gestion_patient_id" value="{{ $prefilledPatient->id }}">
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Médecin *
                        @if($fromRdv)
                        <span class="text-green-600 text-xs">(Pré-rempli depuis le rendez-vous)</span>
                        @endif
                    </label>
                    <select name="medecin_id" id="medecin_select" required
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        onchange="updateNumeroEntreeSimple();">
                        <option value="">Sélectionner un médecin</option>
                        @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}" {{ $fromRdv && $prefilledMedecin && $medecin->id ==
                            $prefilledMedecin->id ? 'selected' : '' }}>
                            {{ $medecin->nom_complet }}{{ $medecin->specialite ? ' - ' . $medecin->specialite : '' }}
                        </option>
                        @endforeach
                    </select>

                    @if($fromRdv && $prefilledMedecin)
                    <input type="hidden" name="medecin_id" value="{{ $prefilledMedecin->id }}">
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Prescripteur</label>
                    <select name="prescripteur_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        <option value="">Sélectionner un prescripteur</option>
                        <option value="extern" selected>Extern</option>
                        @foreach($prescripteurs as $prescripteur)
                        <option value="{{ $prescripteur->id }}">{{ $prescripteur->nom }}{{ $prescripteur->specialite ? '
                            - ' . $prescripteur->specialite : '' }}</option>
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
                    <small class="text-blue-600 dark:text-blue-400 mb-2 block">
                        <i class="fas fa-info-circle mr-1"></i>
                        Cliquez sur l'icône + pour sélectionner vos examens
                    </small>
                    <div class="flex-container-safe">
                        <select name="examen_id" id="examen_id" required disabled
                            class="flex-item-safe border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                            onchange="updateTotal()">
                            <option value="">Sélectionnez des examens via l'icône +</option>
                        </select>
                        <button type="button" onclick="openExamenModal()"
                            class="flex-button-safe bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center"
                            title="Choisir un examen à ajouter">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Zone d'affichage des examens sélectionnés -->
                <div id="examens_selectionnes_div" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Examens
                        sélectionnés</label>
                    <div id="examens_liste"
                        class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 space-y-2">
                        <!-- Les examens ajoutés apparaîtront ici -->
                    </div>
                    <button type="button" onclick="ajouterExamenAListe()"
                        class="mt-2 bg-blue-500 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded">
                        Ajouter cet examen à la liste
                    </button>
                </div>

                <!-- Champ quantité pour médicament (caché par défaut) -->
                <div id="quantite_medicament_div" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Quantité du
                        médicament *</label>
                    <input type="number" name="quantite_medicament" id="quantite_medicament" min="1" value="1"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        onchange="updateTotal()">
                    <small class="text-gray-500 dark:text-gray-400" id="stock_info"></small>

                    <!-- Informations de stock pour les médicaments -->
                    <div id="stock_details"
                        class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
                        style="display: none;">
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <div class="font-semibold mb-1">📦 Informations de stock :</div>
                            <div id="stock_details_content"></div>
                        </div>
                    </div>
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
                    <select name="assurance_id" id="assurance_id" disabled
                        class="form-select bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
                        <option value="">-- Sélectionner une assurance --</option>
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
                <button type="submit" id="submitBtn"
                    class="bg-blue-600 dark:bg-blue-700 text-white px-5 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition font-semibold shadow"
                    onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Enregistrement en cours...'; this.form.submit();">
                    Enregistrer
                </button>
            </div>
        </div>

        <!-- Champs cachés pour les examens multiples -->
        <input type="hidden" name="examens_data" id="examens_data" value="">
        <input type="hidden" name="examens_multiple" id="examens_multiple" value="false">
    </form>
</div>

<!-- Modal pour choisir un examen -->
<div id="examenModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div
        class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Choisir un examen à ajouter</h3>
                <button type="button" onclick="closeExamenModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Examens
                        disponibles</label>
                    <select id="modal_examen_select"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        <option value="">Sélectionner un examen</option>
                        @foreach($exam_types ?? [] as $examen)
                        <option value="{{ $examen->id }}" data-nom="{{ $examen->nom }}"
                            data-tarif="{{ $examen->tarif }}" data-part-cabinet="{{ $examen->part_cabinet }}"
                            data-part-medecin="{{ $examen->part_medecin }}"
                            data-service-type="{{ $examen->service ? $examen->service->type_service : 'CONSULTATIONS EXTERNES' }}"
                            data-is-pharmacie="{{ $examen->service && ($examen->service->type_service === 'PHARMACIE') ? 'true' : 'false' }}"
                            data-stock="{{ $examen->service && $examen->service->pharmacie ? $examen->service->pharmacie->stock : '' }}">
                            {{ $examen->nom }} - {{ number_format($examen->tarif, 2) }} MRU
                        </option>
                        @endforeach
                    </select>
                    <small class="text-gray-500 dark:text-gray-400 mt-1">
                        Sélectionnez un examen dans la liste pour l'ajouter à votre facture.
                    </small>
                </div>

                <!-- Champ quantité pour médicament (caché par défaut) -->
                <div id="modal_quantite_div" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Quantité</label>
                    <input type="number" id="modal_quantite" min="1" value="1"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    <small id="modal_stock_info" class="text-gray-500 dark:text-gray-400"></small>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeExamenModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                    Annuler
                </button>
                <button type="button" onclick="ajouterExamenDeModal()"
                    class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Ajouter cet examen
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const assuranceToggle = document.getElementById('hasAssurance');
        const assuranceFields = document.getElementById('assuranceFields');
        const assuranceSelect = document.getElementById('assurance_id');
        const couvertureField = document.getElementById('couverture');
        const couvertureInput = document.getElementById('couverture');
        const modePaiementField = document.getElementById('modePaiement');

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

    // Variables globales pour les examens multiples
    let examensSelectionnes = [];
    let prochainIdExamen = null;

    // Fonctions pour le modal d'examen
    function openExamenModal() {
        document.getElementById('examenModal').classList.remove('hidden');
        // Réinitialiser la sélection
        document.getElementById('modal_examen_select').value = '';
        document.getElementById('modal_quantite_div').style.display = 'none';
        document.getElementById('modal_quantite').value = 1;
    }

    function closeExamenModal() {
        document.getElementById('examenModal').classList.add('hidden');
    }

    function ajouterExamenDeModal() {
        const select = document.getElementById('modal_examen_select');
        const selectedOption = select.options[select.selectedIndex];

        if (!select.value) {
            alert('Veuillez sélectionner un examen.');
            return;
        }

        const quantiteInput = document.getElementById('modal_quantite');
        const quantite = parseInt(quantiteInput.value) || 1;

        const examen = {
            id: select.value,
            nom: selectedOption.getAttribute('data-nom'),
            tarif: parseFloat(selectedOption.getAttribute('data-tarif')),
            quantite: quantite,
            total: parseFloat(selectedOption.getAttribute('data-tarif')) * quantite,
            isPharmacie: selectedOption.getAttribute('data-is-pharmacie') === 'true'
        };

        // Vérifier si l'examen existe déjà dans la liste
        const existant = examensSelectionnes.find(e => e.id === examen.id);
        if (existant) {
            existant.quantite += quantite;
            existant.total = existant.tarif * existant.quantite;
        } else {
            examensSelectionnes.push(examen);
        }

        // Mettre à jour l'affichage
        afficherExamensSelectionnes();

        // Activer le mode examens multiples
        document.getElementById('examens_multiple').value = 'true';
        document.getElementById('examens_data').value = JSON.stringify(examensSelectionnes);

        // Afficher la zone des examens sélectionnés
        document.getElementById('examens_selectionnes_div').style.display = 'block';

        // Fermer la modal
        closeExamenModal();
    }

    // Gestion de la quantité pour les médicaments dans la modal
    document.addEventListener('DOMContentLoaded', function() {
        const modalSelect = document.getElementById('modal_examen_select');
        if (modalSelect) {
            modalSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const quantiteDiv = document.getElementById('modal_quantite_div');
                const stockInfo = document.getElementById('modal_stock_info');

                if (this.value && selectedOption.getAttribute('data-is-pharmacie') === 'true') {
                    quantiteDiv.style.display = 'block';
                    const stock = selectedOption.getAttribute('data-stock');
                    if (stock) {
                        stockInfo.textContent = `Stock disponible: ${stock} unités`;
                        stockInfo.className = parseInt(stock) > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                    }
                } else {
                    quantiteDiv.style.display = 'none';
                    stockInfo.textContent = '';
                    document.getElementById('modal_quantite').value = 1;
                }
            });
        }
    });

    function ajouterExamenAListe() {
        const select = document.getElementById('examen_id');
        const selectedOption = select.options[select.selectedIndex];

        if (!select.value) {
            alert('Veuillez sélectionner un examen.');
            return;
        }

        const quantiteInput = document.getElementById('quantite_medicament');
        const quantite = parseInt(quantiteInput.value) || 1;

        const examen = {
            id: select.value,
            nom: selectedOption.getAttribute('data-nom'),
            tarif: parseFloat(selectedOption.getAttribute('data-tarif')),
            quantite: quantite,
            total: parseFloat(selectedOption.getAttribute('data-tarif')) * quantite,
            isPharmacie: selectedOption.getAttribute('data-is-pharmacie') === 'true'
        };

        // Vérifier si l'examen existe déjà
        const existant = examensSelectionnes.find(e => e.id === examen.id);
        if (existant) {
            existant.quantite += quantite;
            existant.total = existant.tarif * existant.quantite;
        } else {
            examensSelectionnes.push(examen);
        }

        // Mettre à jour l'affichage
        afficherExamensSelectionnes();

        // Activer le mode examens multiples
        document.getElementById('examens_multiple').value = 'true';
        document.getElementById('examens_data').value = JSON.stringify(examensSelectionnes);

        // Réinitialiser les sélections
        select.value = '';
        quantiteInput.value = '1';
        document.getElementById('quantite_medicament_div').style.display = 'none';
        document.getElementById('stock_info').textContent = '';
        document.getElementById('display_total').value = '';
        document.getElementById('total').value = '';

        // Afficher la zone des examens sélectionnés
        document.getElementById('examens_selectionnes_div').style.display = 'block';
    }

    function afficherExamensSelectionnes() {
        const container = document.getElementById('examens_liste');
        let html = '';
        let totalGeneral = 0;

        examensSelectionnes.forEach((examen, index) => {
            totalGeneral += examen.total;
            html += `
                <div class="flex justify-between items-center bg-white dark:bg-gray-700 p-2 rounded border">
                    <div>
                        <span class="font-medium">${examen.nom}</span>
                        ${examen.isPharmacie ? ` <span class="text-sm text-gray-500">(Qté: ${examen.quantite})</span>` : ''}
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            ${examen.tarif.toFixed(2)} MRU × ${examen.quantite} = ${examen.total.toFixed(2)} MRU
                        </div>
                    </div>
                    <button type="button" onclick="supprimerExamen(${index})"
                        class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
        });

        if (examensSelectionnes.length === 0) {
            html = '<div class="text-gray-500 text-center">Aucun examen sélectionné</div>';
            document.getElementById('examens_selectionnes_div').style.display = 'none';
            document.getElementById('examens_multiple').value = 'false';
        }

        container.innerHTML = html;

        // Mettre à jour le total
        document.getElementById('display_total').value = totalGeneral.toFixed(2);
        document.getElementById('total').value = totalGeneral.toFixed(2);
    }

    function supprimerExamen(index) {
        examensSelectionnes.splice(index, 1);
        afficherExamensSelectionnes();
        document.getElementById('examens_data').value = JSON.stringify(examensSelectionnes);

        if (examensSelectionnes.length === 0) {
            document.getElementById('examens_multiple').value = 'false';
        }
    }

    // Fermer le modal en cliquant en dehors
    document.getElementById('examenModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeExamenModal();
        }
    });
</script>
<script>
    function updateTotal() {
        // Le select est désactivé, donc on calcule le total uniquement à partir des examens sélectionnés
        const examensSelectionnes = JSON.parse(document.getElementById('examens_data').value || '[]');
        const displayTotal = document.getElementById('display_total');
        const totalHidden = document.getElementById('total');

        let totalGeneral = 0;

        examensSelectionnes.forEach(examen => {
            totalGeneral += examen.total || 0;
        });

        if (displayTotal) {
            displayTotal.value = totalGeneral.toFixed(2);
        }
        if (totalHidden) {
            totalHidden.value = totalGeneral.toFixed(2);
        }

        // Mettre à jour l'affichage des examens sélectionnés
        afficherExamensSelectionnes();


    // Mettre à jour le total quand la quantité change
    document.addEventListener('DOMContentLoaded', function() {
        // Le select est désactivé, donc on n'a plus besoin d'écouter ses changements
        // Le total se met à jour automatiquement via la modal

        // Appeler updateTotal au chargement de la page
        setTimeout(function() {
            if (typeof updateTotal === 'function') {
                updateTotal();
            }

            // Forcer le mode examens multiples puisque le select est désactivé
            document.getElementById('examens_multiple').value = 'true';
        }, 100);





                // Fonction pour afficher les informations de stock des médicaments
        function afficherInfosStock(examenId) {
            const stockDetails = document.getElementById('stock_details');
            const stockDetailsContent = document.getElementById('stock_details_content');
            const quantiteDiv = document.getElementById('quantite_medicament_div');

            if (examenId) {
                // Récupérer les informations de l'examen via AJAX
                fetch(`/api/examens/${examenId}/stock-info`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.is_medicament && data.stock_info) {
                            const stockInfo = data.stock_info;
                            stockDetailsContent.innerHTML = `
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="font-medium">Médicament :</span>
                                        <span class="text-green-600 dark:text-green-400">${stockInfo.nom_medicament}</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Stock disponible :</span>
                                        <span class="text-blue-600 dark:text-blue-400">${stockInfo.stock} unités</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Prix unitaire :</span>
                                        <span class="text-purple-600 dark:text-purple-400">${stockInfo.prix_vente} MRU</span>
                                    </div>
                                    <div>
                                        <span class="font-medium">Statut :</span>
                                        <span class="${stockInfo.stock > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}">${stockInfo.stock > 0 ? 'En stock' : 'Rupture'}</span>
                                    </div>
                                </div>
                            `;
                            stockDetails.style.display = 'block';
                            quantiteDiv.style.display = 'block';
                        } else {
                            stockDetails.style.display = 'none';
                            quantiteDiv.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération des infos stock:', error);
                        stockDetails.style.display = 'none';
                        quantiteDiv.style.display = 'none';
                    });
            } else {
                stockDetails.style.display = 'none';
                quantiteDiv.style.display = 'none';
            }
        }

        // Le select est désactivé, donc on n'a plus besoin d'écouter ses changements
    });
</script>

<script>
    // Données des numéros par médecin (scope global)
const numerosParMedecin = @json($numeros_par_medecin);

// Fonction globale pour mettre à jour le numéro d'entrée
function updateNumeroEntreeSimple() {
    const medecinSelect = document.getElementById('medecin_select');
    const numeroEntreeDisplay = document.getElementById('numero_entree_display');

    if (medecinSelect && numeroEntreeDisplay) {
        const medecinId = medecinSelect.value;

        if (medecinId && numerosParMedecin[medecinId]) {
            numeroEntreeDisplay.value = numerosParMedecin[medecinId];
        } else {
            numeroEntreeDisplay.value = 1;
        }
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
