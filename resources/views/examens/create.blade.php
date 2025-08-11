@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ajouter un examen</h2>
        <a href="{{ route('examens.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">← Retour
            à la liste</a>
    </div>

    <form method="POST" action="{{ route('examens.store') }}">
        @csrf

        <div class="grid gap-4">
            <!-- Nom -->
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom de
                    l'examen</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('nom')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Service -->
            <div>
                <label for="idsvc" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Service
                    associé</label>
                <select name="idsvc" id="idsvc"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                    <option value="">-- Sélectionner un service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" data-type="{{ $service->type_service }}" {{
                        old('idsvc')==$service->id ? 'selected' : '' }}>
                        {{ $service->nom }}
                    </option>
                    @endforeach
                </select>
                @error('idsvc')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Médicaments (affiché seulement si Pharmacie est sélectionné) -->
            <div id="medicaments-section" class="hidden">
                <label for="medicament_id"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200">Médicament</label>
                <div class="relative">
                    <input type="text" id="medicament_search" placeholder="Rechercher un médicament..."
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white mb-2"
                        autocomplete="off">
                    <div id="medicament_suggestions"
                        class="absolute z-10 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded shadow hidden max-h-56 overflow-auto">
                    </div>
                    <select name="medicament_id" id="medicament_id"
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                        <option value="">-- Sélectionner un médicament --</option>
                        @foreach($medicaments as $medicament)
                        <option value="{{ $medicament->id }}" data-prix="{{ $medicament->prix_vente }}"
                            data-nom="{{ strtolower($medicament->nom_medicament) }}">
                            {{ $medicament->nom_medicament }} - {{ number_format($medicament->prix_vente, 0, ',', ' ')
                            }} MRU (Stock: {{ $medicament->stock }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tarif -->
            <div>
                <label for="tarif" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tarif (en
                    MRU)</label>
                <input type="number" name="tarif" id="tarif" value="{{ old('tarif') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('tarif')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Part Medcin -->
            <div>
                <label for="tarif" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Part Medecin (en
                    MRU)</label>
                <input type="number" name="part_medecin" id="part_medecin" value="{{ old('part_medecin') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('part_medecin')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Part Cabinet -->
            <div>
                <label for="part_cabinet" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Part
                    Cabinet (en MRU)</label>
                <input type="number" name="part_cabinet" id="part_cabinet" value="{{ old('part_cabinet') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('part_cabinet')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" id="submitBtn"
                class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition font-semibold shadow"
                onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                Ajouter
            </button>
        </div>
    </form>
    <p class="text-sm text-gray-500 dark:text-gray-300 mt-4">
        Remplissez soit la <strong>part médecin</strong> soit la <strong>part cabinet</strong>, l'autre sera calculée
        automatiquement selon le tarif.
    </p>

</div>
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tarifInput = document.getElementById('tarif');
        const partMedecinInput = document.getElementById('part_medecin');
        const partCabinetInput = document.getElementById('part_cabinet');
        const serviceSelect = document.getElementById('idsvc');
        const medicamentsSection = document.getElementById('medicaments-section');
        const medicamentSelect = document.getElementById('medicament_id');
        const medicamentSearch = document.getElementById('medicament_search');
        const medicamentSuggestions = document.getElementById('medicament_suggestions');
        const nomInput = document.getElementById('nom');

        // Gestion de l'affichage des médicaments
        function toggleMedicamentsSection() {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const type = selectedOption ? (selectedOption.dataset.type || '').toUpperCase() : '';
            const isPharmacie = type === 'PHARMACIE';

            if (isPharmacie) {
                medicamentsSection.classList.remove('hidden');
                medicamentSelect.required = true;
                // Rendre le nom dérivé du médicament sélectionné
                nomInput.readOnly = true;
                nomInput.placeholder = 'Choisissez un médicament';
                // Forcer la part médecin à 0 et part cabinet = tarif si déjà renseigné
                partMedecinInput.value = 0;
                partMedecinInput.readOnly = true;
                if (tarifInput.value) {
                    partCabinetInput.value = parseFloat(tarifInput.value).toFixed(2);
                }
            } else {
                medicamentsSection.classList.add('hidden');
                medicamentSelect.required = false;
                medicamentSelect.value = '';
                nomInput.readOnly = false;
                nomInput.placeholder = '';
                partMedecinInput.readOnly = false;
                medicamentSuggestions.classList.add('hidden');
            }
        }

        // Mise à jour automatique du tarif lors de la sélection d'un médicament
        function updateTarifFromMedicament() {
            const selectedOption = medicamentSelect.options[medicamentSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.prix) {
                const prix = parseFloat(selectedOption.dataset.prix);
                tarifInput.value = prix;

                // Déclencher le calcul automatique des parts
                if (serviceSelect.options[serviceSelect.selectedIndex] && (serviceSelect.options[serviceSelect.selectedIndex].dataset.type || '').toUpperCase() === 'PHARMACIE') {
                    partMedecinInput.value = 0;
                    partCabinetInput.value = prix.toFixed(2);
                } else {
                    if (lastModified === 'medecin') {
                        calculateMissingPart('medecin');
                    } else if (lastModified === 'cabinet') {
                        calculateMissingPart('cabinet');
                    }
                }
                // Nom d'examen = nom du médicament
                const nomMed = selectedOption.text.split(' - ')[0];
                nomInput.value = nomMed;
            }
        }

        // Autocomplete réactif pour médicaments
        function filterMedicaments() {
            const searchTerm = (medicamentSearch.value || '').toLowerCase();
            const options = Array.from(medicamentSelect.querySelectorAll('option')).filter(o => o.value !== '');

            const matched = options.filter(o => (o.dataset.nom || '').includes(searchTerm)).slice(0, 10);

            if (searchTerm.length === 0 || matched.length === 0) {
                medicamentSuggestions.classList.add('hidden');
                medicamentSuggestions.innerHTML = '';
                return;
            }

            medicamentSuggestions.innerHTML = matched.map(o => {
                const label = o.textContent;
                return `<div class="px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" data-value="${o.value}">${label}</div>`;
            }).join('');
            medicamentSuggestions.classList.remove('hidden');
        }

        // Sélection depuis la liste des suggestions
        medicamentSuggestions.addEventListener('click', function (e) {
            const item = e.target.closest('[data-value]');
            if (!item) return;
            const value = item.getAttribute('data-value');
            medicamentSelect.value = value;
            medicamentSuggestions.classList.add('hidden');
            updateTarifFromMedicament();
        });

        // Fermer la liste si clic à l'extérieur
        document.addEventListener('click', function (e) {
            if (!medicamentSuggestions.contains(e.target) && e.target !== medicamentSearch) {
                medicamentSuggestions.classList.add('hidden');
            }
        });

        // Écouter les changements de service
        serviceSelect.addEventListener('change', toggleMedicamentsSection);

        // Écouter les changements de médicament
        medicamentSelect.addEventListener('change', updateTarifFromMedicament);

        // Écouter la recherche de médicaments
        medicamentSearch.addEventListener('input', filterMedicaments);

        // Initialiser l'état au chargement
        toggleMedicamentsSection();

        let lastModified = null;
        let typingTimeout = null;

        function calculateMissingPart(source) {
            const tarif = parseFloat(tarifInput.value);
            const partMedecin = parseFloat(partMedecinInput.value);
            const partCabinet = parseFloat(partCabinetInput.value);

            if (isNaN(tarif)) {
                partMedecinInput.value = '';
                partCabinetInput.value = '';
                return;
            }

            if (source === 'medecin' && !isNaN(partMedecin)) {
                const cabinet = tarif - partMedecin;
                partCabinetInput.value = cabinet >= 0 ? cabinet.toFixed(2) : '';
            }

            if (source === 'cabinet' && !isNaN(partCabinet)) {
                const medecin = tarif - partCabinet;
                partMedecinInput.value = medecin >= 0 ? medecin.toFixed(2) : '';
            }
        }

        function activateAllFields() {
            partMedecinInput.disabled = false;
            partCabinetInput.disabled = false;
        }

        partMedecinInput.addEventListener('input', function () {
            clearTimeout(typingTimeout);
            lastModified = 'medecin';
            partCabinetInput.disabled = true;

            const partCabinet = parseFloat(partCabinetInput.value);
            if (!isNaN(partCabinet)) {
                partCabinetInput.value = ''; // efface si l'utilisateur modifie manuellement les deux
            }

            calculateMissingPart('medecin');

            typingTimeout = setTimeout(() => {
                activateAllFields();
            }, 1000);
        });

        partCabinetInput.addEventListener('input', function () {
            clearTimeout(typingTimeout);
            lastModified = 'cabinet';
            partMedecinInput.disabled = true;

            const partMedecin = parseFloat(partMedecinInput.value);
            if (!isNaN(partMedecin)) {
                partMedecinInput.value = ''; // efface si l'utilisateur modifie manuellement les deux
            }

            calculateMissingPart('cabinet');

            typingTimeout = setTimeout(() => {
                activateAllFields();
            }, 1000);
        });

        tarifInput.addEventListener('input', function () {
            // Si PHARMACIE: part médecin 0 et part cabinet = tarif
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const type = selectedOption ? (selectedOption.dataset.type || '').toUpperCase() : '';
            if (type === 'PHARMACIE') {
                const prix = parseFloat(tarifInput.value || '0');
                partMedecinInput.value = 0;
                partCabinetInput.value = isNaN(prix) ? '' : prix.toFixed(2);
                return;
            }
            if (lastModified === 'medecin') {
                calculateMissingPart('medecin');
            } else if (lastModified === 'cabinet') {
                calculateMissingPart('cabinet');
            }
        });
    });
</script>
@endpush
@endsection