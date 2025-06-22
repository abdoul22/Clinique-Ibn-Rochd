@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Modifier le Médecin</h2>
        <a href="{{ route(auth()->user()->role->name . '.medecins.index') }}"
            class="text-sm text-blue-600 hover:underline">← Retour à la liste</a>
    </div>

    @php
    $role = Auth::user()->role->name;
    $route = $role === 'superadmin'
    ? route('superadmin.medecins.update', $medecin->id)
    : route('admin.medecins.update', $medecin->id);
    @endphp

    <form method="POST" action="{{ $route }}">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-4">
            <input type="text" name="nom" value="{{ $medecin->nom }}" placeholder="Nom"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="prenom" value="{{ $medecin->prenom }}" placeholder="Prénom"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="specialite" value="{{ $medecin->specialite }}" placeholder="Spécialité"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="telephone" value="{{ $medecin->telephone }}" placeholder="Téléphone"
                class="border border-gray-300 rounded px-3 py-2 w-full">

            <input type="email" name="email" value="{{ $medecin->email }}" placeholder="Email"
                class="border border-gray-300 rounded px-3 py-2 w-full">
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
