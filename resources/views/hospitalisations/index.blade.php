@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <!-- Header avec titre et description -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg mr-4">
                <i class="fas fa-hospital text-blue-600 dark:text-blue-400 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestion des Hospitalisations</h1>
                <p class="text-gray-600 dark:text-gray-300 mt-1">Gérez les hospitalisations des patients</p>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex space-x-4">
            <a href="{{ route('hospitalisations.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Nouvelle Hospitalisation
            </a>
            <button
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium flex items-center transition-colors">
                <i class="fas fa-bed mr-2"></i>
                Créer une Chambre
            </button>
        </div>
    </div>

    @if(session('success'))
    <div
        class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if(session('error'))
    <div
        class="bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    </div>
    @endif

    <!-- Cartes de statistiques -->
    @php
    // Récupérer toutes les hospitalisations pour les statistiques
    $allHospitalisations = \App\Models\Hospitalisation::all();
    $total = $allHospitalisations->count();
    $enCours = $allHospitalisations->where('statut', 'en cours')->count();
    $terminees = $allHospitalisations->where('statut', 'terminé')->count();
    $annulees = $allHospitalisations->where('statut', 'annulé')->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 border-blue-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $total }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-hospital text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- En Cours -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 border-green-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">En Cours</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $enCours }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Terminées -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 border-blue-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Terminées</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $terminees }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-check text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Annulées -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 border-red-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Annulées</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $annulees }}</p>
                </div>
                <div class="bg-red-100 dark:bg-red-900 p-3 rounded-full">
                    <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des hospitalisations -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ID</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Patient</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Médecin</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Service</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Chambre/Lit</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date Admission</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Statut</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($hospitalisations as $hospitalisation)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span
                                    class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded text-sm font-mono">
                                    #{{ $hospitalisation->id }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-user text-blue-600 mr-3"></i>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $hospitalisation->patient->nom ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $hospitalisation->lit->chambre->nom ?? 'N/A' }} - Lit {{
                                        $hospitalisation->lit->numero ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            @if($hospitalisation->medecin)
                            {{ $hospitalisation->medecin->nom_complet }} {{ $hospitalisation->medecin->prenom }}
                            @else
                            N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-xs font-medium">
                                {{ strtoupper($hospitalisation->service->nom ?? 'HOSPITALISATION') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div>
                                <strong>Ch:</strong> {{ $hospitalisation->lit->chambre->nom ?? 'N/A' }}
                            </div>
                            <div>
                                <strong>Lit:</strong> {{ $hospitalisation->lit->numero ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div>
                                {{ \Carbon\Carbon::parse($hospitalisation->date_entree)->format('d/m/Y') }}
                            </div>
                            @if($hospitalisation->date_sortie)
                            <div class="text-xs text-gray-500">
                                Sortie: {{ \Carbon\Carbon::parse($hospitalisation->date_sortie)->format('d/m/Y') }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($hospitalisation->statut)
                            @case('en cours')
                            <span
                                class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full text-xs font-medium">
                                en cours
                            </span>
                            @break
                            @case('terminé')
                            <span
                                class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-xs font-medium">
                                terminé
                            </span>
                            @break
                            @case('annulé')
                            <span
                                class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded-full text-xs font-medium">
                                Annulé
                                @if($hospitalisation->annulator)
                                (par {{ $hospitalisation->annulator->name }})
                                @endif
                            </span>
                            @break
                            @default
                            <span
                                class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $hospitalisation->statut }}
                            </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('hospitalisations.show', $hospitalisation) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                    title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($hospitalisation->statut === 'en cours')
                                <a href="{{ route('hospitalisations.edit', $hospitalisation) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif

                                <form action="{{ route('hospitalisations.destroy', $hospitalisation) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Supprimer cette hospitalisation ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                        title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-hospital text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <p class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune hospitalisation
                                    trouvée</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Commencez par ajouter une nouvelle
                                    hospitalisation</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($hospitalisations->hasPages())
        <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $hospitalisations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection