@extends('layouts.app')

@section('content')
<div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Ajouter un membre du personnel</h1>

    <form action="{{ route('personnels.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label class="block font-semibold">Nom :</label>
            <input type="text" name="nom" value="{{ old('nom') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <label class="block font-semibold">Fonction :</label>
            <input type="text" name="fonction" value="{{ old('fonction') }}" class="w-full border rounded px-3 py-2"
                required>
        </div>
<div>
            <label class="block font-semibold">Salaire :</label>
            <input type="text" name="salaire" value="{{ old('salaire') }}" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block font-semibold">Téléphone :</label>
            <input type="text" name="telephone" value="{{ old('telephone') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-semibold">Adresse :</label>
            <input type="text" name="adresse" value="{{ old('adresse') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="text-right">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
