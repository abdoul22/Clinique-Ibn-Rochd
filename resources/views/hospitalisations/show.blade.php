@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Détail de l'hospitalisation #{{ $hospitalisation->id }}</h1>
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Patient :</strong> {{ $hospitalisation->patient->nom ?? '-' }} {{ $hospitalisation->patient->prenom ?? '' }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Médecin :</strong> {{ $hospitalisation->medecin->nom ?? '-' }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Service :</strong> {{ $hospitalisation->service->nom ?? '-' }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Date d'entrée :</strong> {{ $hospitalisation->date_entree }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Date de sortie :</strong> {{ $hospitalisation->date_sortie ?? '-' }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Motif :</strong> {{ $hospitalisation->motif ?? '-' }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Statut :</strong> {{ ucfirst($hospitalisation->statut) }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Chambre :</strong> {{ $hospitalisation->chambre ?? '-' }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Lit :</strong> {{ $hospitalisation->lit ?? '-' }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Montant total :</strong> {{ $hospitalisation->montant_total ? number_format($hospitalisation->montant_total, 2) . ' MRU' : '-' }}</div>
        <div class="mb-2 text-gray-900 dark:text-gray-100"><strong>Observation :</strong> {{ $hospitalisation->observation ?? '-' }}</div>
    </div>
    <div class="flex justify-end gap-2 mt-4">
        <a href="{{ route('hospitalisations.edit', $hospitalisation->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Modifier</a>
        <form action="{{ route('hospitalisations.destroy', $hospitalisation->id) }}" method="POST" onsubmit="return confirm('Supprimer cette hospitalisation ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Supprimer</button>
        </form>
        <a href="{{ route('hospitalisations.index') }}" class="bg-gray-500 dark:bg-gray-700 text-white px-4 py-2 rounded">Retour</a>
    </div>
</div>
@endsection
