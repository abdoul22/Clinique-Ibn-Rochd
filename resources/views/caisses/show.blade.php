@extends('layouts.app')

@section('content')
<!-- Actions - juste sous la navbar -->
<div class="max-w-4xl mx-auto flex flex-wrap justify-center gap-4 mb-6 print:hidden">
    <a href="{{ route(auth()->user()->role->name . '.caisses.edit', $caisse) }}"
        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Modifier
    </a>
    @php
        $hospitalisation = \App\Models\HospitalisationCharge::where('caisse_id', $caisse->id)
            ->with('hospitalisation')
            ->first()?->hospitalisation;
        $printRoute = $hospitalisation 
            ? route(auth()->user()->role->name === 'admin' ? 'admin.hospitalisations.print' : 'hospitalisations.print', $hospitalisation->id)
            : route(auth()->user()->role->name . '.caisses.printSingle', $caisse->id);
    @endphp
    <a href="{{ $printRoute }}"
        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
        </svg>
        Imprimer
    </a>
    <a href="{{ route(auth()->user()->role->name . '.caisses.index') }}"
        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
        Liste des examens
    </a>
</div>

<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
    <!-- En-tête bilingue -->
    <div class="p-6 bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-900 dark:to-blue-950 text-white">
        <div class="flex items-start justify-between text-sm mb-4">
            <!-- Section française -->
            <div class="text-left flex-1">
                <div class="font-semibold">{{ config('clinique.name') }}</div>
                <div>{{ config('clinique.director_name') }}</div>
                <div>{{ config('clinique.director_specialty') }}</div>
                <div class="text-xs text-blue-200 mt-1">{{ config('clinique.center_type') }}</div>
                <div class="text-xs text-blue-200">{{ config('clinique.services_description') }}</div>
            </div>

            <!-- Logo centré -->
            <div class="flex-shrink-0 mx-6">
                <img src="{{ asset(config('clinique.logo_path')) }}" alt="Logo {{ config('clinique.name') }}" class="h-16 w-auto">
            </div>

            <!-- Section arabe -->
            <div class="text-right flex-1" style="direction: rtl;">
                <div class="font-semibold">{{ config('clinique.name_ar') }}</div>
                <div>{{ config('clinique.director_name_ar') }}</div>
                <div>{{ config('clinique.director_specialty_ar') }}</div>
                <div class="text-xs text-blue-200 mt-1">{{ config('clinique.center_type_ar') }}</div>
                <div class="text-xs text-blue-200">{{ config('clinique.services_description_ar') }}</div>
            </div>
        </div>

        <div class="text-center mt-4">
            <div class="text-xs text-blue-200">
                {{ config('clinique.phone') }} <br>
                {{ config('clinique.address') }}
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-sm">Reçu d'examen médical</p>
            <p class="text-xs opacity-80 mt-2">Date de création: {{ $caisse->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Corps du document -->
    <div class="p-6">
        <div class="flex flex-col md:flex-row justify-between items-start mb-8 gap-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Facture N° {{ $caisse->numero_facture }}
                </h2>
                <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Informations de l'examen</h2>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">N° d'entrée:</span> {{
                    $caisse->numero_entre }}</p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Date de l'examen:</span> {{
                    $caisse->date_examen ? $caisse->date_examen->format('d/m/Y') : 'N/A' }}</p>
                @if($caisse->examens_data)
                <div>
                    <p class="font-medium text-gray-800 dark:text-gray-200">Examens effectués :</p>
                    @php
                    // Décoder le JSON si c'est une chaîne
                    $examensData = is_string($caisse->examens_data) 
                        ? json_decode($caisse->examens_data, true) 
                        : $caisse->examens_data;
                    @endphp
                    @if(is_array($examensData) && count($examensData) > 0)
                    @foreach($examensData as $examenData)
                    @php
                    $examen = \App\Models\Examen::find($examenData['id']);
                    $tarifEffectif = $examen ? $examen->getTarifPourAssurance($caisse->assurance_id) : 0;
                    @endphp
                    @if($examen)
                    <p class="ml-4 text-gray-800 dark:text-gray-200">- {{ $examen->nom }} ({{ $examenData['quantite']
                        }}x) : {{ number_format($tarifEffectif * $examenData['quantite'], 2) }} MRU</p>
                    @endif
                    @endforeach
                    @endif
                </div>
                @else
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Type d'examen:</span> {{
                    $caisse->examen->nom ?? 'N/A' }}</p>
                @endif
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Service:</span>
                    @php
                    $servicesList = [];
                    
                    // Si la facture a plusieurs examens (examens_data)
                    if ($caisse->examens_data) {
                        // Décoder le JSON si c'est une chaîne
                        $examensData = is_string($caisse->examens_data) 
                            ? json_decode($caisse->examens_data, true) 
                            : $caisse->examens_data;
                        if (is_array($examensData)) {
                            foreach ($examensData as $examenData) {
                            $examen = \App\Models\Examen::find($examenData['id']);
                            if ($examen && $examen->service) {
                                $service = $examen->service;
                                // Déterminer le nom du service
                                if ($service->type_service === 'PHARMACIE' || strtoupper($service->nom ?? '') === 'PHARMACIE' || $service->pharmacie_id !== null) {
                                    $serviceName = 'PHARMACIE';
                                } else {
                                    $serviceName = $service->nom;
                                }
                                // Ajouter le service à la liste s'il n'est pas déjà présent
                                if (!in_array($serviceName, $servicesList)) {
                                    $servicesList[] = $serviceName;
                                }
                            }
                        }
                        }
                    } else {
                        // Facture avec un seul examen (ancien format)
                        $svc = $caisse->service;
                        
                        // Si pas de service direct, essayer via l'examen
                        if (!$svc && $caisse->examen) {
                            $svc = $caisse->examen->service;
                        }
                        
                        if ($svc) {
                            $serviceLabel = $svc && $svc->type_service === 'PHARMACIE' ? 'PHARMACIE' : ($svc->nom ?? 'N/A');
                            $servicesList[] = $serviceLabel;
                        } else {
                            $servicesList[] = 'N/A';
                        }
                    }
                    
                    // Afficher les services séparés par " - "
                    $serviceLabel = !empty($servicesList) ? implode(' - ', $servicesList) : 'N/A';
                    @endphp
                    {{ $serviceLabel }}
                </p>
            </div>

            <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg">
                <p class="text-lg font-bold text-center text-gray-900 dark:text-white">Total</p>
                @if($caisse->assurance && $caisse->couverture > 0)
                @php
                    // Recalculer le total à partir de la couverture pour éviter les problèmes
                    // avec les caisses créées avant la correction
                    $totalBrut = $caisse->total;
                    // Si le total semble être le montant brut (vérifier avec l'état de caisse)
                    // Utiliser la recette de l'état de caisse qui est toujours correcte
                    $totalNet = $caisse->etatCaisse ? $caisse->etatCaisse->recette : ($totalBrut * (1 - ($caisse->couverture / 100)));
                @endphp
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 text-center">{{
                    number_format($totalNet, 2) }} MRU</p>
                <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-1">
                    ({{ $caisse->couverture }}% pris en charge par {{ $caisse->assurance->nom }})
                </p>
                @else
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 text-center">{{
                    number_format($caisse->total, 2) }} MRU</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Informations patient -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3 border-b pb-2 text-gray-900 dark:text-white">Informations patient
                </h3>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Nom:</span> {{
                    $caisse->patient->first_name ?? 'N/A' }} {{ $caisse->patient->last_name ?? '' }}</p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Âge:</span> {{
                    $caisse->patient->age ? $caisse->patient->age . ' ans' : 'N/A' }}
                    @if($caisse->patient->date_of_birth)
                    <span class="text-xs text-gray-500">(né le {{ $caisse->patient->date_of_birth->format('d/m/Y')
                        }})</span>
                    @endif
                </p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Téléphone:</span> {{
                    $caisse->patient->phone ?? 'N/A' }}</p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Adresse:</span> {{
                    $caisse->patient->address ?? 'N/A' }}</p>
            </div>

            <!-- Informations médicales -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-3 border-b pb-2 text-gray-900 dark:text-white">Personnel médical
                </h3>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Médecin:</span>
                    @if($caisse->medecin)
                    {{ $caisse->medecin->nom_complet_avec_prenom }}
                    @else
                    N/A
                    @endif
                </p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Prescripteur:</span>
                    @if($caisse->prescripteur)
                    {{ $caisse->prescripteur->nom }}
                    @else
                    Externe
                    @endif
                </p>
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium">Caissier:</span> {{
                    $caisse->nom_caissier }}</p>
                @if($caisse->modifier)
                <p class="text-gray-800 dark:text-gray-200"><span class="font-medium" style="color: rgba(167, 32, 17, 1);">Modifié par:</span> {{
                    $caisse->modifier->name }} <small class="text-xs text-gray-500 dark:text-gray-400">{{ $caisse->updated_at->format('d/m/Y à H:i') }}</small></p>
                @endif
            </div>
            @if($caisse->mode_paiements && $caisse->mode_paiements->count() > 0)
            @php
                // Utiliser la recette réelle depuis EtatCaisse si disponible, sinon le total de la caisse
                $montantPaiement = $caisse->etatCaisse ? $caisse->etatCaisse->recette : ($caisse->total ?? 0);
                // Récupérer le premier mode de paiement pour afficher le type
                $premierPaiement = $caisse->mode_paiements->first();
            @endphp
            <p class="text-gray-800 dark:text-gray-200"><strong>Mode de paiement :</strong> {{ $premierPaiement->type }} ({{ number_format($montantPaiement, 0, ',', ' ') }} MRU)</p>
            @if($caisse->couverture !== null)
            <p class="text-gray-800 dark:text-gray-200"><strong>Couverture assurance :</strong> {{ $caisse->couverture }}%</p>
            @endif
            @endif
        </div>

    </div>

    <!-- Pied de page -->
    <div class="bg-gray-100 dark:bg-gray-900 p-4 text-center text-sm text-gray-600 dark:text-gray-300">
        <p>{{ config('clinique.name') }} - Téléphone: {{ config('clinique.phone') }} - Email: {{ config('clinique.email') }}</p>
        <p class="mt-1">Adresse: {{ config('clinique.address') }}</p>
    </div>
</div>
@endsection
