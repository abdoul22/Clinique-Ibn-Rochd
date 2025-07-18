@extends('layouts.app')
@section('title', 'Ajouter un Médicament')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Ajouter un Médicament</h1>
            <a href="{{ route('pharmacie.index') }}"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informations du Médicament</h2>
            </div>

            <form action="{{ route('pharmacie.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du médicament -->
                    <div class="md:col-span-2">
                        <label for="nom_medicament"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nom du Médicament *
                        </label>
                        <input type="text" name="nom_medicament" id="nom_medicament" value="{{ old('nom_medicament') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('nom_medicament') border-red-500 @enderror"
                            required>
                        @error('nom_medicament')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix d'achat -->
                    <div>
                        <label for="prix_achat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Prix d'Achat (MRU) *
                        </label>
                        <input type="number" name="prix_achat" id="prix_achat" step="0.01"
                            value="{{ old('prix_achat') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('prix_achat') border-red-500 @enderror"
                            required>
                        @error('prix_achat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix de vente -->
                    <div>
                        <label for="prix_vente" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Prix de Vente (MRU) *
                        </label>
                        <input type="number" name="prix_vente" id="prix_vente" step="0.01"
                            value="{{ old('prix_vente') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('prix_vente') border-red-500 @enderror"
                            required>
                        @error('prix_vente')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prix unitaire -->
                    <div>
                        <label for="prix_unitaire"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Prix Unitaire (MRU) *
                        </label>
                        <input type="number" name="prix_unitaire" id="prix_unitaire" step="0.01"
                            value="{{ old('prix_unitaire') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('prix_unitaire') border-red-500 @enderror"
                            required>
                        @error('prix_unitaire')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantité -->
                    <div>
                        <label for="quantite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Quantité par Unité *
                        </label>
                        <input type="number" name="quantite" id="quantite" value="{{ old('quantite', 1) }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('quantite') border-red-500 @enderror"
                            required>
                        @error('quantite')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Stock Initial *
                        </label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('stock') border-red-500 @enderror"
                            required>
                        @error('stock')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Statut *
                        </label>
                        <select name="statut" id="statut"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('statut') border-red-500 @enderror"
                            required>
                            <option value="actif" {{ old('statut')=='actif' ? 'selected' : '' }}>Actif</option>
                            <option value="inactif" {{ old('statut')=='inactif' ? 'selected' : '' }}>Inactif</option>
                            <option value="rupture" {{ old('statut')=='rupture' ? 'selected' : '' }}>Rupture</option>
                        </select>
                        @error('statut')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label for="categorie" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Catégorie
                        </label>
                        <input type="text" name="categorie" id="categorie" value="{{ old('categorie') }}"
                            placeholder="Ex: Antibiotiques, Antalgiques..."
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('categorie') border-red-500 @enderror">
                        @error('categorie')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fournisseur -->
                    <div>
                        <label for="fournisseur"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Fournisseur
                        </label>
                        <input type="text" name="fournisseur" id="fournisseur" value="{{ old('fournisseur') }}"
                            placeholder="Nom du fournisseur"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('fournisseur') border-red-500 @enderror">
                        @error('fournisseur')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date d'expiration -->
                    <div>
                        <label for="date_expiration"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date d'Expiration
                        </label>
                        <input type="date" name="date_expiration" id="date_expiration"
                            value="{{ old('date_expiration') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('date_expiration') border-red-500 @enderror">
                        @error('date_expiration')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                            placeholder="Description du médicament..."
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-6">
                    <a href="{{ route('pharmacie.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Annuler
                    </a>
                    <button type="submit" id="submitBtn"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Ajout en cours...'; this.form.submit();">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection