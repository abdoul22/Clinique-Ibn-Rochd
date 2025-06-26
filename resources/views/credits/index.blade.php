@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Liste des Crédits</h1>
        <a href="{{ route('credits.create') }}"
            class="mr-3 bg-purple-800 hover:bg-purple-600 text-white text-sm px-4 py-2 rounded transition">Donner Un
            Crédit</a>
    </div>

    <!-- Crédits Personnel -->
    <h2 class="text-lg font-semibold text-gray-700 mb-2">Crédits du Personnel</h2>
    <x-credit-table :credits="$creditsPersonnel" />

    <!-- Crédits Assurance -->
    <h2 class="text-lg font-semibold text-gray-700 mt-8 mb-2">Crédits des Assurances</h2>
    <x-credit-table :credits="$creditsAssurance" />
</div>
@endsection
