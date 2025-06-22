@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Modifier l'assurance</h2>

    <form action="{{ route('assurances.update', $assurance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium">Nom</label>
            <input type="text" name="nom" value="{{ old('nom', $assurance->nom) }}" required
                class="w-full px-3 py-2 border rounded mt-1">
            @error('nom')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('assurances.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Annuler</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Mettre à
                jour</button>
        </div>
    </form>
</div>
@endsection
