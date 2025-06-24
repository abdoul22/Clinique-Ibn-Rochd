@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg">
    <h1 class="text-2xl font-bold mb-4">Ajouter un Crédit Personnel</h1>

    <form action="{{ route('credits.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-gray-700">Personnel</label>
            <select name="source_id" class="w-full border rounded px-3 py-2">
                @foreach($personnels as $personnel)
                <option value="{{ $personnel->id }}">{{ $personnel->nom }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-gray-700">Montant</label>
            <input type="number" name="montant" class="w-full border rounded px-3 py-2" required>
        </div>

        <div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection
