@extends('layouts.app')
@section('title', 'Détails du Rendez-vous')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-info-circle text-rose-600 mr-2"></i>Détails du Rendez-vous
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Consultation du rendez-vous #{{ $rendezVous->numero_entree }}
            </p>
        </div>
        <a href="{{ route('medecin.rendezvous.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                    Informations du Rendez-vous
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Date du RDV</p>
                        <p class="text-lg text-gray-900 dark:text-white font-bold mt-1">
                            <i class="fas fa-calendar-day text-blue-500 mr-2"></i>{{ $rendezVous->date_rdv->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Statut</p>
                        <div class="mt-1">
                            @if($rendezVous->statut === 'annule')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <i class="fas fa-times-circle mr-2 mt-1"></i>Annulé
                                </span>
                            @elseif($rendezVous->isPaid())
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i class="fas fa-check-double mr-2 mt-1"></i>Payé / Terminé
                                </span>
                            @elseif($rendezVous->isExpired())
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-300">
                                    <i class="fas fa-clock mr-2 mt-1"></i>Expiré
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-2 mt-1"></i>Confirmé
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Motif de consultation</p>
                    <div class="mt-2 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700">
                        <p class="text-gray-900 dark:text-white leading-relaxed italic">
                            "{{ $rendezVous->motif ?? 'Pas de motif spécifié' }}"
                        </p>
                    </div>
                </div>

                @if($rendezVous->notes)
                <div class="mt-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Notes administratives</p>
                    <div class="mt-2 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-100 dark:border-yellow-900/30">
                        <p class="text-gray-800 dark:text-yellow-200 leading-relaxed">
                            {{ $rendezVous->notes }}
                        </p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Boutons d'action rapide -->
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('medecin.consultations.create', ['patient_id' => $rendezVous->patient_id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg transform hover:scale-105">
                    <i class="fas fa-plus-circle mr-2"></i>Démarrer la consultation
                </a>
                <a href="{{ route('medecin.ordonnances.create', ['patient_id' => $rendezVous->patient_id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition shadow-lg transform hover:scale-105">
                    <i class="fas fa-prescription mr-2"></i>Nouvelle Ordonnance
                </a>
            </div>
        </div>

        <!-- Sidebar Patient -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                    Le Patient
                </h2>
                
                <div class="flex items-center mb-6">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-2xl font-bold shadow-md">
                        {{ substr($rendezVous->patient->first_name, 0, 1) }}{{ substr($rendezVous->patient->last_name, 0, 1) }}
                    </div>
                    <div class="ml-4">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $rendezVous->patient->first_name }} {{ $rendezVous->patient->last_name }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            #{{ $rendezVous->patient->id }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <i class="fas fa-phone w-8 text-gray-400"></i>
                        <span class="text-gray-900 dark:text-white">{{ $rendezVous->patient->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-birthday-cake w-8 text-gray-400"></i>
                        <span class="text-gray-900 dark:text-white">
                            {{ $rendezVous->patient->age ?? '?' }} ans 
                            @if($rendezVous->patient->gender)
                                <span class="ml-1 text-gray-500">({{ $rendezVous->patient->gender }})</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt w-8 text-gray-400 mt-1"></i>
                        <span class="text-gray-900 dark:text-white leading-tight">
                            {{ $rendezVous->patient->address ?? 'Pas d\'adresse' }}
                        </span>
                    </div>
                </div>

                <div class="mt-8">
                    @if(isset($dossierId))
                    <a href="{{ route('dossiers.show', $dossierId) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition font-semibold">
                        <i class="fas fa-folder-open mr-2"></i>Dossier Médical
                    </a>
                    @else
                    <a href="{{ route('medecin.patients.show', $rendezVous->patient->id) }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition font-semibold">
                        <i class="fas fa-user-circle mr-2"></i>Fiche Patient
                    </a>
                    @endif
                </div>
            </div>

            <!-- Historique rapide -->
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-4">Dernière visite</h3>
                @php
                    $lastCaisse = $rendezVous->patient->caisses()
                        ->where('medecin_id', $medecin->id)
                        ->where('id', '!=', $rendezVous->id)
                        ->latest()
                        ->first();
                @endphp
                
                @if($lastCaisse)
                    <p class="text-gray-900 dark:text-white font-medium">{{ $lastCaisse->created_at->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $lastCaisse->examen->nom ?? 'Consultation' }}</p>
                @else
                    <p class="text-gray-500 italic text-sm">Première visite avec vous</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

