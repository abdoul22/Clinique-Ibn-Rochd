@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Modifier le service</h2>
    <form action="{{ route('services.update', $service->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom</label>
            <input type="text" name="nom" value="{{ old('nom', $service->nom) }}" required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded mt-1 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Observation</label>
            <textarea name="observation" rows="3"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded mt-1 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">{{ old('observation', $service->observation) }}</textarea>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="{{ route('services.index') }}"
                class="bg-gray-600 dark:bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-700 dark:hover:bg-gray-800">Annuler</a>
            <button type="submit"
                class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800">Mettre
                à jour</button>
        </div>
    </form>
</div>
@endsection
