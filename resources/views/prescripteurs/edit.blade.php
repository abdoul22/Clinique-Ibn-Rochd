@extends('layouts.app')

@section('content')
<div
    class="max-w-2xl mx-auto bg-white p-6 rounded shadow dark:bg-gray-900 dark:text-gray-100 dark:shadow-lg dark:border dark:border-gray-700">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold dark:text-gray-100">Modifier le Prescripteur</h2>
        <a href="{{ route('prescripteurs.update', $prescripteur->id) }}"
            class="text-sm text-blue-600 hover:underline dark:text-blue-400 dark:hover:text-blue-300">← Retour à la
            liste</a>
    </div>

    @php
    $role = Auth::user()->role->name;
    $route = $role === 'superadmin'
    ? route('prescripteurs.update', $prescripteur->id)
    : route('admin.prescripteurs.update', $prescripteur->id);
    @endphp

    <form method="POST" action="{{ $route }}">
        @csrf
        @method('PUT')

        <div class="grid gap-4">
            <input type="text" name="nom" value="{{ $prescripteur->nom }}" placeholder="Nom"
                class="border border-gray-300 rounded px-3 py-2 w-full dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400"
                required>

            <input type="text" name="specialite" value="{{ $prescripteur->specialite }}" placeholder="Spécialité"
                class="border border-gray-300 rounded px-3 py-2 w-full dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400">
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition dark:bg-green-700 dark:hover:bg-green-800 dark:text-gray-100">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
