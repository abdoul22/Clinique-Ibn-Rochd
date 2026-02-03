@extends('layouts.app')

@section('title', 'Modifier l\'Administrateur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header avec gradient -->
        <div class="gradient-header mb-8">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-edit mr-3"></i>Modifier l'Administrateur
                        </h1>
                        <p class="text-blue-100 text-lg">Modifiez les informations de l'utilisateur</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('superadmin.admins.show', $admin->id) }}"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-eye mr-2"></i>Voir les détails
                        </a>
                        <a href="{{ route('superadmin.admins.index') }}"
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

        <form method="POST" action="{{ route('superadmin.admins.update', $admin->id) }}" class="space-y-8">
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
                            </label>
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-envelope mr-1"></i>Adresse email *
                            </label>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fonction et statut -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-green-600 dark:bg-green-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-briefcase mr-3"></i>
                        @if($admin->role?->name === 'superadmin')
                            Rôle et Statut
                        @else
                            Rôle, Fonction et Statut
                        @endif
                    </h2>
                </div>
                <div class="p-6">
                    @if($admin->role?->name === 'superadmin')
                        <!-- Interface pour SUPERADMIN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Rôle (non modifiable pour superadmin) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-crown mr-1"></i>Rôle
                                </label>
                                <div class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                        <i class="fas fa-crown mr-2"></i>Super Administrateur
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>Le rôle Super Administrateur ne peut pas être modifié
                                </p>
                            </div>

                            <!-- Statut d'approbation (toujours approuvé pour superadmin) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-shield-alt mr-1"></i>Statut d'approbation
                                </label>
                                <div class="flex items-center">
                                    <label class="relative inline-flex items-center cursor-not-allowed opacity-75">
                                        <input type="checkbox" checked disabled class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                            Approuvé
                                        </span>
                                    </label>
                                </div>
                                <!-- Champ caché pour maintenir l'approbation -->
                                <input type="hidden" name="is_approved" value="1">
                            </div>
                        </div>
                    @else
                        <!-- Interface pour ADMIN et MEDECIN -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Sélection du Rôle -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-user-shield mr-1"></i>Rôle *
                                </label>
                                <select name="user_role" id="user-role-select"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                    onchange="toggleRoleFields(this.value)">
                                    <option value="admin" {{ old('user_role', $admin->role?->name) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="medecin" {{ old('user_role', $admin->role?->name) === 'medecin' ? 'selected' : '' }}>Médecin</option>
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Définit les permissions de l'utilisateur
                                </p>
                            </div>

                            <!-- Sélection de la Fonction (pour admins) -->
                            <div id="fonction-select" style="{{ old('user_role', $admin->role?->name) === 'medecin' ? 'display:none' : '' }}">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-briefcase mr-1"></i>Fonction
                                </label>
                                <select name="fonction"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                                    <option value="">-- Choisir --</option>
                                    <option value="Caissier" {{ old('fonction', $admin->fonction) == 'Caissier' ? 'selected' : '' }}>Caissier</option>
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    La fonction sera synchronisée avec le module personnel
                                </p>
                            </div>

                            <!-- Sélection du Médecin Associé (pour médecins seulement) -->
                            <div id="medecin-select" style="{{ old('user_role', $admin->role?->name) === 'medecin' ? '' : 'display:none' }}">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-user-md mr-1"></i>Médecin Associé
                                </label>
                                <select name="medecin_id"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                                    <option value="">-- Choisir un médecin --</option>
                                    
                                    @if($admin->medecin && !$medecinsList->contains('id', $admin->medecin_id))
                                        {{-- Afficher le médecin actuellement associé même s'il n'est plus disponible --}}
                                        <option value="{{ $admin->medecin->id }}" selected>
                                            {{ $admin->medecin->nom_complet_avec_prenom }} - {{ $admin->medecin->specialite ?? 'Médecin' }} (Actuel)
                                        </option>
                                    @endif
                                    
                                    @foreach($medecinsList as $medecinItem)
                                        <option value="{{ $medecinItem->id }}" {{ old('medecin_id', $admin->medecin_id) == $medecinItem->id ? 'selected' : '' }}>
                                            {{ $medecinItem->nom_complet_avec_prenom }} - {{ $medecinItem->specialite ?? 'Médecin' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Lier ce compte à un profil médecin existant
                                </p>
                            </div>

                            <!-- Statut d'approbation -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-shield-alt mr-1"></i>Statut d'approbation
                                </label>
                                <div class="flex items-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_approved" value="1" {{ old('is_approved',
                                            $admin->is_approved) ? 'checked' : '' }}
                                        class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                            {{ $admin->is_approved ? 'Approuvé' : 'En attente' }}
                                        </span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Détermine si l'utilisateur peut accéder à l'application
                                </p>
                            </div>
                        </div>

                        <script>
                            function toggleRoleFields(role) {
                                const fonctionDiv = document.getElementById('fonction-select');
                                const medecinDiv = document.getElementById('medecin-select');
                                
                                if (role === 'medecin') {
                                    fonctionDiv.style.display = 'none';
                                    medecinDiv.style.display = 'block';
                                } else {
                                    fonctionDiv.style.display = 'block';
                                    medecinDiv.style.display = 'none';
                                }
                            }
                        </script>
                    @endif
                </div>
            </div>

            <!-- Informations système -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-purple-600 dark:bg-purple-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-info-circle mr-3"></i>Informations Système
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600 dark:text-gray-400">Créé le :</span>
                                <span class="ml-2 text-gray-900 dark:text-white">{{ $admin->created_at->format('d/m/Y à
                                    H:i') }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600 dark:text-gray-400">Dernière connexion :</span>
                                <span class="ml-2 text-gray-900 dark:text-white">
                                    {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Jamais
                                    connecté' }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-user-shield text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600 dark:text-gray-400">Rôle :</span>
                                <span class="ml-2 text-gray-900 dark:text-white">{{ $admin->role->name ?? 'Admin'
                                    }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-sync-alt text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600 dark:text-gray-400">Synchronisation :</span>
                                <span class="ml-2 text-gray-900 dark:text-white">
                                    {{ $admin->fonction ? 'Avec le personnel' : 'Non synchronisé' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 justify-end">
                <a href="{{ route('superadmin.admins.show', $admin->id) }}"
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
