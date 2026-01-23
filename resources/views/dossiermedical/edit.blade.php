@extends('layouts.app')
@section('title', 'Modifier le Dossier Médical - ' . $dossier->patient->first_name . ' ' . $dossier->patient->last_name)

@section('content')
@php
    $role = auth()->user()->role?->name;
    $routePrefix = match($role) {
        'superadmin' => 'superadmin',
        'admin' => 'admin',
        default => ''
    };
    $updateRoute = $routePrefix ? "{$routePrefix}.dossiers.update" : 'dossiers.update';
    $showRoute = $routePrefix ? "{$routePrefix}.dossiers.show" : 'dossiers.show';
@endphp

<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                        Modifier le Dossier Médical
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ $dossier->numero_dossier }} - {{ $dossier->patient->first_name }} {{ $dossier->patient->last_name }}
                    </p>
                </div>
                <a href="{{ route($showRoute, $dossier->id) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <form action="{{ route($updateRoute, $dossier->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Statut -->
                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Statut du Dossier <span class="text-red-500">*</span>
                    </label>
                    <select id="statut" name="statut" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('statut') border-red-500 @enderror">
                        <option value="actif" {{ old('statut', $dossier->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ old('statut', $dossier->statut) === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        <option value="archive" {{ old('statut', $dossier->statut) === 'archive' ? 'selected' : '' }}>Archivé</option>
                    </select>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Informations en lecture seule -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Date de création
                        </label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">
                            {{ $dossier->date_creation ? $dossier->date_creation->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Dernière visite
                        </label>
                        <p class="text-gray-900 dark:text-gray-100 mt-1">
                            {{ $dossier->derniere_visite ? $dossier->derniere_visite->format('d/m/Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notes générales -->
            <div class="mb-6">
                <label for="notes_generales" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notes Générales
                </label>
                <textarea id="notes_generales" name="notes_generales" rows="6"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('notes_generales') border-red-500 @enderror"
                    placeholder="Ajoutez des notes générales sur le dossier médical...">{{ old('notes_generales', $dossier->notes_generales) }}</textarea>
                @error('notes_generales')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Maximum 1000 caractères
                </p>
            </div>

            <!-- Statistiques (en lecture seule) -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Statistiques</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nombre de visites</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $dossier->nombre_visites ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total dépensé</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($dossier->total_depense ?? 0, 2) }} MRU</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Statut actuel</p>
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                            {{ $dossier->statut === 'actif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $dossier->statut === 'inactif' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $dossier->statut === 'archive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}">
                            {{ ucfirst($dossier->statut) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route($showRoute, $dossier->id) }}"
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
