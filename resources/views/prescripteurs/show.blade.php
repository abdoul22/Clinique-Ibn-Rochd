@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Détails du prescripteur</h2>

    <div class="mb-4">
        <strong>Nom:</strong>
        <p>{{ $prescripteur->nom }}</p>
    </div>

    <div class="mb-4">
        <strong>Spécialité:</strong>
        <p>{{ $prescripteur->specialite }}</p>
    </div>

    <div class="flex justify-between mt-6">
        <a href="{{ route('prescripteurs.edit', $prescripteur) }}"
            class="bg-yellow-500 text-white px-4 py-2 rounded">Modifier</a>
        <a href="{{ route('prescripteurs.index') }}" class="text-blue-600 hover:underline">← Retour</a>
    </div>
</div>
@endsection
