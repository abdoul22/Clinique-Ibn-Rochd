@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- En-tête moderne -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Liste des Assurances</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gérez les assurances et leurs crédits
                </p>
            </div>

            <div class="flex flex-col md:flex-row md:items-center gap-3">
                @php
                $user = auth()->user();
                $role = $user->role?->name ?? 'guest';
                
                // Construction directe de l'URL selon le rôle
                if ($role === 'superadmin') {
                    $createUrl = url('/superadmin/assurances/create');
                    $pdfUrl = url('/superadmin/assurances/export/pdf');
                } elseif ($role === 'admin') {
                    $createUrl = url('/admin/assurances/create');
                    $pdfUrl = url('/admin/assurances/export/pdf');
                } else {
                    $createUrl = url('/assurances/create');
                    $pdfUrl = url('/assurances/export/pdf');
                }
                @endphp

                <!-- Bouton Ajouter -->
                <a href="{{ $createUrl }}"
                    class="bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter une assurance
                </a>

                <!-- Bouton PDF -->
                <a href="{{ $pdfUrl }}"
                    class="bg-red-500 hover:bg-red-600 dark:bg-red-600 dark:hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center transition-all duration-200 shadow-lg hover:shadow-xl">
                    Télécharger PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-blue-500 dark:bg-blue-600 text-white shadow-lg rounded-xl p-6 transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Assurances</p>
                    <p class="text-3xl font-bold mt-2">{{ $assurances->total() }}</p>
                </div>
                <div class="bg-blue-600 dark:bg-blue-700 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-emerald-500 dark:bg-emerald-600 text-white shadow-lg rounded-xl p-6 transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium">Crédit Total</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($assurances->sum('credit'), 0, ',', ' ') }}</p>
                    <p class="text-emerald-100 text-xs mt-1">MRU</p>
                </div>
                <div class="bg-emerald-600 dark:bg-emerald-700 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-purple-500 dark:bg-purple-600 text-white shadow-lg rounded-xl p-6 transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Crédit Moyen</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($assurances->avg('credit'), 0, ',', ' ') }}</p>
                    <p class="text-purple-100 text-xs mt-1">MRU</p>
                </div>
                <div class="bg-purple-600 dark:bg-purple-700 rounded-full p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid responsive de cartes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($assurances as $assurance)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-indigo-200 dark:border-indigo-800/50 hover:border-indigo-400 dark:hover:border-indigo-600">
            <!-- En-tête de la carte avec ID -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 dark:from-indigo-700 dark:to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-bold text-white uppercase tracking-wider">
                        Assurance #{{ $assurance->id }}
                    </span>
                </div>
            </div>

            <!-- Contenu de la carte -->
            <div class="p-6 bg-gray-50 dark:bg-gray-800">
                <!-- Nom de l'assurance -->
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-5">
                    {{ $assurance->nom }}
                </h3>

                <!-- Badge de crédit disponible -->
                <div class="mb-4">
                    <div class="flex items-center space-x-2 mb-3">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-base font-semibold text-gray-700 dark:text-gray-200">
                            Crédit disponible
                        </span>
                    </div>
                    <div>
                        <span class="px-5 py-2.5 inline-flex text-xl font-extrabold rounded-lg shadow-md bg-gradient-to-r from-emerald-500 to-emerald-600 text-white">
                            {{ $assurance->credit_format }} MRU
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 mt-2 border-t-2 border-gray-300 dark:border-gray-600">
                    <div class="flex space-x-3">
                        <!-- Voir les détails -->
                        <a href="{{ route(auth()->user()->role->name . '.assurances.show', $assurance->id) }}"
                            class="p-2.5 text-indigo-600 hover:text-white hover:bg-indigo-600 dark:text-indigo-400 dark:hover:text-white dark:hover:bg-indigo-600 rounded-lg transition-all duration-200 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800 shadow-sm hover:shadow-md"
                            title="Voir les détails">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route(auth()->user()->role->name . '.assurances.edit', $assurance->id) }}"
                            class="p-2.5 text-amber-600 hover:text-white hover:bg-amber-600 dark:text-amber-400 dark:hover:text-white dark:hover:bg-amber-600 rounded-lg transition-all duration-200 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 shadow-sm hover:shadow-md"
                            title="Modifier">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route(auth()->user()->role->name . '.assurances.destroy', $assurance->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette assurance ?')"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2.5 text-rose-600 hover:text-white hover:bg-rose-600 dark:text-rose-400 dark:hover:text-white dark:hover:bg-rose-600 rounded-lg transition-all duration-200 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 shadow-sm hover:shadow-md"
                                title="Supprimer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune assurance trouvée</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Commencez par ajouter votre première assurance</p>
            <a href="{{ $createUrl }}"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Ajouter la première assurance
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($assurances->hasPages())
    <div class="mt-6">
        <div class="flex justify-center">
            {{ $assurances->onEachSide(1)->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
