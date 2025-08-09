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
                    <option value="LABORATOIRE" {{ old('type_service')=='LABORATOIRE' ? 'selected' : '' }}>LABORATOIRE
                    </option>
                    <option value="PHARMACIE" {{ old('type_service')=='PHARMACIE' ? 'selected' : '' }}>PHARMACIE
                    </option>
                    <option value="MEDECINE DENTAIRE" {{ old('type_service')=='MEDECINE DENTAIRE' ? 'selected' : '' }}>
                        MÉDECINE DENTAIRE</option>
                    <option value="IMAGERIE MEDICALE" {{ old('type_service')=='IMAGERIE MEDICALE' ? 'selected' : '' }}>
                        IMAGERIE MÉDICALE</option>
                    <option value="CONSULTATIONS EXTERNES" {{ old('type_service')=='CONSULTATIONS EXTERNES' ? 'selected'
                        : '' }}>CONSULTATIONS EXTERNES</option>
                    <option value="HOSPITALISATION" {{ old('type_service')=='HOSPITALISATION' ? 'selected' : '' }}>
                        HOSPITALISATION</option>
                    <option value="BLOC OPERATOIRE" {{ old('type_service')=='BLOC OPERATOIRE' ? 'selected' : '' }}>BLOC
                        OPÉRATOIRE</option>
                    <option value="INFIRMERIE" {{ old('type_service')=='INFIRMERIE' ? 'selected' : '' }}>INFIRMERIE
                    </option>
                    <option value="EXPLORATIONS FONCTIONNELLES" {{ old('type_service')=='EXPLORATIONS FONCTIONNELLES'
                        ? 'selected' : '' }}>EXPLORATIONS FONCTIONNELLES</option>
                </select>
                @error('type_service')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div id="pharmacie-fields" class="hidden">
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
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('type_service');
    const pharmFields = document.getElementById('pharmacie-fields');
    function toggleFields(){
      const isPharmacie = typeSelect.value === 'PHARMACIE';
      pharmFields.classList.toggle('hidden', false); // keep observation visible for all
    }
    typeSelect.addEventListener('change', toggleFields);
    toggleFields();
  });
</script>
@endpush
@endsection
