@extends('layouts.app')

@section('content')
<div
    class="max-w-2xl mx-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow">
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Ajouter un membre du personnel</h1>

    <form action="{{ route('personnels.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf

        <div class="col-span-2">
            <label class="block font-semibold text-gray-700 dark:text-gray-300">Nom</label>
            <input type="text" name="nom" value="{{ old('nom') }}"
                class="form-input bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600"
                required>
        </div>

        <div>
            <label class="block font-semibold text-gray-700 dark:text-gray-300">Fonction</label>
            <input type="text" name="fonction" value="{{ old('fonction') }}"
                class="form-input bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600"
                required>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 dark:text-gray-300">Salaire</label>
            <input type="number" name="salaire" value="{{ old('salaire') }}"
                class="form-input bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600"
                required>
        </div>
        <div>
            <label class="block font-semibold text-gray-700 dark:text-gray-300">Téléphone</label>
            <input type="text" name="telephone" value="{{ old('telephone') }}"
                class="form-input bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
        </div>

        <div class="col-span-2">
            <label class="block font-semibold text-gray-700 dark:text-gray-300">Adresse</label>
            <input type="text" name="adresse" value="{{ old('adresse') }}"
                class="form-input bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-300 dark:border-gray-600">
        </div>

        <div class="col-span-2 text-right">
            <button type="submit" id="submitBtn" class="form-button"
                onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
