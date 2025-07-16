@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
                Modifier la chambre
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Modifiez les informations de la chambre {{
                $chambre->nom_complet }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <form action="{{ route('chambres.update', $chambre->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom de la chambre -->
                    <div class="md:col-span-2">
                        <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nom de la chambre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $chambre->nom) }}" required
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
                            <option value="standard" {{ (old('type', $chambre->type) == 'standard') ? 'selected' : ''
                                }}>Standard</option>
                            <option value="simple" {{ (old('type', $chambre->type) == 'simple') ? 'selected' : ''
                                }}>Simple</option>
                            <option value="double" {{ (old('type', $chambre->type) == 'double') ? 'selected' : ''
                                }}>Double</option>
                            <option value="suite" {{ (old('type', $chambre->type) == 'suite') ? 'selected' : '' }}>Suite
                            </option>
                            <option value="VIP" {{ (old('type', $chambre->type) == 'VIP') ? 'selected' : '' }}>VIP
                            </option>
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
                            value="{{ old('capacite_lits', $chambre->capacite_lits) }}" required
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
                        <input type="text" name="etage" id="etage" value="{{ old('etage', $chambre->etage) }}"
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
                            value="{{ old('tarif_journalier', $chambre->tarif_journalier) }}"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ex: 1500.00">
                        @error('tarif_journalier')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select name="statut" id="statut" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionnez un statut</option>
                            <option value="active" {{ (old('statut', $chambre->statut) == 'active') ? 'selected' : ''
                                }}>Active</option>
                            <option value="inactive" {{ (old('statut', $chambre->statut) == 'inactive') ? 'selected' :
                                '' }}>Inactive</option>
                            <option value="maintenance" {{ (old('statut', $chambre->statut) == 'maintenance') ?
                                'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('statut')
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
                            placeholder="Description de la chambre...">{{ old('description', $chambre->description) }}</textarea>
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
                            placeholder="Liste des équipements disponibles...">{{ old('equipements', $chambre->equipements) }}</textarea>
                        @error('equipements')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('chambres.show', $chambre->id) }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        Annuler
                    </a>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
