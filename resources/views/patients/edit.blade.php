@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header avec gradient -->
        <div class="gradient-header mb-8">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-user-edit mr-3"></i>Modifier le Patient
                        </h1>
                        <p class="text-blue-100 text-lg">Modifiez les informations du patient</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route(auth()->user()->role->name . '.patients.show', $patient->id) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-eye mr-2"></i>Voir les détails
                        </a>
                        <a href="{{ route(auth()->user()->role->name . '.patients.index') }}"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            @php
            $role = Auth::user()->role->name;
            $route = match($role) {
                'superadmin' => route('superadmin.patients.update', ['patient' => $patient->id]),
                'admin' => route('admin.patients.update', ['patient' => $patient->id]),
                'medecin' => route('medecin.patients.update', ['patient' => $patient->id]),
                default => route('admin.patients.update', ['patient' => $patient->id])
            };
            @endphp
            <form method="POST" action="{{ $route }}">
                @csrf
                @method('PUT')

                <!-- Informations personnelles -->
                <div class="bg-blue-600 dark:bg-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user mr-3"></i>Informations du Patient
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-user mr-1"></i>Prénom *
                            </label>
                            <input type="text" name="first_name" value="{{ $patient->first_name }}"
                                placeholder="Entrez le prénom"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all duration-200"
                                required>
                            @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-user mr-1"></i>Nom de famille *
                            </label>
                            <input type="text" name="last_name" value="{{ $patient->last_name }}"
                                placeholder="Entrez le nom de famille"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all duration-200"
                                required>
                            @error('last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-birthday-cake mr-1"></i>Âge *
                            </label>
                            <input type="number" name="age" value="{{ $patient->age }}"
                                placeholder="Entrez l'âge (ex: 25)"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all duration-200"
                                min="0" max="150" required>
                            @error('age')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-venus-mars mr-1"></i>Sexe *
                            </label>
                            <select name="gender"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all duration-200">
                                <option value="">Sélectionner le sexe</option>
                                <option value="Homme" {{ $patient->gender == 'Homme' ? 'selected' : '' }}>Homme</option>
                                <option value="Femme" {{ $patient->gender == 'Femme' ? 'selected' : '' }}>Femme</option>
                            </select>
                            @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="bg-green-600 dark:bg-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-address-book mr-3"></i>Informations de Contact
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-phone mr-1"></i>Numéro de téléphone *
                            </label>
                            <input type="text" name="phone" value="{{ $patient->phone }}"
                                placeholder="Entrez le numéro de téléphone"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all duration-200"
                                required>
                            @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>Adresse *
                            </label>
                            <input type="text" name="address" value="{{ $patient->address }}"
                                placeholder="Entrez l'adresse complète"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-all duration-200"
                                required>
                            @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4">
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route(auth()->user()->role->name . '.patients.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </a>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>Mettre à jour
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
