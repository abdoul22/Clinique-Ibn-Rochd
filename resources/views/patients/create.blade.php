@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Ajouter un Patient</h2>
        <a href="{{ route('patients.index') }}" class="text-sm text-blue-600 hover:underline">← Retour à la liste</a>
    </div>

    <form method="POST" action="{{ route(auth()->user()->role->name . '.patients.store') }}">
        @csrf

        <div class="grid md:grid-cols-2 gap-4">
            <input type="text" name="first_name" placeholder="Prénom"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="last_name" placeholder="Nom"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="date" name="date_of_birth" class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <select name="gender" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="">Sexe</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
            </select>

            <input type="text" name="phone" placeholder="Téléphone"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="address" placeholder="Adresse"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <select name="type_patient" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                <option value="">Type de patient</option>
                <option value="Interne">Interne</option>
                <option value="Externe">Externe</option>
            </select>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Ajouter</button>
        </div>
    </form>
</div>
@endsection
