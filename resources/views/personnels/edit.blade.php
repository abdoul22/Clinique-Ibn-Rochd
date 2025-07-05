@extends('layouts.app')

@section('content')
<div class="card max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Modifier le personnel</h1>

    <form action="{{ route('personnels.update', $personnel) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold">Nom :</label>
            <input type="text" name="nom" value="{{ old('nom', $personnel->nom) }}" class="form-input" required>
        </div>

        <div>
            <label class="block font-semibold">Fonction :</label>
            <input type="text" name="fonction" value="{{ old('fonction', $personnel->fonction) }}" class="form-input"
                required>
        </div>
        <div>
            <label class="block font-semibold">Salaire :</label>
            <input type="text" name="salaire" value="{{ old('salaire', $personnel->salaire) }}" class="form-input"
                required>
        </div>
        <div>
            <label class="block font-semibold">Téléphone :</label>
            <input type="text" name="telephone" value="{{ old('telephone', $personnel->telephone) }}"
                class="form-input">
        </div>

        <div>
            <label class="block font-semibold">Adresse :</label>
            <input type="text" name="adresse" value="{{ old('adresse', $personnel->adresse) }}" class="form-input">
        </div>

        <div class="text-right">
            <button type="submit" class="form-button">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
