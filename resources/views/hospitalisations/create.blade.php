@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Nouvelle hospitalisation</h1>
    <form method="POST" action="{{ route('hospitalisations.store') }}" class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Patient *</label>
            <select name="gestion_patient_id" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" required>
                <option value="">Sélectionner un patient</option>
                @foreach($patients as $patient)
                <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Médecin *</label>
            <select name="medecin_id" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" required>
                <option value="">Sélectionner un médecin</option>
                @foreach($medecins as $medecin)
                <option value="{{ $medecin->id }}">{{ $medecin->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Service *</label>
            <select name="service_id" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" required>
                <option value="">Sélectionner un service</option>
                @foreach($services as $service)
                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Date d'entrée *</label>
            <input type="date" name="date_entree" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Date de sortie</label>
            <input type="date" name="date_sortie" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Motif</label>
            <input type="text" name="motif" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Statut *</label>
            <select name="statut" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white" required>
                <option value="en cours">En cours</option>
                <option value="terminé">Terminé</option>
                <option value="annulé">Annulé</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Chambre</label>
            <input type="text" name="chambre" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Lit</label>
            <input type="text" name="lit" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Montant total</label>
            <input type="number" step="0.01" name="montant_total" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-medium text-gray-700 dark:text-gray-200">Observation</label>
            <textarea name="observation" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"></textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('hospitalisations.index') }}" class="bg-gray-500 dark:bg-gray-700 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
