@extends('layouts.app')
@section('title', "Dashboard Administrateur")

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Tableau de bord de l'Administrateur</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Gestion des patients --}}
        <a href="{{ route('admin.patients.index') }}"
            class="bg-purple-600 text-white rounded-2xl p-6 shadow hover:bg-purple-700 transition">
            <h2 class="text-xl font-semibold mb-2">Gestion des patients</h2>
            <p class="text-sm text-purple-100">Accéder à la liste, création et mise à jour des dossiers patients.</p>
        </a>

        {{-- Rendez-vous --}}
        <a href="{{ route('admin.rendezvous.index') }}"
            class="bg-yellow-500 text-white rounded-2xl p-6 shadow hover:bg-yellow-600 transition">
            <h2 class="text-xl font-semibold mb-2">Rendez-vous</h2>
            <p class="text-sm text-yellow-100">Programmer, suivre ou annuler des rendez-vous médicaux.</p>
        </a>

        {{-- Dossier Médical --}}
        <a href="{{ route('admin.dossiers.index') }}"
            class="bg-red-500 text-white rounded-2xl p-6 shadow hover:bg-red-600 transition">
            <h2 class="text-xl font-semibold mb-2">Dossier Médical</h2>
            <p class="text-sm text-red-100">Consulter et modifier les dossiers médicaux des patients.</p>
        </a>

        {{-- Hospitalisations --}}
        <a href="{{ route('admin.hospitalisations.index') }}"
            class="bg-pink-600 text-white rounded-2xl p-6 shadow hover:bg-pink-700 transition">
            <h2 class="text-xl font-semibold mb-2">Hospitalisations</h2>
            <p class="text-sm text-pink-100">Suivi des séjours et admissions des patients.</p>
        </a>

        {{-- Gestion des Factures --}}
        <a href="{{ route('admin.caisses.index') }}"
            class="bg-gray-700 text-white rounded-2xl p-6 shadow hover:bg-gray-800 transition">
            <h2 class="text-xl font-semibold mb-2">Gestion des Factures</h2>
            <p class="text-sm text-gray-300">Suivi des entrées/sorties d'argent et clôtures de caisse.</p>
        </a>

        {{-- Récap. Services --}}
        <a href="{{ route('admin.recap-services.index') }}"
            class="bg-indigo-600 text-white rounded-2xl p-6 shadow hover:bg-indigo-700 transition">
            <h2 class="text-xl font-semibold mb-2">Récap. Services</h2>
            <p class="text-sm text-indigo-100">Voir les totaux de services selon les dates.</p>
        </a>

        {{-- Récap. Opérateurs --}}
        <a href="{{ route('admin.recap-operateurs.index') }}"
            class="bg-teal-600 text-white rounded-2xl p-6 shadow hover:bg-teal-700 transition">
            <h2 class="text-xl font-semibold mb-2">Récap. Opérateurs</h2>
            <p class="text-sm text-teal-100">Analyser les consultations et les parts des médecins.</p>
        </a>

        {{-- Situation Journalière --}}
        <a href="{{ route('admin.situation-journaliere.index') }}"
            class="bg-gradient-to-br from-violet-600 to-purple-700 dark:from-violet-500 dark:to-purple-600 text-white rounded-2xl p-6 shadow hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
            <h2 class="text-xl font-semibold mb-2 text-white">Situation Journalière</h2>
            <p class="text-sm text-white opacity-95">Rapport quotidien du caissier : recettes, dépenses, crédits et liquidités.</p>
        </a>

    </div>
</div>
@endsection
