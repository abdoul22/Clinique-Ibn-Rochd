@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ajouter un Service</h2>
        <a href="{{ route('services.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">←
            Retour à la liste</a>
    </div>

    <form method="POST" action="{{ route('services.store') }}">
        @csrf

        <div class="grid gap-4">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom du
                    service</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('nom')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="type_service" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type de
                    service <span class="text-red-500">*</span></label>
                <select name="type_service" id="type_service" required
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    <option value="">-- Sélectionner --</option>
                    <option value="consultations" {{ old('type_service')=='consultations' ? 'selected' : '' }}>
                        Consultations</option>
                    <option value="examens" {{ old('type_service')=='examens' ? 'selected' : '' }}>Examens</option>
                    <option value="pharmacie" {{ old('type_service')=='pharmacie' ? 'selected' : '' }}>Pharmacie
                    </option>
                    <option value="infirmerie" {{ old('type_service')=='infirmerie' ? 'selected' : '' }}>Infirmerie
                    </option>
                    <option value="bloc" {{ old('type_service')=='bloc' ? 'selected' : '' }}>Bloc</option>
                    <option value="laboratoire" {{ old('type_service')=='laboratoire' ? 'selected' : '' }}>Laboratoire
                    </option>
                    <option value="hospitalisation" {{ old('type_service')=='hospitalisation' ? 'selected' : '' }}>
                        Hospitalisation</option>
                    <option value="dentaire" {{ old('type_service')=='dentaire' ? 'selected' : '' }}>Dentaire</option>
                </select>
                @error('type_service')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="observation" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Observation
                    (facultatif)</label>
                <textarea name="observation" id="observation" rows="4"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">{{ old('observation') }}</textarea>
                @error('observation')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" id="submitBtn"
                class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition font-semibold shadow"
                onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                Ajouter
            </button>
        </div>
    </form>
</div>
@endsection