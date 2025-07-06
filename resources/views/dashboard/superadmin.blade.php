@extends('layouts.app')
@section('title', 'Dashboard Superadmin')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <h1 class="page-title mb-6">Tableau de bord du Super Admin</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- A) Gérer les utilisateurs (admins) --}}
        <a href="{{ route('superadmin.admins.index') }}"
            class="bg-blue-600 text-white rounded-2xl p-6 shadow hover:bg-blue-700 transition">
            <h2 class="text-xl font-semibold mb-2">Gérer les utilisateurs</h2>
            <p class="text-sm text-blue-100">Approuver, supprimer ou consulter les comptes administrateurs.</p>
        </a>

        {{-- B) Gérer les médecins --}}
        <a href="{{ route('superadmin.medecins.index') }}"
            class="bg-green-600 text-white rounded-2xl p-6 shadow hover:bg-green-700 transition">
            <h2 class="text-xl font-semibold mb-2">Gérer les médecins</h2>
            <p class="text-sm text-green-100">Lister, modifier ou supprimer les médecins enregistrés.</p>
        </a>

        {{-- C) Gestion des patients --}}
        <a href="{{ route('superadmin.patients.index') }}"
            class="bg-purple-600 text-white rounded-2xl p-6 shadow hover:bg-purple-700 transition">
            <h2 class="text-xl font-semibold mb-2">Gestion des patients</h2>
            <p class="text-sm text-purple-100">Accéder à la liste, création et mise à jour des dossiers patients.</p>
        </a>

        {{-- D) Gestion des rendez-vous --}}
        <a href="{{ url('/rendezvous') }}"
            class="bg-yellow-500 text-white rounded-2xl p-6 shadow hover:bg-yellow-600 transition">
            <h2 class="text-xl font-semibold mb-2">Rendez-vous</h2>
            <p class="text-sm text-yellow-100">Programmer, suivre ou annuler des rendez-vous médicaux.</p>
        </a>

        {{-- E) Dossier médical électronique --}}
        <a href="{{ route('dossiers.index') }}"
            class="bg-red-500 text-white rounded-2xl p-6 shadow hover:bg-red-600 transition">
            <h2 class="text-xl font-semibold mb-2">Dossier Médical</h2>
            <p class="text-sm text-red-100">Consulter et modifier les dossiers médicaux des patients.</p>
        </a>

        {{-- G) Services hospitaliers --}}
        <a href="{{ route('services.index') }}"
            class="bg-cyan-600 text-white rounded-2xl p-6 shadow hover:bg-cyan-700 transition">
            <h2 class="text-xl font-semibold mb-2">Services hospitaliers</h2>
            <p class="text-sm text-cyan-100">Configurer et surveiller les services offerts par la clinique.</p>
        </a>

        {{-- H) Hospitalisations --}}
        <a href="{{ url('/hospitalisations') }}"
            class="bg-pink-600 text-white rounded-2xl p-6 shadow hover:bg-pink-700 transition">
            <h2 class="text-xl font-semibold mb-2">Hospitalisations</h2>
            <p class="text-sm text-pink-100">Suivi des séjours et admissions des patients.</p>
        </a>

        {{-- I) Médicaments et Pharmacie --}}
        <a href="{{ url('/pharmacie') }}"
            class="bg-teal-600 text-white rounded-2xl p-6 shadow hover:bg-teal-700 transition">
            <h2 class="text-xl font-semibold mb-2">Pharmacie</h2>
            <p class="text-sm text-teal-100">Suivi du stock de médicaments et prescriptions.</p>
        </a>


        {{-- L) Caisse --}}
        <a href="{{ route('caisses.index') }}"
            class="bg-gray-700 text-white rounded-2xl p-6 shadow hover:bg-gray-800 transition">
            <h2 class="text-xl font-semibold mb-2">Gestion des Factures</h2>
            <p class="text-sm text-gray-300">Suivi des entrées/sorties d'argent et clôtures de caisse.</p>
        </a>

        {{-- 1) Module Personnel --}}
        <a href="{{ route('personnels.index') }}"
            class="bg-sky-600 text-white rounded-2xl p-6 shadow hover:bg-sky-700 transition">
            <h2 class="text-xl font-semibold mb-2">Gestion du Personnel</h2>
            <p class="text-sm text-sky-100">Ajouter, modifier ou supprimer les membres du personnel.</p>
        </a>


        {{-- 4) Module Prescripteurs --}}
        <a href="{{ route('prescripteurs.index') }}"
            class="bg-amber-600 text-white rounded-2xl p-6 shadow hover:bg-orange-700 transition">
            <h2 class="text-xl font-semibold mb-2">Prescripteurs</h2>
            <p class="text-sm text-orange-100">Gérer les médecins prescripteurs externes.</p>
        </a>

        {{-- 5) Module Examens --}}
        <a href="{{ route('examens.index') }}"
            class="bg-pink-600 text-white rounded-2xl p-6 shadow hover:bg-pink-700 transition">
            <h2 class="text-xl font-semibold mb-2">Examens</h2>
            <p class="text-sm text-pink-100">Définir les examens médicaux avec leurs tarifs.</p>
        </a>

        {{-- 6) Module Assurances --}}
        <a href="{{ route('assurances.index') }}"
            class="bg-lime-600 text-white rounded-2xl p-6 shadow hover:bg-lime-700 transition">
            <h2 class="text-xl font-semibold mb-2">Assurances</h2>
            <p class="text-sm text-lime-100">Gérer les assurances partenaires de la clinique.</p>
        </a>

        {{-- 7) Module Dépenses --}}
        <a href="{{ route('depenses.index') }}"
            class="bg-red-600 text-white rounded-2xl p-6 shadow hover:bg-red-700 transition">
            <h2 class="text-xl font-semibold mb-2">Dépenses</h2>
            <p class="text-sm text-red-100">Enregistrer les dépenses opérationnelles.</p>
        </a>

        {{-- 8) Module État de Caisse --}}
        <a href="{{ route('etatcaisse.index') }}"
            class="bg-gray-600 text-white rounded-2xl p-6 shadow hover:bg-gray-900 transition">
            <h2 class="text-xl font-semibold mb-2">État de Caisse</h2>
            <p class="text-sm text-amber-100">Suivre les recettes, dépenses et parts.</p>
        </a>

        {{-- 9) Récapitulatif Journalier des Services --}}
        <a href="{{ route('recap-services.index') }}"
            class="bg-indigo-600 text-white rounded-2xl p-6 shadow hover:bg-indigo-700 transition">
            <h2 class="text-xl font-semibold mb-2">Récap. Services</h2>
            <p class="text-sm text-indigo-100">Voir les totaux de services selon les dates.</p>
        </a>

        {{-- 10) Récapitulatif Journalier des Médecins --}}
        <a href="{{ route('recap-operateurs.index') }}"
            class="bg-teal-600 text-white rounded-2xl p-6 shadow hover:bg-teal-700 transition">
            <h2 class="text-xl font-semibold mb-2">Récap. Opérateurs</h2>
            <p class="text-sm text-teal-100">Analyser les consultations et les parts des médecins.</p>
        </a>

        {{-- 11) Part du Cabinet --}}
        <a href="{{ route('credits.index') }}"
            class="bg-purple-800 text-white rounded-2xl p-6 shadow hover:bg-purple-700 transition">
            <h2 class="text-xl font-semibold mb-2">Suivi du Crédit</h2>
            <p class="text-sm text-purple-100">Gérer les Credits liés au personnels et assurances.</p>
        </a>
        {{-- 11) Mode de Paiement --}}
        <a href="{{ route('modepaiements.index') }}"
            class="bg-emerald-800 text-white rounded-2xl p-6 shadow hover:bg-emerald-700 transition">
            <h2 class="text-xl font-semibold mb-2">Mode de Paiement</h2>
            <p class="text-sm text-purple-100">Gérer la trésorie liés au Paiements Bankily, Masrivi, et autres.</p>
        </a>
    </div>
</div>
@endsection
