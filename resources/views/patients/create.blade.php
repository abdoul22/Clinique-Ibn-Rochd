@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ajouter un Patient</h2>
        <a href="{{ route('patients.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">←
            Retour à la liste</a>
    </div>

    <form method="POST" action="{{ route(auth()->user()->role->name . '.patients.store') }}">
        @csrf

        <div class="grid md:grid-cols-2 gap-4">
            <input type="text" name="first_name" placeholder="Prénom"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                required>

            <input type="text" name="last_name" placeholder="Nom"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                required>

            <input type="date" name="date_of_birth"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                required>

            <select name="gender"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                required>
                <option value="">Sexe</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
            </select>

            <input type="text" name="phone" placeholder="Téléphone"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                required>

            <input type="text" name="address" placeholder="Adresse"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-blue-500"
                required>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" id="submitBtn"
                class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition font-semibold shadow"
                onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                Ajouter
            </button>
        </div>
    </form>
</div>
@endsection
