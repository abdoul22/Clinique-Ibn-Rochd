@extends('layouts.app')

@section('content')
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

                    <!-- Médecin -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                        <div class="flex items-center mb-4">
                            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 mr-4">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Médecin Traitant</h3>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nom</p>
                                <p class="font-medium text-gray-900 dark:text-white">Dr. {{
                                    $hospitalisation->medecin->nom ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Couverture assurance</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $hospitalisation->couverture ??
                                    0 }}%</p>
                            </div>
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
                    <form method="POST"
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
                                <select name="examen_id"
                                    class="form-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <option value="">Sélectionner...</option>
                                    @foreach($examens as $ex)
                                    <option value="{{ $ex->id }}">{{ $ex->nom }} ({{ number_format($ex->tarif, 0, ',', '
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
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                    Ajouter
                                </button>
                            </div>
                        </div>
                    </form>
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
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                    <td colspan="3" class="py-3 px-4 font-semibold text-gray-900 dark:text-white">Total
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
                            <p class="font-medium text-gray-900 dark:text-white">{{ $stay->chambre->nom ?? '—' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ optional($stay->start_at)->format('d/m/Y H:i') }} -
                                {{ optional($stay->end_at)->format('d/m/Y H:i') ?? 'En cours' }}
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

@push('scripts')
<script>
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

    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('paiement-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
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
</script>
@endpush
@endsection