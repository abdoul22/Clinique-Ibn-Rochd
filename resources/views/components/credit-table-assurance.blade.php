<!-- Desktop Table Headers (Hidden on mobile) -->
<div class="desktop-only">
    <div
        class="grid grid-cols-7 gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 font-medium text-sm text-gray-700 dark:text-gray-300">
        <div class="flex items-center">Facture</div>
        <div class="flex items-center">Nom</div>
        <div class="flex items-center">Montant</div>
        <div class="flex items-center">Mode de paiement</div>
        <div class="flex items-center">Statut</div>
        <div class="flex items-center">Date de prise</div>
        <div class="flex items-center justify-end">Actions</div>
    </div>
</div>

@forelse($credits as $credit)
<!-- Desktop Table View (Hidden on mobile) -->
<div class="desktop-only">
    <div
        class="grid grid-cols-7 gap-4 p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150">
        <!-- Facture -->
        <div class="flex items-center">
            @if($credit->caisse)
            <a href="{{ route('caisses.show', $credit->caisse->id) }}"
                class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                #{{ $credit->caisse->numero_facture }}
            </a>
            @else
            <span class="text-sm text-gray-400 dark:text-gray-500">-</span>
            @endif
        </div>

        <!-- Name with Type Badge -->
        <div class="flex items-center space-x-2">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ $credit->source?->nom ?? 'N/A' }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Assurance
                </div>
            </div>
        </div>

        <!-- Amount -->
        <div class="flex items-center">
            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ number_format($credit->montant, 0, ',', ' ') }} <span class="text-xs text-gray-500">MRU</span>
            </div>
        </div>

        <!-- Payment Mode -->
        <div class="flex items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ ucfirst($credit->mode_paiement_id ?? 'Non défini') }}
            </span>
        </div>

        <!-- Status -->
        <div class="flex items-center">
            @if($credit->status === 'non payé')
            <span
                class="inline-flex items-center gap-1 text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-1 rounded-full font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Non payé
            </span>
            @elseif($credit->status === 'partiellement payé')
            <span
                class="inline-flex items-center gap-1 text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-1 rounded-full font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Partiel
            </span>
            @else
            <span
                class="inline-flex items-center gap-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded-full font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Payé
            </span>
            @endif
        </div>

        <!-- Date -->
        <div class="flex items-center">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <div class="font-medium">{{ $credit->created_at->format('d/m/Y') }}</div>
                <div class="text-xs opacity-75">{{ $credit->created_at->format('H:i') }}</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end">
            @if($credit->status !== 'payé')
            <a href="{{ route('credits.payer', $credit->id) }}"
                class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition-all duration-200 transform hover:scale-105">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Payer
            </a>
            @else
            <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">Terminé</span>
            @endif
        </div>
    </div>
</div>

<!-- Mobile Card View (Visible on mobile) -->
<div
    class="mobile-responsive mobile-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-3 shadow-sm hover:shadow-md transition-shadow duration-200">
    <!-- Card Header -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
            </div>

            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white">
                    {{ $credit->source?->nom ?? 'N/A' }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Assurance
                </p>
            </div>
        </div>

        <span class="text-xs font-mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
            #{{ $credit->id }}
        </span>
    </div>

    <!-- Card Content -->
    <div class="space-y-3">
        <!-- Facture -->
        @if($credit->caisse)
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">Facture:</span>
            <a href="{{ route('caisses.show', $credit->caisse->id) }}"
                class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                #{{ $credit->caisse->numero_facture }}
            </a>
        </div>
        @endif

        <!-- Amount -->
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">Montant:</span>
            <span class="text-lg font-bold text-gray-900 dark:text-white">
                {{ number_format($credit->montant, 0, ',', ' ') }} <span class="text-sm text-gray-500">MRU</span>
            </span>
        </div>

        <!-- Status -->
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">Statut:</span>
            @if($credit->status === 'non payé')
            <span
                class="inline-flex items-center gap-1 text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-1 rounded-full font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Non payé
            </span>
            @elseif($credit->status === 'partiellement payé')
            <span
                class="inline-flex items-center gap-1 text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-1 rounded-full font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Partiel
            </span>
            @else
            <span
                class="inline-flex items-center gap-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded-full font-medium">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Payé
            </span>
            @endif
        </div>

        <!-- Payment Mode -->
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">Mode de paiement:</span>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ ucfirst($credit->mode_paiement_id ?? 'Non défini') }}
            </span>
        </div>

        <!-- Date -->
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">Date de prise:</span>
            <div class="text-sm text-gray-900 dark:text-white">
                <div class="font-medium">{{ $credit->created_at->format('d/m/Y') }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $credit->created_at->format('H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Card Actions -->
    @if($credit->status !== 'payé')
    <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
        <a href="{{ route('credits.payer', $credit->id) }}"
            class="w-full inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-4 rounded-lg transition-all duration-200 transform hover:scale-105">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Payer le crédit
        </a>
    </div>
    @endif
</div>

@empty
<!-- Empty State -->
<div class="text-center py-12">
    <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
        </svg>
    </div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun crédit trouvé</h3>
    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
        Aucun crédit ne correspond aux critères de recherche sélectionnés. Essayez de modifier vos filtres.
    </p>
</div>
@endforelse
