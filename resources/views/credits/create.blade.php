@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg">

    <h1 class="page-title mb-4">Ajouter un Crédit</h1>
    @if($errors->any())
    <div class="alert alert-error mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-error mb-4">
        <ul class="list-disc list-inside text-sm">
            <li>{{ session('error') }}</li>
        </ul>
    </div>
    @endif
    <form action="{{ route('credits.store') }}" method="POST"
        class="space-y-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow">
        @csrf

        <!-- Sélection de la source, le champ name=source_id est porté par le select -->

        <div>
            <label class="block text-bold font-medium text-gray-700 dark:text-gray-300">Type de source</label>
            <select name="source_type" id="source-type" class="form-select" required>
                <option value="personnel" selected>Personnel</option>
            </select>
        </div>

        <div id="personnel-section" class="">
            <label class="block text-bold font-medium text-gray-700 dark:text-gray-300">Personnel</label>
            @if($personnels->count() > 0)
            <select id="personnel-select" name="source_id" class="form-select">
                @foreach($personnels as $personnel)
                @php
                $personnel->updateCredit(); // Mettre à jour le crédit actuel
                $montantMaxCredit = $personnel->montant_max_credit;
                @endphp
                <option value="{{ $personnel->id }}" data-salaire="{{ $personnel->salaire }}"
                    data-credit-actuel="{{ $personnel->credit }}" data-credit-max="{{ $montantMaxCredit }}">
                    {{ $personnel->nom }} (Salaire: {{ number_format($personnel->salaire, 0, ',', ' ') }} MRU)
                </option>
                @endforeach
            </select>
            <div
                class="text-sm text-gray-700 dark:text-gray-300 space-y-1 mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                <p><strong class="font-bold text-green-500 dark:text-green-400">💰 Salaire :</strong> <span
                        id="salaire">--</span> MRU</p>
                <p><strong class="font-bold text-orange-500 dark:text-orange-400">💳 Crédit actuel :</strong> <span
                        id="credit-actuel">--</span> MRU</p>
                <p><strong class="font-bold text-blue-500 dark:text-blue-400">📊 Crédit maximum possible :</strong>
                    <span id="credit-max">--</span> MRU
                </p>
                <p><strong class="font-bold text-purple-500 dark:text-purple-400">💵 Salaire net après déduction
                        :</strong> <span id="salaire-net">--</span> MRU</p>
            </div>
            @else
            <div
                class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Aucun personnel trouvé
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>Vous devez d'abord créer du personnel avant de pouvoir leur donner des crédits.</p>
                            <p class="mt-1">
                                <a href="{{ route('personnels.create') }}"
                                    class="font-medium underline text-yellow-800 dark:text-yellow-200 hover:text-yellow-600 dark:hover:text-yellow-100">
                                    Cliquez ici pour ajouter du personnel →
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div id="assurance-section" class="hidden">
            <label class="block text-bold font-medium text-gray-700 dark:text-gray-300">Assurance</label>
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            Les crédits d'assurance sont créés automatiquement lors de la facturation d'un patient
                            assuré.
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <p>Utilisez cette page uniquement pour accorder des crédits au personnel.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label for="mode_paiement_id" class="block text-bold font-medium text-gray-700 dark:text-gray-300">Mode de
                paiement</label>
            <select name="mode_paiement_id" id="mode_paiement_id" class="form-select">
                @foreach($modes as $mode)
                <option value="{{ $mode }}" {{ $mode==='espèces' ? 'selected' : '' }}>{{ ucfirst($mode) }}</option>
                @endforeach
            </select>
            <div id="mode-paiement-info" class="text-sm text-gray-600 dark:text-gray-400 mt-2 hidden">
                <p class="text-blue-600 dark:text-blue-400">
                    <strong>Note :</strong> Les crédits du personnel sont payés par déduction salariale, pas par mode de
                    paiement.
                </p>
            </div>
            <div id="mode-paiement-info-assurance" class="text-sm text-gray-600 dark:text-gray-400 mt-2 hidden">
                <p class="text-green-600 dark:text-green-400">
                    <strong>Note :</strong> Les crédits des assurances sont payés lors du remboursement par l'assurance.
                </p>
            </div>
        </div>

        <div>
            <label class="block text-bold font-medium text-gray-700 dark:text-gray-300">Montant</label>
            <input type="number" name="montant" class="form-input" required placeholder="Ex: 5000">
        </div>

        <div>
            <button type="submit" id="submitBtn" class="form-button"
                onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sourceTypeSelect = document.getElementById('source-type');
        const personnelSection = document.getElementById('personnel-section');
        const assuranceSection = document.getElementById('assurance-section');
        const personnelSelect = document.getElementById('personnel-select');
        const assuranceSelect = document.getElementById('assurance-select');
        const salaireEl = document.getElementById('salaire');
        const creditActuelEl = document.getElementById('credit-actuel');
        const creditMaxEl = document.getElementById('credit-max');
        const salaireNetEl = document.getElementById('salaire-net');
        const assuranceCreditActuelEl = document.getElementById('assurance-credit-actuel');

        function updatePersonnelInfos() {
            const selected = personnelSelect?.options[personnelSelect.selectedIndex];
            if (!selected) return;

            const salaire = parseFloat(selected.dataset.salaire) || 0;
            const creditActuel = parseFloat(selected.dataset.creditActuel) || 0;
            const creditMax = parseFloat(selected.dataset.creditMax) || 0;
            const salaireNet = salaire - creditActuel;

            if (salaireEl) salaireEl.textContent = salaire.toLocaleString();
            if (creditActuelEl) creditActuelEl.textContent = creditActuel.toLocaleString();
            if (creditMaxEl) creditMaxEl.textContent = creditMax.toLocaleString();
            if (salaireNetEl) salaireNetEl.textContent = salaireNet.toLocaleString();
        }

        function updateAssuranceInfos() {
            const selected = assuranceSelect?.options[assuranceSelect.selectedIndex];
            if (!selected) return;

            const creditActuel = parseFloat(selected.dataset.creditActuel) || 0;
            if (assuranceCreditActuelEl) assuranceCreditActuelEl.textContent = creditActuel.toLocaleString();
        }

        function applySourceUI(value){
            if (value === 'personnel') {
                personnelSection.classList.remove('hidden');
                assuranceSection.classList.add('hidden');
                personnelSelect.required = true;
                assuranceSelect.required = false;
                // Assigner le name pour soumission
                if (personnelSelect) { personnelSelect.setAttribute('name', 'source_id'); }
                if (assuranceSelect) { assuranceSelect.removeAttribute('name'); }
                updatePersonnelInfos();

                // Masquer le mode de paiement pour les crédits personnel
                document.getElementById('mode-paiement_id').parentElement.classList.remove('hidden');
                document.getElementById('mode-paiement_id').required = false;
                document.getElementById('mode-paiement-info').classList.remove('hidden');
                document.getElementById('mode-paiement-info-assurance').classList.add('hidden');
            } else if (value === 'assurance') {
                personnelSection.classList.add('hidden');
                assuranceSection.classList.remove('hidden');
                personnelSelect.required = false;
                assuranceSelect.required = true;
                if (assuranceSelect) { assuranceSelect.setAttribute('name', 'source_id'); }
                if (personnelSelect) { personnelSelect.removeAttribute('name'); }
                updateAssuranceInfos();

                // Masquer le mode de paiement pour les assurances
                document.getElementById('mode_paiement_id').parentElement.classList.add('hidden');
                document.getElementById('mode_paiement_id').required = false;
                document.getElementById('mode-paiement-info').classList.add('hidden');
                document.getElementById('mode-paiement-info-assurance').classList.remove('hidden');
            } else {
                personnelSection.classList.add('hidden');
                assuranceSection.classList.add('hidden');
                personnelSelect.required = false;
                assuranceSelect.required = false;
                if (personnelSelect) { personnelSelect.removeAttribute('name'); }
                if (assuranceSelect) { assuranceSelect.removeAttribute('name'); }

                // Masquer le mode de paiement
                document.getElementById('mode_paiement_id').parentElement.classList.add('hidden');
                document.getElementById('mode_paiement_id').required = false;
                document.getElementById('mode-paiement-info').classList.add('hidden');
                document.getElementById('mode-paiement-info-assurance').classList.add('hidden');
            }
        }

        sourceTypeSelect.addEventListener('change', function(){ applySourceUI(this.value); });

        // Defaults at load
        sourceTypeSelect.value = 'personnel';
        applySourceUI('personnel');
        // Default mode paiement 'espèces' (déjà présélectionné dans le select)

        // Écouter les changements sur les selects pour mettre à jour le champ hidden
        if (personnelSelect) {
            personnelSelect.addEventListener('change', function() {
                if (sourceTypeSelect.value === 'personnel') {
                    document.getElementById('source_id_hidden').value = this.value;
                    updatePersonnelInfos();
                }
            });
        }

        if (assuranceSelect) {
            assuranceSelect.addEventListener('change', function() {
                if (sourceTypeSelect.value === 'assurance') {
                    document.getElementById('source_id_hidden').value = this.value;
                    updateAssuranceInfos();
                }
            });
        }
    });
</script>
@endpush
