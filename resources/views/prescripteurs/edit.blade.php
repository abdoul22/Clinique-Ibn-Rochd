@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Modifier le Prescripteur</h2>
        <a href="{{ route('prescripteurs.update', $prescripteur->id) }}"
            class="text-sm text-blue-600 hover:underline">← Retour à la liste</a>
    </div>

    @php
    $role = Auth::user()->role->name;
    $route = $role === 'superadmin'
    ? route('prescripteurs.update', $prescripteur->id)
    : route('admin.prescripteurs.update', $prescripteur->id);
    @endphp

    <form method="POST" action="{{ $route }}">
        @csrf
        @method('PUT')

        <div class="grid gap-4">
            <input type="text" name="nom" value="{{ $prescripteur->nom }}" placeholder="Nom"
                class="border border-gray-300 rounded px-3 py-2 w-full" required>

            <input type="text" name="specialite" value="{{ $prescripteur->specialite }}" placeholder="Spécialité"
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
