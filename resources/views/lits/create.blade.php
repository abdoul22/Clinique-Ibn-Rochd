@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 21V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m16 0h2m-2 0h-5m-9 0H2m2 0h5"></path>
                </svg>
                Créer un nouveau lit
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Ajoutez un nouveau lit à une chambre existante</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <form action="{{ route('lits.store') }}" method="POST">
                @csrf

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
                            <option value="{{ $chambre->id }}" {{ old('chambre_id')==$chambre->id ? 'selected' : '' }}>
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
                        <input type="number" name="numero" id="numero" min="1" value="{{ old('numero') }}" required
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
                            <option value="standard" {{ old('type')=='standard' ? 'selected' : '' }}>Standard</option>
                            <option value="medicalise" {{ old('type')=='medicalise' ? 'selected' : '' }}>Médicalisé
                            </option>
                            <option value="reanimation" {{ old('type')=='reanimation' ? 'selected' : '' }}>Réanimation
                            </option>
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
                            <option value="libre" {{ old('statut')=='libre' ? 'selected' : '' }}>Libre</option>
                            <option value="occupe" {{ old('statut')=='occupe' ? 'selected' : '' }}>Occupé</option>
                            <option value="maintenance" {{ old('statut')=='maintenance' ? 'selected' : '' }}>Maintenance
                            </option>
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
                            placeholder="Description optionnelle du lit...">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('lits.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        Annuler
                    </a>
                    <button type="submit" id="submitBtn"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200"
                        onclick="this.disabled=true; this.innerHTML='<span class=\'inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2\'></span>Création en cours...'; this.form.submit();">
                        Créer le lit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection