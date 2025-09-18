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
                <a href="{{ route('recap-operateurs.index') }}"
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

    <!-- Liste des médecins -->
    <div class="space-y-6">
        @forelse($allDoctors as $doctor)
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- En-tête du médecin -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                Dr. {{ $doctor['medecin']->nom }} {{ $doctor['medecin']->prenom ?? '' }}
                            </h3>
                            <p class="text-indigo-100">
                                {{ $doctor['role'] }} - {{ $doctor['medecin']->fonction ?? 'Médecin' }}
                            </p>
                            @if(isset($doctor['hospitalisations']) && count($doctor['hospitalisations']) > 1)
                            <p class="text-indigo-100 text-sm">
                                Impliqué dans {{ count($doctor['hospitalisations']) }} hospitalisations
                            </p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-indigo-100 text-sm">Part Médecin Total</p>
                        <p class="text-2xl font-bold text-white">{{ number_format($doctor['part_medecin'], 0, ',', ' ')
                            }} MRU</p>
                    </div>
                </div>
            </div>

            <!-- Détails des examens -->
            <div class="p-6">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Examens effectués</h4>
                <div class="space-y-3">
                    @forelse($doctor['examens'] as $examen)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $examen['nom'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($examen['date'])->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-600 dark:text-green-400">
                                {{ number_format($examen['part_medecin'], 0, ',', ' ') }} MRU
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucun examen trouvé</p>
                    @endforelse
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun médecin trouvé</h3>
            <p class="text-gray-500 dark:text-gray-400">Aucun médecin n'a été impliqué dans les hospitalisations de
                cette date.</p>
        </div>
        @endforelse
    </div>

    <!-- Informations sur les hospitalisations -->
    @if($hospitalisations->count() > 0)
    <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hospitalisations du {{
            \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($hospitalisations as $hospitalisation)
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900 dark:text-white">
                        {{ $hospitalisation->patient->nom ?? 'N/A' }} {{ $hospitalisation->patient->prenom ?? '' }}
                    </h4>
                    <span
                        class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded">
                        #{{ $hospitalisation->id }}
                    </span>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                    <p><strong>Service:</strong> {{ $hospitalisation->service->nom ?? '—' }}</p>
                    <p><strong>Chambre:</strong> {{ $hospitalisation->chambre->numero ?? '—' }}</p>
                    <p><strong>Médecin traitant:</strong> {{ $hospitalisation->medecin->nom_complet_avec_prenom ?? '—'
                        }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

