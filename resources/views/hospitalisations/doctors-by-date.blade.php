@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Détails des Médecins - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Récapitulatif de tous les médecins impliqués dans les hospitalisations du {{
                    \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                @php
                $role = auth()->user()->role->name;
                $recapRoute = ($role === 'superadmin' || $role === 'admin') ? $role . '.recap-operateurs.index' :
                'recap-operateurs.index';
                @endphp
                <a href="{{ route($recapRoute) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour au récapitulatif
                </a>
            </div>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Hospitalisations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $hospitalisations->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Médecins impliqués</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $allDoctors->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="bg-emerald-100 dark:bg-emerald-900/30 p-3 rounded-full">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Recettes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalRecettes, 0,
                        ',', ' ') }} MRU</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Parts Médecins</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalPartMedecin, 0,
                        ',', ' ') }} MRU</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des médecins en Grid Responsive -->
    @if($allDoctors->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($allDoctors as $doctor)
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
            <!-- En-tête du médecin -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-4">
                <div class="flex flex-col space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-full flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-white truncate">
                                Dr. {{ $doctor['medecin']->nom }} {{ $doctor['medecin']->prenom ?? '' }}
                            </h3>
                            <p class="text-xs text-indigo-100 truncate">
                                {{ $doctor['role'] }} - {{ $doctor['medecin']->fonction ?? 'Médecin' }}
                            </p>
                        </div>
                    </div>
                    @if(isset($doctor['hospitalisations']) && count($doctor['hospitalisations']) > 1)
                    <div class="bg-white/10 rounded-lg px-3 py-1.5">
                        <p class="text-indigo-100 text-xs text-center">
                            Impliqué dans {{ count($doctor['hospitalisations']) }} hospitalisations
                        </p>
                    </div>
                    @endif
                    <div class="bg-white/10 rounded-lg px-3 py-2">
                        <p class="text-indigo-100 text-xs text-center">Part Médecin Total</p>
                        <p class="text-xl font-bold text-white text-center">{{ number_format($doctor['part_medecin'], 0, ',', ' ') }} MRU</p>
                    </div>
                </div>
            </div>

            <!-- Détails des examens -->
            <div class="p-4 flex-1 flex flex-col">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Examens effectués</h4>
                <div class="space-y-2 flex-1">
                    @forelse($doctor['examens'] as $examen)
                    <div class="p-2.5 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $examen['nom'] }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($examen['date'])->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-semibold text-sm text-green-600 dark:text-green-400">
                                    {{ number_format($examen['part_medecin'], 0, ',', ' ') }} MRU
                                </p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center text-sm py-4">Aucun examen</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun médecin trouvé</h3>
        <p class="text-gray-500 dark:text-gray-400">Aucun médecin n'a été impliqué dans les hospitalisations de
            cette date.</p>
    </div>
    @endif

    <!-- Informations sur les hospitalisations -->
    @if($hospitalisations->count() > 0)
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hospitalisations du {{
            \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($hospitalisations as $hospitalisation)
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900 dark:text-white">
                        {{ $hospitalisation->patient->nom ?? 'N/A' }} {{ $hospitalisation->patient->prenom ?? '' }}
                    </h4>
                    <a href="{{ route(auth()->user()->role->name === 'admin' ? 'admin.hospitalisations.show' : 'hospitalisations.show', $hospitalisation->id) }}"
                        class="text-xs bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-800 dark:text-blue-300 px-2 py-1 rounded transition-colors duration-200"
                        title="Voir la facture">
                        #{{ $hospitalisation->id }}
                    </a>
                </div>

                <!-- Statut -->
                <div class="mb-3">
                    @if($hospitalisation->statut === 'en cours')
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></div>
                        En Cours
                    </span>
                    @elseif($hospitalisation->statut === 'terminé')
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Terminé
                    </span>
                    @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Annulé
                    </span>
                    @endif
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p><strong>Service:</strong> {{ $hospitalisation->service->nom ?? '—' }}</p>
                    <p><strong>Chambre:</strong>
                        @if($hospitalisation->lit_id)
                        @php
                        $lit = \App\Models\Lit::with('chambre')->find($hospitalisation->lit_id);
                        @endphp
                        @if($lit && $lit->chambre)
                        {{ $lit->chambre->nom ?? $lit->chambre->numero ?? '—' }}
                        @else
                        —
                        @endif
                        @else
                        —
                        @endif
                    </p>
                    <p><strong>Médecin{{ $hospitalisation->getAllInvolvedDoctors()->count() > 1 ? 's' : '' }}:</strong>
                        @php
                            $medecinsImpliques = $hospitalisation->getAllInvolvedDoctors();
                            $nombreMedecins = $medecinsImpliques->count();
                        @endphp
                        @if($nombreMedecins === 0)
                            <span class="text-gray-500 dark:text-gray-400">—</span>
                        @elseif($nombreMedecins === 1)
                            <span class="font-medium text-blue-600 dark:text-blue-400">
                                {{ $medecinsImpliques->first()['medecin']->nom }} {{ $medecinsImpliques->first()['medecin']->prenom ?? '' }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                </svg>
                                {{ $nombreMedecins }} médecins
                            </span>
                        @endif
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
