@extends('layouts.app')

@section('content')
<div class="card max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Ajouter un membre du personnel</h1>

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