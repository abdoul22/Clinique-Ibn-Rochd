@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Modifier l'examen</h2>
    <form action="{{ route('examens.update', $examen->id) }}" method="POST">
        @csrf
        @method('PUT')
        <!-- Nom -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom</label>
            <input type="text" id="nom" name="nom" value="{{ old('nom', $examen->nom) }}" required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded mt-1 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @error('nom')
            <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <!-- Service -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Service associé</label>
            <select name="idsvc"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded mt-1 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                required>
                <option value="">-- Choisir un service --</option>
                @foreach($services as $service)
                <option value="{{ $service->id }}" {{ old('idsvc', $examen->idsvc) == $service->id ? 'selected' : '' }}>
                    {{ $service->nom }}
                </option>
                @endforeach
            </select>
            @error('idsvc')
            <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <!-- Tarif -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tarif (en MRU)</label>
            <input type="number" id="tarif" name="tarif" value="{{ old('tarif', $examen->tarif) }}" required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded mt-1 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @error('tarif')
            <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <!-- Part Medecin -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Part Medecin (en MRU)</label>
            <input type="number" id="part_medecin" name="part_medecin"
                value="{{ old('part_medecin', $examen->part_medecin) }}" required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded mt-1 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @error('part_medecin')
            <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <!-- Part Cabinet -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Part Cabinet (en MRU)</label>
            <input type="number" id="part_cabinet" name="part_cabinet"
                value="{{ old('part_cabinet', $examen->part_cabinet) }}" required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded mt-1 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            @error('part_cabinet')
            <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex justify-end space-x-2">
            <a href="{{ route('examens.index') }}"
                class="bg-gray-600 dark:bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-700 dark:hover:bg-gray-800">Annuler</a>
            <button type="submit"
                class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800">Mettre
                à jour</button>
        </div>
    </form>
</div>
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tarifInput = document.getElementById('tarif');
        const partMedecinInput = document.getElementById('part_medecin');
        const partCabinetInput = document.getElementById('part_cabinet');
        let lastModified = null;
        let typingTimeout = null;
        function calculateMissingPart(source) {
            const tarif = parseFloat(tarifInput.value);
            const partMedecin = parseFloat(partMedecinInput.value);
            const partCabinet = parseFloat(partCabinetInput.value);
            if (isNaN(tarif)) {
                partMedecinInput.value = '';
                partCabinetInput.value = '';
                return;
            }
            if (source === 'medecin' && !isNaN(partMedecin)) {
                const cabinet = tarif - partMedecin;
                partCabinetInput.value = cabinet >= 0 ? cabinet.toFixed(2) : '';
            }
            if (source === 'cabinet' && !isNaN(partCabinet)) {
                const medecin = tarif - partCabinet;
                partMedecinInput.value = medecin >= 0 ? medecin.toFixed(2) : '';
            }
        }
        function activateAllFields() {
            partMedecinInput.disabled = false;
            partCabinetInput.disabled = false;
        }
        partMedecinInput.addEventListener('input', function () {
            clearTimeout(typingTimeout);
            lastModified = 'medecin';
            partCabinetInput.disabled = true;
            const partCabinet = parseFloat(partCabinetInput.value);
            if (!isNaN(partCabinet)) {
                partCabinetInput.value = '';
            }
            calculateMissingPart('medecin');
            typingTimeout = setTimeout(() => {
                activateAllFields();
            }, 1000);
        });
        partCabinetInput.addEventListener('input', function () {
            clearTimeout(typingTimeout);
            lastModified = 'cabinet';
            partMedecinInput.disabled = true;
            const partMedecin = parseFloat(partMedecinInput.value);
            if (!isNaN(partMedecin)) {
                partMedecinInput.value = '';
            }
            calculateMissingPart('cabinet');
            typingTimeout = setTimeout(() => {
                activateAllFields();
            }, 1000);
        });
        tarifInput.addEventListener('input', function () {
            if (lastModified === 'medecin') {
                calculateMissingPart('medecin');
            } else if (lastModified === 'cabinet') {
                calculateMissingPart('cabinet');
            }
        });
    });
</script>
@endpush
@endsection
