@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto card">
    <div class="flex justify-between items-center mb-6">
        <h2 class="page-title">Ajouter une Dépense</h2>
        @if(auth()->user()->role && auth()->user()->role->name === 'admin')
        <a href="{{ route('dashboard.admin') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
            ← Retour au dashboard
        </a>
        @else
        <a href="{{ route('depenses.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
            ← Retour à la liste
        </a>
        @endif
    </div>

    <form method="POST" action="{{ auth()->user()->role && auth()->user()->role->name === 'admin' ? route('admin.depenses.store') : route('depenses.store') }}">
        @csrf

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid gap-4">
            <input type="text" name="nom" placeholder="Nom de la dépense" class="form-input w-full"
                value="{{ old('nom') }}" required>
            @error('nom')
            <p class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>
        <div class="my-4">
            <label for="mode_paiement_id" class="block text-bold font-medium text-gray-700 dark:text-gray-300">Mode de
                paiement</label>
            <select name="mode_paiement_id" id="mode_paiement_id" required class="form-select">
                <option value="">-- Sélectionner --</option>
                @foreach($modes as $mode)
                <option value="{{ $mode }}" {{ old('mode_paiement_id') == $mode ? 'selected' : '' }}>{{ ucfirst($mode) }}</option>
                @endforeach
            </select>
            @error('mode_paiement_id')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="grid gap-4 my-3 ">
            <input type="text" name="montant" placeholder="Montant de la dépense" class="form-input w-full"
                value="{{ old('montant') }}" required>
            @error('montant')
            <p class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" id="submitBtn" class="form-button"
                onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                Ajouter
            </button>
        </div>
    </form>
</div>
@endsection
