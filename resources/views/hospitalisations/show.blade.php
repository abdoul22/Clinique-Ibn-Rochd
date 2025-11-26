@extends('layouts.app')

@section('content')

@php
    // Détecter le rôle de l'utilisateur pour utiliser les bonnes routes
    $routePrefix = auth()->user()->role->name === 'admin' ? 'admin.' : '';
@endphp

<div
    class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header avec navigation -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.hospitalisations.index') : route('hospitalisations.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Hospitalisation #{{ $hospitalisation->id }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ $hospitalisation->patient->nom ?? '' }} {{ $hospitalisation->patient->prenom ?? '' }}
                        </p>
                    </div>
                </div>

                <!-- Statut avec changement rapide -->
                <div class="flex items-center space-x-4">
                    @if($hospitalisation->statut === 'en cours')
                    <span
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></div>
                        En Cours
                    </span>
                    @elseif($hospitalisation->statut === 'terminé')
                    <span
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Terminé
                    </span>
                    @else
                    <span
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Annulé
                    </span>
                    @endif

                    <!-- Bouton d'impression -->
                    <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.hospitalisations.print', $hospitalisation->id) : route('hospitalisations.print', $hospitalisation->id) }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                        Imprimer
                    </a>

                    @if($hospitalisation->statut === 'en cours')
                    <form method="POST"
                        action="{{ auth()->user()->role?->name === 'admin' ? route('admin.hospitalisations.updateStatus', $hospitalisation->id) : route('hospitalisations.updateStatus', $hospitalisation->id) }}"
                        class="inline" id="statut-form">
                        @csrf
                        @method('PATCH')
                        <select name="statut" onchange="handleStatutChange(this)"
                            class="form-select border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm rounded-lg shadow-sm">
                            <option value="en cours" {{ $hospitalisation->statut === 'en cours' ? 'selected' : '' }}>En
                                Cours</option>
                            <option value="annulé" {{ $hospitalisation->statut === 'annulé' ? 'selected' : '' }}>Annulé
                            </option>
                            <option value="terminé" disabled style="color:gray;">Terminé (payer d'abord)</option>
                        </select>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Résumé financier en haut -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 text-white shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Résumé Financier</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <p class="text-blue-100 text-sm">Charges facturées</p>
                                <p class="text-2xl font-bold">{{ number_format($totaux['facturees'], 0, ',', ' ') }} MRU
                                </p>
                            </div>
                            <div>
                                <p class="text-blue-100 text-sm">En attente</p>
                                <p class="text-2xl font-bold">{{ number_format($totaux['total'], 0, ',', ' ') }} MRU</p>
                            </div>
                            <div>
                                <p class="text-blue-100 text-sm">Total général</p>
                                <p class="text-3xl font-bold">{{ number_format($totaux['montant_total'], 0, ',', ' ') }}
                                    MRU</p>
                            </div>
                            <div>
                                <p class="text-blue-100 text-sm">Durée séjour</p>
                                <p class="text-2xl font-bold">{{ $dureeSejour ?? ($joursHospitalisation . ' jour' .
                                    ($joursHospitalisation > 1 ? 's' : '')) }}</p>
                            </div>
                        </div>
                    </div>
                    @if($totaux['total'] > 0 && $hospitalisation->statut !== 'annulé')
                    <div>
                        <button onclick="document.getElementById('paiement-modal').classList.remove('hidden')"
                            class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-all duration-200 shadow-lg">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            Payer Tout
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Grille principale -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne gauche - Informations principales -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations patient et médecin -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Patient -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 mr-4">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Patient</h3>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nom complet</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $hospitalisation->patient->nom
                                    ?? '-' }} {{ $hospitalisation->patient->prenom ?? '' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Service</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $hospitalisation->service->nom
                                    ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Médecins Impliqués -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Médecins Impliqués</h3>
                            </div>
                            @if($medecinsImpliques->count() > 1)
                            <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.hospitalisations.doctors', $hospitalisation->id) : route('hospitalisations.doctors', $hospitalisation->id) }}"
                                class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg text-sm font-medium hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Voir détails
                            </a>
                            @endif
                        </div>

                        @if($medecinsImpliques->count() > 0)
                        <div class="space-y-4">
                            @foreach($medecinsImpliques as $index => $doctor)
                            <div
                                class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white text-sm font-bold">{{ $index + 1 }}</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">{{
                                                $doctor['medecin']->nom_complet_avec_prenom }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $doctor['role'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Part médecin</p>
                                        <p class="font-semibold text-green-600 dark:text-green-400">{{
                                            number_format($doctor['part_medecin'], 0, ',', ' ') }} MRU</p>
                                    </div>
                                </div>

                                @if(count($doctor['examens']) > 0)
                                <div class="mt-3">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Examens effectués:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($doctor['examens'] as $examen)
                                        <span
                                            class="inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs">
                                            {{ $examen['nom'] }}
                                        </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach

                            <div
                                class="bg-green-50 dark:bg-green-900 rounded-lg p-3 border border-green-200 dark:border-green-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-green-800 dark:text-green-200">Total Part
                                        Médecins:</span>
                                    <span class="font-bold text-green-600 dark:text-green-400">{{
                                        number_format($medecinsImpliques->sum('part_medecin'), 0, ',', ' ') }}
                                        MRU</span>
                                </div>
                            </div>

                            <!-- Pharmacien Impliqué -->
                            @if($hospitalisation->pharmacien_id)
                            <div
                                class="bg-indigo-50 dark:bg-indigo-900 rounded-lg p-4 border border-indigo-200 dark:border-indigo-700 mt-4">
                                <div class="flex items-center mb-3">
                                    <div
                                        class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.452a1 1 0 00-.898.467l-.368.794a1 1 0 001.286 1.286l.794-.368c.149-.074.312-.06.466.007l2.387.452a2 2 0 00.948-3.084l-2.387-.452zm0 0A2.884 2.884 0 1015.428 19.428">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase">
                                            Pharmacien Responsable</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{
                                            $hospitalisation->pharmacien->nom_complet_avec_prenom }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-6">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Aucun médecin impliqué pour le moment</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Ajoutez des examens pour voir les
                                médecins</p>
                        </div>
                        @endif

                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Couverture assurance</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $hospitalisation->couverture ?? 0
                                }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Période et logement -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Période -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 mr-4">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Période</h3>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Entrée</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{
                                    \Carbon\Carbon::parse($hospitalisation->date_entree)->format('d/m/Y') }}</p>
                            </div>
                            @if($hospitalisation->date_sortie)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sortie</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{
                                    \Carbon\Carbon::parse($hospitalisation->date_sortie)->format('d/m/Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Logement -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900/30 mr-4">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Logement</h3>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Chambre</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    @if($hospitalisation->lit && $hospitalisation->lit->chambre)
                                    {{ $hospitalisation->lit->chambre->nom }}
                                    @else
                                    Non assignée
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Lit</p>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    @if($hospitalisation->lit)
                                    Lit {{ $hospitalisation->lit->numero }}
                                    @else
                                    Non assigné
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charges non facturées -->
                @if($hospitalisation->statut === 'en cours')
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter des charges
                    </h3>

                    <!-- Formulaire d'ajout -->
                    <form method="POST" id="add-charge-form"
                        action="{{ auth()->user()->role?->name === 'admin' ? route('admin.hospitalisations.addCharge', $hospitalisation->id) : route('hospitalisations.addCharge', $hospitalisation->id) }}"
                        class="mb-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                                <select name="charge_type" id="charge_type"
                                    class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <option value="examen">Examen/Service</option>
                                    <option value="pharmacy">Médicament</option>
                                </select>
                            </div>
                            <div id="examen_field">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Examen</label>
                                <select name="examen_id" id="examen_id"
                                    class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <option value="">Sélectionner...</option>
                                    @foreach($examens as $ex)
                                    <option value="{{ $ex->id }}" data-medecin-id="{{ $ex->medecin_id }}">{{ $ex->nom }}
                                        ({{ number_format($ex->tarif, 0, ',', '
                                        ') }} MRU)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="medicament_field" style="display: none;">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Médicament</label>
                                <select name="medicament_id"
                                    class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <option value="">Sélectionner...</option>
                                    @foreach($medicaments as $m)
                                    <option value="{{ $m->id }}">{{ $m->nom_medicament }} ({{
                                        number_format($m->prix_vente, 0, ',', ' ') }} MRU)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantité</label>
                                <input type="number" name="quantity" value="1" min="1" max="999"
                                    class="form-input w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            </div>
                            <div class="flex items-end">
                                <button type="button" id="add-charge-btn"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                    Ajouter
                                </button>
                            </div>
                        </div>
                        <!-- Champ caché pour le médecin sélectionné -->
                        <input type="hidden" name="medecin_id" id="selected_medecin_id" value="">
                    </form>

                    <!-- Zone d'affichage des charges ajoutées dynamiquement -->
                    <div id="charges_ajoutees_div" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Charges
                            ajoutées</label>
                        <div id="charges_liste"
                            class="border border-gray-300 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-800 space-y-2">
                            <!-- Les charges ajoutées apparaîtront ici -->
                        </div>
                    </div>
                </div>
                @endif

                <!-- Liste des charges -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-6 text-gray-900 dark:text-white">Charges en attente</h3>

                    @if($chargesNonFacturees->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Date</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">Type</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900 dark:text-white">
                                        Description</th>
                                    <th class="text-right py-3 px-4 font-medium text-gray-900 dark:text-white">Montant
                                    </th>
                                    <th class="text-center py-3 px-4 font-medium text-gray-900 dark:text-white">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chargesNonFacturees as $charge)
                                <tr
                                    class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{
                                        $charge->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($charge->type === 'room_day') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                            @elseif($charge->type === 'pharmacy') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                            @else bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 @endif">
                                            {{ strtoupper($charge->type) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $charge->description_snapshot
                                        }}</td>
                                    <td class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-white">{{
                                        number_format($charge->total_price, 0, ',', ' ') }} MRU</td>
                                    <td class="py-3 px-4 text-center">
                                        @if($charge->type === 'room_day')
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400"
                                            title="Le ROOM_DAY ne peut pas être supprimé">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Protégé
                                        </span>
                                        @elseif($hospitalisation->statut !== 'annulé')
                                        <button type="button" onclick="removeCharge({{ $charge->id }})"
                                            class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200"
                                            title="Supprimer cette charge">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                        @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400"
                                            title="Impossible de supprimer - Hospitalisation annulée">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Verrouillé
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                    <td colspan="4" class="py-3 px-4 font-semibold text-gray-900 dark:text-white">Total
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-lg text-blue-600 dark:text-blue-400">
                                        {{ number_format($totaux['total'], 0, ',', ' ') }} MRU</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Aucune charge en attente</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Colonne droite - Historiques -->
            <div class="space-y-6">
                <!-- Historique des charges facturées -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Charges facturées</h3>
                    @if($chargesFacturees->count() > 0)
                    <div class="space-y-3">
                        @foreach($chargesFacturees->take(5) as $charge)
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white text-sm">{{
                                    $charge->description_snapshot }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{
                                    $charge->billed_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-600 dark:text-green-400">{{
                                    number_format($charge->total_price, 0, ',', ' ') }} MRU</p>
                                @if($charge->caisse_id)
                                <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.caisses.show', $charge->caisse_id) : route('caisses.show', $charge->caisse_id) }}"
                                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                    Facture #{{ $charge->caisse_id }}
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucune charge facturée</p>
                    @endif
                </div>

                <!-- Observations -->
                @if($hospitalisation->observation)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Observations</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $hospitalisation->observation }}
                        </p>
                    </div>
                </div>
                @endif

                <!-- Historique des séjours -->
                @if($hospitalisation->roomStays && $hospitalisation->roomStays->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Historique des séjours</h3>
                    <div class="space-y-3">
                        @foreach($hospitalisation->roomStays as $stay)
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="font-medium text-gray-900 dark:text-white">
                                @if($stay->chambre && $stay->chambre->nom)
                                Chambre {{ $stay->chambre->nom }}
                                @else
                                —
                                @endif
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ optional($stay->start_at)->format('d/m/Y H:i') }} -
                                @if($stay->end_at)
                                {{ $stay->end_at->format('d/m/Y H:i') }}
                                @else
                                @if($hospitalisation->statut === 'terminé')
                                Terminé
                                @elseif($hospitalisation->statut === 'annulé')
                                Annulé
                                @else
                                En cours
                                @endif
                                @endif
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de paiement rapide -->
<div id="paiement-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Paiement Rapide</h3>
                <button onclick="document.getElementById('paiement-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form method="POST"
                action="{{ auth()->user()->role?->name === 'admin' ? route('admin.hospitalisations.payerTout', $hospitalisation->id) : route('hospitalisations.payerTout', $hospitalisation->id) }}">
                @csrf
                <div class="mb-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Vous allez facturer toutes les charges en attente pour un montant total de:
                    </p>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">

                            {{ number_format($totaux['total'], 0, ',', ' ') }} MRU
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $chargesNonFacturees->count() }} charge(s) à facturer
                        </p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mode de
                        paiement</label>
                    <select name="type" required
                        class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">Sélectionner...</option>
                        <option value="espèces">Espèces</option>
                        <option value="bankily">Bankily</option>
                        <option value="masrivi">Masrivi</option>
                        <option value="sedad">Sedad</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('paiement-modal').classList.add('hidden')"
                        class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Confirmer le paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de sélection de médecin pour examen -->
<div id="medecin-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Sélectionner un médecin</h3>
                <button onclick="closeMedecinModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Choisissez le médecin qui effectuera cet examen :
                </p>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-full mr-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-blue-900 dark:text-blue-100" id="examen-name">Examen sélectionné
                            </p>
                            <p class="text-sm text-blue-700 dark:text-blue-300" id="examen-price">Prix</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Médecin</label>
                <select id="medecin-select"
                    class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Sélectionner un médecin...</option>
                    @foreach($medecins as $medecin)
                    <option value="{{ $medecin->id }}">{{ $medecin->nom_complet_avec_prenom }} - {{
                        $medecin->fonction ?? 'Médecin' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeMedecinModal()"
                    class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    Annuler
                </button>
                <button type="button" id="confirm-medecin-btn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de sélection de pharmacien pour médicaments -->
<div id="pharmacien-modal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Sélectionner un pharmacien</h3>
                <button onclick="closePharmacienModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Choisissez le pharmacien responsable des médicaments de cette hospitalisation :
                </p>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 dark:bg-green-900 p-2 rounded-full mr-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-green-900 dark:text-green-100">Médicaments en attente</p>
                            <p class="text-sm text-green-700 dark:text-green-300">Un seul pharmacien pour toute
                                l'hospitalisation</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pharmacien</label>
                <select id="pharmacien-select"
                    class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Sélectionner un pharmacien...</option>
                    @foreach($pharmaciens as $pharmacien)
                    <option value="{{ $pharmacien->id }}">{{ $pharmacien->nom_complet_avec_prenom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closePharmacienModal()"
                    class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    Annuler
                </button>
                <button type="button" id="confirm-pharmacien-btn"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed">
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Variables globales pour la modale de médecin
    let currentExamenData = null;
    // Variables pour la modale de pharmacien
    let currentMedicamentData = null;
    let hospitalisationPharmacienId = {{ $hospitalisation->pharmacien_id ?? 'null' }};
    const hospitalisationId = {{ $hospitalisation->id }};

    // Gestion du changement de type de charge
    document.getElementById('charge_type')?.addEventListener('change', function(){
        const examenField = document.getElementById('examen_field');
        const medicamentField = document.getElementById('medicament_field');

        if (this.value === 'pharmacy') {
            examenField.style.display = 'none';
            medicamentField.style.display = 'block';
            // Réinitialiser le champ examen
            document.querySelector('select[name="examen_id"]').value = '';
        } else {
            examenField.style.display = 'block';
            medicamentField.style.display = 'none';
            // Réinitialiser le champ médicament
            document.querySelector('select[name="medicament_id"]').value = '';
        }
    });

    // Gestion du bouton "Ajouter" pour les examens et médicaments
    document.getElementById('add-charge-btn')?.addEventListener('click', function(e) {
        e.preventDefault();

        const chargeType = document.getElementById('charge_type').value;
        const examenId = document.getElementById('examen_id').value;
        const medicamentId = document.querySelector('select[name="medicament_id"]').value;

        if (chargeType === 'examen' && examenId) {
            // Récupérer les données de l'examen sélectionné
            const selectedOption = document.querySelector(`#examen_id option[value="${examenId}"]`);
            const examenName = selectedOption.textContent.split(' (')[0];
            const examenPrice = selectedOption.textContent.match(/\(([^)]+)\)/)?.[1] || '';
            const examenMedecinId = selectedOption.getAttribute('data-medecin-id');

            currentExamenData = {
                id: examenId,
                name: examenName,
                price: examenPrice,
                medecinId: examenMedecinId
            };

            // Afficher la modale de sélection de médecin
            openMedecinModal();
        } else if (chargeType === 'pharmacy' && medicamentId) {
            // Pour les médicaments, vérifier si un pharmacien a déjà été sélectionné
            if (!hospitalisationPharmacienId) {
                // Pas de pharmacien sélectionné, afficher la modale de pharmacien
                currentMedicamentData = {
                    id: medicamentId,
                    quantity: parseInt(document.querySelector('input[name="quantity"]').value) || 1
                };
                openPharmacienModal();
            } else {
                // Pharmacien déjà sélectionné, soumettre directement
                addChargeAjax();
            }
        } else {
            addChargeAjax();
        }
    });

    // Fonctions pour la modale de médecin
    function openMedecinModal() {
        if (!currentExamenData) return;

        // Mettre à jour les informations de l'examen dans la modale
        document.getElementById('examen-name').textContent = currentExamenData.name;
        document.getElementById('examen-price').textContent = currentExamenData.price;

        // Sélectionner le médecin par défaut de l'examen
        document.getElementById('medecin-select').value = currentExamenData.medecinId || '';

        // Afficher la modale
        document.getElementById('medecin-modal').classList.remove('hidden');
    }

    function closeMedecinModal() {
        document.getElementById('medecin-modal').classList.add('hidden');
        currentExamenData = null;
    }

    // Confirmer la sélection du médecin
    document.getElementById('confirm-medecin-btn')?.addEventListener('click', function() {
        const selectedMedecinId = document.getElementById('medecin-select').value;

        if (!selectedMedecinId) {
            alert('Veuillez sélectionner un médecin.');
            return;
        }

        // Mettre à jour le champ caché avec le médecin sélectionné
        document.getElementById('selected_medecin_id').value = selectedMedecinId;

        // Fermer la modale
        closeMedecinModal();

        // Soumettre via AJAX
        addChargeAjax();
    });

    // Fonctions pour la modale de pharmacien
    function openPharmacienModal() {
        // Afficher la modale
        document.getElementById('pharmacien-modal').classList.remove('hidden');
    }

    function closePharmacienModal() {
        document.getElementById('pharmacien-modal').classList.add('hidden');
        currentMedicamentData = null;
    }

    // Confirmer la sélection du pharmacien
    document.getElementById('confirm-pharmacien-btn')?.addEventListener('click', function() {
        const selectedPharmacienId = document.getElementById('pharmacien-select').value;

        if (!selectedPharmacienId) {
            alert('Veuillez sélectionner un pharmacien.');
            return;
        }

        // Mettre à jour la variable globale
        hospitalisationPharmacienId = selectedPharmacienId;

        // Fermer la modale
        closePharmacienModal();

        // Soumettre via AJAX avec le pharmacien
        addChargeAjax();
    });

    // Fermer les modales en cliquant à l'extérieur
    document.getElementById('paiement-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    document.getElementById('medecin-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeMedecinModal();
        }
    });

    document.getElementById('pharmacien-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closePharmacienModal();
        }
    });

    // Empêcher la sélection manuelle de "Terminé"
    function handleStatutChange(select) {
        if (select.value === 'terminé') {
            alert('Vous devez payer la facture pour terminer l\'hospitalisation.');
            select.value = 'en cours';
            return false;
        }
        document.getElementById('statut-form').submit();
    }

    // Fonction pour ajouter une charge via AJAX
    function addChargeAjax() {
        const form = document.getElementById('add-charge-form');
        const formData = new FormData(form);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Ajouter le pharmacien_id s'il est disponible et que c'est un médicament
        const chargeType = document.getElementById('charge_type').value;
        if (chargeType === 'pharmacy' && hospitalisationPharmacienId) {
            formData.append('pharmacien_id', hospitalisationPharmacienId);
        }

        // Afficher un indicateur de chargement
        const button = document.getElementById('add-charge-btn');
        const originalContent = button.innerHTML;
        button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        button.disabled = true;

        fetch(`{{ route($routePrefix . 'hospitalisations.addCharge', ':id') }}`.replace(':id', hospitalisationId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ajouter la charge à la liste
                addChargeToList(data.charge);

                // Mettre à jour les totaux
                updateTotals(data.totals);

                // Réinitialiser le formulaire
                resetForm();

                // Afficher un message de succès
                showNotification('Charge ajoutée avec succès', 'success');

                // Afficher la zone des charges ajoutées
                document.getElementById('charges_ajoutees_div').style.display = 'block';
            } else {
                showNotification(data.message || 'Erreur lors de l\'ajout', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de l\'ajout de la charge', 'error');
        })
        .finally(() => {
            // Restaurer le bouton
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    // Fonction pour ajouter une charge à la liste d'affichage
    function addChargeToList(charge) {
        const container = document.getElementById('charges_liste');
        const chargeElement = document.createElement('div');

        const typeClass = charge.type === 'pharmacy' ?
            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
            'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300';

        chargeElement.className = 'flex justify-between items-center bg-white dark:bg-gray-700 p-2 rounded border';
        chargeElement.innerHTML = `
            <div>
                <span class="font-medium">${charge.description}</span>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${typeClass} mr-2">
                        ${charge.type.toUpperCase()}
                    </span>
                    ${charge.total_price.toLocaleString()} MRU
                </div>
            </div>
            <button type="button" onclick="removeChargeFromList(this, ${charge.id})"
                class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        `;

        container.appendChild(chargeElement);
    }

    // Fonction pour supprimer une charge de la liste d'affichage
    function removeChargeFromList(button, chargeId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette charge ?')) {
            return;
        }

        const hospitalisationId = {{ $hospitalisation->id }};
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`{{ route($routePrefix . 'hospitalisations.removeCharge', [':id', ':chargeId']) }}`.replace(':id', hospitalisationId).replace(':chargeId', chargeId), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Supprimer l'élément de la liste
                button.closest('div').remove();

                // Mettre à jour les totaux
                updateTotals(data.totals);

                // Masquer la zone si plus de charges
                const container = document.getElementById('charges_liste');
                if (container.children.length === 0) {
                    document.getElementById('charges_ajoutees_div').style.display = 'none';
                }

                // Afficher un message de succès
                showNotification('Charge supprimée avec succès', 'success');

                // Recharger la page si plus de charges pour synchroniser
                if (data.totals.count === 0) {
                    location.reload();
                }
            } else {
                showNotification(data.message || 'Erreur lors de la suppression', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la suppression de la charge', 'error');
        });
    }

    // Fonction pour réinitialiser le formulaire
    function resetForm() {
        // Sauvegarder le type actuel avant de réinitialiser
        const currentChargeType = document.getElementById('charge_type').value;

        document.getElementById('add-charge-form').reset();
        document.getElementById('selected_medecin_id').value = '';
        currentExamenData = null;

        // Restaurer le type de charge
        if (currentChargeType) {
            document.getElementById('charge_type').value = currentChargeType;
            // Réafficher le bon champ (examen ou médicament)
            const examenField = document.getElementById('examen_field');
            const medicamentField = document.getElementById('medicament_field');
            if (currentChargeType === 'pharmacy') {
                examenField.style.display = 'none';
                medicamentField.style.display = 'block';
            } else {
                examenField.style.display = 'block';
                medicamentField.style.display = 'none';
            }
        }
    }

    // Fonction pour supprimer une charge
    function removeCharge(chargeId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette charge ?')) {
            return;
        }

        const hospitalisationId = {{ $hospitalisation->id }};
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Afficher un indicateur de chargement
        const button = event.target.closest('button');
        const originalContent = button.innerHTML;
        button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        button.disabled = true;

        fetch(`{{ route($routePrefix . 'hospitalisations.removeCharge', [':id', ':chargeId']) }}`.replace(':id', hospitalisationId).replace(':chargeId', chargeId), {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Supprimer la ligne du tableau
                const row = button.closest('tr');
                row.remove();

                // Mettre à jour les totaux
                updateTotals(data.totals);

                // Afficher un message de succès
                showNotification('Charge supprimée avec succès', 'success');

                // Si plus de charges, masquer le tableau
                if (data.totals.count === 0) {
                    location.reload(); // Recharger pour masquer le tableau
                }
            } else {
                showNotification(data.message || 'Erreur lors de la suppression', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors de la suppression de la charge', 'error');
        })
        .finally(() => {
            // Restaurer le bouton
            button.innerHTML = originalContent;
            button.disabled = false;
        });
    }

    // Fonction pour mettre à jour les totaux
    function updateTotals(totals) {
        // Mettre à jour le total dans le tableau
        const totalCell = document.querySelector('tfoot td:last-child');
        if (totalCell) {
            totalCell.textContent = `${totals.total.toLocaleString()} MRU`;
        }

        // Mettre à jour le total dans le résumé financier si présent
        const summaryTotal = document.querySelector('.text-3xl.font-bold.text-blue-600');
        if (summaryTotal) {
            summaryTotal.textContent = `${totals.total.toLocaleString()} MRU`;
        }
    }

    // Fonction pour afficher des notifications
    function showNotification(message, type = 'info') {
        // Créer l'élément de notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        // Ajouter au DOM
        document.body.appendChild(notification);

        // Supprimer après 3 secondes
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>

<style>
    /* Styles pour forcer le mode sombre sur la section Médecins Impliqués */
    .dark .bg-gray-50 {
        background-color: #374151 !important;
    }

    .dark .text-gray-900 {
        color: #f9fafb !important;
    }

    .dark .border-gray-200 {
        border-color: #4b5563 !important;
    }

    /* S'assurer que les badges d'examens s'adaptent au mode sombre */
    .dark .bg-blue-100 {
        background-color: #1e3a8a !important;
    }

    .dark .text-blue-700 {
        color: #93c5fd !important;
    }
</style>
@endpush
@endsection
