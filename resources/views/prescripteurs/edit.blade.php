@extends('layouts.app')

@section('content')
<div
    class="max-w-2xl mx-auto bg-white p-6 rounded shadow dark:bg-gray-900 dark:text-gray-100 dark:shadow-lg dark:border dark:border-gray-700">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Modifier le Prescripteur</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Modifiez les informations du prescripteur <strong>{{ $prescripteur->nom }}</strong>
        </p>
    </div>

    @php
    $userRole = Auth::user()->role->name;
    
    if ($userRole === 'medecin') {
        $updateRoute = route('medecin.prescripteurs.update', $prescripteur->id);
        $indexRoute = route('medecin.prescripteurs.index');
    } elseif ($userRole === 'admin') {
        $updateRoute = route('admin.prescripteurs.update', $prescripteur->id);
        $indexRoute = route('admin.prescripteurs.index');
    } else {
        $updateRoute = route('prescripteurs.update', $prescripteur->id);
        $indexRoute = route('prescripteurs.index');
    }
    @endphp

    <form method="POST" action="{{ $updateRoute }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="return_page" value="{{ $page ?? 1 }}">

        <div class="space-y-4">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Nom
                </label>
                <input type="text" 
                    name="nom" 
                    id="nom"
                    value="{{ $prescripteur->nom }}" 
                    placeholder="Nom du prescripteur"
                    class="border border-gray-300 rounded-lg px-4 py-2.5 w-full dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    required>
            </div>

            <div>
                <label for="specialite" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Spécialité
                </label>
                <input type="text" 
                    name="specialite" 
                    id="specialite"
                    value="{{ $prescripteur->specialite }}" 
                    list="specialites-list"
                    placeholder="Tapez librement une spécialité..."
                    class="border border-gray-300 rounded-lg px-4 py-2.5 w-full dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <datalist id="specialites-list">
                    @php
                        $specialitesExistantes = \App\Models\Prescripteur::whereNotNull('specialite')
                            ->where('specialite', '!=', '')
                            ->distinct()
                            ->pluck('specialite')
                            ->sort();
                    @endphp
                    @foreach($specialitesExistantes as $spec)
                        <option value="{{ $spec }}">
                    @endforeach
                </datalist>
                <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800 dark:text-blue-300">
                            <p class="font-semibold mb-1">✨ Saisie libre</p>
                            <p>Vous pouvez taper <strong>n'importe quelle spécialité</strong> que vous souhaitez. Les suggestions sont là pour vous aider, mais vous n'êtes pas limité à ces choix.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ $indexRoute }}?page={{ $page ?? 1 }}"
                class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                Annuler
            </a>
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg flex items-center dark:bg-green-700 dark:hover:bg-green-800">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
