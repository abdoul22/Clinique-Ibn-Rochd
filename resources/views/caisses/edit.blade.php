@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold">Modifier l'examen #{{ $caisse->numero_entre }}</h1>
        <a href="{{ route('caisses.index') }}" class="text-blue-600 hover:underline flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
        </a>
    </div>

    <form method="POST" action="{{ route(auth()->user()->role->name . '.caisses.update', ['caisse' => $caisse->id]) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Colonne de gauche -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro d'entrée *</label>
                    <input type="text" name="numero_entre" value="{{ $caisse->numero_entre }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient *</label>
                    <select name="patient_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ $caisse->patient_id == $patient->id ? 'selected' : '' }}>
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Médecin *</label>
                    <select name="medecin_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($medecins as $medecin)
                        <option value="{{ $medecin->id }}" {{ $caisse->medecin_id == $medecin->id ? 'selected' : '' }}>
                            {{ $medecin->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prescripteur</label>
                    <select name="prescripteur_id"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Aucun prescripteur</option>
                        @foreach($prescripteurs as $prescripteur)
                        <option value="{{ $prescripteur->id }}" {{ $caisse->prescripteur_id == $prescripteur->id ?
                            'selected' : '' }}>
                            {{ $prescripteur->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Colonne de droite -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type d'examen *</label>
                    <select name="examen_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($exam_types as $type)
                        <option value="{{ $type->id }}" {{ $caisse->examen_id == $type->id ? 'selected' : '' }}>
                            {{ $type->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service *</label>
                    <select name="service_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ $caisse->service_id == $service->id ? 'selected' : '' }}>
                            {{ $service->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de l'examen *</label>
                    <input type="date" name="date_examen" value="{{ $caisse->date_examen->format('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total (MRU) *</label>
                    <input type="number" name="total" step="0.01" value="{{ $caisse->total }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="border-t pt-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Caissier *</label>
                <input type="text" name="nom_caissier" value="{{ $caisse->nom_caissier }}" required
                    class="w-full md:w-1/2 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('caisses.index') }}"
                    class="bg-gray-500 text-white px-5 py-2 rounded-lg hover:bg-gray-600 transition">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                    Mettre à jour
                </button>
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Pré-sélectionner l'examen existant
        const examenId = "{{ $examen->examen_id ?? '' }}";
        if (examenId) {
            document.getElementById('examen_id').value = examenId;
        }
        updateTotal();
    });
    </script>
@endsection
