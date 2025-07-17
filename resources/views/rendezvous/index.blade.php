@extends('layouts.app')

@section('title', 'Gestion des Rendez-vous')

@section('content')
<div class="w-full px-0 sm:px-2 lg:px-4 py-4 sm:py-8">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4 sm:mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200">Gestion des Rendez-vous</h1>
        <a href="{{ route('rendezvous.create') }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-base w-full sm:w-auto text-center lg:w-auto">
            <i class="fas fa-plus mr-2"></i>Nouveau Rendez-vous
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('rendezvous.index') }}"
            class="flex flex-col lg:flex-row lg:space-x-4 space-y-4 lg:space-y-0">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Médecin</label>
                <select name="medecin_id"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 text-base">
                    <option value="">Tous les médecins</option>
                    @foreach($medecins as $medecin)
                    <option value="{{ $medecin->id }}" {{ request('medecin_id')==$medecin->id ? 'selected' : '' }}>
                        {{ $medecin->nom }} {{ $medecin->prenom }} - {{ $medecin->specialite }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 text-base">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                <select name="statut"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 text-base">
                    <option value="">Tous les statuts</option>
                    <option value="confirme" {{ request('statut')=='confirme' ? 'selected' : '' }}>Confirmé</option>
                    <option value="annule" {{ request('statut')=='annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Téléphone
                    patient</label>
                <input type="text" name="patient_phone" value="{{ request('patient_phone') }}"
                    placeholder="Numéro du patient"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 text-base">
            </div>
            <div class="flex flex-row space-x-2 items-end lg:items-center">
                <button type="submit"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-base w-full sm:w-auto lg:w-auto">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                <a href="{{ route('rendezvous.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-base w-full sm:w-auto lg:w-auto text-center">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Navigation du calendrier -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">
                {{ \Carbon\Carbon::create($currentYear, $currentMonth)->format('F Y') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('rendezvous.index', array_merge(request()->query(), ['month' => $currentMonth - 1, 'year' => $currentYear])) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg text-base">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <a href="{{ route('rendezvous.index', array_merge(request()->query(), ['month' => $currentMonth + 1, 'year' => $currentYear])) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg text-base">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>

        <!-- Calendrier -->
        <div class="grid grid-cols-7 gap-1 overflow-x-auto">
            <!-- En-têtes des jours -->
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300 text-sm">
                Lun</div>
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300 text-sm">
                Mar</div>
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300 text-sm">
                Mer</div>
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300 text-sm">
                Jeu</div>
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300 text-sm">
                Ven</div>
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300 text-sm">
                Sam</div>
            <div
                class="bg-gray-100 dark:bg-gray-700 p-2 text-center font-semibold text-gray-700 dark:text-gray-300 text-sm">
                Dim</div>

            <!-- Jours du calendrier -->
            @foreach($calendarData as $day)
            <div
                class="min-h-[80px] sm:min-h-[120px] border border-gray-200 dark:border-gray-600 p-1 sm:p-2 {{ $day['isCurrentMonth'] ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }} {{ $day['isToday'] ? 'ring-2 ring-blue-500' : '' }}">
                <div
                    class="text-xs sm:text-sm font-medium {{ $day['isCurrentMonth'] ? 'text-gray-900 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500' }} {{ $day['isToday'] ? 'text-blue-600 dark:text-blue-400 font-bold' : '' }}">
                    {{ $day['date']->format('j') }}
                </div>

                @if($day['count'] > 0)
                <div class="mt-1">
                    <span
                        class="inline-flex items-center px-1 sm:px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        {{ $day['count'] }} RDV
                    </span>
                </div>

                <div class="mt-1 space-y-1">
                    @foreach($day['rendezVous']->take(2) as $rdv)
                    <div class="text-xs p-1 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                        onclick="showRendezVousDetails({{ $rdv->id }})"
                        title="{{ $rdv->created_at->format('H:i') }} - {{ $rdv->patient_nom_complet }}">
                        <div class="font-medium">{{ $rdv->created_at->format('H:i') }}</div>
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
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Liste des Rendez-vous</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Numéro d'entrée</th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Patient</th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Médecin</th>
                        <th
                            class="hidden lg:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Motif</th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Statut</th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date</th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($rendezVous as $rdv)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm">{{ $rdv->numero_entree }}</td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{
                                $rdv->patient->first_name }} {{ $rdv->patient->last_name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rdv->patient->phone }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $rdv->medecin->nom }}
                                {{ $rdv->medecin->prenom }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $rdv->medecin->specialite }}</div>
                        </td>
                        <td class="hidden lg:table-cell px-3 sm:px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-gray-200">{{ Str::limit($rdv->motif, 50) }}
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            @switch($rdv->statut)
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
                            @endswitch
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm">{{ $rdv->date_rdv->format('d/m') }}</td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route(request()->routeIs('admin.*') ? 'admin.rendezvous.show' : 'rendezvous.show', $rdv->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(Auth::user() && Auth::user()->role?->name === 'superadmin' &&
                                !request()->routeIs('admin.*'))
                                <a href="{{ route('rendezvous.edit', $rdv->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('rendezvous.destroy', $rdv->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @if(Auth::user() && Auth::user()->role?->name === 'admin' &&
                                !request()->routeIs('admin.*') && $rdv->statut === 'confirme')
                                <form action="{{ route('rendezvous.change-status', $rdv->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <input type="hidden" name="statut" value="annule">
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1"
                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 sm:px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Aucun rendez-vous trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rendezVous->hasPages())
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $rendezVous->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal pour les détails du rendez-vous -->
<div id="rendezVousModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-4">Détails du Rendez-vous</h3>
            <div id="rendezVousDetails" class="text-sm text-gray-600 dark:text-gray-400">
                <!-- Les détails seront chargés ici -->
            </div>
            <div class="mt-4">
                <button onclick="closeRendezVousModal()"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showRendezVousDetails(id) {
        // Ici vous pouvez ajouter une requête AJAX pour charger les détails
        document.getElementById('rendezVousModal').classList.remove('hidden');
    }

    function closeRendezVousModal() {
        document.getElementById('rendezVousModal').classList.add('hidden');
    }
</script>
@endsection
