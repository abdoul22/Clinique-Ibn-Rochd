@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Modifier une Part du Cabinet</h2>

    <form action="{{ route('partcabinets.update', $partcabinet->id) }}" method="POST"
        class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf
        @method('PUT')

        <!-- Prestation -->
        <div class="col-span-1">
            <label for="prestation" class="block text-sm font-medium text-gray-700">Prestation</label>
            <input type="text" id="prestation" name="prestation"
                value="{{ old('prestation', $partcabinet->prestation) }}"
                class="mt-1 py-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>

        <!-- Prix -->
        <div class="col-span-1">
            <label for="prix" class="block text-sm font-medium text-gray-700">Prix</label>
            <input type="number" step="0.01" id="prix" name="prix" value="{{ old('prix', $partcabinet->prix) }}"
                class="mt-1 py-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>

        <!-- Part Cabinet -->
        <div class="col-span-1">
            <label for="part_cabinet" class="block text-sm font-medium text-gray-700">Part Cabinet</label>
            <input type="number" step="0.01" id="part_cabinet" name="part_cabinet"
                value="{{ old('part_cabinet', $partcabinet->part_cabinet) }}"
                class="mt-1 py-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>

        <!-- Part Médecin -->
        <div class="col-span-1">
            <label for="part_medecin" class="block text-sm font-medium text-gray-700">Part Médecin</label>
            <input type="number" step="0.01" id="part_medecin" name="part_medecin"
                value="{{ old('part_medecin', $partcabinet->part_medecin) }}"
                class="mt-1 py-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        </div>

        <!-- Bouton -->
        <div class="col-span-full flex justify-end">
            <button type="submit"
                class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
