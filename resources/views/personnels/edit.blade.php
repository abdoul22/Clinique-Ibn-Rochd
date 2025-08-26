@extends('layouts.app')

@section('title', 'Modifier le Personnel')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header avec gradient -->
        <div class="gradient-header mb-8">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-edit mr-3"></i>Modifier le Personnel
                        </h1>
                        <p class="text-blue-100 text-lg">Modifiez les informations du membre du personnel</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('personnels.show', $personnel) }}"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-eye mr-2"></i>Voir les détails
                        </a>
                        <a href="{{ route('personnels.index') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div
            class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div
            class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded mb-6">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif



        <form action="{{ route('personnels.update', $personnel) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Informations personnelles -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-blue-600 dark:bg-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user mr-3"></i>Informations Personnelles
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-user mr-1"></i>Nom complet *
                                @if(isset($isLinkedToUser) && $isLinkedToUser)
                                <span class="text-xs text-orange-600">(Géré par les utilisateurs)</span>
                                @endif
                            </label>
                            <input type="text" name="nom" value="{{ old('nom', $personnel->nom) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white @if(isset($isLinkedToUser) && $isLinkedToUser) bg-gray-100 dark:bg-gray-700 cursor-not-allowed @else bg-white dark:bg-gray-900 @endif"
                                @if(isset($isLinkedToUser) && $isLinkedToUser) readonly @else required @endif>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-briefcase mr-1"></i>Fonction *
                                @if(isset($isLinkedToUser) && $isLinkedToUser)
                                <span class="text-xs text-orange-600">(Géré par les utilisateurs)</span>
                                @endif
                            </label>
                            @if(isset($isLinkedToUser) && $isLinkedToUser)
                            <input type="text" value="{{ $personnel->fonction }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed"
                                readonly>
                            <input type="hidden" name="fonction" value="{{ $personnel->fonction }}">
                            @else
                            <select name="fonction"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                required>
                                <option value="">Sélectionner une fonction</option>
                                <option value="Caissier" {{ old('fonction', $personnel->fonction) == 'Caissier' ?
                                    'selected' : '' }}>Caissier</option>
                                <option value="RH" {{ old('fonction', $personnel->fonction) == 'RH' ? 'selected' : ''
                                    }}>RH</option>
                                <option value="Support" {{ old('fonction', $personnel->fonction) == 'Support' ?
                                    'selected' : '' }}>Support</option>
                                <option value="Infirmier" {{ old('fonction', $personnel->fonction) == 'Infirmier' ?
                                    'selected' : '' }}>Infirmier</option>
                                <option value="Médecin" {{ old('fonction', $personnel->fonction) == 'Médecin' ?
                                    'selected' : '' }}>Médecin</option>
                                <option value="Réceptionniste" {{ old('fonction', $personnel->fonction) ==
                                    'Réceptionniste' ? 'selected' : '' }}>Réceptionniste</option>
                                <option value="Gestionnaire" {{ old('fonction', $personnel->fonction) == 'Gestionnaire'
                                    ? 'selected' : '' }}>Gestionnaire</option>
                            </select>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-phone mr-1"></i>Numéro de téléphone
                            </label>
                            <input type="text" name="telephone" value="{{ old('telephone', $personnel->telephone) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                placeholder="Ex: +222 12345678">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>Adresse
                            </label>
                            <input type="text" name="adresse" value="{{ old('adresse', $personnel->adresse) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                placeholder="Adresse complète">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations financières -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-green-600 dark:bg-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-money-bill-wave mr-3"></i>Informations Financières
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-salary mr-1"></i>Salaire mensuel (MRU) *
                            </label>
                            <input type="number" name="salaire" value="{{ old('salaire', $personnel->salaire) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                step="0.01" min="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-credit-card mr-1"></i>Crédit actuel (MRU)
                            </label>
                            <input type="number" name="credit" value="{{ old('credit', $personnel->credit) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                step="0.01" min="0" readonly>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Géré automatiquement par le système
                                de crédits</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statut d'approbation -->
            @if(!(isset($isLinkedToUser) && $isLinkedToUser))
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-purple-600 dark:bg-purple-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user-check mr-3"></i>Statut d'Approval
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-toggle-on mr-1"></i>Statut d'approbation
                            </label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Détermine si le personnel peut accéder aux fonctionnalités de l'application
                            </p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_approved" value="1" {{ old('is_approved',
                                    $personnel->is_approved) ? 'checked' : '' }}
                                class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600">
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                    {{ $personnel->is_approved ? 'Approuvé' : 'En attente' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Statut d'approbation en lecture seule pour les personnels liés aux utilisateurs -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gray-600 dark:bg-gray-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user-check mr-3"></i>Statut d'Approval
                        <span class="text-xs bg-gray-800 px-2 py-1 rounded ml-2">Géré par les utilisateurs</span>
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-toggle-on mr-1"></i>Statut d'approbation (lecture seule)
                            </label>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Ce statut est géré via le module de gestion des utilisateurs
                            </p>
                        </div>
                        <div class="flex items-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $personnel->is_approved ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                <i class="fas {{ $personnel->is_approved ? 'fa-check' : 'fa-clock' }} mr-1"></i>
                                {{ $personnel->is_approved ? 'Approuvé' : 'En attente' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('personnels.show', $personnel) }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i>Annuler
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection