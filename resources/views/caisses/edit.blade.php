@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    @php
    $role = Auth::user()->role->name;
    $updateRouteName = match($role) {
        'superadmin' => 'superadmin.caisses.update',
        'admin' => 'admin.caisses.update',
        default => 'caisses.update'
    };
    $indexRouteName = match($role) {
        'superadmin' => 'superadmin.caisses.index',
        'admin' => 'admin.caisses.index',
        default => 'caisses.index'
    };
    @endphp
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Modifier l'examen #{{ $caisse->numero_entre }}</h1>
        <a href="{{ route($indexRouteName, ['page' => $page ?? 1]) }}"
            class="text-blue-600 dark:text-blue-400 hover:underline flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
        </a>
    </div>

    <form method="POST" action="{{ route($updateRouteName, $caisse->id) }}" id="formFacture">
        @csrf
        @method('PUT')
        <input type="hidden" name="return_page" value="{{ $page ?? 1 }}">
        
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Numéro d'entrée
                    </label>
                    <input type="text" id="numero_entree_display" name="numero_entre" value="{{ $caisse->numero_entre }}"
                        readonly
                        class="w-full font-bold bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-gray-900 dark:text-gray-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Numéro de téléphone *
                    </label>
                    <input type="text" id="patient_phone" name="patient_phone" list="telephones-list"
                        value="{{ $caisse->patient->phone ?? '' }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        placeholder="Tapez le numéro de téléphone du patient...">
                    <datalist id="telephones-list">
                        @foreach($patients as $patient)
                        <option value="{{ $patient->phone }}" 
                            data-id="{{ $patient->id }}" 
                            data-nom="{{ $patient->first_name }} {{ $patient->last_name }}">
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Patient *
                    </label>
                    <input type="text" id="patient_search" list="patients-list"
                        value="{{ $caisse->patient->first_name }} {{ $caisse->patient->last_name }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        placeholder="Tapez le nom du patient...">
                    <input type="hidden" name="gestion_patient_id" id="gestion_patient_id" 
                        value="{{ $caisse->gestion_patient_id }}" required>
                    <datalist id="patients-list">
                        @foreach($patients as $patient)
                        <option value="{{ $patient->first_name }} {{ $patient->last_name }}" 
                            data-id="{{ $patient->id }}" 
                            data-telephone="{{ $patient->phone }}">
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Médecin *
                    </label>
                    <input type="text" id="medecin_search" list="medecins-list"
                        value="{{ $caisse->medecin->nom_complet_avec_specialite ?? '' }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        placeholder="Tapez le nom du médecin...">
                    <input type="hidden" name="medecin_id" id="medecin_id" 
                        value="{{ $caisse->medecin_id }}" required>
                    <datalist id="medecins-list">
                        @php
                        $medecinsParFonction = $medecins->groupBy('fonction');
                        $ordrefonctions = ['Pr', 'Dr', 'Tss', 'SGF', 'IDE'];
                        @endphp
                        @foreach($ordrefonctions as $fonction)
                        @if(isset($medecinsParFonction[$fonction]) && $medecinsParFonction[$fonction]->count() > 0)
                            @foreach($medecinsParFonction[$fonction] as $medecin)
                            <option value="{{ $medecin->nom_complet_avec_specialite }}" 
                                data-id="{{ $medecin->id }}">
                            @endforeach
                        @endif
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Prescripteur</label>
                    @php
                        $prescripteurValue = 'Externe';
                        $prescripteurIdValue = 'extern';
                        if ($caisse->prescripteur_id) {
                            $prescripteurActuel = $prescripteurs->find($caisse->prescripteur_id);
                            if ($prescripteurActuel) {
                                $prescripteurValue = $prescripteurActuel->nom . ($prescripteurActuel->specialite ? ' - ' . $prescripteurActuel->specialite : '');
                                $prescripteurIdValue = $prescripteurActuel->id;
                            }
                        }
                    @endphp
                    <input type="text" id="prescripteur_search" list="prescripteurs-list" value="{{ $prescripteurValue }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                        placeholder="Tapez le nom du prescripteur...">
                    <input type="hidden" name="prescripteur_id" id="prescripteur_id" value="{{ $prescripteurIdValue }}">
                    <datalist id="prescripteurs-list">
                        <option value="Externe" data-id="extern">
                        @foreach($prescripteurs as $prescripteur)
                        <option value="{{ $prescripteur->nom }}{{ $prescripteur->specialite ? ' - ' . $prescripteur->specialite : '' }}" 
                            data-id="{{ $prescripteur->id }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <!-- Colonne de droite -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Date de l'examen
                        *</label>
                    <input type="date" name="date_examen" required
                        value="{{ $caisse->date_examen ? \Carbon\Carbon::parse($caisse->date_examen)->format('Y-m-d') : date('Y-m-d') }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Type d'examen
                        *</label>
                    <small class="text-blue-600 dark:text-blue-400 mb-2 block">
                        <i class="fas fa-info-circle mr-1"></i>
                        Cliquez sur l'icône + pour sélectionner vos examens
                    </small>
                    <div class="flex gap-2">
                        <select name="examen_id" id="examen_id" required disabled
                            class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                            onchange="updateTotal()">
                            <option value="">Sélectionnez des examens via l'icône +</option>
                        </select>
                        <button type="button" onclick="openExamenModal()"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center"
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
                        <input type="checkbox" id="hasAssurance" {{ $caisse->assurance_id ? 'checked' : '' }}> Le patient a une assurance ?
                    </label>
                </div>

                {{-- Champs assurance : nom + couverture --}}
                <div id="assuranceFields" style="display: {{ $caisse->assurance_id ? 'block' : 'none' }};">
                    <label for="assurance_id" class="text-gray-700 dark:text-gray-200">Nom de l'assurance <span
                            class="text-red-500">*</span>:</label>
                    <select name="assurance_id" id="assurance_id" {{ $caisse->assurance_id ? '' : 'disabled' }}
                        class="form-select bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
                        <option value="">-- Sélectionner une assurance --</option>
                        @foreach ($assurances as $assurance)
                        <option value="{{ $assurance->id }}" {{ $assurance->id == $caisse->assurance_id ? 'selected' : '' }}>{{ $assurance->nom }}</option>
                        @endforeach
                    </select>

                    <label for="couverture" class="text-gray-700 dark:text-gray-200">Couverture (%) :</label>
                    <div class="relative">
                        <input type="text" name="couverture" id="couverture" {{ $caisse->assurance_id ? '' : 'disabled' }}
                            class="form-input border border-b-black px-2 py-2 {{ $caisse->assurance_id ? 'bg-white dark:bg-gray-900 text-gray-900 dark:text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }} border-gray-300 dark:border-gray-600 w-full"
                            min="0" max="100" pattern="[0-9]+" placeholder="{{ $caisse->assurance_id ? 'Ex: 90' : 'Sélectionnez d\'abord une assurance' }}" value="{{ $caisse->couverture ?? '' }}">
                        <div id="couverture-lock-icon"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" style="display: {{ $caisse->assurance_id ? 'none' : 'flex' }};">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <small id="couverture-help" class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                        {{ $caisse->assurance_id ? '💡 Saisissez le pourcentage de couverture (0-100%)' : '🔒 Ce champ sera activé après sélection d\'une assurance' }}
                    </small>
                </div>

                {{-- Mode de paiement : visible sauf si assurance 100% --}}
                <div id="modePaiement" style="display: {{ $caisse->couverture == 100 ? 'none' : 'block' }};">
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Mode de paiement :</label>
                    <select name="type" id="type"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        @php
                            // Récupérer le paiement principal (relation hasOne)
                            $paiement = $caisse->paiements;
                            // Si pas trouvé, chercher dans mode_paiements (hasMany)
                            if (!$paiement && $caisse->mode_paiements->isNotEmpty()) {
                                $paiement = $caisse->mode_paiements->where('source', 'caisse')->first();
                            }
                            $currentType = $paiement ? $paiement->type : 'especes';
                        @endphp
                        <option value="especes" {{ $currentType == 'espèces' || $currentType == 'especes' ? 'selected' : '' }}>Espèces</option>
                        <option value="bankily" {{ $currentType == 'bankily' ? 'selected' : '' }}>Bankily</option>
                        <option value="masrivi" {{ $currentType == 'masrivi' ? 'selected' : '' }}>Masrivi</option>
                        <option value="sedad" {{ $currentType == 'sedad' ? 'selected' : '' }}>Sedad</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Total (MRU) *</label>
                    <input type="text" id="display_total" readonly value="{{ number_format($caisse->total, 2) }}"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="hidden" name="total" id="total" value="{{ $caisse->total }}" required>
                </div>
            </div>
        </div>

        <div class="border-t pt-4 border-gray-200 dark:border-gray-700">
            <div class="text-gray-900 dark:text-white">Caissier *</div>
            <div class="mb-4">
                <div class="w-full md:w-1/2 bg-gray-200 dark:bg-gray-900 py-4 mt-2 pl-2 ">
                    <strong class="text-gray-900 dark:text-white">{{ $caisse->nom_caissier }}</strong>
                </div>
                @if($caisse->modifier)
                <div class="w-full md:w-1/2 bg-blue-100 dark:bg-blue-900 py-2 mt-2 pl-2 rounded">
                    <small class="text-gray-700 dark:text-gray-300">Modifié par: <strong>{{ $caisse->modifier->name }}</strong></small>
                </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route($indexRouteName, ['page' => $page ?? 1]) }}"
                    class="bg-gray-500 dark:bg-gray-700 text-white px-5 py-2 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-800 transition">
                    Annuler
                </a>
                <button type="submit" id="submitBtn"
                    class="bg-blue-600 dark:bg-blue-700 text-white px-5 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition font-semibold shadow">
                    Mettre à jour
                </button>
            </div>
        </div>

        <!-- Champs cachés pour les examens multiples -->
        <input type="hidden" name="examens_data" id="examens_data" value="">
        <input type="hidden" name="examens_multiple" id="examens_multiple" value="false">
        
        <!-- Stocker les données existantes dans un script tag (Laravel décode auto le JSON) -->
        <script id="examens_data_loader" type="application/json">
            @json($caisse->examens_data ?: [])
        </script>
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
                <!-- Section Médicaments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        <i class="fas fa-pills mr-1"></i> Médicaments
                    </label>
                    <input type="text" id="modal_medicament_search" list="medicaments-list"
                        placeholder="Rechercher un médicament..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    <datalist id="medicaments-list">
                        @foreach($exam_types ?? [] as $examen)
                        @if($examen->service && $examen->service->type_service === 'PHARMACIE')
                        <option value="{{ $examen->nom }}" 
                            data-id="{{ $examen->id }}"
                            data-tarif="{{ $examen->tarif }}"
                            data-part-cabinet="{{ $examen->part_cabinet }}"
                            data-part-medecin="{{ $examen->part_medecin }}"
                            data-stock="{{ $examen->service->pharmacie ? $examen->service->pharmacie->stock : '' }}">
                            {{ number_format($examen->tarif, 2) }} MRU
                        </option>
                        @endif
                        @endforeach
                    </datalist>
                    <small class="text-gray-500 dark:text-gray-400 mt-1">
                        Tapez pour rechercher un médicament dans la liste.
                    </small>
                </div>

                <!-- Section Examens -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        <i class="fas fa-stethoscope mr-1"></i> Examens disponibles
                    </label>
                    <input type="text" id="modal_examen_search" list="examens-list"
                        placeholder="Rechercher un examen..."
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    <datalist id="examens-list">
                        @foreach($exam_types ?? [] as $examen)
                        @if(!$examen->service || $examen->service->type_service !== 'PHARMACIE')
                        <option value="{{ $examen->nom }}" 
                            data-id="{{ $examen->id }}"
                            data-tarif="{{ $examen->tarif }}"
                            data-part-cabinet="{{ $examen->part_cabinet }}"
                            data-part-medecin="{{ $examen->part_medecin }}">
                            {{ number_format($examen->tarif, 2) }} MRU
                        </option>
                        @endif
                        @endforeach
                    </datalist>
                    <small class="text-gray-500 dark:text-gray-400 mt-1">
                        Tapez pour rechercher un examen dans la liste.
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
                <button type="button" onclick="ajouterExamenDeModal()" id="btnAjouterExamenModal"
                    class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded cursor-pointer">
                    Ajouter cet examen
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // Variables globales pour les examens multiples - INITIALISER AVEC LES DONNÉES EXISTANTES
    let examensSelectionnes = [];
    
    // Charger les données existantes depuis le script tag JSON
    console.log('=== CHARGEMENT INITIAL DES DONNÉES ===');
    
    const loaderElement = document.getElementById('examens_data_loader');
    if (loaderElement) {
        const jsonContent = loaderElement.textContent || loaderElement.innerText;
        console.log('JSON brut depuis script tag:', jsonContent);
        console.log('Type:', typeof jsonContent);
        
        if (jsonContent && jsonContent.trim().length > 2) { // Plus que juste []
            try {
                const dataExistante = JSON.parse(jsonContent.trim());
                
                console.log('✅ Données parsées:', dataExistante);
                console.log('Type:', typeof dataExistante);
                console.log('Est array?', Array.isArray(dataExistante));
                console.log('Longueur:', Array.isArray(dataExistante) ? dataExistante.length : 0);
                
                if (Array.isArray(dataExistante) && dataExistante.length > 0) {
                    examensSelectionnes = dataExistante.map(ex => ({
                        id: String(ex.id),
                        nom: ex.nom,
                        tarif: parseFloat(ex.tarif),
                        quantite: parseInt(ex.quantite) || 1,
                        total: parseFloat(ex.total),
                        isPharmacie: ex.isPharmacie || false
                    }));
                    console.log('✅ Examens chargés:', examensSelectionnes);
                } else {
                    console.log('ℹ️ Aucun examen existant');
                }
            } catch(e) {
                console.error('❌ Erreur lors du parsing JSON:', e);
                console.error('Data brute:', jsonContent);
            }
        } else {
            console.log('ℹ️ Pas de données existantes');
        }
    } else {
        console.error('❌ Élément examens_data_loader introuvable');
    }
    
    console.log('=== FIN CHARGEMENT - examensSelectionnes:', examensSelectionnes);

    document.addEventListener('DOMContentLoaded', function () {
        const assuranceToggle = document.getElementById('hasAssurance');
        const assuranceFields = document.getElementById('assuranceFields');
        const assuranceSelect = document.getElementById('assurance_id');
        const couvertureField = document.getElementById('couverture');
        const couvertureInput = document.getElementById('couverture');
        const couvertureLockIcon = document.getElementById('couverture-lock-icon');
        const couvertureHelp = document.getElementById('couverture-help');
        const modePaiementField = document.getElementById('modePaiement');

        // Afficher les examens existants si disponibles (avec délai pour s'assurer que toutes les fonctions sont chargées)
        setTimeout(function() {
            console.log('Initialisation examens - Nombre:', examensSelectionnes.length);
            console.log('examensSelectionnes contenu:', examensSelectionnes);
            
            if (examensSelectionnes && examensSelectionnes.length > 0) {
                console.log('Affichage des examens existants...');
                document.getElementById('examens_selectionnes_div').style.display = 'block';
                document.getElementById('examens_multiple').value = 'true';
                document.getElementById('examens_data').value = JSON.stringify(examensSelectionnes);
                
                console.log('Champs cachés mis à jour:');
                console.log('- examens_multiple:', document.getElementById('examens_multiple').value);
                console.log('- examens_data:', document.getElementById('examens_data').value);
                
                if (typeof afficherExamensSelectionnes === 'function') {
                    afficherExamensSelectionnes();
                } else {
                    console.error('La fonction afficherExamensSelectionnes n\'est pas définie');
                }
            } else {
                console.log('Aucun examen à afficher - examensSelectionnes est vide');
            }
        }, 200);

        // Affiche/masque les champs assurance
        assuranceToggle.addEventListener('change', function () {
            if (this.checked) {
                assuranceFields.style.display = 'block';
                assuranceSelect.disabled = false;
                assuranceSelect.required = true;
                // Le champ couverture reste désactivé jusqu'à ce qu'une assurance soit sélectionnée
                couvertureInput.disabled = true;
                couvertureInput.value = '';
                couvertureInput.placeholder = 'Sélectionnez d\'abord une assurance';
                couvertureInput.className = 'form-input border border-b-black px-2 py-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-600 w-full';
                couvertureLockIcon.style.display = 'flex';
                couvertureHelp.textContent = '🔒 Ce champ sera activé après sélection d\'une assurance';
            } else {
                assuranceFields.style.display = 'none';
                assuranceSelect.disabled = true;
                assuranceSelect.required = false;
                // Réinitialiser les valeurs
                assuranceSelect.value = '';
                couvertureInput.value = '';
                couvertureInput.disabled = true;
                couvertureLockIcon.style.display = 'flex';
                couvertureHelp.textContent = '🔒 Ce champ sera activé après sélection d\'une assurance';
                modePaiementField.style.display = 'block'; // réaffiche toujours
            }
        });

        // Gérer l'activation/désactivation du champ couverture selon la sélection d'assurance
        assuranceSelect.addEventListener('change', function () {
            if (this.value && this.value !== '') {
                // Une assurance est sélectionnée, activer le champ couverture
                couvertureInput.disabled = false;
                couvertureInput.placeholder = 'Ex: 90';
                couvertureInput.className = 'form-input border border-b-black px-2 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white border-gray-300 dark:border-gray-600 w-full';
                couvertureLockIcon.style.display = 'none';
                couvertureHelp.textContent = '💡 Saisissez le pourcentage de couverture (0-100%)';
            } else {
                // Aucune assurance sélectionnée, désactiver le champ couverture
                couvertureInput.disabled = true;
                couvertureInput.value = '';
                couvertureInput.placeholder = 'Sélectionnez d\'abord une assurance';
                couvertureInput.className = 'form-input border border-b-black px-2 py-2 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-600 w-full';
                couvertureLockIcon.style.display = 'flex';
                couvertureHelp.textContent = '🔒 Ce champ sera activé après sélection d\'une assurance';
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

        // Validation du formulaire avant soumission
        document.getElementById('formFacture').addEventListener('submit', function(e) {
            // Debug: afficher les données avant soumission
            console.log('=== SOUMISSION DU FORMULAIRE ===');
            console.log('examens_multiple:', document.getElementById('examens_multiple').value);
            console.log('examens_data:', document.getElementById('examens_data').value);
            console.log('examensSelectionnes:', examensSelectionnes);
            console.log('total:', document.getElementById('total').value);
            
            const hasAssurance = document.getElementById('hasAssurance').checked;
            const assuranceId = document.getElementById('assurance_id').value;
            const couverture = document.getElementById('couverture').value;

            // Si le checkbox assurance est coché mais aucune assurance sélectionnée
            if (hasAssurance && (!assuranceId || assuranceId === '')) {
                e.preventDefault();
                alert('Veuillez sélectionner une assurance si le patient en a une.');
                return false;
            }

            // Si une assurance est sélectionnée mais pas de couverture
            if (assuranceId && assuranceId !== '' && (!couverture || couverture === '')) {
                e.preventDefault();
                alert('Veuillez saisir le pourcentage de couverture de l\'assurance.');
                return false;
            }

            // Si couverture saisie mais pas d'assurance sélectionnée
            if (couverture && couverture !== '' && (!assuranceId || assuranceId === '')) {
                e.preventDefault();
                alert('Veuillez sélectionner une assurance avant de saisir la couverture.');
                return false;
            }
        });

        // Synchronisation intelligente Patient/Téléphone/Médecin avec datalist
        const patientSearch = document.getElementById('patient_search');
        const phoneInput = document.getElementById('patient_phone');
        const gestionPatientId = document.getElementById('gestion_patient_id');
        const patientsList = document.getElementById('patients-list');
        const telephonesList = document.getElementById('telephones-list');
        const medecinSearch = document.getElementById('medecin_search');
        const medecinId = document.getElementById('medecin_id');
        const medecinsList = document.getElementById('medecins-list');

        // Fonction pour ajouter un effet visuel
        function addSyncEffect(element) {
            element.classList.add('field-highlight');
            setTimeout(() => {
                element.classList.remove('field-highlight');
            }, 1000);
        }

        // Fonction pour trouver un patient par nom
        function findPatientByName(nom) {
            const options = Array.from(patientsList.options);
            return options.find(opt => opt.value === nom);
        }

        // Fonction pour trouver un patient par téléphone
        function findPatientByPhone(phone) {
            const options = Array.from(telephonesList.options);
            return options.find(opt => opt.value === phone);
        }

        // Fonction pour trouver un médecin par nom
        function findMedecinByName(nom) {
            const options = Array.from(medecinsList.options);
            return options.find(opt => opt.value === nom);
        }

        // Synchronisation Patient -> Téléphone
        if (patientSearch && phoneInput && gestionPatientId && patientsList) {
            patientSearch.addEventListener('change', function() {
                const nomPatient = this.value.trim();
                const patientOption = findPatientByName(nomPatient);
                
                if (patientOption) {
                    const patientId = patientOption.getAttribute('data-id');
                    const telephone = patientOption.getAttribute('data-telephone');
                    
                    gestionPatientId.value = patientId;
                    
                    if (telephone) {
                        phoneInput.value = telephone;
                        addSyncEffect(phoneInput);
                    }
                    
                    addSyncEffect(patientSearch);
                }
            });

            patientSearch.addEventListener('input', function() {
                const nomPatient = this.value.trim();
                
                if (nomPatient === '') {
                    phoneInput.value = '';
                    gestionPatientId.value = '';
                    return;
                }
                
                const patientOption = findPatientByName(nomPatient);
                if (!patientOption) {
                    gestionPatientId.value = '';
                }
            });
        }

        // Synchronisation Téléphone -> Patient
        if (phoneInput && patientSearch && gestionPatientId && telephonesList) {
            phoneInput.addEventListener('change', function() {
                const phone = this.value.trim();
                const phoneOption = findPatientByPhone(phone);
                
                if (phoneOption) {
                    const patientId = phoneOption.getAttribute('data-id');
                    const nomPatient = phoneOption.getAttribute('data-nom');
                    
                    gestionPatientId.value = patientId;
                    
                    if (nomPatient) {
                        patientSearch.value = nomPatient;
                        addSyncEffect(patientSearch);
                    }
                    
                    addSyncEffect(phoneInput);
                }
            });

            phoneInput.addEventListener('input', function() {
                const phone = this.value.trim();
                
                if (phone === '') {
                    patientSearch.value = '';
                    gestionPatientId.value = '';
                    return;
                }
                
                const phoneOption = findPatientByPhone(phone);
                if (!phoneOption) {
                    gestionPatientId.value = '';
                }
            });
        }

        // Synchronisation Médecin
        if (medecinSearch && medecinId && medecinsList) {
            medecinSearch.addEventListener('change', function() {
                const nomMedecin = this.value.trim();
                const medecinOption = findMedecinByName(nomMedecin);
                
                if (medecinOption) {
                    const medId = medecinOption.getAttribute('data-id');
                    medecinId.value = medId;
                    addSyncEffect(medecinSearch);
                    // Appeler updateNumerosByDate si elle existe
                    if (typeof updateNumerosByDate === 'function') {
                        updateNumerosByDate();
                    }
                }
            });

            medecinSearch.addEventListener('input', function() {
                const nomMedecin = this.value.trim();
                
                if (nomMedecin === '') {
                    medecinId.value = '';
                    return;
                }
                
                const medecinOption = findMedecinByName(nomMedecin);
                if (!medecinOption) {
                    medecinId.value = '';
                }
            });
        }

        // Synchronisation Prescripteur
        const prescripteurSearch = document.getElementById('prescripteur_search');
        const prescripteurId = document.getElementById('prescripteur_id');
        const prescripteursList = document.getElementById('prescripteurs-list');

        // Fonction pour trouver un prescripteur par nom
        function findPrescripteurByName(nom) {
            const options = Array.from(prescripteursList.options);
            return options.find(opt => opt.value === nom);
        }

        if (prescripteurSearch && prescripteurId && prescripteursList) {
            prescripteurSearch.addEventListener('change', function() {
                const nomPrescripteur = this.value.trim();
                const prescripteurOption = findPrescripteurByName(nomPrescripteur);
                
                if (prescripteurOption) {
                    const prescId = prescripteurOption.getAttribute('data-id');
                    prescripteurId.value = prescId;
                    addSyncEffect(prescripteurSearch);
                } else {
                    // Si aucune correspondance, vider l'ID
                    prescripteurId.value = '';
                }
            });

            prescripteurSearch.addEventListener('input', function() {
                const nomPrescripteur = this.value.trim();
                
                if (nomPrescripteur === '') {
                    prescripteurId.value = 'extern';
                    return;
                }
                
                const prescripteurOption = findPrescripteurByName(nomPrescripteur);
                if (!prescripteurOption) {
                    prescripteurId.value = '';
                }
            });
        }
    });

    let prochainIdExamen = null;

    // Fonctions pour le modal d'examen
    function openExamenModal() {
        document.getElementById('examenModal').classList.remove('hidden');
        // Réinitialiser la sélection
        document.getElementById('modal_medicament_search').value = '';
        document.getElementById('modal_examen_search').value = '';
        document.getElementById('modal_quantite_div').style.display = 'none';
        document.getElementById('modal_quantite').value = 1;
    }

    function closeExamenModal() {
        document.getElementById('examenModal').classList.add('hidden');
    }

    function ajouterExamenDeModal() {
        console.log('Fonction ajouterExamenDeModal appelée');
        const medicamentSearch = document.getElementById('modal_medicament_search').value.trim();
        const examenSearch = document.getElementById('modal_examen_search').value.trim();
        console.log('Médicament recherché:', medicamentSearch);
        console.log('Examen recherché:', examenSearch);

        let selectedOption = null;
        let isMedicament = false;

        // Chercher dans les médicaments
        if (medicamentSearch) {
            const medicamentsList = document.querySelectorAll('#medicaments-list option');
            console.log('Nombre de médicaments disponibles:', medicamentsList.length);
            medicamentsList.forEach(opt => {
                if (opt.value === medicamentSearch) {
                    selectedOption = opt;
                    isMedicament = true;
                    console.log('Médicament trouvé:', opt);
                }
            });
        }

        // Chercher dans les examens
        if (!selectedOption && examenSearch) {
            const examensList = document.querySelectorAll('#examens-list option');
            console.log('Nombre d\'examens disponibles:', examensList.length);
            examensList.forEach(opt => {
                if (opt.value === examenSearch) {
                    selectedOption = opt;
                    isMedicament = false;
                    console.log('Examen trouvé:', opt);
                }
            });
        }

        if (!selectedOption) {
            console.warn('Aucun examen/médicament sélectionné');
            alert('Veuillez sélectionner un examen ou un médicament dans la liste déroulante.');
            return;
        }

        const quantiteInput = document.getElementById('modal_quantite');
        const quantite = parseInt(quantiteInput.value) || 1;

        const examen = {
            id: String(selectedOption.getAttribute('data-id')),
            nom: selectedOption.value,
            tarif: parseFloat(selectedOption.getAttribute('data-tarif')),
            quantite: quantite,
            total: parseFloat(selectedOption.getAttribute('data-tarif')) * quantite,
            isPharmacie: isMedicament
        };

        console.log('Examen à ajouter:', examen);

        // Vérifier si l'examen existe déjà dans la liste
        const existant = examensSelectionnes.find(e => String(e.id) === String(examen.id));
        if (existant) {
            console.log('Examen déjà existant, mise à jour de la quantité');
            existant.quantite += quantite;
            existant.total = existant.tarif * existant.quantite;
        } else {
            console.log('Nouvel examen, ajout à la liste');
            examensSelectionnes.push(examen);
        }

        console.log('Liste des examens après ajout:', examensSelectionnes);

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
        const medicamentSearch = document.getElementById('modal_medicament_search');
        const examenSearch = document.getElementById('modal_examen_search');
        const quantiteDiv = document.getElementById('modal_quantite_div');
        const stockInfo = document.getElementById('modal_stock_info');

        // Quand on sélectionne un médicament
        if (medicamentSearch) {
            medicamentSearch.addEventListener('input', function() {
                const value = this.value.trim();
                if (value) {
                    // Effacer le champ examen
                    examenSearch.value = '';
                    
                    // Trouver le médicament sélectionné
                    const medicamentsList = document.querySelectorAll('#medicaments-list option');
                    let selectedOption = null;
                    medicamentsList.forEach(opt => {
                        if (opt.value === value) {
                            selectedOption = opt;
                        }
                    });

                    if (selectedOption) {
                        quantiteDiv.style.display = 'block';
                        const stock = selectedOption.getAttribute('data-stock');
                        if (stock) {
                            stockInfo.textContent = `Stock disponible: ${stock} unités`;
                            stockInfo.className = parseInt(stock) > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
                        }
                    }
                }
            });
        }

        // Quand on sélectionne un examen
        if (examenSearch) {
            examenSearch.addEventListener('input', function() {
                const value = this.value.trim();
                if (value) {
                    // Effacer le champ médicament
                    medicamentSearch.value = '';
                    // Cacher le champ quantité (les examens n'ont pas de quantité)
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
            document.getElementById('examens_data').value = '';
        } else {
            // Mettre à jour les champs cachés pour la soumission
            document.getElementById('examens_multiple').value = 'true';
            document.getElementById('examens_data').value = JSON.stringify(examensSelectionnes);
        }

        container.innerHTML = html;

        // Mettre à jour le total
        document.getElementById('display_total').value = totalGeneral.toFixed(2);
        document.getElementById('total').value = totalGeneral.toFixed(2);
        
        console.log('afficherExamensSelectionnes() - Champs mis à jour:', {
            examens_multiple: document.getElementById('examens_multiple').value,
            examens_data: document.getElementById('examens_data').value,
            total: document.getElementById('total').value
        });
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
        // Utiliser la variable globale examensSelectionnes
        const displayTotal = document.getElementById('display_total');
        const totalHidden = document.getElementById('total');

        let totalGeneral = 0;

        if (Array.isArray(examensSelectionnes)) {
            examensSelectionnes.forEach(examen => {
                totalGeneral += examen.total || 0;
            });
        }

        if (displayTotal) {
            displayTotal.value = totalGeneral.toFixed(2);
        }
        if (totalHidden) {
            totalHidden.value = totalGeneral.toFixed(2);
        }

        // Mettre à jour l'affichage des examens sélectionnés si la fonction existe
        if (typeof afficherExamensSelectionnes === 'function') {
            afficherExamensSelectionnes();
        }
    }

    // Mettre à jour le total quand la quantité change
    document.addEventListener('DOMContentLoaded', function() {
        // Le select est désactivé, donc on n'a plus besoin d'écouter ses changements
        // Le total se met à jour automatiquement via la modal

        // Appeler updateTotal au chargement de la page
        setTimeout(function() {
            if (typeof updateTotal === 'function') {
                updateTotal();
            }
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
// Fonction pour mettre à jour les numéros d'entrée quand la date ou le médecin change
function updateNumerosByDate() {
    const dateExamen = document.querySelector('input[name="date_examen"]').value;
    const medecinSelect = document.querySelector('select[name="medecin_id"]');

    if (dateExamen && medecinSelect && medecinSelect.value) {
        // Faire une requête AJAX pour récupérer les nouveaux numéros
        fetch(`/api/next-numero-entree?medecin_id=${medecinSelect.value}&date_examen=${dateExamen}`)
            .then(response => response.json())
            .then(data => {
                if (data.numero) {
                    document.getElementById('numero_entree_display').value = data.numero;
                }
            })
            .catch(error => console.error('Erreur lors de la mise à jour du numéro d\'entrée:', error));
    }
}

// Ajouter l'événement de changement de date
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="date_examen"]');

    if (dateInput) {
        dateInput.addEventListener('change', updateNumerosByDate);
    }
});
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

<style>
    /* Style pour la synchronisation intelligente des champs avec effet visuel */
    .field-highlight {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-color: #2196f3 !important;
        transition: all 0.3s ease;
    }
</style>

@endsection
