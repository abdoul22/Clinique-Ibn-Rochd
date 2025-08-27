@extends('layouts.app')

@section('title', 'Nouveau Rendez-vous')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Nouveau Rendez-vous</h1>
            <a href="{{ route('rendezvous.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <form action="{{ route('rendezvous.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Patient -->
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Patient <span class="text-red-500">*</span>
                        </label>
                        <select name="patient_id" id="patient_id"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('patient_id') border-red-500 @enderror"
                            required>
                            <option value="">Sélectionner un patient</option>
                            @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" data-phone="{{ $patient->phone }}" {{
                                old('patient_id')==$patient->id ? 'selected' : '' }}>
                                {{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->phone }}
                            </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Numéro de téléphone -->
                    <div class="relative">
                        <label for="patient_phone"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Numéro de téléphone <span class="text-gray-500">(auto-rempli)</span>
                        </label>
                        <input type="tel" name="patient_phone" id="patient_phone" value="{{ old('patient_phone') }}"
                            placeholder="Saisir le numéro pour rechercher le patient..."
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('patient_phone') border-red-500 @enderror"
                            autocomplete="off">
                        <div id="phone_suggestions"
                            class="hidden absolute z-50 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-y-auto mt-1">
                        </div>
                        @error('patient_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Médecin -->
                    <div>
                        <label for="medecin_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Médecin <span class="text-red-500">*</span>
                        </label>
                        <select name="medecin_id" id="medecin_id"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('medecin_id') border-red-500 @enderror"
                            required>
                            <option value="">Sélectionner un médecin</option>
                            @php
                            $medecinsParFonction = $medecins->groupBy('fonction');
                            $ordrefonctions = ['Pr', 'Dr', 'Tss', 'SGF', 'IDE'];
                            @endphp
                            @foreach($ordrefonctions as $fonction)
                            @if(isset($medecinsParFonction[$fonction]) && $medecinsParFonction[$fonction]->count() > 0)
                            <optgroup label="{{ $medecinsParFonction[$fonction]->first()->fonction_complet }}s">
                                @foreach($medecinsParFonction[$fonction] as $medecin)
                                <option value="{{ $medecin->id }}" {{ old('medecin_id')==$medecin->id ? 'selected' : ''
                                    }}>
                                    {{ $medecin->nom_complet }} {{ $medecin->prenom }} - {{ $medecin->specialite }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                            @endforeach
                        </select>
                        @error('medecin_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date du rendez-vous -->
                    <div>
                        <label for="date_rdv" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date du rendez-vous <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_rdv" id="date_rdv" value="{{ old('date_rdv', date('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('date_rdv') border-red-500 @enderror"
                            required>
                        @error('date_rdv')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Numéro d'entrée -->
                    <div>
                        <label for="numero_entree"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Numéro d'entrée
                        </label>
                        <input type="text" name="numero_entree" id="numero_entree" value="1"
                            class="w-full font-bold bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-gray-900 dark:text-gray-100"
                            readonly>
                        <input type="hidden" name="numero_entree_hidden" id="numero_entree_hidden" value="1" />
                    </div>

                    <!-- Motif -->
                    <div class="md:col-span-2">
                        <label for="motif" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motif de consultation <span class="text-red-500">*</span>
                        </label>
                        <div class="flex space-x-2">
                            <select name="motif" id="motif"
                                class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('motif') border-red-500 @enderror"
                                required>
                                <option value="premier visite" selected>premier visite</option>
                                @foreach($motifs as $motif)
                                <option value="{{ $motif->nom }}" {{ old('motif')==$motif->nom ? 'selected' : '' }}>
                                    {{ $motif->nom }}
                                </option>
                                @endforeach
                                <option value="autre">Autre (saisir manuellement)</option>
                            </select>
                            <a href="{{ route('motifs.create') }}" target="_blank"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center"
                                title="Ajouter un nouveau motif">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                        <input type="text" name="motif_custom" id="motif_custom" value="{{ old('motif_custom') }}"
                            placeholder="Saisir un motif personnalisé..."
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 mt-2 hidden"
                            style="display: none;">
                        @error('motif')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes supplémentaires
                        </label>
                        <textarea name="notes" id="notes" rows="4" placeholder="Informations complémentaires..."
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-6">
                    <a href="{{ route('rendezvous.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Annuler
                    </a>
                    <button type="submit" id="submitBtn"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Création en cours...'; this.form.submit();">
                        <i class="fas fa-save mr-2"></i>Créer le rendez-vous
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validation côté client - avec vérifications de sécurité
        const form = document.querySelector('form');
        const medecinSelect = document.getElementById('medecin_id');
        const dateInput = document.getElementById('date_rdv');
        const heureInput = document.getElementById('heure_rdv');
        const motifSelect = document.getElementById('motif');
        const motifCustom = document.getElementById('motif_custom');
        const patientSelect = document.getElementById('patient_id');
        const patientPhone = document.getElementById('patient_phone');
        const phoneSuggestions = document.getElementById('phone_suggestions');

        // Données des patients pour la recherche
        const patientsData = [
            @foreach($patients as $patient)
            {
                id: {{ $patient->id }},
                name: '{{ $patient->first_name }} {{ $patient->last_name }}',
                phone: '{{ $patient->phone }}'
            },
            @endforeach
        ];

        // Auto-remplir le téléphone quand on sélectionne un patient - AVEC VÉRIFICATION
        if (patientSelect && patientPhone) {
            patientSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    const phone = selectedOption.getAttribute('data-phone');
                    patientPhone.value = phone || '';
                } else {
                    patientPhone.value = '';
                }
            });
        }

        // Recherche de patient par numéro de téléphone - AVEC VÉRIFICATIONS
        if (patientPhone && phoneSuggestions) {
            patientPhone.addEventListener('input', function() {
                const phone = this.value.trim();

                if (phone.length < 3) {
                    phoneSuggestions.classList.add('hidden');
                    return;
                }

                // Filtrer les patients par numéro de téléphone
                const filteredPatients = patientsData.filter(patient =>
                    patient.phone.includes(phone)
                );

                if (filteredPatients.length > 0) {
                    displayPhoneSuggestions(filteredPatients);
                } else {
                    phoneSuggestions.classList.add('hidden');
                }
            });
        }

        // Afficher les suggestions
        function displayPhoneSuggestions(patients) {
            phoneSuggestions.innerHTML = '';

            patients.forEach(patient => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0';
                div.innerHTML = `
                    <div class="font-medium text-gray-900 dark:text-gray-200">${patient.name}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">${patient.phone}</div>
                `;

                div.addEventListener('click', function() {
                    patientPhone.value = patient.phone;
                    patientSelect.value = patient.id;
                    phoneSuggestions.classList.add('hidden');
                });

                phoneSuggestions.appendChild(div);
            });

            phoneSuggestions.classList.remove('hidden');
        }

        // Masquer les suggestions quand on clique ailleurs
        if (patientPhone && phoneSuggestions) {
            document.addEventListener('click', function(e) {
                if (!patientPhone.contains(e.target) && !phoneSuggestions.contains(e.target)) {
                    phoneSuggestions.classList.add('hidden');
                }
            });
        }

        // Gestion du champ motif personnalisé
        if (motifSelect && motifCustom) {
            motifSelect.addEventListener('change', function() {
                if (this.value === 'autre') {
                    motifCustom.style.display = 'block';
                    motifCustom.required = true;
                    motifCustom.focus();
                } else {
                    motifCustom.style.display = 'none';
                    motifCustom.required = false;
                    motifCustom.value = '';
                }
            });
        }

        // Vérifier la disponibilité du créneau
        async function checkAvailability() {
            // Vérifications de sécurité
            if (!medecinSelect || !dateInput || !heureInput) {
                return;
            }

            const medecinId = medecinSelect.value;
            const date = dateInput.value;
            const heure = heureInput.value;

            if (medecinId && date && heure) {
                try {
                    const response = await fetch(`/api/rendezvous/check-availability?medecin_id=${medecinId}&date=${date}&heure=${heure}`);
                    const data = await response.json();

                    if (!data.available) {
                        alert('Ce créneau n\'est pas disponible pour ce médecin.');
                        heureInput.focus();
                    }
                } catch (error) {
                    console.error('Erreur lors de la vérification:', error);
                }
            }
        }

        // Écouter les changements
        if (medecinSelect) {
            medecinSelect.addEventListener('change', checkAvailability);
        }
        if (dateInput) {
            dateInput.addEventListener('change', checkAvailability);
        }
        if (heureInput) {
            heureInput.addEventListener('change', checkAvailability);
        }

        // Initialiser l'état du champ personnalisé
        if (motifSelect && motifCustom) {
            if (motifSelect.value === 'autre') {
                motifCustom.style.display = 'block';
                motifCustom.required = true;
            }
        }

        // Initialiser le téléphone si un patient est déjà sélectionné
        if (patientSelect && patientPhone) {
            const selectedOption = patientSelect.options[patientSelect.selectedIndex];
            const phone = selectedOption ? selectedOption.getAttribute('data-phone') : null;
            if (phone) {
                patientPhone.value = phone;
            }
        }

        // Mettre à jour le numéro d'entrée quand un médecin est sélectionné
        const numeroEntreeDisplay = document.getElementById('numero_entree');
        const numeroEntreeHidden = document.getElementById('numero_entree_hidden');

        // Numéros par médecin passés depuis le contrôleur
        const numerosParMedecin = @json($numeros_par_medecin);

        // Fonction pour mettre à jour le numéro d'entrée
        function updateNumeroEntree() {
            if (medecinSelect && numeroEntreeDisplay) {
                const medecinId = medecinSelect.value;

                if (medecinId && numerosParMedecin[medecinId]) {
                    // Utiliser le numéro pré-calculé pour ce médecin
                    const numeroPrevu = numerosParMedecin[medecinId];

                    numeroEntreeDisplay.value = numeroPrevu;
                    if (numeroEntreeHidden) {
                        numeroEntreeHidden.value = numeroPrevu;
                    }
                } else {
                    // Aucun médecin sélectionné - utiliser le premier numéro disponible
                    const premierNumero = Object.values(numerosParMedecin)[0] || 1;
                    numeroEntreeDisplay.value = premierNumero;
                    if (numeroEntreeHidden) {
                        numeroEntreeHidden.value = premierNumero;
                    }
                }
            }
        }

        // Initialiser le numéro d'entrée au chargement
        if (medecinSelect && numeroEntreeDisplay) {
            // Initialiser avec le premier médecin ou le numéro par défaut
            updateNumeroEntree();

            medecinSelect.addEventListener('change', function() {
                updateNumeroEntree();
            });
        }
    });
</script>
@endpush