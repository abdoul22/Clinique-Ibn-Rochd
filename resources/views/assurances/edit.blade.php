@extends('layouts.app')

@section('content')
<div
    class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded shadow dark:bg-gray-900 dark:text-gray-100 dark:shadow-lg dark:border dark:border-gray-700">
    <h2 class="text-xl font-semibold mb-4 dark:text-gray-100">Modifier l'assurance</h2>

    <form action="{{ route('assurances.update', $assurance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium dark:text-gray-200">Nom</label>
            <input type="text" name="nom" value="{{ old('nom', $assurance->nom) }}" required
                class="w-full px-3 py-2 border rounded mt-1 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400">
            @error('nom')
            <p class="text-sm text-red-500 mt-1 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('assurances.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 dark:bg-gray-800 dark:hover:bg-gray-900 dark:text-gray-100">Annuler</a>
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 dark:text-gray-100">Mettre
                à
                jour</button>
        </div>
    </form>
</div>
@endsection
