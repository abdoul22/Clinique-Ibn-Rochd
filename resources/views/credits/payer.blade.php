@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Paiement du Crédit</h1>

    <p><strong>Type :</strong> {{ class_basename($credit->source_type) === 'Personnel' ? 'Personnel' : 'Assurance' }}
    </p>
    <p><strong>Nom :</strong> {{ $credit->source?->nom }}</p>
    <p><strong>Montant total :</strong> {{ number_format($credit->montant, 0, ',', ' ') }} MRU</p>
    <p><strong>Restant à payer :</strong> {{ number_format($credit->montant - $credit->montant_paye, 0, ',', ' ') }} MRU
    </p>

    <form action="{{ route('credits.payer.store', $credit->id) }}" method="POST" class="mt-4">
        @csrf
        <label class="block mb-2 font-semibold">Montant à payer :</label>
        <input type="number" name="montant" step="100" max="{{ $credit->montant - $credit->montant_paye }}" required
            class="w-full border rounded px-3 py-2 mb-4" />

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
            Confirmer le paiement
        </button>
    </form>
</div>
@endsection
