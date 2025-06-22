@extends('layouts.app')

@section('content')
<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Modifier le personnel</h1>

    <form action="{{ route('personnels.update', $personnel) }}" method="POST"
        class="space-y-4 bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold">Nom :</label>
            <input type="text" name="nom" value="{{ old('nom', $personnel->nom) }}"
                class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block font-semibold">Fonction :</label>
            <input type="text" name="fonction" value="{{ old('fonction', $personnel->fonction) }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
<div>
            <label class="block font-semibold">Salaire :</label>
            <input type="text" name="salaire" value="{{ old('salaire', $personnel->salaire) }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block font-semibold">Credit :</label>
            <input type="text" name="credit" value="{{ old('credit', $personnel->credit) }}"
                class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block font-semibold">Téléphone :</label>
            <input type="text" name="telephone" value="{{ old('telephone', $personnel->telephone) }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-semibold">Adresse :</label>
            <input type="text" name="adresse" value="{{ old('adresse', $personnel->adresse) }}"
                class="w-full border rounded px-3 py-2">
        </div>

        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
