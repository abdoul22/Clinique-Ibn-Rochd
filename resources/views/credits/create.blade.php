@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg">

    <h1 class="text-2xl font-bold mb-4">Ajouter un Crédit Personnel</h1>
    @if($errors->any())
    <div class="bg-red-100 text-red-700 border border-red-400 p-3 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('credits.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-bold font-medium text-gray-700">Personnel</label>
            <select name="source_id" id="personnel-select" class="w-full border rounded px-3 py-2">
                @foreach($personnels as $personnel)
                @php
                $totalCredits = $personnel->credits->sum('montant');
                $totalPayes = $personnel->credits->sum('montant_paye');
                $creditActuel = $totalCredits - $totalPayes;
                $creditEligible = $personnel->salaire - $creditActuel;
                @endphp
                <option value="{{ $personnel->id }}" data-salaire="{{ $personnel->salaire }}"
                    data-credit-actuel="{{ $creditActuel }}" data-credit-eligible="{{ $creditEligible }}">
                    {{ $personnel->nom }}
                </option>
                @endforeach
            </select>
        </div>
<div class="mb-4">
            <label for="mode_paiement_id" class="block text-bold font-medium">Mode de paiement</label>
            <select name="mode_paiement_id" id="mode_paiement_id" required class="w-full border rounded p-2">
                <option value="">-- Sélectionner --</option>
               @foreach($modes as $mode)
                <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
                @endforeach
            </select>
        </div>
        <div class="text-sm text-gray-700 space-y-1">
            <p><strong class="font-bold text-green-500">Salaire :</strong> <span id="salaire">--</span> MRU</p>
            <p><strong class="font-bold text-orange-500">Crédit actuel :</strong> <span id="credit-actuel">--</span> MRU
            </p>
            <p><strong class="font-bold text-indigo-500">Crédit éligible :</strong> <span id="credit-eligible">--</span>
                MRU</p>
        </div>

        <div>
            <label class="block text-bold font-medium text-gray-700">Montant</label>
            <input type="number" name="montant" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('personnel-select');
        const salaireEl = document.getElementById('salaire');
        const creditActuelEl = document.getElementById('credit-actuel');
        const creditEligibleEl = document.getElementById('credit-eligible');
        const montantInput = document.querySelector('input[name="montant"]');
        const form = document.querySelector('form');

        let creditEligible = 0;

        function updateInfos() {
            const selected = select.options[select.selectedIndex];
            const salaire = parseFloat(selected.dataset.salaire) || 0;
            const creditActuel = parseFloat(selected.dataset.creditActuel) || 0;
            creditEligible = parseFloat(selected.dataset.creditEligible) || 0;

            salaireEl.textContent = salaire.toLocaleString();
            creditActuelEl.textContent = creditActuel.toLocaleString();
            creditEligibleEl.textContent = creditEligible.toLocaleString();
        }

        select.addEventListener('change', updateInfos);
        updateInfos(); // Initial

        form.addEventListener('submit', (e) => {
            const montant = parseFloat(montantInput.value);
            if (montant > creditEligible) {
                e.preventDefault();
                alert("Montant supérieur au crédit éligible !");
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('personnel-select');
        const salaireEl = document.getElementById('salaire');
        const creditActuelEl = document.getElementById('credit-actuel');
        const creditEligibleEl = document.getElementById('credit-eligible');

        function updateInfos() {
            const selected = select.options[select.selectedIndex];
            const salaire = parseFloat(selected.dataset.salaire) || 0;
            const creditActuel = parseFloat(selected.dataset.creditActuel) || 0;
            const creditEligible = parseFloat(selected.dataset.creditEligible) || 0;

            salaireEl.textContent = salaire.toLocaleString();
            creditActuelEl.textContent = creditActuel.toLocaleString();
            creditEligibleEl.textContent = creditEligible.toLocaleString();
        }

        select.addEventListener('change', updateInfos);
        updateInfos(); // Initial load
    });
</script>
@endpush
