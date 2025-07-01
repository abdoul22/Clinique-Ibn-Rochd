@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto card">
    <div class="flex justify-between items-center mb-6">
        <h2 class="page-title">Modifier la Dépense</h2>
        <a href="{{ route('depenses.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
            ← Retour à la liste
        </a>
    </div>

    <form method="POST" action="{{ route('depenses.update', $depense->id) }}">
        @csrf
        @method('PUT')

        <div class="grid gap-4">
            <input type="text" name="nom" placeholder="Nom de la dépense" class="form-input w-full"
                value="{{ old('nom', $depense->nom) }}" required>
            @error('nom')
            <p class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 my-3">
            <input type="text" name="montant" placeholder="Montant de la dépense" class="form-input w-full"
                value="{{ old('montant', $depense->montant) }}" required>
            @error('montant')
            <p class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="form-button">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection
