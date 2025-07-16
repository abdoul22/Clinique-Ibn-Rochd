@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                        Détails du lit
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $lit->nom_complet }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('lits.edit', $lit->id) }}"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                    <a href="{{ route('lits.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Informations du lit</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Numéro</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $lit->numero }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chambre</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                @if($lit->chambre)
                                @if($lit->chambre->nom)
                                {{ $lit->chambre->nom }}
                                @if($lit->chambre->batiment)
                                - {{ $lit->chambre->batiment }}
                                @endif
                                @if($lit->chambre->etage)
                                (Étage {{ $lit->chambre->etage }})
                                @endif
                                @else
                                <span class="text-red-500">Nom de chambre manquant</span>
                                @endif
                                @else
                                <span class="text-red-500">Chambre non trouvée (ID: {{ $lit->chambre_id }})</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucfirst($lit->type) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                            @if($lit->statut === 'libre')
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Libre
                            </span>
                            @elseif($lit->statut === 'occupe')
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                Occupé
                            </span>
                            @else
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Maintenance
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($lit->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $lit->description }}</p>
                    </div>
                    @endif
                </div>

                <!-- Hospitalisation actuelle -->
                @if($lit->hospitalisationActuelle)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mt-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Hospitalisation actuelle</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Patient</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $lit->hospitalisationActuelle->patient->nom ?? 'N/A' }} {{
                                $lit->hospitalisationActuelle->patient->prenom ?? '' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Médecin</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                Dr. {{ $lit->hospitalisationActuelle->medecin->nom ?? 'N/A' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date
                                d'entrée</label>
                            <p class="mt-1 text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($lit->hospitalisationActuelle->date_entree)->format('d/m/Y
                                H:i') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {{ ucfirst($lit->hospitalisationActuelle->statut) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Statistiques et actions -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Actions</h2>

                    <div class="space-y-3">
                        @if($lit->statut === 'libre')
                        <div class="p-3 bg-green-50 dark:bg-green-900 rounded-lg">
                            <p class="text-sm text-green-800 dark:text-green-200">
                                <i class="fas fa-check-circle mr-2"></i>Ce lit est disponible pour une hospitalisation
                            </p>
                        </div>
                        @elseif($lit->statut === 'occupe')
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Ce lit est actuellement occupé
                            </p>
                        </div>
                        @else
                        <div class="p-3 bg-red-50 dark:bg-red-900 rounded-lg">
                            <p class="text-sm text-red-800 dark:text-red-200">
                                <i class="fas fa-tools mr-2"></i>Ce lit est en maintenance
                            </p>
                        </div>
                        @endif

                        <form action="{{ route('lits.destroy', $lit->id) }}" method="POST" class="inline w-full"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce lit ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>Supprimer le lit
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
