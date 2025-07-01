@extends('layouts.app')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <h1 class="page-title mb-6">Approbation des administrateurs</h1>

    @if(session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Bouton de filtre -->
    <div class="mb-4">
        <button id="toggleFilterBtn" class="form-button">
            Afficher uniquement les admins en attente
        </button>
    </div>

    <div class="table-container">
        <table class="table-main" id="adminsTable">
            <thead class="bg-gray-100 dark:bg-gray-700 text-xs uppercase">
                <tr>
                    <th class="table-header">Nom</th>
                    <th class="table-header">Email</th>
                    <th class="table-header">Fonction</th>
                    <th class="table-header">Statut</th>
                    <th class="table-header text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @foreach($admins as $admin)
                <tr class="table-row {{ $admin->is_approved ? 'approved-row' : 'pending-row' }}">
                    <td class="table-cell font-medium">{{ $admin->name }}</td>
                    <td class="table-cell">{{ $admin->email }}</td>
                    <td class="table-cell">
                        <form action="{{ route('superadmin.admins.assignRole', $admin->id) }}" method="POST">
                            @csrf
                            <select name="fonction" onchange="this.form.submit()" class="form-select">
                                <option value="">-- Choisir --</option>
                                <option value="Caissier" {{ $admin->fonction == 'Caissier' ? 'selected' : '' }}>Caissier
                                </option>
                                <option value="RH" {{ $admin->fonction == 'RH' ? 'selected' : '' }}>RH</option>
                                <option value="Support" {{ $admin->fonction == 'Support' ? 'selected' : '' }}>Support
                                </option>
                            </select>
                        </form>
                    </td>
                    <td class="table-cell">
                        @if($admin->is_approved)
                        <span class="text-green-600 dark:text-green-400 font-semibold">Approuvé</span>
                        @else
                        <span class="text-red-600 dark:text-red-400 font-semibold">En attente</span>
                        @endif
                    </td>
                    <td class="table-cell text-right space-x-2">
                        @if(!$admin->is_approved)
                        <form action="{{ route('superadmin.admins.approve', $admin->id) }}" method="POST"
                            class="inline">
                            @csrf
                            <button
                                class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300"
                                title="Approuver">
                                ✅
                            </button>
                        </form>
                        <form action="{{ route('superadmin.admins.reject', $admin->id) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                title="Rejeter">
                                ❌
                            </button>
                        </form>
                        @else
                        <a href="{{ route('superadmin.admins.show', $admin->id) }}"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                            title="Voir">
                            👁️
                        </a>
                        <a href="{{ route('superadmin.admins.edit', $admin->id) }}"
                            class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300"
                            title="Modifier">
                            ✏️
                        </a>
                        <form action="{{ route('superadmin.admins.destroy', $admin->id) }}" method="POST"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
                                title="Supprimer">
                                🗑️
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Script JS pour le filtre -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('toggleFilterBtn');
        let filterActive = false;

        toggleBtn.addEventListener('click', () => {
            const approvedRows = document.querySelectorAll('.approved-row');
            filterActive = !filterActive;

            approvedRows.forEach(row => {
                row.style.display = filterActive ? 'none' : '';
            });

            toggleBtn.textContent = filterActive
                ? 'Afficher tous les administrateurs'
                : 'Afficher uniquement les admins en attente';
        });
    });
</script>
@endsection
