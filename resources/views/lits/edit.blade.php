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
                Modifier le lit
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Modifiez les informations du lit {{ $lit->nom_complet }}
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <form action="{{ route('lits.update', $lit->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Chambre -->
                    <div class="md:col-span-2">
                        <label for="chambre_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Chambre <span class="text-red-500">*</span>
                        </label>
                        <select name="chambre_id" id="chambre_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionnez une chambre</option>
                            @foreach($chambres as $chambre)
                            <option value="{{ $chambre->id }}" {{ (old('chambre_id', $lit->chambre_id) == $chambre->id)
                                ? 'selected' : '' }}>
                                {{ $chambre->nom_complet }} ({{ $chambre->type }})
                            </option>
                            @endforeach
                        </select>
                        @error('chambre_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Numéro de lit -->
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Numéro de lit <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="numero" id="numero" min="1" value="{{ old('numero', $lit->numero) }}"
                            required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ex: 1">
                        @error('numero')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type de lit -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Type de lit <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Sélectionnez un type</option>
                            <option value="standard" {{ (old('type', $lit->type) == 'standard') ? 'selected' : ''
                                }}>Standard</option>
                            <option value="medicalise" {{ (old('type', $lit->type) == 'medicalise') ? 'selected' : ''
                                }}>Médicalisé</option>
                            <option value="reanimation" {{ (old('type', $lit->type) == 'reanimation') ? 'selected' : ''
                                }}>Réanimation</option>
                        </select>
                        @error('type')
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
                            <option value="libre" {{ (old('statut', $lit->statut) == 'libre') ? 'selected' : '' }}>Libre
                            </option>
                            <option value="occupe" {{ (old('statut', $lit->statut) == 'occupe') ? 'selected' : ''
                                }}>Occupé</option>
                            <option value="maintenance" {{ (old('statut', $lit->statut) == 'maintenance') ? 'selected' :
                                '' }}>Maintenance</option>
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
                            placeholder="Description optionnelle du lit...">{{ old('description', $lit->description) }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('lits.show', $lit->id) }}"
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
