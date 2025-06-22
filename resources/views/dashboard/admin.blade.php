@extends('layouts.app')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de bord de l'Administrateur</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">


        {{-- Patients --}}
        <a href="{{ url('/patients') }}"
            class="bg-purple-600 text-white rounded-2xl p-6 shadow hover:bg-purple-700 transition">
            <h2 class="text-xl font-semibold mb-2">Patients</h2>
            <p class="text-sm text-purple-100">Créer, consulter ou mettre à jour les dossiers patients.</p>
        </a>

        {{-- Rendez-vous --}}
        <a href="{{ url('/rendezvous') }}"
            class="bg-lime-600 text-white rounded-2xl p-6 shadow hover:bg-lime-700 transition">
            <h2 class="text-xl font-semibold mb-2">Rendez-vous</h2>
            <p class="text-sm text-yellow-100">Planifier ou suivre les rendez-vous médicaux.</p>
        </a>

        {{-- Dossier médical --}}
        <a href="{{ url('/dossiers') }}"
            class="bg-red-500 text-white rounded-2xl p-6 shadow hover:bg-red-600 transition">
            <h2 class="text-xl font-semibold mb-2">Dossier Médical</h2>
            <p class="text-sm text-red-100">Accès aux dossiers médicaux autorisés.</p>
        </a>

        {{-- Caisse --}}
        <a href="{{ route('caisses.index') }}"
            class="bg-gray-700 text-white rounded-2xl p-6 shadow hover:bg-gray-800 transition">
            <h2 class="text-xl font-semibold mb-2">Caisse</h2>
            <p class="text-sm text-gray-300">Gérer les flux de trésorerie de la clinique.</p>
        </a>

    </div>
</div>
@endsection
