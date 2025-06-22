@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Ajouter un Médecin</h2>
        <a href="{{ route(auth()->user()->role->name . '.medecins.index') }}"
            class="text-sm text-blue-600 hover:underline">← Retour à la liste</a>
    </div>

    <form method="POST" action="{{ route(auth()->user()->role->name . '.medecins.store') }}">
        @csrf

        <div class="grid md:grid-cols-2 gap-4">
            <input type="text" name="nom" placeholder="Nom" class="border border-gray-300 rounded px-3 py-2 w-full"
                required>

            <input type="text" name="prenom" placeholder="Prénom"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="specialite" placeholder="Spécialité"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="tarif_consultation" placeholder="Tarif de la consultation"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

                <input type="text" name="part_medecin"
                placeholder="Part du medecin" class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="telephone" placeholder="Téléphone"
                class="border border-gray-300 rounded px-3 py-2 w-full">

            <input type="email" name="email" placeholder="Email"
                class="border border-gray-300 rounded px-3 py-2 w-full">
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Ajouter</button>
        </div>
    </form>
</div>
@endsection
