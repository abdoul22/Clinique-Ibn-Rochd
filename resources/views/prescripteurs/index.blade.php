@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- En-tête moderne -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Prescripteurs</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gérez vos prescripteurs et suivez leurs performances
                </p>
            </div>

            @php
                $userRole = auth()->user()->role?->name ?? 'guest';
                $canCreate = in_array($userRole, ['superadmin', 'admin', 'medecin']);
                $canEdit = in_array($userRole, ['superadmin']);
                
                // Déterminer les routes selon le rôle
                if ($userRole === 'medecin') {
                    $createRoute = route('medecin.prescripteurs.create');
                    $printRoute = route('medecin.prescripteurs.index'); // Médecin n'a pas d'accès direct print/pdf dans le contrôleur actuel mais on peut adapter
                    $pdfRoute = route('medecin.prescripteurs.index');
                } elseif ($userRole === 'admin') {
                    $createRoute = route('admin.prescripteurs.create');
                    $printRoute = route('admin.prescripteurs.print');
                    $pdfRoute = route('admin.prescripteurs.exportPdf');
                } elseif ($userRole === 'superadmin') {
                    $createRoute = route('superadmin.prescripteurs.create');
                    $printRoute = route('superadmin.prescripteurs.print');
                    $pdfRoute = route('superadmin.prescripteurs.exportPdf');
                } else {
                    $createRoute = route('prescripteurs.create');
                    $printRoute = route('prescripteurs.print');
                    $pdfRoute = route('prescripteurs.exportPdf');
                }
            @endphp

            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <!-- Bouton Ajouter - Visible pour admin et médecin aussi -->
                @if($canCreate)
                <a href="{{ $createRoute }}"
                    class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter un prescripteur
                </a>
                @endif

                <!-- Bouton PDF - Seulement superadmin -->
                @if($canEdit)
                <a href="{{ $pdfRoute }}"
                    class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-xl">
                    Télécharger PDF
                </a>

                <!-- Bouton Imprimer - Seulement superadmin -->
                <a href="{{ $printRoute }}" target="_blank"
                    class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-xl">
                    Imprimer
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Formulaire de filtrage -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ $userRole === 'medecin' ? route('medecin.prescripteurs.index') : ($userRole === 'admin' ? route('admin.prescripteurs.index') : route('prescripteurs.index')) }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Filtre par nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Rechercher par nom
                    </label>
                    <input type="text" 
                        name="nom" 
                        id="nom" 
                        value="{{ request('nom') }}"
                        placeholder="Entrez le nom..."
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                </div>

                <!-- Filtre par spécialité -->
                <div>
                    <label for="specialite" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Filtrer par spécialité
                    </label>
                    <select name="specialite" 
                        id="specialite"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-all duration-200">
                        <option value="">Toutes les spécialités</option>
                        @foreach($specialites as $specialite)
                            <option value="{{ $specialite }}" {{ request('specialite') == $specialite ? 'selected' : '' }}>
                                {{ $specialite }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Boutons d'action -->
                <div class="flex items-end gap-2">
                    <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrer
                    </button>
                    @if(request('nom') || request('specialite'))
                    <a href="{{ $userRole === 'medecin' ? route('medecin.prescripteurs.index') : ($userRole === 'admin' ? route('admin.prescripteurs.index') : route('prescripteurs.index')) }}" 
                        class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Réinitialiser
                    </a>
                    @endif
                </div>
            </div>

            <!-- Indicateur de filtres actifs -->
            @if(request('nom') || request('specialite'))
            <div class="flex items-center gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtres actifs :</span>
                @if(request('nom'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    Nom : {{ request('nom') }}
                </span>
                @endif
                @if(request('specialite'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                    Spécialité : {{ request('specialite') }}
                </span>
                @endif
            </div>
            @endif
        </form>
    </div>

    <!-- Nombre de résultats -->
    @if(request('nom') || request('specialite'))
    <div class="mb-4">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium text-blue-800 dark:text-blue-300">
                    {{ $prescripteurs->total() }} prescripteur(s) trouvé(s)
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Grid responsive de cartes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
        @forelse($prescripteurs as $prescripteur)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-200 dark:border-gray-700">
            <!-- En-tête de la carte avec ID -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-100 uppercase tracking-wider">
                        Prescripteur #{{ $prescripteur->id }}
                    </span>
                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-white/20 text-white backdrop-blur-sm">
                        {{ $prescripteur->specialite ?? 'Non spécifiée' }}
                    </span>
                </div>
            </div>

            <!-- Contenu de la carte -->
            <div class="p-6">
                <!-- Nom du prescripteur -->
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    {{ $prescripteur->nom }}
                </h3>

                <!-- Badge de spécialité (mobile uniquement) -->
                <div class="mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ $prescripteur->specialite ?? 'Non spécifiée' }}
                    </span>
                </div>

                <!-- Actions -->
                @if($canEdit)
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex space-x-3">
                        <!-- Voir les statistiques -->
                        <a href="{{ route('prescripteurs.show', $prescripteur->id) }}"
                            class="p-2 text-green-600 hover:text-green-900 hover:bg-green-50 dark:text-green-400 dark:hover:text-green-300 dark:hover:bg-green-900/20 rounded-lg transition-all duration-200"
                            title="Voir les statistiques">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route('prescripteurs.edit', $prescripteur->id) }}"
                            class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:text-indigo-300 dark:hover:bg-indigo-900/20 rounded-lg transition-all duration-200"
                            title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route('prescripteurs.destroy', $prescripteur->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce prescripteur ?')"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200"
                                title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @empty
        <!-- État vide -->
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Aucun prescripteur trouvé</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    @if(request('nom') || request('specialite'))
                        Aucun prescripteur ne correspond aux critères de recherche
                    @else
                        Commencez par ajouter votre premier prescripteur
                    @endif
                </p>
                @if($canCreate)
                <a href="{{ $createRoute }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-cyan-600 dark:hover:bg-cyan-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter le premier prescripteur
                </a>
                @endif
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($prescripteurs->hasPages())
    <div class="mt-6">
        <div class="flex justify-center">
            {{ $prescripteurs->appends(request()->query())->onEachSide(1)->links() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Animation du formulaire de filtrage
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[method="GET"]');
        const inputs = form.querySelectorAll('input, select');
        
        // Animation au focus
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('transform', 'scale-105', 'transition-transform', 'duration-200');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('transform', 'scale-105');
            });
        });

        // Soumission automatique du formulaire lors de la sélection d'une spécialité
        const specialiteSelect = document.getElementById('specialite');
        if (specialiteSelect) {
            specialiteSelect.addEventListener('change', function() {
                if (this.value !== '') {
                    form.submit();
                }
            });
        }

        // Permettre la soumission avec la touche Entrée
        const nomInput = document.getElementById('nom');
        if (nomInput) {
            nomInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        }
    });
</script>
@endpush
