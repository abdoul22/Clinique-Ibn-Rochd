@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="page-title text-gray-900 dark:text-white">Services hospitaliers</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('services.create') }}"
            class="bg-cyan-600 dark:bg-cyan-800 hover:bg-cyan-700 dark:hover:bg-cyan-900 text-white text-sm px-4 py-2 rounded transition">
            + Ajouter un service
        </a>

        <!-- Bouton PDF -->
        <a href="{{ route('services.exportPdf') }}"
            class="bg-red-500 dark:bg-red-700 hover:bg-red-600 dark:hover:bg-red-900 text-white text-sm px-4 py-2 rounded flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Télécharger PDF
        </a>

        <!-- Bouton Impression -->
        <a href="{{ route('services.print') }}" target="_blank"
            class="bg-gray-600 dark:bg-gray-800 hover:bg-gray-700 dark:hover:bg-gray-900 text-white text-sm px-4 py-2 rounded flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-4 0h-4v4h4v-4z" />
            </svg>
            Imprimer
        </a>
    </div>
</div>

<!-- Tableau -->
<div class="table-container bg-white dark:bg-gray-800 rounded shadow dark:shadow-lg">
    <table class="table-main">
        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            <tr>
                <th class="table-header">ID</th>
                <th class="table-header">Nom</th>
                <th class="table-header">Type de service</th>
                <th class="table-header">Observation</th>
                <th class="table-header">Actions</th>
            </tr>
        </thead>
        <tbody class="table-body">
            @foreach($services as $service)
            <tr class="table-row dark:hover:bg-gray-900">
                <td class="table-cell text-gray-900 dark:text-gray-100">{{ $service->id }}</td>
                <td class="table-cell text-gray-900 dark:text-gray-100">
                    @if($service->type_service === 'medicament' && $service->pharmacie)
                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $service->nom_affichage }}</span>
                    @else
                    {{ $service->nom_affichage }}
                    @endif
                </td>
                <td class="table-cell">
                    @php
                    $badgeColors = [
                    'examen' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'medicament' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'consultation' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                    'pharmacie' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
                    'medecins' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    ];
                    $type = strtolower($service->type_service);
                    $badgeClass = $badgeColors[$type] ?? 'bg-gray-200 text-gray-800 dark:bg-gray-700
                    dark:text-gray-200';
                    @endphp
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                        {{ ucfirst($service->type_service) }}
                    </span>
                </td>
                <td class="table-cell text-gray-900 dark:text-gray-100">
                    @if($service->type_service === 'medicament' && $service->pharmacie)
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $service->observation_affichage }}</span>
                    @else
                    {{ $service->observation_affichage }}
                    @endif
                </td>
                <td class="table-cell">
                    <div class="table-actions">
                        <!-- Modifier -->
                        <a href="{{ route('services.edit', $service->id) }}" class="action-btn action-btn-edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                            </svg>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn action-btn-delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container">
    {{ $services->links() }}
</div>
@endsection
