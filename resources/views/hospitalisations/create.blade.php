@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-200 via-slate-100 to-blue-100 dark:from-gray-900 dark:via-blue-900 dark:to-indigo-900 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <!-- En-tête moderne avec animation -->
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mb-4 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                    Nouvelle Hospitalisation
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Créer une nouvelle hospitalisation pour un patient</p>

                <!-- Bouton retour moderne -->
                <div class="mt-6">
                    <a href="{{ route('hospitalisations.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour à la liste
                    </a>
                </div>
            </div>

            <!-- Formulaire moderne -->
            <form method="POST" action="{{ route('hospitalisations.store') }}" id="hospitalisationForm"
                  class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border-2 border-gray-400 dark:border-gray-700 drop-shadow-2xl">
                @csrf

                <!-- Section 1: Informations Patient & Médecin -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        Informations Principales
                    </h2>
                    <p class="text-blue-100 dark:text-blue-100 text-white mt-2">Sélectionnez le patient et le médecin responsable</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Patient -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                Patient *
                            </label>
                            <select name="gestion_patient_id" required
                                    class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 text-lg shadow-sm">
                                <option value="">Sélectionner un patient</option>
                                @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->nom }} {{ $patient->prenom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Médecin -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Médecin Traitant *
                            </label>
                            <select name="medecin_id" required
                                    class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-200 text-lg shadow-sm">
                                <option value="">Sélectionner un médecin</option>
                                @foreach($medecins as $medecin)
                                <option value="{{ $medecin->id }}">Dr. {{ $medecin->nom }} {{ $medecin->prenom ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Service -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                Service *
                            </label>
                            <select name="service_id" required
                                    class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all duration-200 text-lg shadow-sm">
                                <option value="">Sélectionner un service</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ $defaultServiceId == $service->id ? 'selected' : '' }}>{{ $service->nom }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Statut -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Statut *
                            </label>
                            <select name="statut" required
                                    class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-orange-500 focus:ring-4 focus:ring-orange-500/20 transition-all duration-200 text-lg shadow-sm">
                                <option value="en cours">En cours</option>
                                <option value="terminé">Terminé</option>
                                <option value="annulé">Annulé</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Période -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        Période d'Hospitalisation
                    </h2>
                    <p class="text-white mt-2">Définissez les dates d'entrée et de sortie</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Date d'entrée -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                Date d'entrée *
                            </label>
                            <input type="date" name="date_entree" id="date_entree" required
                                   class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 text-lg shadow-sm">
                        </div>

                        <!-- Date de sortie -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Date de sortie (optionnelle)
                            </label>
                            <input type="date" name="date_sortie" id="date_sortie"
                                   class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-200 text-lg shadow-sm">
                        </div>

                        <!-- Motif -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                Motif
                            </label>
                            <input type="text" name="motif" placeholder="Motif de l'hospitalisation"
                                   class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 transition-all duration-200 text-lg shadow-sm">
                        </div>
                    </div>

                    <!-- Indicateur de durée -->
                    <div id="duree-indicator" class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-700 hidden">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-blue-800 dark:text-blue-200">Durée du séjour</p>
                                <p id="duree-text" class="text-blue-600 dark:text-blue-300"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Logement -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        Logement & Tarification
                    </h2>
                    <p class="text-white mt-2">Sélectionnez la chambre et le lit pour le patient</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Chambre -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-emerald-100 dark:bg-emerald-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                Chambre *
                            </label>
                            <select id="chambre-select" name="chambre_id" required
                                    class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 transition-all duration-200 text-lg shadow-sm">
                                <option value="">Sélectionner une chambre</option>
                                @foreach($chambres as $chambre)
                                <option value="{{ $chambre->id }}" data-prix="{{ $chambre->tarif_journalier ?? 5000 }}" data-lits-count="{{ $chambre->lits->count() }}">
                                    {{ $chambre->nom_complet }} - {{ number_format($chambre->tarif_journalier ?? 5000, 0, ',', ' ') }} MRU/jour ({{ $chambre->lits->count() }} lits libres)
                                </option>
                                @endforeach
                            </select>
                            <!-- Alerte pour chambre sans lits libres -->
                            <div id="chambre-alert" class="hidden mt-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <p class="text-red-700 dark:text-red-300 font-medium">Cette chambre n'a aucun lit libre disponible. Veuillez choisir une autre chambre.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Lit -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                    </svg>
                                </div>
                                Lit *
                            </label>
                            <select id="lit-select" name="lit_id" required disabled
                                    class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 transition-all duration-200 text-lg shadow-sm disabled:opacity-50">
                                <option value="">Sélectionner d'abord une chambre</option>
                            </select>
                        </div>

                        <!-- Montant total -->
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div class="w-5 h-5 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                                Montant Total Estimé
                            </label>
                            <div class="relative">
                                <input type="number" step="0.01" name="montant_total" id="montant_total" readonly
                                       class="w-full px-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white text-lg font-bold text-center">
                                <div class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">
                                    MRU
                                </div>
                            </div>                            
                        </div>
                    </div>

                    <!-- Résumé tarifaire -->
                    <div id="tarif-summary" class="mt-8 p-6 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl border border-yellow-200 dark:border-yellow-700 hidden">
                        <h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-200 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16a2 2 0 002 2z"></path>
                            </svg>
                            Résumé Tarifaire
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">Prix par jour</p>
                                <p id="prix-jour" class="text-xl font-bold text-yellow-800 dark:text-yellow-200">0 MRU</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">Nombre de jours</p>
                                <p id="nb-jours" class="text-xl font-bold text-yellow-800 dark:text-yellow-200">0</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">Total</p>
                                <p id="total-calcule" class="text-xl font-bold text-yellow-800 dark:text-yellow-200">0 MRU</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Observations -->
                <div class="bg-gradient-to-r from-gray-500 to-slate-600 p-6">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        Observations Médicales
                    </h2>
                    <p class="text-white mt-2">Notes et observations supplémentaires</p>
                </div>

                <div class="p-8">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Observations</label>
                        <textarea name="observation" rows="4" placeholder="Observations supplémentaires, notes médicales, instructions particulières..."
                                  class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-gray-500 focus:ring-4 focus:ring-gray-500/20 transition-all duration-200 text-lg resize-none shadow-sm"></textarea>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="bg-gray-50 dark:bg-gray-700 px-8 py-8">
                    <div class="flex flex-col sm:flex-row justify-center sm:justify-end gap-4 max-w-md sm:max-w-none mx-auto sm:mx-0">
                        <a href="{{ route('hospitalisations.index') }}"
                           class="inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-gray-600 border-2 border-gray-300 dark:border-gray-500 rounded-xl font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 transition-all duration-200 text-lg min-w-[180px]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Annuler
                        </a>
                        <button type="submit" id="submitBtn"
                                class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 text-lg shadow-lg hover:shadow-xl transform hover:scale-105 min-w-[220px]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span id="submit-text">Créer l'Hospitalisation</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données des chambres et lits
    const litsParChambre = @json($litsParChambre);
    const chambresData = @json($chambresData);

    // Éléments du DOM
    const chambreSelect = document.getElementById('chambre-select');
    const litSelect = document.getElementById('lit-select');
    const dateEntree = document.getElementById('date_entree');
    const dateSortie = document.getElementById('date_sortie');
    const montantTotal = document.getElementById('montant_total');
    const dureeIndicator = document.getElementById('duree-indicator');
    const dureeText = document.getElementById('duree-text');
    const tarifSummary = document.getElementById('tarif-summary');
    const prixJour = document.getElementById('prix-jour');
    const nbJours = document.getElementById('nb-jours');
    const totalCalcule = document.getElementById('total-calcule');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submit-text');

    // Variables globales
    let prixParJour = 0;
    let nombreJours = 1;

    // Définir la date d'entrée par défaut à aujourd'hui
    const today = new Date().toISOString().split('T')[0];
    dateEntree.value = today;

    // Gestion du changement de chambre
    chambreSelect.addEventListener('change', function() {
        const chambreId = this.value;
        const chambreAlert = document.getElementById('chambre-alert');

        litSelect.disabled = !chambreId;
        litSelect.innerHTML = '<option value="">Sélectionner un lit</option>';
        chambreAlert.classList.add('hidden');

        if (chambreId) {
            const selectedOption = this.options[this.selectedIndex];
            const litsCount = parseInt(selectedOption.getAttribute('data-lits-count')) || 0;

            // Vérifier si la chambre a des lits libres
            if (litsCount === 0) {
                // Afficher l'alerte
                chambreAlert.classList.remove('hidden');
                litSelect.disabled = true;
                litSelect.innerHTML = '<option value="">Aucun lit libre dans cette chambre</option>';

                // Empêcher la soumission du formulaire
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

                // Réinitialiser les prix
                prixParJour = 0;
                calculerMontantTotal();
                return;
            } else {
                // Réactiver le bouton de soumission
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            if (litsParChambre[chambreId]) {
                // Remplir les lits disponibles
                litsParChambre[chambreId].forEach(lit => {
                    const option = document.createElement('option');
                    option.value = lit.id;
                    option.textContent = lit.nom_complet;
                    litSelect.appendChild(option);
                });

                // Mettre à jour le prix par jour
                if (chambresData[chambreId]) {
                    prixParJour = chambresData[chambreId].prix_par_jour;
                    calculerMontantTotal();
                }
            }
        } else {
            prixParJour = 0;
            calculerMontantTotal();

            // Réactiver le bouton de soumission
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });

    // Gestion des changements de dates
    dateEntree.addEventListener('change', calculerDuree);
    dateSortie.addEventListener('change', calculerDuree);

    function calculerDuree() {
        const entree = new Date(dateEntree.value);
        const sortie = dateSortie.value ? new Date(dateSortie.value) : null;

        if (dateEntree.value) {
            if (sortie && sortie >= entree) {
                // Calculer la différence en jours - corriger le calcul pour éviter le problème de "2 jours"
                const diffTime = Math.abs(sortie - entree);
                nombreJours = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 pour inclure le jour d'entrée

                dureeIndicator.classList.remove('hidden');
                dureeText.textContent = `${nombreJours} jour${nombreJours > 1 ? 's' : ''} (du ${formatDate(entree)} au ${formatDate(sortie)})`;
            } else if (!sortie) {
                // Pas de date de sortie - pour une nouvelle hospitalisation, c'est 1 jour par défaut
                const aujourd_hui = new Date();
                const entreeDate = new Date(dateEntree.value);

                // Si c'est aujourd'hui ou dans le futur, c'est 1 jour
                if (entreeDate >= aujourd_hui || entreeDate.toDateString() === aujourd_hui.toDateString()) {
                    nombreJours = 1;
                } else {
                    // Si c'est dans le passé, calculer depuis la date d'entrée
                    const diffTime = Math.abs(aujourd_hui - entreeDate);
                    nombreJours = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                }

                dureeIndicator.classList.remove('hidden');
                dureeText.textContent = `${nombreJours} jour${nombreJours > 1 ? 's' : ''} (depuis le ${formatDate(entree)})`;
            } else {
                dureeIndicator.classList.add('hidden');
                nombreJours = 1;
            }
        } else {
            dureeIndicator.classList.add('hidden');
            nombreJours = 1;
        }

        calculerMontantTotal();
    }

    function calculerMontantTotal() {
        if (prixParJour > 0 && nombreJours > 0) {
            const total = prixParJour * nombreJours;
            montantTotal.value = total.toFixed(2);

            // Afficher le résumé tarifaire
            tarifSummary.classList.remove('hidden');
            prixJour.textContent = `${formatNumber(prixParJour)} MRU`;
            nbJours.textContent = nombreJours;
            totalCalcule.textContent = `${formatNumber(total)} MRU`;
        } else {
            montantTotal.value = '';
            tarifSummary.classList.add('hidden');
        }
    }

    function formatDate(date) {
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('fr-FR').format(number);
    }

    // Gestion de la soumission du formulaire
    document.getElementById('hospitalisationForm').addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        submitText.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Création en cours...
        `;
    });

    // Calculer la durée initiale
    calculerDuree();

    // Animation d'entrée pour les sections
    const sections = document.querySelectorAll('form > div');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        setTimeout(() => {
            section.style.transition = 'all 0.6s ease-out';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, index * 150);
    });

    // Validation en temps réel
    const requiredFields = document.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('border-red-500', 'ring-red-500');
                this.classList.remove('border-gray-200', 'dark:border-gray-600');
            } else {
                this.classList.remove('border-red-500', 'ring-red-500');
                this.classList.add('border-green-500', 'ring-green-500');
                setTimeout(() => {
                    this.classList.remove('border-green-500', 'ring-green-500');
                    this.classList.add('border-gray-200', 'dark:border-gray-600');
                }, 2000);
            }
        });
    });

    // Effet de focus amélioré
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('transform', 'scale-105');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('transform', 'scale-105');
        });
    });
});
</script>

<style>
/* Animations personnalisées */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-slide-in-up {
    animation: slideInUp 0.6s ease-out;
}

.animate-pulse-slow {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Styles pour les focus states */
input:focus, select:focus, textarea:focus {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Gradient text pour les titres */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Hover effects pour les boutons */
button:hover, .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Styles pour les sections avec gradient */
.section-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Animation pour les icônes */
.icon-bounce:hover {
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0,-30px,0);
    }
    70% {
        transform: translate3d(0,-15px,0);
    }
    90% {
        transform: translate3d(0,-4px,0);
    }
}
</style>
@endsection
