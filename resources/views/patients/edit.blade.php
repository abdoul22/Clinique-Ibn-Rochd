@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Modifier le Patient</h2>
        <a href="{{ route('patients.index') }}" class="text-sm text-blue-600 hover:underline">← Retour à la liste</a>
    </div>
    @php
    $role = Auth::user()->role->name;
    $route = $role === 'superadmin'
    ? route('superadmin.patients.update', ['patient' => $patient->id])
    : route('admin.patients.update', ['patient' => $patient->id]);
    @endphp
    <form method="POST" action="{{ $route }}">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-4">
            <input type="text" name="first_name" value="{{ $patient->first_name }}" placeholder="Prénom"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="last_name" value="{{ $patient->last_name }}" placeholder="Nom"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="date" name="date_of_birth" value="{{ $patient->date_of_birth }}"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <select name="gender" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="">Sexe</option>
                <option value="Homme" {{ $patient->gender == 'Homme' ? 'selected' : '' }}>Homme</option>
                <option value="Femme" {{ $patient->gender == 'Femme' ? 'selected' : '' }}>Femme</option>
            </select>

            <input type="text" name="phone" value="{{ $patient->phone }}" placeholder="Téléphone"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="address" value="{{ $patient->address }}" placeholder="Adresse"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <select name="type_patient" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="">Type de patient</option>
                <option value="Interne" {{ $patient->type_patient == 'Interne' ? 'selected' : '' }}>Interne</option>
                <option value="Externe" {{ $patient->type_patient == 'Externe' ? 'selected' : '' }}>Externe</option>
            </select>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">Mettre
                à jour</button>
        </div>
    </form>
</div>
@endsection
