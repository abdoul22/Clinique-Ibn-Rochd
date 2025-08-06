@extends('layouts.app')

@section('title', 'Détails du Personnel')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header avec gradient -->
        <div class="gradient-header mb-8">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-user-tie mr-3"></i>Détails du Personnel
                        </h1>
                        <p class="text-blue-100 text-lg">Informations complètes du membre du personnel</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('personnels.edit', $personnel) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                        <a href="{{ route('personnels.index') }}"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
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

        <!-- Informations principales -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Carte d'identité -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="bg-blue-600 dark:bg-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-id-card mr-3"></i>Informations Personnelles
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center">
                        <div
                            class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-user text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $personnel->nom }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $personnel->fonction }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center">
                            <i class="fas fa-phone text-gray-400 mr-3 w-5"></i>
                            <span class="text-gray-900 dark:text-white">
                                {{ $personnel->telephone ?: 'Non renseigné' }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-3 w-5"></i>
                            <span class="text-gray-900 dark:text-white">
                                {{ $personnel->adresse ?: 'Non renseignée' }}
                            </span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-gray-400 mr-3 w-5"></i>
                            <span class="text-gray-900 dark:text-white">
                                Membre depuis {{ $personnel->created_at->format('d/m/Y') }}
                            </span>
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
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($personnel->salaire, 2) }} MRU
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Salaire mensuel</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ number_format($personnel->credit, 2) }} MRU
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Crédit actuel</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Statut d'approbation :</span>
                            @if($personnel->is_approved)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                <i class="fas fa-check mr-1"></i>Approuvé
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                <i class="fas fa-clock mr-1"></i>En attente
                            </span>
                            @endif
                        </div>

                        @if($personnel->statut_credit)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Statut des crédits :</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $personnel->statut_color }}">
                                {{ ucfirst($personnel->statut_credit) }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-purple-600 dark:bg-purple-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-cogs mr-3"></i>Actions Rapides
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('personnels.edit', $personnel) }}"
                        class="flex items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors duration-200">
                        <i class="fas fa-edit text-blue-600 dark:text-blue-400 mr-3"></i>
                        <span class="text-blue-600 dark:text-blue-400 font-semibold">Modifier</span>
                    </a>

                    <a href="{{ route('credits.create', ['personnel_id' => $personnel->id]) }}"
                        class="flex items-center justify-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors duration-200">
                        <i class="fas fa-plus text-green-600 dark:text-green-400 mr-3"></i>
                        <span class="text-green-600 dark:text-green-400 font-semibold">Nouveau Crédit</span>
                    </a>

                    <form action="{{ route('personnels.destroy', $personnel) }}" method="POST" class="inline"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce personnel ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full flex items-center justify-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors duration-200">
                            <i class="fas fa-trash text-red-600 dark:text-red-400 mr-3"></i>
                            <span class="text-red-600 dark:text-red-400 font-semibold">Supprimer</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
