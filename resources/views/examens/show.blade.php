@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-6">Détail de l'examen</h2>

    <div class="mb-4">
        <span class="font-medium text-gray-700">Nom :</span>
        <p class="text-gray-900">{{ $examen->nom }}</p>
    </div>

    <div class="mb-4">
        <span class="font-medium text-gray-700">Service associé :</span>
        <p class="text-gray-900">{{ $examen->service->nom ?? 'Non défini' }}</p>
    </div>

    <div class="mb-4">
        <span class="font-medium text-gray-700">Tarif :</span>
        <p class="text-gray-900">{{ number_format($examen->tarif, 0, ',', ' ') }} MRU</p>
    </div>

    <div class="mt-6 flex justify-end">
        <a href="{{ route('examens.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">←
            Retour</a>
    </div>
</div>
@endsection
