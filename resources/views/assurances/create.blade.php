@extends('layouts.app')

@section('content')
<div
    class="max-w-xl mx-auto bg-white p-6 rounded shadow dark:bg-gray-900 dark:text-gray-100 dark:shadow-lg dark:border dark:border-gray-700">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold dark:text-gray-100">Ajouter une assurance</h2>
        <a href="{{ route('assurances.index') }}"
            class="text-sm text-blue-600 hover:underline dark:text-blue-400 dark:hover:text-blue-300">← Retour à la
            liste</a>
    </div>

    <form method="POST" action="{{ route('assurances.store') }}">
        @csrf

        <div class="grid gap-4">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom de
                    l'assurance</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400"
                    required>
                @error('nom')
                <p class="text-sm text-red-500 mt-1 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition dark:bg-blue-700 dark:hover:bg-blue-800 dark:text-gray-100">Ajouter</button>
        </div>
    </form>
</div>
@endsection
