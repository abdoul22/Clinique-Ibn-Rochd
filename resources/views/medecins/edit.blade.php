@extends('layouts.app')
@section('title', 'Modifier le Médecin')

@section('content')

<!-- Header avec gradient -->
<div class="gradient-header mb-8">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <div class="flex items-center mb-2">
                    <a href="{{ route(auth()->user()->role->name . '.medecins.show', $medecin->id) }}"
                        class="text-blue-100 hover:text-white mr-4 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">
                        <i class="fas fa-edit mr-3"></i>Modifier le Médecin
                    </h1>
                </div>
                <p class="text-blue-100 text-lg">Modifiez les informations du Dr. {{ $medecin->nom }} {{
                    $medecin->prenom }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route(auth()->user()->role->name . '.medecins.show', $medecin->id) }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-eye mr-2"></i>Voir les détails
                </a>
                <a href="{{ route(auth()->user()->role->name . '.medecins.index') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-list mr-2"></i>Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto">

        <!-- Formulaire principal -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">

            <!-- Header du formulaire -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12">
                        <div class="h-12 w-12 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-white">Informations du Médecin</h2>
                        <p class="text-blue-100">Tous les champs marqués d'un * sont obligatoires</p>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <form method="POST" action="{{ route(auth()->user()->role->name . '.medecins.update', $medecin->id) }}"
                class="p-6">
                @csrf
                @method('PUT')

                <!-- Messages d'erreur -->
                @if ($errors->any())
                <div class="mb-6 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Erreurs de validation</h3>
                    </div>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Informations de base -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>Informations de base
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nom * <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom', $medecin->nom) }}"
                                placeholder="Nom du médecin"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-200 @error('nom') border-red-500 @enderror"
                                required>
                            @error('nom')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Prénom -->
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Prénom * <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $medecin->prenom) }}"
                                placeholder="Prénom du médecin"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-200 @error('prenom') border-red-500 @enderror"
                                required>
                            @error('prenom')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Spécialité -->
                        <div class="md:col-span-2">
                            <label for="specialite"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Spécialité * <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="specialite" name="specialite"
                                value="{{ old('specialite', $medecin->specialite) }}" placeholder="Spécialité médicale"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-200 @error('specialite') border-red-500 @enderror"
                                required>
                            @error('specialite')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-address-book text-green-500 mr-2"></i>Informations de contact
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Téléphone -->
                        <div>
                            <label for="telephone"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-phone mr-1"></i>Numéro de téléphone
                            </label>
                            <input type="tel" id="telephone" name="telephone"
                                value="{{ old('telephone', $medecin->telephone) }}" placeholder="+222 XX XX XX XX"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-200">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-envelope mr-1"></i>Adresse email
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $medecin->email) }}"
                                placeholder="medecin@clinique.com"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-200 @error('email') border-red-500 @enderror">
                            @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div class="md:col-span-2">
                            <label for="adresse"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>Adresse
                            </label>
                            <textarea id="adresse" name="adresse" rows="3" placeholder="Adresse complète du médecin"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-200">{{ old('adresse', $medecin->adresse) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Statut et configuration -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-toggle-on text-purple-500 mr-2"></i>Statut et configuration
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Statut -->
                        <div>
                            <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-user-check mr-1"></i>Statut
                            </label>
                            <select id="statut" name="statut"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-200">
                                <option value="actif" {{ old('statut', $medecin->statut) == 'actif' ? 'selected' : ''
                                    }}>Actif</option>
                                <option value="inactif" {{ old('statut', $medecin->statut) == 'inactif' ? 'selected' :
                                    '' }}>Inactif</option>
                                <option value="suspendu" {{ old('statut', $medecin->statut) == 'suspendu' ? 'selected' :
                                    '' }}>Suspendu</option>
                                <option value="retraité" {{ old('statut', $medecin->statut) == 'retraité' ? 'selected' :
                                    '' }}>Retraité</option>
                            </select>
                        </div>

                        <!-- ID Médecin (lecture seule) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-id-card mr-1"></i>ID Médecin
                            </label>
                            <input type="text" value="#{{ $medecin->id }}"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed"
                                disabled>
                        </div>
                    </div>
                </div>



                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="gradient-button flex-1 flex items-center justify-center px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>Enregistrer les modifications
                    </button>

                    <a href="{{ route(auth()->user()->role->name . '.medecins.show', $medecin->id) }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>

        <!-- Carte d'informations -->
        <div
            class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-clock text-gray-500 mr-2"></i>Informations de modification
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Créé le :</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $medecin->created_at->format('d/m/Y à
                        H:i') }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-500 dark:text-gray-400">Dernière modification :</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $medecin->updated_at->format('d/m/Y à
                        H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation d'entrée pour le formulaire
        const form = document.querySelector('form');
        form.style.opacity = '0';
        form.style.transform = 'translateY(20px)';

        setTimeout(() => {
            form.style.transition = 'all 0.5s ease';
            form.style.opacity = '1';
            form.style.transform = 'translateY(0)';
        }, 100);

        // Validation en temps réel
        const requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('border-red-500');
                } else {
                    this.classList.remove('border-red-500');
                }
            });
        });
    });
</script>
@endpush
