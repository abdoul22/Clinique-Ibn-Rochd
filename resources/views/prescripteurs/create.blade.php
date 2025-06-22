@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Ajouter un prescripteur</h2>

    <form method="POST" action="{{ route('prescripteurs.store') }}">
        @csrf

        <div class="mb-4">
            <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
            <input type="text" name="nom" id="nom" required
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
        </div>

        <div class="mb-4">
            <label for="specialite" class="block text-sm font-medium text-gray-700">Spécialité</label>
            <input type="text" name="specialite" id="specialite"
                class="w-full border border-gray-300 rounded px-3 py-2 mt-1">
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Ajouter</button>
        </div>
    </form>
</div>
@endsection
