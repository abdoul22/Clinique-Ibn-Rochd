@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- En-tête avec bouton retour -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-money-bill-wave text-red-600 dark:text-red-400"></i>
                Détail de la dépense #{{ $depense->id }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Informations complètes de la dépense
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('depenses.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Informations de la dépense -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                Informations de la Dépense
            </h2>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">ID de la dépense:</span>
                    <span class="text-gray-900 dark:text-white font-semibold">#{{ $depense->id }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Nom:</span>
                    <span class="text-gray-900 dark:text-white font-semibold">{{ $depense->nom }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Montant:</span>
                    <span class="text-gray-900 dark:text-white font-bold text-red-600 dark:text-red-400">
                        {{ number_format($depense->montant, 0, ',', ' ') }} MRU
                    </span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Mode de paiement:</span>
                    <span class="text-gray-900 dark:text-white">
                        {{ ucfirst($depense->mode_paiement_id) }}
                    </span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Source:</span>
                    <span class="text-gray-900 dark:text-white">{{ ucfirst($depense->source) }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Date de création:</span>
                    <span class="text-gray-900 dark:text-white">
                        {{ $depense->created_at ? $depense->created_at->format('d/m/Y H:i') : 'N/A' }}
                    </span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Statut:</span>
                    <span class="text-gray-900 dark:text-white">
                        @if($depense->rembourse)
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-400">
                            Remboursé
                        </span>
                        @else
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-400">
                            Payé
                        </span>
                        @endif
                    </span>
                </div>

                @if($depense->creator)
                <div class="flex justify-between items-center py-3">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Créé par:</span>
                    <span class="text-gray-900 dark:text-white">
                        {{ $depense->creator->name }}
                        @if($depense->creator->role)
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">
                            ({{ ucfirst($depense->creator->role->name) }})
                        </span>
                        @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-chart-pie text-green-600 dark:text-green-400"></i>
                Résumé Financier
            </h2>

            <div class="space-y-6">
                <!-- Montant total -->
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-red-700 dark:text-red-300">Montant Total</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                                {{ number_format($depense->montant, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                        <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full">
                            <i class="fas fa-money-bill-wave text-red-600 dark:text-red-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                @if($depense->credit)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Crédit personnel lié</p>
                            <p class="text-base text-yellow-900 dark:text-yellow-200">{{ $depense->credit->nom ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full">
                            <i class="fas fa-credit-card text-yellow-600 dark:text-yellow-400 text-xl"></i>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
