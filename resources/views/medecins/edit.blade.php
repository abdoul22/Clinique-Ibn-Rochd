@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Modifier le Médecin</h2>
        <a href="{{ route(auth()->user()->role->name . '.medecins.index') }}"
            class="text-sm text-blue-600 dark:text-blue-400 hover:underline">← Retour à la liste</a>
    </div>

    @php
    $role = Auth::user()->role->name;
    $route = $role === 'superadmin'
    ? route('superadmin.medecins.update', $medecin->id)
    : route('admin.medecins.update', $medecin->id);
    @endphp

    <form method="POST" action="{{ $route }}">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-4">
            <input type="text" name="nom" value="{{ $medecin->nom }}" placeholder="Nom"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500" required>

            <input type="text" name="prenom" value="{{ $medecin->prenom }}" placeholder="Prénom"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500" required>

            <input type="text" name="specialite" value="{{ $medecin->specialite }}" placeholder="Spécialité"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500" required>

            <input type="text" name="telephone" value="{{ $medecin->telephone }}" placeholder="Téléphone"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500">

            <input type="email" name="email" value="{{ $medecin->email }}" placeholder="Email"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500">
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-green-600 dark:bg-green-700 text-white px-6 py-2 rounded hover:bg-green-700 dark:hover:bg-green-800 transition font-semibold shadow">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
