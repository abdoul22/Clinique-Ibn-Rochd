@extends('layouts.app')
@section('title', 'Dossier Médical - ' . $dossier->patient->first_name . ' ' . $dossier->patient->last_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête du dossier -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                        Dossier Médical
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ $dossier->numero_dossier }} - {{ $dossier->patient->first_name }} {{
                        $dossier->patient->last_name }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('dossiers.edit', $dossier->id) }}"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                    <a href="{{ route('dossiers.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations du patient -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Informations personnelles -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <i class="fas fa-user mr-2"></i>Informations Patient
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom complet</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">
                            {{ $dossier->patient->first_name }} {{ $dossier->patient->last_name }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Âge</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">
                            {{ $dossier->patient->age ? $dossier->patient->age . ' ans' : 'Non renseigné' }}
                            @if($dossier->patient->date_of_birth)
                            <span class="text-xs text-gray-500">(né le {{
                                $dossier->patient->date_of_birth->format('d/m/Y') }})</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Téléphone</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">
                            {{ $dossier->patient->phone }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">
                            {{ $dossier->patient->email ?: 'Non renseigné' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Adresse</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">
                            {{ $dossier->patient->address ?: 'Non renseignée' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques du dossier -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <i class="fas fa-chart-bar mr-2"></i>Statistiques
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total examens</span>
                        <span class="text-lg font-semibold text-blue-600 dark:text-blue-400">{{
                            $statistiques['total_examens'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total rendez-vous</span>
                        <span class="text-lg font-semibold text-green-600 dark:text-green-400">{{
                            $statistiques['total_rendez_vous'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">RDV confirmés</span>
                        <span class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">{{
                            $statistiques['rendez_vous_confirmes'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">RDV terminés</span>
                        <span class="text-lg font-semibold text-purple-600 dark:text-purple-400">{{
                            $statistiques['rendez_vous_termines'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Médecins consultés</span>
                        <span class="text-lg font-semibold text-indigo-600 dark:text-indigo-400">{{
                            $statistiques['medecins_consultes'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total dépenses</span>
                        <span class="text-lg font-semibold text-red-600 dark:text-red-400">{{
                            number_format($statistiques['total_depense'], 0, ',', ' ') }} MRU</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations du dossier -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <i class="fas fa-folder-medical mr-2"></i>Informations Dossier
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">N° Dossier</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">{{ $dossier->numero_dossier }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Date de création</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">{{ $dossier->date_creation->format('d/m/Y')
                            }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Dernière visite</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">
                            {{ $dossier->derniere_visite ? $dossier->derniere_visite->format('d/m/Y') : 'Aucune' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre de visites</label>
                        <p class="text-sm text-gray-900 dark:text-gray-200">{{ $dossier->nombre_visites }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @switch($dossier->statut)
                                @case('actif') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 @break
                                @case('inactif') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 @break
                                @case('archive') bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 @break
                            @endswitch">
                            {{ ucfirst($dossier->statut) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des examens -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                <i class="fas fa-stethoscope mr-2"></i>Historique des Examens
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Examen</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Médecin</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Prescripteur</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Montant</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($examens as $examen)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            {{ $examen->date_examen->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $examen->examen->nom ?? 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $examen->service->nom ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            @if($examen->medecin)
                            {{ $examen->medecin->nom_complet_avec_prenom }}
                            @else
                            N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            {{ $examen->prescripteur->nom ?? 'N/A' }} {{ $examen->prescripteur->prenom ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">
                            {{ number_format($examen->total, 0, ',', ' ') }} MRU
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('caisses.show', $examen->id) }}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Aucun examen trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination des examens -->
        @if($examens->hasPages())
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-600">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Affichage de {{ $examens->firstItem() ?? 1 }} à {{ $examens->lastItem() ?? $examens->count() }}
                    sur {{ $examens->total() ?? $examens->count() }} examens
                </div>
                <div class="flex justify-center">
                    {{ $examens->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Historique des rendez-vous -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                <i class="fas fa-calendar-alt mr-2"></i>Historique des Rendez-vous
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date & Heure</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Médecin</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Motif</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Statut</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($rendezVous as $rdv)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $rdv->date_rdv->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($rdv->heure_rdv)->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            @if($rdv->medecin)
                            {{ $rdv->medecin->nom_complet_avec_prenom }}
                            @else
                            N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                            {{ Str::limit($rdv->motif, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($rdv->statut)
                            @case('en_attente')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                En attente
                            </span>
                            @break
                            @case('confirme')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                Confirmé
                            </span>
                            @break
                            @case('termine')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                Terminé
                            </span>
                            @break
                            @case('annule')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                Annulé
                            </span>
                            @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('rendezvous.show', $rdv->id) }}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Aucun rendez-vous trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination des rendez-vous -->
        @if($rendezVous->hasPages())
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-600">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Affichage de {{ $rendezVous->firstItem() ?? 1 }} à {{ $rendezVous->lastItem() ??
                    $rendezVous->count() }}
                    sur {{ $rendezVous->total() ?? $rendezVous->count() }} rendez-vous
                </div>
                <div class="flex justify-center">
                    {{ $rendezVous->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
