@extends('layouts.app')
@section('title', 'Modes de Paiement')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Modes de Paiement</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Gestion et suivi des paiements</p>
                </div>
            </div>
        </div>

        <!-- Messages de session -->
        @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300">
            {{ session('success') }}
        </div>
        @endif

        <!-- Formulaire de filtrage avancé -->
        <form method="GET" action="{{ route('modepaiements.index') }}" class="mb-8" id="filterForm">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtres Avancés
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Période -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Période</label>
                        <select name="period" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" onchange="updatePeriodFields()">
                            <option value="">Toutes les périodes</option>
                            <option value="day" {{ request('period') === 'day' ? 'selected' : '' }}>Jour</option>
                            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Semaine</option>
                            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Mois</option>
                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }}>Année</option>
                            <option value="range" {{ request('period') === 'range' ? 'selected' : '' }}>Plage personnalisée</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div id="dateField" style="display: {{ request('period') === 'day' ? 'block' : 'none' }};">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    <!-- Type de paiement -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de Paiement</label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">Tous les types</option>
                            @foreach($typesModes as $typeMode)
                            <option value="{{ $typeMode }}" {{ request('type') === $typeMode ? 'selected' : '' }}>{{ ucfirst($typeMode) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Source de paiement -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Source</label>
                        <select name="source" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">Toutes les sources</option>
                            <option value="facture" {{ request('source') === 'facture' ? 'selected' : '' }}>Factures</option>
                            <option value="depense" {{ request('source') === 'depense' ? 'selected' : '' }}>Dépenses</option>
                            <option value="part_medecin" {{ request('source') === 'part_medecin' ? 'selected' : '' }}>Part Médecin</option>
                            <option value="credit_assurance" {{ request('source') === 'credit_assurance' ? 'selected' : '' }}>Crédit Assurance</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('modepaiements.index') }}" class="inline-flex items-center px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium rounded-lg transition shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        <!-- Cartes de résumé statistique -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total des paiements -->
            <div class="group bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl shadow-lg p-6 text-white overflow-hidden relative">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition">
                    <svg class="absolute -right-8 -top-8 w-32 h-32" fill="currentColor" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50" opacity="0.1"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium opacity-90">Total Paiements</h3>
                        <svg class="w-5 h-5 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ number_format($paiements->sum('montant'), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-1">MRU</p>
                </div>
            </div>

            <!-- Paiements en espèces -->
            <div class="group bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700 rounded-xl shadow-lg p-6 text-white overflow-hidden relative">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition">
                    <svg class="absolute -right-8 -top-8 w-32 h-32" fill="currentColor" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50" opacity="0.1"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium opacity-90">Espèces</h3>
                        <svg class="w-5 h-5 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.16 2.75a.75.75 0 00-.743.882l.534 3.201H5.75a.75.75 0 000 1.5h2.122l.66 3.966H6.75a.75.75 0 000 1.5h2.211l.534 3.2a.75.75 0 001.485-.247l-.61-3.653h3.958l.534 3.2a.75.75 0 001.485-.247l-.61-3.653H17.75a.75.75 0 000-1.5h-2.211l-.66-3.966H17.25a.75.75 0 000-1.5h-2.122l-.534-3.2a.75.75 0 00-1.485.247l.61 3.653H9.25l-.534-3.2a.75.75 0 00-.743-.882zM12.332 11.75l.66-3.966H9.034l-.66 3.966h3.958z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ number_format($paiements->where('type', 'espèces')->sum('montant'), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-1">MRU</p>
                </div>
            </div>

            <!-- Paiements numériques -->
            <div class="group bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl shadow-lg p-6 text-white overflow-hidden relative">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition">
                    <svg class="absolute -right-8 -top-8 w-32 h-32" fill="currentColor" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50" opacity="0.1"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium opacity-90">Paiements Numériques</h3>
                        <svg class="w-5 h-5 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3.25 3A2.25 2.25 0 001 5.25v9.5A2.25 2.25 0 003.25 17h13.5A2.25 2.25 0 0019 14.75v-9.5A2.25 2.25 0 0016.75 3H3.25zm0 1.5h13.5a.75.75 0 01.75.75v2h-15v-2a.75.75 0 01.75-.75zm0 12h13.5a.75.75 0 01-.75.75H3.25a.75.75 0 01-.75-.75zm.75-4.5h13.5v3a.75.75 0 01-.75.75H3.25a.75.75 0 01-.75-.75v-3z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ number_format($paiements->whereIn('type', ['bankily', 'masrivi', 'sedad'])->sum('montant'), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-1">MRU</p>
                </div>
            </div>

            <!-- Part Médecin -->
            <div class="group bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl shadow-lg p-6 text-white overflow-hidden relative">
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition">
                    <svg class="absolute -right-8 -top-8 w-32 h-32" fill="currentColor" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="50" opacity="0.1"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium opacity-90">Part Médecin</h3>
                        <svg class="w-5 h-5 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold">{{ number_format($paiements->where('source', 'part_medecin')->sum('montant'), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-1">MRU</p>
                </div>
            </div>
        </div>

        <!-- Tableau des paiements -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Source</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">Montant</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Facture Caisse</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($paiements as $paiement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-300 font-medium">{{ $paiement->id }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $paiement->type === 'espèces' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300' : '' }}
                                    {{ in_array($paiement->type, ['bankily', 'masrivi', 'sedad']) ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}
                                ">
                                    {{ ucfirst($paiement->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $paiement->source === 'part_medecin' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                                    {{ $paiement->source === 'depense' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    {{ $paiement->source === 'facture' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $paiement->source === 'credit_assurance' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300' : '' }}
                                ">
                                    @if($paiement->source === 'part_medecin')
                                        Part Médecin
                                    @elseif($paiement->source === 'depense')
                                        Dépense
                                    @elseif($paiement->source === 'facture')
                                        Facture
                                    @elseif($paiement->source === 'credit_assurance')
                                        Crédit Assurance
                                    @else
                                        {{ ucfirst($paiement->source ?? 'N/A') }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                {{ number_format($paiement->montant, 0, ',', ' ') }} MRU
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                @if($paiement->caisse_id)
                                    <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.caisses.show', $paiement->caisse_id) : route('caisses.show', $paiement->caisse_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                        Facture #{{ $paiement->caisse_id }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $paiement->created_at->translatedFormat('d M Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium">Aucun paiement trouvé</p>
                                    <p class="text-sm">Ajustez vos filtres pour voir plus de résultats</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($paiements->hasPages())
        <div class="mt-8">
            {{ $paiements->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function updatePeriodFields() {
    const period = document.querySelector('[name="period"]').value;
    const dateField = document.getElementById('dateField');
    
    // Masquer tous les champs de date
    [document.getElementById('dateField')].forEach(el => {
        if (el) el.style.display = 'none';
    });
    
    // Afficher le champ approprié
    if (period === 'day' && dateField) {
        dateField.style.display = 'block';
    }
}

// Initialiser au chargement
updatePeriodFields();

// Soumission automatique du formulaire au changement de filtre
document.querySelectorAll('select[name="type"], select[name="source"]').forEach(select => {
    select.addEventListener('change', () => {
        document.getElementById('filterForm').submit();
    });
});
</script>
@endsection
