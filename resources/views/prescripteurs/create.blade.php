@extends('layouts.app')

@section('content')
<div
    class="max-w-2xl mx-auto bg-white p-6 rounded shadow dark:bg-gray-900 dark:text-gray-100 dark:shadow-lg dark:border dark:border-gray-700">
    <h2 class="text-xl font-bold mb-4 dark:text-gray-100">Ajouter un prescripteur</h2>

    <form method="POST" action="{{ route('prescripteurs.store') }}">
        @csrf

        <div class="mb-4">
            <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom</label>
            <input type="text" name="nom" id="nom" required
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400">
        </div>

        <div class="mb-4">
            <label for="specialite"
                class="block text-sm font-medium text-gray-700 dark:text-gray-200">Spécialité</label>
            <input type="text" name="specialite" id="specialite"
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400">
        </div>

        <div class="flex justify-end">
            <button type="submit" id="submitBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded dark:bg-blue-700 dark:hover:bg-blue-800 dark:text-gray-100"
                onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                Ajouter
            </button>
        </div>
    </form>
</div>
@endsection
