@extends('layouts.app')

@section('content')
<div
    class="max-w-xl mx-auto bg-white p-6 rounded shadow dark:bg-gray-900 dark:text-gray-100 dark:shadow-lg dark:border dark:border-gray-700">
    <h2 class="text-xl font-bold mb-4 dark:text-gray-100">Détails du prescripteur</h2>

    <div class="mb-4">
        <strong class="dark:text-gray-200">Nom:</strong>
        <p class="dark:text-gray-100">{{ $prescripteur->nom }}</p>
    </div>

    <div class="mb-4">
        <strong class="dark:text-gray-200">Spécialité:</strong>
        <p class="dark:text-gray-100">{{ $prescripteur->specialite }}</p>
    </div>

    <div class="flex justify-between mt-6">
        <a href="{{ route('prescripteurs.edit', $prescripteur) }}"
            class="bg-indigo-500 text-white px-4 py-2 rounded dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:text-gray-100">Modifier</a>
        <a href="{{ route('prescripteurs.index') }}"
            class="text-blue-600 hover:underline dark:text-blue-400 dark:hover:text-blue-300">← Retour</a>
    </div>
</div>
@endsection