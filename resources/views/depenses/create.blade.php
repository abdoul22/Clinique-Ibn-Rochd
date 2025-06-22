@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Ajouter une Dépense</h2>
        <a href="{{ route('depenses.index') }}" class="text-sm text-blue-600 hover:underline">← Retour à la liste</a>
    </div>

    <form method="POST" action="{{ route('depenses.store') }}">
        @csrf

        <div class="grid gap-4">
            <input type="text" name="nom" placeholder="Nom de la dépense"
                class="border border-gray-300 rounded px-3 py-2 w-full"
                value="{{ old('nom') }}" required>

            @error('nom')
            <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>
        <div class="grid gap-4 my-3 ">
            <input type="text" name="montant" placeholder="Montant de la dépense"
                class="border border-gray-300 rounded px-3 py-2 w-full" value="{{ old('montant') }}" required>

            @error('montant')
            <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Ajouter</button>
        </div>
    </form>
</div>
@endsection
