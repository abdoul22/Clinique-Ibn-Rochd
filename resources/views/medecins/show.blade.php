@extends('layouts.app')
@section('title', 'Détails du Médecin')

@section('content')

<!-- Header avec gradient -->
<div class="gradient-header mb-8">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <div class="flex items-center mb-2">
                    <a href="{{ route(auth()->user()->role->name . '.medecins.index') }}"
                        class="text-blue-100 hover:text-white mr-4 transition-colors duration-200">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">
                        <i class="fas fa-user-md mr-3"></i>Détails du Médecin
                    </h1>
                </div>
                <p class="text-blue-100 text-lg">Informations complètes de {{ $medecin->nom_complet_avec_prenom }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route(auth()->user()->role->name . '.medecins.edit', $medecin->id) }}"
                    class="gradient-button flex items-center justify-center px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <a href="{{ route('medecins.stats', $medecin->id) }}"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-chart-bar mr-2"></i>Statistiques
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<div class="container mx-auto px-4">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Carte principale - Informations personnelles -->
        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16">
                            <div class="h-16 w-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                                <i class="fas fa-user-md text-white text-2xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-2xl font-bold text-white">{{ $medecin->nom_complet_avec_prenom }}
                            </h2>
                            <p class="text-blue-100">{{ $medecin->specialite }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Informations de base -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>Informations de base
                            </h3>

                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-id-card text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">ID Médecin</p>
                                        <p class="font-medium text-gray-900 dark:text-white">#{{ $medecin->id }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-stethoscope text-green-600 dark:text-green-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Spécialité</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medecin->specialite }}
                                        </p>
                                    </div>
                                </div>

                                @if($medecin->numero_licence)
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-certificate text-purple-600 dark:text-purple-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Numéro de licence</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medecin->numero_licence
                                            }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($medecin->experience)
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Expérience</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medecin->experience }}
                                        </p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Contact et localisation -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                                <i class="fas fa-address-book text-green-500 mr-2"></i>Contact & Localisation
                            </h3>

                            <div class="space-y-3">
                                @if($medecin->email)
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-envelope text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medecin->email }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($medecin->telephone)
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-phone text-green-600 dark:text-green-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Téléphone</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medecin->telephone }}
                                        </p>
                                    </div>
                                </div>
                                @endif

                                @if($medecin->adresse)
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-map-marker-alt text-red-600 dark:text-red-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Adresse</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medecin->adresse }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($medecin->ville)
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-city text-indigo-600 dark:text-indigo-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Ville</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medecin->ville }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($medecin->pays)
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-flag text-yellow-600 dark:text-yellow-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Pays</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $medecin->pays }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Statut et actions -->
        <div class="lg:col-span-1">
            <div class="space-y-6">

                <!-- Carte de statut -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-toggle-on text-blue-500 mr-2"></i>Statut
                    </h3>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Statut actuel</span>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $medecin->statut == 'actif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                <i
                                    class="fas {{ $medecin->statut == 'actif' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ ucfirst($medecin->statut ?? 'actif') }}
                            </span>
                        </div>

                        @if($medecin->date_embauche)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Date d'embauche</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($medecin->date_embauche)->format('d/m/Y') }}
                            </span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Membre depuis</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $medecin->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Carte d'actions rapides -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-cogs text-green-500 mr-2"></i>Actions rapides
                    </h3>

                    <div class="space-y-3">
                        <a href="{{ route(auth()->user()->role->name . '.medecins.edit', $medecin->id) }}"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Modifier le profil
                        </a>

                        <a href="{{ route('medecins.stats', $medecin->id) }}"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-chart-bar mr-2"></i>Voir les statistiques
                        </a>

                        <a href="{{ route(auth()->user()->role->name . '.medecins.index') }}"
                            class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-list mr-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>

                <!-- Carte de suppression -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-red-200 dark:border-red-700 p-6">
                    <h3 class="text-lg font-semibold text-red-900 dark:text-red-100 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Zone dangereuse
                    </h3>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Cette action est irréversible. Toutes les données associées à ce médecin seront supprimées.
                    </p>

                    <form action="{{ route(auth()->user()->role->name . '.medecins.destroy', $medecin->id) }}"
                        method="POST"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce médecin ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i>Supprimer le médecin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation d'entrée pour les cartes
        const cards = document.querySelectorAll('.bg-white, .dark\\:bg-gray-800');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endpush