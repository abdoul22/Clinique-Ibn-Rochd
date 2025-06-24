@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Payer un Crédit</h1>

    <div class="mb-4">
        <a href="{{ route('credits.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">←
            Retour à la liste des crédits</a>
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-lg mx-auto">
        <form method="POST" action="{{ route('credits.payer.store', ['credit' => $credit->id]) }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Nom de la source :</label>
                <p class="text-lg">{{ $credit->source?->nom ?? '---' }}</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Type :</label>
                <p>{{ class_basename($credit->source_type) === 'Personnel' ? 'Personnel' : 'Assurance' }}</p>
            </div>

            <div class="mb-4">
                <label for="montant" class="block text-gray-700 font-bold mb-2">Montant à payer :</label>
                <input type="number" id="montant" name="montant" step="0.01" min="1"
                    max="{{ $credit->montant - $credit->montant_paye }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Enregistrer le paiement
            </button>
        </form>
    </div>
</div>
@endsection
