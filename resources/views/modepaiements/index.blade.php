@extends('layouts.app')
@section('title', 'Modes de Paiement')

@section('content')
<style>
    :root[data-theme="light"] .modepaiements-container,
    html:not(.dark) .modepaiements-container {
        background: linear-gradient(to bottom right, rgb(241, 245, 249), rgb(219, 234, 254), rgb(226, 232, 240));
        color: rgb(15, 23, 42);
    }
    
    html.dark .modepaiements-container {
        background: linear-gradient(to bottom right, rgb(15, 23, 42), rgb(75, 0, 130), rgb(15, 23, 42));
        color: rgb(255, 255, 255);
    }
</style>

<div class="modepaiements-container min-h-screen p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="mb-12">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-5xl font-bold text-slate-900 dark:text-white drop-shadow-lg">Modes de Paiement</h1>
                    <p class="text-slate-600 dark:text-purple-200 mt-2 text-lg">Gestion complète et suivi des paiements</p>
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
            <div class="bg-white/40 dark:bg-white/5 backdrop-blur-lg rounded-2xl shadow-xl p-6 border border-slate-200 dark:border-white/20 lg:shadow-2xl">
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtres Avancés
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Période -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-100 mb-2">Période</label>
                        <select name="period" class="w-full px-4 py-2.5 border border-slate-300 dark:border-white/20 rounded-lg bg-white dark:bg-white/10 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-gray-300 focus:ring-2 focus:ring-blue-500 dark:focus:ring-purple-400 focus:border-transparent transition backdrop-blur-sm" onchange="updatePeriodFields()">
                            <option value="" style="color: #1f2937;">Toutes les périodes</option>
                            <option value="day" {{ request('period') === 'day' ? 'selected' : '' }} style="color: #1f2937;">Jour</option>
                            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }} style="color: #1f2937;">Semaine</option>
                            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }} style="color: #1f2937;">Mois</option>
                            <option value="year" {{ request('period') === 'year' ? 'selected' : '' }} style="color: #1f2937;">Année</option>
                            <option value="range" {{ request('period') === 'range' ? 'selected' : '' }} style="color: #1f2937;">Plage personnalisée</option>
                        </select>
                    </div>

                    <!-- Date -->
                    <div id="dateField" style="display: {{ request('period') === 'day' ? 'block' : 'none' }};">
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-100 mb-2">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="w-full px-4 py-2.5 border border-slate-300 dark:border-white/20 rounded-lg bg-white dark:bg-white/10 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-purple-400 focus:border-transparent transition backdrop-blur-sm">
                    </div>

                    <!-- Type de paiement -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-100 mb-2">Type de Paiement</label>
                        <select name="type" class="w-full px-4 py-2.5 border border-slate-300 dark:border-white/20 rounded-lg bg-white dark:bg-white/10 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-purple-400 focus:border-transparent transition backdrop-blur-sm">
                            <option value="" style="color: #1f2937;">Tous les types</option>
                            @foreach($typesModes as $typeMode)
                            <option value="{{ $typeMode }}" {{ request('type') === $typeMode ? 'selected' : '' }} style="color: #1f2937;">{{ ucfirst($typeMode) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Source de paiement -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-gray-100 mb-2">Source</label>
                        <select name="source" class="w-full px-4 py-2.5 border border-slate-300 dark:border-white/20 rounded-lg bg-white dark:bg-white/10 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-purple-400 focus:border-transparent transition backdrop-blur-sm">
                            <option value="" style="color: #1f2937;">Toutes les sources</option>
                            <option value="facture" {{ request('source') === 'facture' ? 'selected' : '' }} style="color: #1f2937;">Factures</option>
                            <option value="depense" {{ request('source') === 'depense' ? 'selected' : '' }} style="color: #1f2937;">Dépenses</option>
                            <option value="part_medecin" {{ request('source') === 'part_medecin' ? 'selected' : '' }} style="color: #1f2937;">Part Médecin</option>
                            <option value="credit_assurance" {{ request('source') === 'credit_assurance' ? 'selected' : '' }} style="color: #1f2937;">Crédit Assurance</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 dark:from-purple-500 dark:to-pink-500 hover:from-blue-600 hover:to-blue-700 dark:hover:from-purple-600 dark:hover:to-pink-600 text-white font-semibold rounded-lg transition shadow-lg hover:shadow-blue-500/50 dark:hover:shadow-purple-500/50">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('modepaiements.index') }}" class="inline-flex items-center px-6 py-2.5 bg-slate-200 dark:bg-white/10 hover:bg-slate-300 dark:hover:bg-white/20 text-slate-900 dark:text-white font-semibold rounded-lg transition border border-slate-300 dark:border-white/20 backdrop-blur-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Réinitialiser
                    </a>
                </div>
            </div>
        </form>

        <!-- Cartes de résumé statistique -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
            <!-- Total des paiements -->
            <div class="group bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-500/20 dark:to-cyan-500/20 backdrop-blur-xl rounded-2xl shadow-lg dark:shadow-xl p-6 text-slate-900 dark:text-white overflow-hidden relative border border-blue-200 dark:border-blue-300/30 hover:border-blue-300 dark:hover:border-blue-300/50 transition">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-200/10 dark:from-blue-500/10 to-cyan-200/10 dark:to-cyan-500/10 group-hover:from-blue-300/20 dark:group-hover:from-blue-500/20 group-hover:to-cyan-300/20 dark:group-hover:to-cyan-500/20 transition duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold opacity-90">Total Paiements</h3>
                        <svg class="w-6 h-6 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-4xl font-black text-blue-700 dark:text-blue-100 drop-shadow">{{ number_format($paiements->sum('montant'), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-2">MRU</p>
                </div>
            </div>

            <!-- Paiements en espèces -->
            <div class="group bg-gradient-to-br from-emerald-100 to-teal-100 dark:from-emerald-500/20 dark:to-teal-500/20 backdrop-blur-xl rounded-2xl shadow-lg dark:shadow-xl p-6 text-slate-900 dark:text-white overflow-hidden relative border border-emerald-200 dark:border-emerald-300/30 hover:border-emerald-300 dark:hover:border-emerald-300/50 transition">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-200/10 dark:from-emerald-500/10 to-teal-200/10 dark:to-teal-500/10 group-hover:from-emerald-300/20 dark:group-hover:from-emerald-500/20 group-hover:to-teal-300/20 dark:group-hover:to-teal-500/20 transition duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold opacity-90">Espèces</h3>
                        <svg class="w-6 h-6 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.16 2.75a.75.75 0 00-.743.882l.534 3.201H5.75a.75.75 0 000 1.5h2.122l.66 3.966H6.75a.75.75 0 000 1.5h2.211l.534 3.2a.75.75 0 001.485-.247l-.61-3.653h3.958l.534 3.2a.75.75 0 001.485-.247l-.61-3.653H17.75a.75.75 0 000-1.5h-2.211l-.66-3.966H17.25a.75.75 0 000-1.5h-2.122l-.534-3.2a.75.75 0 00-1.485.247l.61 3.653H9.25l-.534-3.2a.75.75 0 00-.743-.882zM12.332 11.75l.66-3.966H9.034l-.66 3.966h3.958z"/>
                        </svg>
                    </div>
                    <p class="text-4xl font-black text-emerald-700 dark:text-emerald-100 drop-shadow">{{ number_format($paiements->where('type', 'espèces')->sum('montant'), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-2">MRU</p>
                </div>
            </div>

            <!-- Paiements numériques -->
            <div class="group bg-gradient-to-br from-violet-100 to-purple-100 dark:from-violet-500/20 dark:to-purple-500/20 backdrop-blur-xl rounded-2xl shadow-lg dark:shadow-xl p-6 text-slate-900 dark:text-white overflow-hidden relative border border-violet-200 dark:border-violet-300/30 hover:border-violet-300 dark:hover:border-violet-300/50 transition">
                <div class="absolute inset-0 bg-gradient-to-br from-violet-200/10 dark:from-violet-500/10 to-purple-200/10 dark:to-purple-500/10 group-hover:from-violet-300/20 dark:group-hover:from-violet-500/20 group-hover:to-purple-300/20 dark:group-hover:to-purple-500/20 transition duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold opacity-90">Numériques</h3>
                        <svg class="w-6 h-6 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3.25 3A2.25 2.25 0 001 5.25v9.5A2.25 2.25 0 003.25 17h13.5A2.25 2.25 0 0019 14.75v-9.5A2.25 2.25 0 0016.75 3H3.25zm0 1.5h13.5a.75.75 0 01.75.75v2h-15v-2a.75.75 0 01.75-.75zm0 12h13.5a.75.75 0 01-.75.75H3.25a.75.75 0 01-.75-.75zm.75-4.5h13.5v3a.75.75 0 01-.75.75H3.25a.75.75 0 01-.75-.75v-3z"/>
                        </svg>
                    </div>
                    <p class="text-4xl font-black text-violet-700 dark:text-violet-100 drop-shadow">{{ number_format($paiements->whereIn('type', ['bankily', 'masrivi', 'sedad'])->sum('montant'), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-2">MRU</p>
                </div>
            </div>

            <!-- Part Médecin -->
            <div class="group bg-gradient-to-br from-rose-100 to-pink-100 dark:from-rose-500/20 dark:to-pink-500/20 backdrop-blur-xl rounded-2xl shadow-lg dark:shadow-xl p-6 text-slate-900 dark:text-white overflow-hidden relative border border-rose-200 dark:border-rose-300/30 hover:border-rose-300 dark:hover:border-rose-300/50 transition">
                <div class="absolute inset-0 bg-gradient-to-br from-rose-200/10 dark:from-rose-500/10 to-pink-200/10 dark:to-pink-500/10 group-hover:from-rose-300/20 dark:group-hover:from-rose-500/20 group-hover:to-pink-300/20 dark:group-hover:to-pink-500/20 transition duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold opacity-90">Part Médecin</h3>
                        <svg class="w-6 h-6 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-4xl font-black text-rose-700 dark:text-rose-100 drop-shadow">{{ number_format(\App\Models\EtatCaisse::where('validated', true)->sum('part_medecin'), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-2">MRU</p>
                </div>
            </div>

            <!-- Dépenses -->
            <div class="group bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-500/20 dark:to-orange-500/20 backdrop-blur-xl rounded-2xl shadow-lg dark:shadow-xl p-6 text-slate-900 dark:text-white overflow-hidden relative border border-amber-200 dark:border-amber-300/30 hover:border-amber-300 dark:hover:border-amber-300/50 transition">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-200/10 dark:from-amber-500/10 to-orange-200/10 dark:to-orange-500/10 group-hover:from-amber-300/20 dark:group-hover:from-amber-500/20 group-hover:to-orange-300/20 dark:group-hover:to-orange-500/20 transition duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold opacity-90">Dépenses</h3>
                        <svg class="w-6 h-6 opacity-60" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM15.657 14.243a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM11 17a1 1 0 102 0v-1a1 1 0 10-2 0v1zM5.757 15.657a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414l-.707.707zM4 10a1 1 0 01-1-1V8a1 1 0 112 0v1a1 1 0 01-1 1zM4.343 5.757a1 1 0 010-1.414l.707-.707a1 1 0 11-1.414 1.414l-.707-.707zM10 5a1 1 0 011 1v6a1 1 0 11-2 0V6a1 1 0 011-1z"/>
                        </svg>
                    </div>
                    <p class="text-4xl font-black text-amber-700 dark:text-amber-100 drop-shadow">{{ number_format(abs($paiements->where('source', 'depense')->sum('montant')), 0, ',', ' ') }}</p>
                    <p class="text-xs opacity-75 mt-2">MRU</p>
                </div>
            </div>
        </div>

        <!-- Tableau des paiements -->
        <div class="bg-white/40 dark:bg-white/5 backdrop-blur-lg rounded-2xl shadow-lg dark:shadow-2xl border border-slate-200 dark:border-white/20 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-100 dark:bg-white/5 border-b border-slate-200 dark:border-white/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-gray-200">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-gray-200">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-gray-200">Source</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-slate-900 dark:text-gray-200">Montant</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-gray-200">Facture Caisse</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-900 dark:text-gray-200">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        @forelse($paiements as $paiement)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition">
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-gray-300 font-medium">{{ $paiement->id }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $paiement->type === 'espèces' ? 'bg-emerald-100 dark:bg-emerald-500/30 text-emerald-800 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-400/50' : '' }}
                                    {{ in_array($paiement->type, ['bankily', 'masrivi', 'sedad']) ? 'bg-violet-100 dark:bg-violet-500/30 text-violet-800 dark:text-violet-200 border border-violet-300 dark:border-violet-400/50' : '' }}
                                ">
                                    {{ ucfirst($paiement->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $paiement->source === 'part_medecin' ? 'bg-rose-100 dark:bg-rose-500/30 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-400/50' : '' }}
                                    {{ $paiement->source === 'depense' ? 'bg-amber-100 dark:bg-amber-500/30 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-400/50' : '' }}
                                    {{ $paiement->source === 'facture' ? 'bg-blue-100 dark:bg-blue-500/30 text-blue-800 dark:text-blue-200 border border-blue-300 dark:border-blue-400/50' : '' }}
                                    {{ $paiement->source === 'credit_assurance' ? 'bg-indigo-100 dark:bg-indigo-500/30 text-indigo-800 dark:text-indigo-200 border border-indigo-300 dark:border-indigo-400/50' : '' }}
                                ">
                                    @if($paiement->source === 'part_medecin')
                                        💰 Part Versée
                                    @elseif($paiement->source === 'depense')
                                        📊 Dépense
                                    @elseif($paiement->source === 'facture')
                                        📋 Facture
                                    @elseif($paiement->source === 'credit_assurance')
                                        🏥 Crédit Assurance
                                    @else
                                        {{ ucfirst($paiement->source ?? 'N/A') }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-slate-900 dark:text-white">
                                {{ number_format($paiement->montant, 0, ',', ' ') }} MRU
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-700 dark:text-gray-300">
                                @if($paiement->source === 'part_medecin')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 dark:bg-rose-500/30 text-rose-800 dark:text-rose-200 border border-rose-300 dark:border-rose-400/50">
                                        💰 Part Versée
                                    </span>
                                @elseif($paiement->source === 'depense')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-500/30 text-amber-800 dark:text-amber-200 border border-amber-300 dark:border-amber-400/50">
                                        📊 Dépense
                                    </span>
                                @elseif($paiement->source === 'credit_assurance')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-500/30 text-indigo-800 dark:text-indigo-200 border border-indigo-300 dark:border-indigo-400/50">
                                        🏥 Crédit Assurance
                                    </span>
                                @elseif($paiement->caisse_id)
                                    <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.caisses.show', $paiement->caisse_id) : route('caisses.show', $paiement->caisse_id) }}" class="text-blue-600 dark:text-blue-300 hover:text-blue-700 dark:hover:text-blue-200 font-semibold transition">
                                        Facture #{{ $paiement->caisse_id }}
                                    </a>
                                @else
                                    <span class="text-slate-400 dark:text-gray-500">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-gray-400">
                                {{ $paiement->created_at->translatedFormat('d M Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xl font-semibold text-slate-700 dark:text-gray-300">Aucun paiement trouvé</p>
                                    <p class="text-sm text-slate-500 dark:text-gray-500 mt-1">Ajustez vos filtres pour voir plus de résultats</p>
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
    
    if (dateField) dateField.style.display = 'none';
    if (period === 'day' && dateField) {
        dateField.style.display = 'block';
    }
}

updatePeriodFields();

document.querySelectorAll('select[name="type"], select[name="source"]').forEach(select => {
    select.addEventListener('change', () => {
        document.getElementById('filterForm').submit();
    });
});
</script>
@endsection
