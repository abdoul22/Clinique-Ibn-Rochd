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
            <select name="mode_paiement_id" id="mode_paiement_id" required class="form-select">
                <option value="">-- Sélectionner --</option>
                @foreach($modes as $mode)
                <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-bold font-medium text-gray-700 dark:text-gray-300">Montant</label>
            <input type="number" name="montant" class="form-input" required>
        </div>

        <div>
            <button type="submit" class="form-button">
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

        sourceTypeSelect.addEventListener('change', () => {
            const selectedType = sourceTypeSelect.value;

            personnelSection.classList.add('hidden');
            assuranceSection.classList.add('hidden');

            if (selectedType === 'personnel') {
                personnelSection.classList.remove('hidden');
            } else if (selectedType === 'assurance') {
                assuranceSection.classList.remove('hidden');
            }
        });

        personnelSelect.addEventListener('change', updatePersonnelInfos);
        assuranceSelect.addEventListener('change', updateAssuranceInfos);
    });
</script>
@endpush
