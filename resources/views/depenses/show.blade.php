@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto mt-8 card">
    <h2 class="page-title mb-4">Détail de la dépense</h2>
    <p><strong>ID :</strong> <span class="text-gray-800 dark:text-gray-200">{{ $depense->id }}</span></p>
    <p class="my-3"><strong>Nom :</strong> <span class="text-gray-800 dark:text-gray-200">{{ $depense->nom }}</span></p>
    <p><strong>Montant :</strong> <span class="text-red-600 dark:text-red-400">{{ $depense->montant }}</span></p>
    <p><strong>Mode de paiement :</strong> <span class="text-blue-700 dark:text-blue-300">{{
            ucfirst($depense->mode_paiement_id) }}</span></p>
    <p><strong>Source :</strong> <span class="text-gray-700 dark:text-gray-300">{{ ucfirst($depense->source) }}</span>
    </p>
    <a href="{{ route('depenses.index') }}" class="text-blue-600 dark:text-blue-400 mt-4 inline-block hover:underline">←
        Retour</a>
</div>
@endsection
