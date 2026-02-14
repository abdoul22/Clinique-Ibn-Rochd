@extends('layouts.app')

@section('content')
<style>
    /* Masquer la flèche native du datalist pour tous les champs avec liste */
    [list]::-webkit-calendar-picker-indicator,
    [list]::-webkit-list-button {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        position: absolute !important;
        left: -9999px !important;
    }
    .datalist-input-wrapper {
        position: relative;
        width: 100%;
    }
    .datalist-input-wrapper .datalist-clear-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 0.5rem;
        left: auto;
    }
    .datalist-input-wrapper:has(input:disabled) .datalist-clear-btn {
        display: none !important;
    }
</style>
<div
    class="min-h-screen bg-gradient-to-br from-gray-200 via-slate-100 to-blue-100 dark:from-gray-900 dark:via-blue-900 dark:to-indigo-900 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <!-- En-tête moderne avec animation -->
            <div class="mb-8 text-center">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mb-4 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                    Nouvelle Hospitalisation
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Créer une nouvelle hospitalisation pour un patient
                </p>

                <!-- Bouton retour moderne -->
                <div class="mt-6">
                    <a href="{{ auth()->user()->role->name === 'admin' ? route('admin.hospitalisations.index') : route('hospitalisations.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour à la liste
                    </a>
                </div>
            </div>

            <!-- Formulaire moderne -->
            <form method="POST"
                action="{{ auth()->user()->role->name === 'admin' ? route('admin.hospitalisations.store') : route('hospitalisations.store') }}"
                id="hospitalisationForm"
                class="bg-white dark:bg-gray-800 shadow-2xl rounded-3xl overflow-hidden border-2 border-gray-400 dark:border-gray-700 drop-shadow-2xl">
                @csrf

                <!-- Section 1: Informations Patient & Médecin -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        Informations Principales
                    </h2>
                    <p class="text-blue-100 dark:text-blue-100 text-white mt-2">Sélectionnez le patient et le médecin
                        responsable</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Patient -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div
                                    class="w-5 h-5 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                </div>
                                Patient *
                            </label>
                            <div class="datalist-input-wrapper relative">
                                <input type="text" name="patient_search" id="patient-search" list="patients-list"
                                    placeholder="Tapez le nom du patient..."
                                    class="w-full px-5 py-4 pr-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 text-lg shadow-sm">
                                <button type="button" class="datalist-clear-btn w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-200 dark:hover:text-gray-300 dark:hover:bg-gray-600 transition-colors cursor-pointer z-10 hidden" aria-label="Effacer" data-target="patient-search" data-clear-ids="gestion_patient_id" data-clear-values="" data-clear-extra="telephone-input"><i class="fas fa-times text-xs"></i></button>
                            </div>
                            <input type="hidden" name="gestion_patient_id" id="gestion_patient_id" required>
                            <datalist id="patients-list">
                                @foreach($patients as $patient)
                                <option value="{{ $patient->nom }} {{ $patient->prenom }}" 
                                    data-id="{{ $patient->id }}" 
                                    data-telephone="{{ $patient->phone ?? '' }}">
                                @endforeach
                            </datalist>
                        </div>

                        <!-- Téléphone -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div
                                    class="w-5 h-5 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                </div>
                                Téléphone
                            </label>
                            <div class="datalist-input-wrapper relative">
                                <input type="text" id="telephone-input" list="telephones-list"
                                    placeholder="Tapez le numéro de téléphone..."
                                    class="w-full px-5 py-4 pr-12 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-200 text-lg shadow-sm">
                                <button type="button" class="datalist-clear-btn w-7 h-7 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-200 dark:hover:text-gray-300 dark:hover:bg-gray-600 transition-colors cursor-pointer z-10 hidden" aria-label="Effacer" data-target="telephone-input" data-clear-ids="gestion_patient_id" data-clear-values="" data-clear-extra="patient-search"><i class="fas fa-times text-xs"></i></button>
                            </div>
                            <datalist id="telephones-list">
                                @foreach($patients as $patient)
                                @if($patient->phone)
                                <option value="{{ $patient->phone }}" 
                                    data-id="{{ $patient->id }}" 
                                    data-nom="{{ $patient->nom }} {{ $patient->prenom }}">
                                @endif
                                @endforeach
                            </datalist>
                        </div>


                        <!-- Service -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div
                                    class="w-5 h-5 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-purple-600 dark:text-purple-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                Service *
                            </label>
                            <select name="service_id" required disabled
                                class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed text-lg shadow-sm">
                                @foreach($services as $service)
                                @if($defaultServiceId == $service->id)
                                <option value="{{ $service->id }}" selected>{{ $service->nom }}</option>
                                @endif
                                @endforeach
                            </select>
                            <input type="hidden" name="service_id" value="{{ $defaultServiceId }}">
                        </div>

                        <!-- Statut -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div
                                    class="w-5 h-5 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-orange-600 dark:text-orange-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Statut *
                            </label>
                            <select name="statut" required disabled
                                class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed text-lg shadow-sm">
                                <option value="en cours" selected>En cours</option>
                            </select>
                            <input type="hidden" name="statut" value="en cours">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Période -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        Période d'Hospitalisation
                    </h2>
                    <p class="text-white mt-2">Définissez la date d'entrée</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
                        <!-- Date d'entrée -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <div
                                    class="w-5 h-5 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                Date d'entrée *
                            </label>
                            <input type="date" name="date_entree" id="date_entree" required disabled
                                value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed text-lg shadow-sm">
                            <input type="hidden" name="date_entree" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Logement -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        Logement & Tarification
                    </h2>
                    <p class="text-white mt-2">Sélectionnez la chambre et le lit pour le patient</p>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Chambre -->
                        <div class <label
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Chambre *
                            </label>
                            <select id="chambre-select" name="chambre_id" required
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200">
                                <option value="">Sélectionner une chambre</option>
                                @foreach($chambres as $chambre)
                                <option value="{{ $chambre->id }}" data-prix="{{ $chambre->tarif_journalier ?? 5000 }}"
                                    data-lits-count="{{ $chambre->lits->count() }}">
                                    {{ $chambre->nom_complet }} - {{ number_format($chambre->tarif_journalier ?? 5000,
                                    0, ',', ' ') }} MRU/jour ({{ $chambre->lits->count() }} lits libres)
                                </option>
                                @endforeach
                            </select>
                            <!-- Alerte pour chambre sans lits libres -->
                            <div id="chambre-alert"
                                class="hidden mt-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    <p class="text-red-700 dark:text-red-300 font-medium">Cette chambre n'a aucun lit
                                        libre disponible. Veuillez choisir une autre chambre.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Lit -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                </svg>
                                Lit *
                            </label>
                            <select id="lit-select" name="lit_id" required disabled
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 disabled:opacity-50 disabled:bg-gray-100 dark:disabled:bg-gray-800">
                                <option value="">Sélectionner d'abord une chambre</option>
                            </select>
                        </div>

                        <!-- Montant total -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                                Montant total
                            </label>
                            <input type="number" step="0.01" name="montant_total" id="montant_total" readonly
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200"
                                placeholder="0.00">
                        </div>
                    </div>

                    <!-- Résumé tarifaire -->
                    <div id="tarif-summary"
                        class="mt-8 p-6 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl border border-yellow-200 dark:border-yellow-700 hidden">
                        <h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-200 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16a2 2 0 002 2z">
                                </path>
                            </svg>
                            Résumé Tarifaire
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">Prix par jour</p>
                                <p id="prix-jour" class="text-xl font-bold text-yellow-800 dark:text-yellow-200">0 MRU
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">Nombre de jours</p>
                                <p id="nb-jours" class="text-xl font-bold text-yellow-800 dark:text-yellow-200">0</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-yellow-600 dark:text-yellow-300">Total</p>
                                <p id="total-calcule" class="text-xl font-bold text-yellow-800 dark:text-yellow-200">0
                                    MRU</p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Boutons d'action -->
                <div class="bg-gray-50 dark:bg-gray-700 px-8 py-8">
                    <div
                        class="flex flex-col sm:flex-row justify-center sm:justify-end gap-4 max-w-md sm:max-w-none mx-auto sm:mx-0">
                        <a href="{{ route('hospitalisations.index') }}"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-gray-600 border-2 border-gray-300 dark:border-gray-500 rounded-xl font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 transition-all duration-200 text-lg min-w-[180px]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                            Annuler
                        </a>
                        <button type="submit" id="submitBtn"
                            class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 text-lg shadow-lg hover:shadow-xl transform hover:scale-105 min-w-[220px]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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
        // Initialisation des boutons X pour les champs Patient et Téléphone
        function initDatalistClearButtons() {
            document.querySelectorAll('.datalist-clear-btn').forEach(btn => {
                if (btn._datalistInit) return;
                btn._datalistInit = true;
                const targetId = btn.dataset.target;
                const clearIds = (btn.dataset.clearIds || '').split(',').filter(Boolean);
                const clearValues = (btn.dataset.clearValues || '').split(',');
                const clearExtra = btn.dataset.clearExtra;
                const targetInput = document.getElementById(targetId);
                if (!targetInput) return;

                function updateVisibility() {
                    btn.classList.toggle('hidden', !targetInput.value.trim() || targetInput.disabled);
                }

                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (targetInput.disabled) return;
                    targetInput.value = '';
                    targetInput.focus();
                    clearIds.forEach((id, i) => {
                        const el = document.getElementById(id);
                        if (el) el.value = clearValues[i] || '';
                    });
                    if (clearExtra) {
                        const extra = document.getElementById(clearExtra);
                        if (extra && !extra.disabled) extra.value = '';
                    }
                    updateVisibility();
                });

                targetInput.addEventListener('input', updateVisibility);
                targetInput.addEventListener('change', updateVisibility);
                updateVisibility();
            });
        }
        initDatalistClearButtons();

        // Données des chambres et lits
        const litsParChambre = @json($litsParChambre);
        const chambresData = @json($chambresData);

    // Éléments du DOM avec vérification
    const chambreSelect = document.getElementById('chambre-select');
    const litSelect = document.getElementById('lit-select');
    const dateEntree = document.getElementById('date_entree');
    const montantTotal = document.getElementById('montant_total');
    const tarifSummary = document.getElementById('tarif-summary');
    const prixJour = document.getElementById('prix-jour');
    const nbJours = document.getElementById('nb-jours');
    const totalCalcule = document.getElementById('total-calcule');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submit-text');

    // Éléments pour la synchronisation patient/téléphone avec datalist
    const patientSearch = document.getElementById('patient-search');
    const telephoneInput = document.getElementById('telephone-input');
    const gestionPatientId = document.getElementById('gestion_patient_id');
    const patientsList = document.getElementById('patients-list');
    const telephonesList = document.getElementById('telephones-list');

    // Vérifier que tous les éléments existent
    if (!chambreSelect || !litSelect || !montantTotal) {
        console.error('Éléments DOM manquants:', {
            chambreSelect: !!chambreSelect,
            litSelect: !!litSelect,
            montantTotal: !!montantTotal
        });
        return;
    }

    // Fonction pour ajouter un effet visuel
    function addSyncEffect(element) {
        element.classList.add('field-highlight');
        setTimeout(() => {
            element.classList.remove('field-highlight');
        }, 1000);
    }


    // Synchronisation Patient/Téléphone - Recherche intelligente avec datalist (comme Spécialité dans prescripteurs)
    if (patientSearch && telephoneInput && gestionPatientId && patientsList && telephonesList) {
        console.log('Initialisation de la synchronisation patient/téléphone avec datalist');

        // Stocker toutes les options originales pour les restaurer si nécessaire
        const originalPatientsOptions = Array.from(patientsList.options).map(opt => ({
            value: opt.value,
            id: opt.getAttribute('data-id'),
            telephone: opt.getAttribute('data-telephone')
        }));
        
        const originalTelephonesOptions = Array.from(telephonesList.options).map(opt => ({
            value: opt.value,
            id: opt.getAttribute('data-id'),
            nom: opt.getAttribute('data-nom')
        }));

        // Fonction pour trouver un patient par nom (correspondance exacte uniquement)
        function findPatientByName(nom) {
            const options = Array.from(patientsList.options);
            return options.find(opt => opt.value === nom);
        }

        // Fonction pour trouver un patient par téléphone (correspondance exacte uniquement)
        function findPatientByPhone(phone) {
            const options = Array.from(telephonesList.options);
            return options.find(opt => opt.value === phone);
        }

        // Variable pour suivre si une sélection explicite a été faite
        let lastSelectedPatientId = null;
        let isUserTyping = false;

        // Quand l'utilisateur sélectionne explicitement un patient depuis le datalist (événement change)
        patientSearch.addEventListener('change', function() {
            const nomPatient = this.value.trim();
            console.log('Patient sélectionné (change):', nomPatient);
            
            const patientOption = findPatientByName(nomPatient);
            
            if (patientOption) {
                const patientId = patientOption.getAttribute('data-id');
                const telephone = patientOption.getAttribute('data-telephone');
                
                // Mettre à jour le champ caché avec l'ID du patient
                gestionPatientId.value = patientId;
                lastSelectedPatientId = patientId;
                
                // Mettre à jour le champ téléphone
                if (telephone) {
                    telephoneInput.value = telephone;
                    addSyncEffect(telephoneInput);
                }
                
                addSyncEffect(patientSearch);
                isUserTyping = false;
            }
        });

        // Quand l'utilisateur tape dans le champ patient (événement input)
        patientSearch.addEventListener('input', function() {
            isUserTyping = true;
            const nomPatient = this.value.trim();
            
            // Si le champ est vidé, vider aussi le téléphone et réinitialiser
            if (nomPatient === '') {
                telephoneInput.value = '';
                gestionPatientId.value = '';
                lastSelectedPatientId = null;
                return;
            }
            
            // Vérifier si la valeur correspond exactement à une option
            const patientOption = findPatientByName(nomPatient);
            
            if (!patientOption) {
                // Si la valeur ne correspond pas exactement, réinitialiser
                gestionPatientId.value = '';
                lastSelectedPatientId = null;
            }
        });

        // Quand l'utilisateur sélectionne explicitement un téléphone depuis le datalist (événement change)
        telephoneInput.addEventListener('change', function() {
            const phone = this.value.trim();
            console.log('Téléphone sélectionné (change):', phone);
            
            const phoneOption = findPatientByPhone(phone);
            
            if (phoneOption) {
                const patientId = phoneOption.getAttribute('data-id');
                const nomPatient = phoneOption.getAttribute('data-nom');
                
                // Mettre à jour le champ caché avec l'ID du patient
                gestionPatientId.value = patientId;
                lastSelectedPatientId = patientId;
                
                // Mettre à jour le champ patient
                if (nomPatient) {
                    patientSearch.value = nomPatient;
                    addSyncEffect(patientSearch);
                }
                
                addSyncEffect(telephoneInput);
                isUserTyping = false;
            }
        });

        // Quand l'utilisateur tape dans le champ téléphone (événement input)
        telephoneInput.addEventListener('input', function() {
            isUserTyping = true;
            const phone = this.value.trim();
            
            // Si le champ est vidé, vider aussi le patient et réinitialiser
            if (phone === '') {
                patientSearch.value = '';
                gestionPatientId.value = '';
                lastSelectedPatientId = null;
                return;
            }
            
            // Vérifier si la valeur correspond exactement à une option
            const phoneOption = findPatientByPhone(phone);
            
            if (!phoneOption) {
                // Si la valeur ne correspond pas exactement, réinitialiser
                gestionPatientId.value = '';
                lastSelectedPatientId = null;
            }
        });
    } else {
        console.error('Éléments manquants:', {
            patientSearch: !!patientSearch,
            telephoneInput: !!telephoneInput,
            gestionPatientId: !!gestionPatientId,
            patientsList: !!patientsList,
            telephonesList: !!telephonesList
        });
    }

    // Variables globales
    let prixParJour = 0;
    let nombreJours = 1;

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

            if (litsParChambre && litsParChambre[chambreId]) {
                // Remplir les lits disponibles
                litsParChambre[chambreId].forEach(lit => {
                    const option = document.createElement('option');
                    option.value = lit.id;
                    option.textContent = lit.nom_complet;
                    litSelect.appendChild(option);
                });

                // Mettre à jour le prix par jour
                if (chambresData && chambresData[chambreId]) {
                    prixParJour = chambresData[chambreId].prix_par_jour || 5000;
                    calculerMontantTotal();
                }
            } else {
                console.warn('Données manquantes pour chambre:', chambreId);
            }
        } else {
            prixParJour = 0;
            calculerMontantTotal();

            // Réactiver le bouton de soumission
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    });

    // Gestion des changements de date d'entrée
    dateEntree.addEventListener('change', calculerDuree);

    function calculerDuree() {
        if (dateEntree.value) {
            // Pour une nouvelle hospitalisation, c'est toujours 1 jour par défaut
            nombreJours = 1;
        } else {
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

        0%,
        100% {
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
    input:focus,
    select:focus,
    textarea:focus {
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
    button:hover,
    .btn:hover {
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

    /* Styles pour la synchronisation patient/téléphone avec datalist */
    .field-highlight {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-color: #2196f3 !important;
        transition: all 0.3s ease;
    }


    @keyframes bounce {

        0%,
        20%,
        53%,
        80%,
        100% {
            transform: translate3d(0, 0, 0);
        }

        40%,
        43% {
            transform: translate3d(0, -30px, 0);
        }

        70% {
            transform: translate3d(0, -15px, 0);
        }

        90% {
            transform: translate3d(0, -4px, 0);
        }
    }
</style>
@endsection
