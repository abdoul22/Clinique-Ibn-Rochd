@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Détails du Médecin</h2>
        <a href="{{ route(Auth::user()->role->name . '.medecins.index') }}"
            class="text-sm text-blue-600 dark:text-blue-400 hover:underline">← Retour à la liste</a>
    </div>
    <div class="space-y-3 text-gray-900 dark:text-gray-100">
        <p><strong>Nom :</strong> {{ $medecin->nom }} {{ $medecin->prenom }}</p>
        <p><strong>Spécialité :</strong> {{ $medecin->specialite }}</p>
        <p><strong>Email :</strong> {{ $medecin->email ?? 'Non renseigné' }}</p>
        <p><strong>Téléphone :</strong> {{ $medecin->telephone ?? 'Non renseigné' }}</p>
        <p><strong>Adresse :</strong> {{ $medecin->adresse ?? 'Non renseignée' }}</p>
        <p><strong>Ville :</strong> {{ $medecin->ville ?? 'Non renseignée' }}</p>
        <p><strong>Pays :</strong> {{ $medecin->pays ?? 'Non renseigné' }}</p>
        <p><strong>Numéro de licence :</strong> {{ $medecin->numero_licence ?? 'Non renseigné' }}</p>
        <p><strong>Date d'embauche :</strong> {{ $medecin->date_embauche ?? 'Non renseignée' }}</p>
        <p><strong>Expérience :</strong> {{ $medecin->experience ?? 'Non renseignée' }}</p>
        <p><strong>Statut :</strong> {{ $medecin->statut ?? 'Non renseigné' }}</p>
    </div>
    <div class="flex justify-end gap-3 mt-6">
        <a href="{{ route(Auth::user()->role->name . '.medecins.edit', $medecin->id) }}"
            class="bg-indigo-800 dark:bg-indigo-900 hover:bg-indigo-600 dark:hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">
             Modifier
        </a>
        <form action="{{ route(Auth::user()->role->name . '.medecins.destroy', $medecin->id) }}" method="POST"
            onsubmit="return confirm('Confirmer la suppression de ce médecin ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 dark:bg-red-800 hover:bg-red-700 dark:hover:bg-red-900 text-white px-4 py-2 rounded text-sm">
                 Supprimer
            </button>
        </form>
    </div>
</div>
@endsection
