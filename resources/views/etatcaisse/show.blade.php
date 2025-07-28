@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 p-6 rounded shadow dark:shadow-lg">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Détails de l'État de Caisse</h2>
        <a href="{{ route('etatcaisse.index') }}"
            class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informations Générales -->
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informations Générales</h3>
            <div class="space-y-3 text-gray-900 dark:text-gray-100">
                <div>
                    <strong class="text-gray-700 dark:text-gray-300">Désignation :</strong>
                    <span class="ml-2">{{ $etatcaisse->designation }}</span>
                </div>
                <div>
                    <strong class="text-gray-700 dark:text-gray-300">Date :</strong>
                    <span class="ml-2">{{ $etatcaisse->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Montants Financiers -->
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Montants Financiers</h3>
            <div class="space-y-3 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between">
                    <strong class="text-gray-700 dark:text-gray-300">Recette :</strong>
                    <span class="text-green-600 dark:text-green-400 font-semibold">
                        {{ number_format($etatcaisse->recette ?? 0, 2) }} MRU
                    </span>
                </div>
                <div class="flex justify-between">
                    <strong class="text-gray-700 dark:text-gray-300">Dépense :</strong>
                    <span class="text-red-600 dark:text-red-400 font-semibold">
                        {{ number_format($etatcaisse->depense ?? 0, 2) }} MRU
                    </span>
                </div>
                @if($etatcaisse->part_medecin)
                <div class="flex justify-between">
                    <strong class="text-gray-700 dark:text-gray-300">Part Médecin :</strong>
                    <span class="text-blue-600 dark:text-blue-400 font-semibold">
                        {{ number_format($etatcaisse->part_medecin, 2) }} MRU
                    </span>
                </div>
                @endif
                @if($etatcaisse->part_clinique)
                <div class="flex justify-between">
                    <strong class="text-gray-700 dark:text-gray-300">Part Clinique :</strong>
                    <span class="text-purple-600 dark:text-purple-400 font-semibold">
                        {{ number_format($etatcaisse->part_clinique, 2) }} MRU
                    </span>
                </div>
                @endif
                <hr class="border-gray-300 dark:border-gray-600">
                <div class="flex justify-between text-lg font-bold">
                    <strong class="text-gray-900 dark:text-white">Solde :</strong>
                    @php
                    $solde = ($etatcaisse->recette ?? 0) - ($etatcaisse->depense ?? 0);
                    @endphp
                    <span
                        class="{{ $solde >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ number_format($solde, 2) }} MRU
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Relations -->
    @if($etatcaisse->personnel || $etatcaisse->assurance || $etatcaisse->medecin)
    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informations Associées</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-900 dark:text-gray-100">
            @if($etatcaisse->personnel)
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Personnel :</strong>
                <div class="mt-1 p-2 bg-blue-50 dark:bg-blue-900/30 rounded">
                    <p class="font-medium">{{ $etatcaisse->personnel->nom }} {{ $etatcaisse->personnel->prenom }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $etatcaisse->personnel->fonction }}</p>
                </div>
            </div>
            @endif

            @if($etatcaisse->assurance && $etatcaisse->caisse)
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Assurance :</strong>
                <div class="mt-1 p-3 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <p class="font-medium text-green-800 dark:text-green-200">{{ $etatcaisse->assurance->nom }}</p>
                    @if($etatcaisse->assurance->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $etatcaisse->assurance->description }}
                    </p>
                    @endif

                    @php
                    $montantTotal = $etatcaisse->caisse->total ?? 0;
                    $couverture = $etatcaisse->caisse->couverture ?? 0;
                    $montantAssurance = $montantTotal * ($couverture / 100);
                    $montantPatient = $montantTotal - $montantAssurance;
                    @endphp

                    <div class="space-y-2 mt-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Couverture :</span>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ $couverture }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Montant total facture
                                :</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{
                                number_format($montantTotal, 2) }} MRU</span>
                        </div>
                        <div
                            class="flex justify-between items-center bg-green-100 dark:bg-green-800/50 px-2 py-1 rounded">
                            <span class="text-sm font-medium text-green-700 dark:text-green-300">Pris en charge par
                                assurance :</span>
                            <span class="text-sm font-bold text-green-700 dark:text-green-300">{{
                                number_format($montantAssurance, 2) }} MRU</span>
                        </div>
                        <div
                            class="flex justify-between items-center bg-blue-100 dark:bg-blue-800/50 px-2 py-1 rounded">
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Payé par le patient
                                :</span>
                            <span class="text-sm font-bold text-blue-700 dark:text-blue-300">{{
                                number_format($montantPatient, 2) }} MRU</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($etatcaisse->medecin)
            <div>
                <strong class="text-gray-700 dark:text-gray-300">Médecin :</strong>
                <div class="mt-1 p-2 bg-purple-50 dark:bg-purple-900/30 rounded">
                    <p class="font-medium">Dr. {{ $etatcaisse->medecin->nom }} {{ $etatcaisse->medecin->prenom }}</p>
                    @if($etatcaisse->medecin->specialite)
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $etatcaisse->medecin->specialite }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="mt-6 flex space-x-3">
        <form action="{{ route('etatcaisse.destroy', $etatcaisse->id) }}" method="POST" class="inline"
            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet état de caisse ?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 flex items-center">
                <i class="fas fa-trash mr-2"></i>
                Supprimer
            </button>
        </form>
    </div>
</div>
@endsection
