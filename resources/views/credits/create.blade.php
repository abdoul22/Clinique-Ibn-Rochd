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
    <form action="{{ route('credits.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-bold font-medium text-gray-700 dark:text-gray-300">Type de source</label>
            <select name="source_type" id="source-type" class="form-select" required>
                <option value="">-- Sélectionner --</option>
                <option value="personnel">Personnel</option>
                <option value="assurance">Assurance</option>
            </select>
        </div>

        <div id="personnel-section" class="hidden">
            <label class="block text-bold font-medium text-gray-700 dark:text-gray-300">Personnel</label>
            <select name="source_id" id="personnel-select" class="form-select">
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
        </div>

        <div id="assurance-section" class="hidden">
            <label class="block text-bold font-medium text-gray-700 dark:text-gray-300">Assurance</label>
            <select name="source_id" id="assurance-select" class="form-select">
                @foreach($assurances as $assurance)
                @php
                $totalCredits = $assurance->credits->sum('montant');
                $totalPayes = $assurance->credits->sum('montant_paye');
                $creditActuel = $totalCredits - $totalPayes;
                @endphp
                <option value="{{ $assurance->id }}" data-credit-actuel="{{ $creditActuel }}">
                    {{ $assurance->nom }}
                </option>
                @endforeach
            </select>
            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1 mt-2">
                <p><strong class="font-bold text-orange-500 dark:text-orange-400">Crédit actuel :</strong> <span
                        id="assurance-credit-actuel">--</span> MRU</p>
            </div>
        </div>

        <div class="mb-4">
            <label for="mode_paiement_id" class="block text-bold font-medium text-gray-700 dark:text-gray-300">Mode de
                paiement</label>
            <select name="mode_paiement_id" id="mode_paiement_id" class="form-select">
                <option value="">-- Sélectionner --</option>
                @foreach($modes as $mode)
                <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
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
            <input type="number" name="montant" class="form-input" required>
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
            const selected = personnelSelect.options[personnelSelect.selectedIndex];
            const salaire = parseFloat(selected.dataset.salaire) || 0;
            const creditActuel = parseFloat(selected.dataset.creditActuel) || 0;
            const creditMax = parseFloat(selected.dataset.creditMax) || 0;
            const salaireNet = salaire - creditActuel;

            salaireEl.textContent = salaire.toLocaleString();
            creditActuelEl.textContent = creditActuel.toLocaleString();
            creditMaxEl.textContent = creditMax.toLocaleString();
            salaireNetEl.textContent = salaireNet.toLocaleString();
        }

        function updateAssuranceInfos() {
            const selected = assuranceSelect.options[assuranceSelect.selectedIndex];
            const creditActuel = parseFloat(selected.dataset.creditActuel) || 0;
            assuranceCreditActuelEl.textContent = creditActuel.toLocaleString();
        }

        sourceTypeSelect.addEventListener('change', function () {
            if (this.value === 'personnel') {
                personnelSection.classList.remove('hidden');
                assuranceSection.classList.add('hidden');
                personnelSelect.required = true;
                assuranceSelect.required = false;
                personnelSelect.disabled = false; // Correction ajoutée
                assuranceSelect.disabled = true;  // Correction ajoutée
                updatePersonnelInfos();

                // Masquer le mode de paiement pour les crédits personnel
                document.getElementById('mode_paiement_id').parentElement.classList.add('hidden');
                document.getElementById('mode_paiement_id').required = false;
                document.getElementById('mode-paiement-info').classList.remove('hidden');
                document.getElementById('mode-paiement-info-assurance').classList.add('hidden');
            } else if (this.value === 'assurance') {
                personnelSection.classList.add('hidden');
                assuranceSection.classList.remove('hidden');
                personnelSelect.required = false;
                assuranceSelect.required = true;
                personnelSelect.disabled = true;  // Correction ajoutée
                assuranceSelect.disabled = false; // Correction ajoutée
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
                personnelSelect.disabled = true;  // Correction ajoutée
                assuranceSelect.disabled = true;  // Correction ajoutée

                // Masquer le mode de paiement
                document.getElementById('mode_paiement_id').parentElement.classList.add('hidden');
                document.getElementById('mode_paiement_id').required = false;
                document.getElementById('mode-paiement-info').classList.add('hidden');
                document.getElementById('mode-paiement-info-assurance').classList.add('hidden');
            }
        });

        personnelSelect.addEventListener('change', updatePersonnelInfos);
        assuranceSelect.addEventListener('change', updateAssuranceInfos);
    });
</script>
@endpush
