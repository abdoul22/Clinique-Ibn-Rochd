@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <!-- En-tête bilingue -->
    <div class="p-6 bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-900 dark:to-blue-950 text-white">
        <div class="flex items-start justify-between text-sm mb-4">
            <!-- Section française -->
            <div class="text-left flex-1">
                <div class="font-semibold">CENTRE IBN ROCHD</div>
                <div>Dr Brahim Ould Ntaghry</div>
                <div>Spécialiste en Imagerie Médicale</div>
                <div class="text-xs text-blue-200 mt-1">Centre Imagerie Médicale</div>
                <div class="text-xs text-blue-200">Scanner – Echographie – Radiologie Générale – Mammographie –
                    Panoramique Dentaire</div>
            </div>

            <!-- Logo centré -->
            <div class="flex-shrink-0 mx-6">
                <img src="{{ asset('images/logo.png') }}" alt="Logo IBN ROCHD" class="h-16 w-auto">
            </div>

            <!-- Section arabe -->
            <div class="text-right flex-1" style="direction: rtl;">
                <div class="font-semibold">مركز ابن رشد</div>
                <div>الدكتور إبراهيم ولد نْتَغري</div>
                <div>اختصاصي في التشخيص الطبي والأشعة</div>
                <div class="text-xs text-blue-200 mt-1">مركز التشخيص الطبي</div>
                <div class="text-xs text-blue-200">فحص بالأشعة – تصوير بالموجات فوق الصوتية – أشعة عامة – تصوير الثدي –
                    أشعة الأسنان البانورامية</div>
            </div>
        </div>

        <div class="text-center mt-4">
            <div class="text-xs text-blue-200">
                Urgences Tél. 26 38 24 84 – 22 30 56 26 <br>
                Avenue John Kennedy, en face de la Polyclinique – Nouakchott
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-sm">Reçu d'examen médical</p>
            <p class="text-xs opacity-80 mt-2">Date d'émission: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Corps du document -->
    <div class="p-6">
        <div class="flex flex-col md:flex-row justify-between items-start mb-8 gap-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Facture N° {{ $caisse->numero_facture }}
                </h2>
                <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Informations de l'examen</h2>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">N° d'entrée:</span> {{
                    $caisse->numero_entre }}</p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Date de l'examen:</span> {{
                    $caisse->date_examen ? $caisse->date_examen->format('d/m/Y') : 'N/A' }}</p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Type d'examen:</span> {{
                    $caisse->examen->nom ?? 'N/A' }}</p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Service:</span>
                    @php
                    $svc = $caisse->service;
                    $serviceLabel = $svc && $svc->type_service === 'PHARMACIE' ? 'PHARMACIE' : ($svc->nom ?? 'N/A');
                    @endphp
                    {{ $serviceLabel }}
                </p>
            </div>

            <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg">
                <p class="text-lg font-bold text-center text-gray-900 dark:text-white">Total</p>
                @if($caisse->assurance && $caisse->couverture > 0)
                @php
                $montantAssurance = $caisse->total * ($caisse->couverture / 100);
                $montantPatient = $caisse->total - $montantAssurance;
                @endphp
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 text-center">{{
                    number_format($montantPatient, 2) }} MRU</p>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-1">
                    ({{ $caisse->couverture }}% pris en charge par {{ $caisse->assurance->nom }})
                </p>
                @else
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 text-center">{{
                    number_format($caisse->total, 2) }} MRU</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Informations patient -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3 border-b pb-2 text-gray-900 dark:text-white">Informations patient
                </h3>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Nom:</span> {{
                    $caisse->patient->first_name ?? 'N/A' }} {{ $caisse->patient->last_name ?? '' }}</p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Date de naissance:</span> {{
                    isset($caisse->patient->date_of_birth) ? $caisse->patient->date_of_birth->format('d/m/Y') : 'N/A' }}
                </p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Téléphone:</span> {{
                    $caisse->patient->phone ?? 'N/A' }}</p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Adresse:</span> {{
                    $caisse->patient->address ?? 'N/A' }}</p>
            </div>

            <!-- Informations médicales -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3 border-b pb-2 text-gray-900 dark:text-white">Personnel médical
                </h3>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Médecin:</span>
                    @if($caisse->medecin)
                    {{ $caisse->medecin->nom_complet_avec_prenom }}
                    @else
                    N/A
                    @endif
                </p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Prescripteur:</span>
                    @if($caisse->prescripteur)
                    {{ $caisse->prescripteur->nom }}
                    @else
                    Externe
                    @endif
                </p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Caissier:</span> {{
                    $caisse->nom_caissier }}</p>
            </div>
            @if($caisse->paiements)
            <p class="text-gray-800 dark:text-gray-200"><strong>Mode de paiement :</strong> {{ $caisse->paiements->type
                }} ({{ number_format($caisse->paiements->montant, 0, ',', ' ') }} MRU)</p>
            @if($caisse->couverture !== null)
            <p class="text-gray-800 dark:text-gray-200"><strong>Couverture assurance :</strong> {{ $caisse->couverture
                }}%</p>
            @endif
            @endif
        </div>

        <!-- Notes et observations -->
        <div class="bg-blue-50 dark:bg-blue-950 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Notes et observations</h3>
            <p class="text-gray-800 dark:text-gray-200">{{ $caisse->observation ?? 'Aucune observation.' }}</p>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="bg-gray-100 dark:bg-gray-900 p-4 text-center text-sm text-gray-600 dark:text-gray-300">
        <p>{{ config('app.name', 'Clinique Médicale') }} - Téléphone: +222 00 00 00 00 - Email: contact@clinique.com</p>
        <p class="mt-1">Adresse: Nouakchott, Mauritanie</p>
    </div>
</div>
<!-- Actions -->
<div class="max-w-4xl mx-auto flex flex-col sm:flex-row justify-center gap-4 mb-8 print:hidden">

    <a href="{{ route(auth()->user()->role->name . '.caisses.exportPdf', $caisse) }}"
        class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
        </svg>
        Télécharger PDF
    </a>
    <a href="{{ route(auth()->user()->role->name . '.caisses.printSingle', $caisse->id) }}"
        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Imprimer
    </a>
    <a href="{{ route(auth()->user()->role->name . '.caisses.index') }}"
        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
        Liste des examens
    </a>
</div>
@endsection