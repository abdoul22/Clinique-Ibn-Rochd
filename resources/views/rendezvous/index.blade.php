@extends('layouts.app')

@section('title', 'Gestion des Rendez-vous')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Gestion des Rendez-vous</h1>
        <a href="{{ route('rendezvous.create') }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Nouveau Rendez-vous
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('rendezvous.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Médecin</label>
                <select name="medecin_id"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2">
                    <option value="">Tous les médecins</option>
                    @foreach($medecins as $medecin)
                    <option value="{{ $medecin->id }}" {{ request('medecin_id')==$medecin->id ? 'selected' : '' }}>
                        {{ $medecin->nom }} {{ $medecin->prenom }} - {{ $medecin->specialite }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                <select name="statut"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente" {{ request('statut')=='en_attente' ? 'selected' : '' }}>En attente
                    </option>
                    <option value="confirme" {{ request('statut')=='confirme' ? 'selected' : '' }}>Confirmé</option>
                    <option value="annule" {{ request('statut')=='annule' ? 'selected' : '' }}>Annulé</option>
                    <option value="termine" {{ request('statut')=='termine' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                <a href="{{ route('rendezvous.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Navigation du calendrier -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('rendezvous.index', array_merge(request()->query(), ['month' => $currentMonth - 1, 'year' => $currentYear])) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="{{ route('rendezvous.index', array_merge(request()->query(), ['month' => $currentMonth + 1, 'year' => $currentYear])) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="grid grid-cols-7 gap-1">
            <!-- En-têtes des jours -->
            <div class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300">Lun
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300">Mar
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300">Mer
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300">Jeu
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300">Ven
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300">Sam
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300">Dim
            </div>

            <!-- Jours du calendrier -->
            @foreach($calendarData as $day)
            <div
                class="min-h-[120px] border border-gray-200 dark:border-gray-600 p-2 {{ $day['isCurrentMonth'] ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }} {{ $day['isToday'] ? 'ring-2 ring-blue-500' : '' }}">
                <div
                    class="text-sm font-medium {{ $day['isCurrentMonth'] ? 'text-gray-900 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500' }} {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400 font-bold' : '' }}">
                    {{ $day['date']->format('j') }}
                </div>

                @if($day['count'] > 0)
                <div class="mt-1">
                    <span
                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        {{ $day['count'] }} RDV
                    </span>
                </div>

                <div class="mt-1 space-y-1">
                    @foreach($day['rendezVous']->take(2) as $rdv)
                    <div class="text-xs p-1 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                        onclick="showRendezVousDetails({{ $rdv->id }})"
                        title="{{ $rdv->heure_rdv }} - {{ $rdv->patient_nom_complet }}">
                        <div class="font-medium">{{ \Carbon\Carbon::parse($rdv->heure_rdv)->format('H:i') }}</div>
                        <div class="truncate">{{ $rdv->patient->first_name }}</div>
                    </div>
                    @endforeach

                    @if($day['count'] > 2)
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        +{{ $day['count'] - 2 }} autres
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Liste des rendez-vous -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Liste des Rendez-vous</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Patient</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Médecin</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date
                            & Heure</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Motif
                        </th>
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
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{
                                $rdv->patient->first_name }} {{
                                $rdv->patient->last_name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rdv->patient->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $rdv->medecin->nom }}
                                {{
                                $rdv->medecin->prenom }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rdv->medecin->specialite }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{
                                $rdv->date_rdv->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{
                                \Carbon\Carbon::parse($rdv->heure_rdv)->format('H:i')
                                }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-200">{{ Str::limit($rdv->motif, 50) }}
                            </div>
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
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                Confirmé
                            </span>
                            @break
                            @case('annule')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                Annulé
                            </span>
                            @break
                            @case('termine')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                Terminé
                            </span>
                            @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('rendezvous.show', $rdv->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('rendezvous.edit', $rdv->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('rendezvous.destroy', $rdv->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Aucun rendez-vous trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal pour les détails du rendez-vous -->
<div id="rdvModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div
        class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-600 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div id="rdvModalContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="closeRendezVousModal()"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showRendezVousDetails(rdvId) {
    // Utiliser la route Laravel au lieu d'une URL hardcodée
    window.location.href = "{{ route('rendezvous.show', ':id') }}".replace(':id', rdvId);
}

function closeRendezVousModal() {
    document.getElementById('rdvModal').classList.add('hidden');
}

// Auto-refresh du calendrier toutes les 5 minutes
setInterval(function() {
    // Optionnel : recharger la page ou faire une requête AJAX
}, 300000);
</script>
@endpush
