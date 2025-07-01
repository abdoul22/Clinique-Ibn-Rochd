@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-700 dark:text-gray-100">Ajouter un Paiement</h2>

    @if ($errors->any())
    <div class="bg-red-100 text-red-800 p-4 mb-4 rounded dark:bg-red-900/20 dark:text-red-300">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('modepaiements.store') }}" method="POST"
        class="space-y-6 bg-white p-6 shadow rounded dark:bg-gray-900 dark:text-gray-100 dark:shadow-lg dark:border dark:border-gray-700">
        @csrf

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type de
                paiement</label>
            <select name="type" id="type" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400">
                <option value="">-- Sélectionnez --</option>
                <option value="espece">Espèces</option>
                <option value="bankily">Bankily</option>
                <option value="masrivi">Masrivi</option>
            </select>
        </div>

        <div>
            <label for="montant" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Montant
                (MRU)</label>
            <input type="number" name="montant" id="montant" step="0.01" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400" />
        </div>

        <div>
            <label for="caisse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Facture
                (Caisse)</label>
            <select name="caisse_id" id="caisse_id" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400">
                <option value="">-- Sélectionnez une facture --</option>
                @foreach ($caisses as $caisse)
                <option value="{{ $caisse->id }}">Facture n°{{ $caisse->id }} - Total: {{ number_format($caisse->total,
                    2, ',', ' ') }} MRU</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end">
            <a href="{{ route('modepaiements.index') }}"
                class="mr-4 text-gray-600 hover:underline dark:text-gray-400 dark:hover:text-gray-200">Annuler</a>
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 dark:text-gray-100">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
