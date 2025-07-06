@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4 dark:text-gray-100">Payer un Crédit Personnel par Déduction Salariale</h1>

    <div class="mb-4">
        <a href="{{ route('credits.index') }}"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-colors duration-200 dark:bg-indigo-700 dark:hover:bg-indigo-800 dark:text-gray-100">←
            Retour à la liste des crédits</a>
    </div>

    <div
        class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg mx-auto dark:bg-gray-800 dark:shadow-lg dark:border dark:border-gray-700">
        <form method="POST" action="{{ route('credits.payer.salaire', ['credit' => $credit->id]) }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Personnel :</label>
                <p class="text-lg dark:text-gray-100">{{ $credit->source?->nom ?? '---' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Salaire actuel :</label>
                <p class="text-lg text-green-600 dark:text-green-400 font-semibold">
                    {{ number_format($credit->source?->salaire ?? 0, 0, ',', ' ') }} MRU
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Crédit total :</label>
                <p class="text-lg text-red-600 dark:text-red-400 font-semibold">
                    {{ number_format($credit->montant, 0, ',', ' ') }} MRU
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Montant déjà payé :</label>
                <p class="text-lg text-blue-600 dark:text-blue-400 font-semibold">
                    {{ number_format($credit->montant_paye, 0, ',', ' ') }} MRU
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Montant restant :</label>
                <p class="text-lg text-orange-600 dark:text-orange-400 font-semibold">
                    {{ number_format($credit->montant - $credit->montant_paye, 0, ',', ' ') }} MRU
                </p>
            </div>

            <div class="mb-4">
                <label for="montant" class="block text-gray-700 font-bold mb-2 dark:text-gray-200">Montant à déduire du
                    salaire :</label>
                <input type="number" id="montant" name="montant" step="0.01" min="1"
                    max="{{ $credit->montant - $credit->montant_paye }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-900 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400"
                    required>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Maximum : {{ number_format($credit->montant - $credit->montant_paye, 0, ',', ' ') }} MRU
                </p>
            </div>

            <div
                class="bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-yellow-800 dark:text-yellow-200 text-sm font-medium">
                        Attention : Ce montant sera déduit du salaire du personnel et enregistré comme une dépense
                        automatique.
                    </p>
                </div>
            </div>

            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 dark:bg-green-700 dark:hover:bg-green-800 dark:text-gray-100 w-full">
                Enregistrer la déduction salariale
            </button>
        </form>
    </div>
</div>
@endsection
