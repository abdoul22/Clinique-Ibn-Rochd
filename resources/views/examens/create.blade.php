@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ajouter un examen</h2>
        <a href="{{ route('examens.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">← Retour
            à la liste</a>
    </div>

    <form method="POST" action="{{ route('examens.store') }}">
        @csrf

        <div class="grid gap-4">
            <!-- Nom -->
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom de
                    l'examen</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('nom')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Service -->
            <div>
                <label for="idsvc" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Service
                    associé</label>
                <select name="idsvc" id="idsvc"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                    <option value="">-- Sélectionner un service --</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}" {{ old('idsvc')==$service->id ? 'selected' : '' }}>
                        {{ $service->nom }}
                    </option>
                    @endforeach
                </select>
                @error('idsvc')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tarif -->
            <div>
                <label for="tarif" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tarif (en
                    MRU)</label>
                <input type="number" name="tarif" id="tarif" value="{{ old('tarif') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('tarif')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Part Medcin -->
            <div>
                <label for="tarif" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Part Medecin (en
                    MRU)</label>
                <input type="number" name="part_medecin" id="part_medecin" value="{{ old('part_medecin') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('part_medecin')
                <p class="text-sm text-red-500 dark:text-red-300 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <!-- Part Cabinet -->
            <div>
                <label for="part_cabinet" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Part
                    Cabinet (en MRU)</label>
                <input type="number" name="part_cabinet" id="part_cabinet" value="{{ old('part_cabinet') }}"
                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                    required>
                @error('part_cabinet')
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
    <p class="text-sm text-gray-500 dark:text-gray-300 mt-4">
        Remplissez soit la <strong>part médecin</strong> soit la <strong>part cabinet</strong>, l'autre sera calculée
        automatiquement selon le tarif.
    </p>

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
                partCabinetInput.value = ''; // efface si l'utilisateur modifie manuellement les deux
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
                partMedecinInput.value = ''; // efface si l'utilisateur modifie manuellement les deux
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
