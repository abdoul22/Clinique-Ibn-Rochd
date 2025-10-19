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
                            <i class="fas fa-user mr-3"></i>Détails du Patient
                        </h1>
                        <p class="text-blue-100 text-lg">Informations complètes du patient</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route(auth()->user()->role->name . '.patients.edit', $patient->id) }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                        <a href="{{ route(auth()->user()->role->name . '.patients.index') }}"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                            <i class="fas fa-arrow-left mr-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal dans un seul cadre -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- En-tête du cadre -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">{{ $patient->first_name }} {{ $patient->last_name }}
                        </h2>
                        <p class="text-blue-100">Patient</p>
                    </div>
                </div>
            </div>

            <!-- Contenu du cadre -->
            <div class="p-8">
                <!-- Informations personnelles -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                        Informations Personnelles
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-venus-mars text-blue-600 mr-3 w-6"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sexe</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $patient->gender }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-birthday-cake text-blue-600 mr-3 w-6"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Âge</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ $patient->age }} ans
                                    @if($patient->date_of_birth)
                                    <span class="text-sm text-gray-500">(né le {{
                                        \Carbon\Carbon::parse($patient->date_of_birth)->format('d/m/Y') }})</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-address-book mr-2 text-green-600"></i>
                        Informations de Contact
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-phone text-green-600 mr-3 w-6"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Téléphone</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $patient->phone }}</p>
                            </div>
                        </div>
                        <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-700 rounded-lg md:col-span-2">
                            <i class="fas fa-map-marker-alt text-green-600 mr-3 w-6 mt-1"></i>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Adresse</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $patient->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-purple-600"></i>
                        Statistiques
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div
                            class="text-center p-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200/50 dark:border-blue-800/30">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                                {{ $patient->rendezVous()->count() }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Rendez-vous</div>
                        </div>
                        <div
                            class="text-center p-6 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200/50 dark:border-green-800/30">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                                {{ $patient->caisses()->count() }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300">Factures</div>
                        </div>
                        <div
                            class="text-center p-6 bg-purple-50 dark:bg-purple-900/30 rounded-lg border border-purple-200/50 dark:border-purple-700/30">
                            <div class="text-3xl font-bold text-purple-600 dark:text-purple-300 mb-2">
                                {{ $patient->created_at->diffForHumans() }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-200">Inscrit</div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-cogs mr-2 text-red-600"></i>
                        Actions
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route(auth()->user()->role->name . '.patients.edit', $patient->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Modifier le patient
                        </a>
                        <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.rendezvous.create', ['patient_id' => $patient->id]) : route('rendezvous.create', ['patient_id' => $patient->id]) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Nouveau rendez-vous
                        </a>
                        <form action="{{ route(auth()->user()->role->name . '.patients.destroy', $patient->id) }}"
                            method="POST" class="inline"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>
                                Supprimer le patient
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
