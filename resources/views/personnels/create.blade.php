@extends('layouts.app')

@section('content')
<div class="card max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Ajouter un membre du personnel</h1>

    <!-- Message d'information -->
    <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clip-rule="evenodd"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">Important :</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    Ce formulaire est destiné uniquement à l'ajout du personnel non-administratif.
                    Les utilisateurs administrateurs de l'application doivent créer leur compte via
                    la page d'inscription et seront ensuite approuvés par le superadmin.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('personnels.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block font-semibold">Nom :</label>
            <input type="text" name="nom" value="{{ old('nom') }}" class="form-input" required>
        </div>

        <div>
            <label class="block font-semibold">Fonction :</label>
            <input type="text" name="fonction" value="{{ old('fonction') }}" class="form-input" required>
        </div>
        <div>
            <label class="block font-semibold">Salaire :</label>
            <input type="text" name="salaire" value="{{ old('salaire') }}" class="form-input" required>
        </div>
        <div>
            <label class="block font-semibold">Téléphone :</label>
            <input type="text" name="telephone" value="{{ old('telephone') }}" class="form-input">
        </div>

        <div>
            <label class="block font-semibold">Adresse :</label>
            <input type="text" name="adresse" value="{{ old('adresse') }}" class="form-input">
        </div>

        <div class="text-right">
            <button type="submit" id="submitBtn" class="form-button"
                onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection