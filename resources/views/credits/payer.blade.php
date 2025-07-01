@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4 dark:text-gray-100">Payer un Crédit</h1>

    <div class="mb-4">
        <a href="{{ route('credits.index') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-colors duration-200 dark:bg-indigo-700 dark:hover:bg-indigo-800 dark:text-gray-100">←
            Retour à la liste des crédits</a>
    </div>

    <div
        class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg mx-auto dark:bg-gray-800 dark:shadow-lg dark:border dark:border-gray-700">
        <form method="POST" action="{{ route('credits.payer.store', ['credit' => $credit->id]) }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Nom de la source :</label>
                <p class="text-lg dark:text-gray-100">{{ $credit->source?->nom ?? '---' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Type :</label>
                <p class="dark:text-gray-100">{{ class_basename($credit->source_type) === 'Personnel' ? 'Personnel' :
                    'Assurance' }}</p>
            </div>

            <div class="mb-4">
                <label for="montant" class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Montant à payer
                    :</label>
                <input type="number" id="montant" name="montant" step="0.01" min="1"
                    max="{{ $credit->montant - $credit->montant_paye }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400"
                    required>
            </div>

            <div class="mb-4">
                <label for="mode_paiement_id" class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Mode de
                    paiement :</label>
                <select name="mode_paiement_id" id="mode_paiement_id" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400">
                    <option value="">-- Sélectionner --</option>
                    @foreach($modes as $mode)
                    <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 dark:bg-green-700 dark:hover:bg-green-800 dark:text-gray-100">
                Enregistrer le paiement
            </button>
        </form>
    </div>
</div>
@endsection
