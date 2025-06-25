@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto mt-8 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Détail de la dépense</h2>
    <p><strong>ID :</strong> {{ $depense->id }}</p>
    <p class="my-3 "><strong>Nom :</strong> {{ $depense->nom }}</p>
    <p><strong>Part Medecin :</strong> {{ $depense->montant }}</p>
    <a href="{{ route('depenses.index') }}" class="text-blue-600 mt-4 inline-block">← Retour</a>
</div>
@endsection
