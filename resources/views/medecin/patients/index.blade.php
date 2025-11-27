@extends('layouts.app')
@section('title', 'Mes Patients')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-users text-yellow-600 mr-2"></i>Mes Patients
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Liste des patients que vous avez consultés
                </p>
            </div>
            <a href="{{ route('medecin.dashboard') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Retour au Dashboard
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow">
            <div class="flex items-center">
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full mr-3">
                    <i class="fas fa-users text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Patients</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $patients->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow">
            <div class="flex items-center">
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full mr-3">
                    <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Consultations</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $patients->sum('consultations_count') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow">
            <div class="flex items-center">
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full mr-3">
                    <i class="fas fa-user-check text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Patients Actifs</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $patients->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des patients -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Patient
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Dernière Consultation
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Nb Consultations
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm shadow">
                                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($patient->date_naissance)
                                            {{ \Carbon\Carbon::parse($patient->date_naissance)->age }} ans
                                        @else
                                            Âge non renseigné
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                <i class="fas fa-phone text-gray-400 mr-1"></i>
                                {{ $patient->phone ?? 'N/A' }}
                            </div>
                            @if($patient->email)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                {{ $patient->email }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($patient->consultations->count() > 0)
                                @php
                                    $lastConsultation = $patient->consultations->first();
                                @endphp
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $lastConsultation->date_consultation->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $lastConsultation->date_consultation->diffForHumans() }}
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Aucune consultation</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <i class="fas fa-clipboard-list mr-1"></i>
                                {{ $patient->consultations_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <!-- Voir le dossier patient -->
                            <a href="{{ route('medecin.patients.show', $patient->id) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-eye mr-1"></i>
                                Voir
                            </a>
                            
                            <!-- Nouvelle consultation pour ce patient -->
                            <a href="{{ route('medecin.consultations.create', ['patient_id' => $patient->id]) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus mr-1"></i>
                                Consulter
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-users text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    Aucun patient trouvé
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    Vous n'avez pas encore consulté de patients.
                                </p>
                                <a href="{{ route('medecin.consultations.create') }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-plus mr-2"></i>Créer une consultation
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($patients->hasPages())
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

