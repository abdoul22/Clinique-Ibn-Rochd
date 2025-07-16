@extends('layouts.app')
@section('title', "Dashboard Administrateur")

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">Tableau de bord de l'Administrateur</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- Patients --}}
        <a href="{{ route('admin.patients.index') }}"
            class="bg-purple-600 dark:bg-purple-800 text-white rounded-2xl p-6 shadow hover:bg-purple-700 dark:hover:bg-purple-900 transition">
            <h2 class="text-xl font-semibold mb-2">Patients</h2>
            <p class="text-sm text-purple-100">Créer, consulter ou mettre à jour les dossiers patients.</p>
        </a>

        {{-- Rendez-vous --}}
        <a href="{{ route('admin.rendezvous.index') }}"
            class="bg-lime-600 dark:bg-lime-800 text-white rounded-2xl p-6 shadow hover:bg-lime-700 dark:hover:bg-lime-900 transition">
            <h2 class="text-xl font-semibold mb-2">Rendez-vous</h2>
            <p class="text-sm text-yellow-100">Planifier ou suivre les rendez-vous médicaux.</p>
        </a>

        {{-- Dossier médical --}}
        <a href="{{ route('admin.dossiers.index') }}"
            class="bg-red-500 dark:bg-red-700 text-white rounded-2xl p-6 shadow hover:bg-red-600 dark:hover:bg-red-900 transition">
            <h2 class="text-xl font-semibold mb-2">Dossier Médical</h2>
            <p class="text-sm text-red-100">Accès aux dossiers médicaux autorisés.</p>
        </a>

        {{-- Hospitalisations --}}
        <a href="{{ route('hospitalisations.index') }}"
            class="bg-blue-600 dark:bg-blue-800 text-white rounded-2xl p-6 shadow hover:bg-blue-700 dark:hover:bg-blue-900 transition">
            <h2 class="text-xl font-semibold mb-2">Hospitalisations</h2>
            <p class="text-sm text-blue-100">Gérer les hospitalisations et les lits.</p>
        </a>

        {{-- Chambres --}}
        <a href="{{ route('chambres.index') }}"
            class="bg-green-600 dark:bg-green-800 text-white rounded-2xl p-6 shadow hover:bg-green-700 dark:hover:bg-green-900 transition">
            <h2 class="text-xl font-semibold mb-2">Gestion des Chambres</h2>
            <p class="text-sm text-green-100">Gérer les chambres et lits disponibles.</p>
        </a>

        {{-- Caisse --}}
        <a href="{{ route('admin.caisses.index') }}"
            class="bg-gray-700 dark:bg-gray-600 text-white rounded-2xl p-6 shadow hover:bg-gray-800 dark:hover:bg-gray-950 transition">
            <h2 class="text-xl font-semibold mb-2">Gestion des Factures</h2>
            <p class="text-sm text-gray-300">Gérer les flux de trésorerie de la clinique.</p>
        </a>

    </div>
</div>
@endsection
