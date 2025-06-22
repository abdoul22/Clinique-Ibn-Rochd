@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Ajouter une assurance</h2>
        <a href="{{ route('assurances.index') }}" class="text-sm text-blue-600 hover:underline">← Retour à la liste</a>
    </div>

    <form method="POST" action="{{ route('assurances.store') }}">
        @csrf

        <div class="grid gap-4">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700">Nom de l'assurance</label>
                <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                    class="mt-1 block w-full border border-gray-300 rounded px-3 py-2" required>
                @error('nom')
                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Ajouter</button>
        </div>
    </form>
</div>
@endsection
