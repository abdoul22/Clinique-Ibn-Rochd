@extends('layouts.app')

@section('content')
<div class="card max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Détails du personnel</h1>

    <div class="space-y-3 text-gray-800 dark:text-gray-100">
        <p><strong>Nom :</strong> {{ $personnel->nom }}</p>
        <p><strong>Fonction :</strong> {{ $personnel->fonction }}</p>
        <p><strong>Téléphone :</strong> {{ $personnel->telephone ?? 'N/A' }}</p>
        <p><strong>Adresse :</strong> {{ $personnel->adresse ?? 'N/A' }}</p>
    </div>

    <div class="mt-6">
        <a href="{{ route('personnels.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">← Retour</a>
    </div>
</div>
@endsection
