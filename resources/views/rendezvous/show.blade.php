@extends('layouts.app')

@section('title', 'Détails du Rendez-vous')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Détails du Rendez-vous</h1>
            <div class="flex space-x-2">
                @if(Auth::user() && Auth::user()->role?->name === 'superadmin')
                <a href="{{ route('rendezvous.edit', $rendezVous->id) }}"
                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <form action="{{ route('rendezvous.destroy', $rendezVous->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')">
                        <i class="fas fa-trash mr-2"></i>Supprimer
                    </button>
                </form>
                @endif
                <a href="{{ request()->routeIs('admin.*') ? route('admin.rendezvous.index') : route('rendezvous.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Informations du Rendez-vous
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Date et Heure</h3>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar text-blue-500 mr-3 w-5"></i>
                                    <span class="text-gray-900 dark:text-gray-200">{{
                                        $rendezVous->date_rdv->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-hashtag text-blue-500 mr-3 w-5"></i>
                                    <span class="text-gray-900 dark:text-gray-200">Numéro d'entrée : {{
                                        $rendezVous->numero_entree }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Statut</h3>
                            <div class="mb-3">
                                @if($rendezVous->statut === 'annule')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                    <i class="fas fa-times mr-2"></i>Annulé
                                </span>
                                @elseif($rendezVous->statut === 'confirme')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    <i class="fas fa-check mr-2"></i>Confirmé
                                </span>
                                <form action="{{ route('rendezvous.change-status', $rendezVous->id) }}" method="POST"
                                    class="inline-block mt-2">
                                    @csrf
                                    <input type="hidden" name="statut" value="annule">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-700 text-white text-xs px-2 py-1 rounded"
                                        onclick="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')">
                                        Annuler
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Motif de consultation</h3>
                        <p class="text-gray-900 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-3 rounded">{{
                            $rendezVous->motif }}</p>
                    </div>

                    @if($rendezVous->notes)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Notes</h3>
                        <p class="text-gray-900 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-3 rounded">{{
                            $rendezVous->notes }}</p>
                    </div>
                    @endif

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-3">Pris par</h3>
                        <div class="flex items-center">
                            <i class="fas fa-user text-green-500 mr-3 w-5"></i>
                            <span class="text-gray-900 dark:text-gray-200">Pris par : {{ $rendezVous->createdBy ?
                                $rendezVous->createdBy->name : 'Utilisateur inconnu' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations du patient et médecin -->
            <div class="space-y-6">
                <!-- Informations du patient -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                        <i class="fas fa-user text-blue-500 mr-2"></i>Patient
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nom
                                complet</label>
                            <p class="text-gray-900 dark:text-gray-200 font-medium">{{ $rendezVous->patient->first_name
                                }} {{ $rendezVous->patient->last_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Téléphone</label>
                            <p class="text-gray-900 dark:text-gray-200">{{ $rendezVous->patient->phone }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Date de
                                naissance</label>
                            <p class="text-gray-900 dark:text-gray-200">{{ $rendezVous->patient->date_of_birth ?
                                $rendezVous->patient->date_of_birth->format('d/m/Y') : 'Non renseignée' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Genre</label>
                            <p class="text-gray-900 dark:text-gray-200">{{ $rendezVous->patient->gender ?? 'Non
                                renseigné' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informations du médecin -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                        <i class="fas fa-user-md text-green-500 mr-2"></i>Médecin
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nom
                                complet</label>
                            <p class="text-gray-900 dark:text-gray-200 font-medium">{{ $rendezVous->medecin->nom }} {{
                                $rendezVous->medecin->prenom }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Spécialité</label>
                            <p class="text-gray-900 dark:text-gray-200">{{ $rendezVous->medecin->specialite }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Téléphone</label>
                            <p class="text-gray-900 dark:text-gray-200">{{ $rendezVous->medecin->telephone }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="text-gray-900 dark:text-gray-200">{{ $rendezVous->medecin->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Actions</h3>

                    <div class="space-y-3">
                        @if(Auth::user() && Auth::user()->role?->name === 'superadmin')
                        <a href="{{ route('rendezvous.edit', $rendezVous->id) }}"
                            class="w-full bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                        <form action="{{ route('rendezvous.destroy', $rendezVous->id) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?')">
                                <i class="fas fa-trash mr-2"></i>Supprimer
                            </button>
                        </form>
                        @endif
                        <a href="{{ request()->routeIs('admin.*') ? route('admin.rendezvous.index') : route('rendezvous.index') }}"
                            class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
                            <i class="fas fa-list mr-2"></i>Liste des rendez-vous
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
