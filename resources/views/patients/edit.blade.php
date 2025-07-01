@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Modifier le Patient</h2>
        <a href="{{ route('patients.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">←
            Retour à la liste</a>
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
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500"
                required>

            <input type="text" name="last_name" value="{{ $patient->last_name }}" placeholder="Nom"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500"
                required>

            <input type="date" name="date_of_birth" value="{{ $patient->date_of_birth }}"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500"
                required>

            <select name="gender"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500"
                required>
                <option value="">Sexe</option>
                <option value="Homme" {{ $patient->gender == 'Homme' ? 'selected' : '' }}>Homme</option>
                <option value="Femme" {{ $patient->gender == 'Femme' ? 'selected' : '' }}>Femme</option>
            </select>

            <input type="text" name="phone" value="{{ $patient->phone }}" placeholder="Téléphone"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500"
                required>

            <input type="text" name="address" value="{{ $patient->address }}" placeholder="Adresse"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500"
                required>

            <select name="type_patient"
                class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded px-3 py-2 w-full focus:ring-2 focus:ring-green-500"
                required>
                <option value="">Type de patient</option>
                <option value="Interne" {{ $patient->type_patient == 'Interne' ? 'selected' : '' }}>Interne</option>
                <option value="Externe" {{ $patient->type_patient == 'Externe' ? 'selected' : '' }}>Externe</option>
            </select>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-green-600 dark:bg-green-700 text-white px-6 py-2 rounded hover:bg-green-700 dark:hover:bg-green-800 transition font-semibold shadow">Mettre
                à jour</button>
        </div>
    </form>
</div>
@endsection
