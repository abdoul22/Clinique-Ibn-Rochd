@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex items-center justify-between mb-4">
        <h1 class="page-title">Liste des Crédits</h1>
        <a href="{{ route('credits.create') }}"
            class="mr-3 bg-purple-800 hover:bg-purple-600 text-white text-sm px-4 py-2 rounded transition">
            Donner Un Crédit
        </a>
    </div>

    <!-- Filtre -->
    <form method="GET" action="{{ route('credits.index') }}" class="flex items-center space-x-2 mb-6">
        <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtrer par statut :</label>
        <select name="status" id="status" onchange="this.form.submit()"
            class="form-select text-sm">
            <option value="">-- Tous --</option>
            <option value="non payé" {{ request('status')=='non payé' ? 'selected' : '' }}>Non payé</option>
            <option value="partiellement payé" {{ request('status')=='partiellement payé' ? 'selected' : '' }}>
                Partiellement payé</option>
            <option value="payé" {{ request('status')=='payé' ? 'selected' : '' }}>Payé</option>
        </select>
    </form>

    <!-- Crédits Personnel -->
    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Crédits du Personnel</h2>
    <x-credit-table :credits="$creditsPersonnel" />
    <div class="mt-4">
        {{ $creditsPersonnel->appends(request()->all())->links() }}
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Page {{ $creditsPersonnel->currentPage() }} sur {{ $creditsPersonnel->lastPage() }}
        </p>
    </div>

    <!-- Crédits Assurance -->
    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mt-8 mb-2">Crédits des Assurances</h2>
    <x-credit-table :credits="$creditsAssurance" />
    <div class="mt-4">
        {{ $creditsAssurance->appends(request()->all())->links() }}
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Page {{ $creditsAssurance->currentPage() }} sur {{ $creditsAssurance->lastPage() }}
        </p>
    </div>
</div>
@endsection
