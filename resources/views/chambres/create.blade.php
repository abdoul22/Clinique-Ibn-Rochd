@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Créer une nouvelle chambre
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Ajoutez une nouvelle chambre à l'hôpital</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <form action="{{ route('chambres.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom de la chambre -->
                    <div class="md:col-span-2">
                        <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nom de la chambre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom') }}" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ex: Chambre 101">
                        @error('nom')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type de chambre -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionnez un type</option>
                            <option value="Mise en observation" {{ old('type')=='Mise en observation' ? 'selected' : ''
                                }}>Mise en observation</option>
                            <option value="Commune" {{ old('type')=='Commune' ? 'selected' : '' }}>Commune</option>
                            <option value="Double" {{ old('type')=='Double' ? 'selected' : '' }}>Double</option>
                            <option value="Individuelle" {{ old('type')=='Individuelle' ? 'selected' : '' }}>
                                Individuelle</option>
                            <option value="VIP" {{ old('type')=='VIP' ? 'selected' : '' }}>VIP</option>
                        </select>
                        @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacité en lits -->
                    <div>
                        <label for="capacite_lits"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Capacité en lits <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="capacite_lits" id="capacite_lits" min="1" max="10"
                            value="{{ old('capacite_lits', 1) }}" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ex: 2">
                        @error('capacite_lits')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Étage -->
                    <div>
                        <label for="etage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Étage
                        </label>
                        <input type="text" name="etage" id="etage" value="{{ old('etage') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ex: 1er étage">
                        @error('etage')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tarif journalier -->
                    <div>
                        <label for="tarif_journalier"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tarif journalier (MRU)
                        </label>
                        <input type="number" name="tarif_journalier" id="tarif_journalier" min="0" step="0.01"
                            value="{{ old('tarif_journalier') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ex: 1500.00">
                        @error('tarif_journalier')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Description de la chambre...">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Équipements -->
                    <div class="md:col-span-2">
                        <label for="equipements"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Équipements
                        </label>
                        <textarea name="equipements" id="equipements" rows="3"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Liste des équipements disponibles...">{{ old('equipements') }}</textarea>
                        @error('equipements')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('chambres.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        Annuler
                    </a>
                    <button type="submit" id="submitBtn"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200"
                        onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Création en cours...'; this.form.submit();">
                        Créer la chambre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@endsection
