@extends('layouts.app')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Approbation des administrateurs</h1>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Bouton de filtre -->
    <div class="mb-4">
        <button id="toggleFilterBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Afficher uniquement les admins en attente
        </button>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full text-sm text-left text-gray-600" id="adminsTable">
            <thead class="bg-gray-100 text-xs uppercase">
                <tr>
                    <th class="px-6 py-3">Nom</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Fonction</th>
                    <th class="px-6 py-3">Statut</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                <tr class="border-b {{ $admin->is_approved ? 'approved-row' : 'pending-row' }}">
                    <td class="px-6 py-4 font-medium">{{ $admin->name }}</td>
                    <td class="px-6 py-4">{{ $admin->email }}</td>
                    <td class="px-6 py-4">
                        <form action="{{ route('superadmin.admins.assignRole', $admin->id) }}" method="POST">
                            @csrf
                            <select name="fonction" onchange="this.form.submit()" class="border rounded px-2 py-1">
                                <option value="">-- Choisir --</option>
                                <option value="Caissier" {{ $admin->fonction == 'Caissier' ? 'selected' : '' }}>Caissier
                                </option>
                                <option value="RH" {{ $admin->fonction == 'RH' ? 'selected' : '' }}>RH</option>
                                <option value="Support" {{ $admin->fonction == 'Support' ? 'selected' : '' }}>Support
                                </option>
                            </select>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        @if($admin->is_approved)
                        <span class="text-green-600 font-semibold">Approuvé</span>
                        @else
                        <span class="text-red-600 font-semibold">En attente</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @if(!$admin->is_approved)
                        <form action="{{ route('superadmin.admins.approve', $admin->id) }}" method="POST"
                            class="inline">
                            @csrf
                            <button class="text-green-600 hover:text-green-800" title="Approuver">
                                ✅
                            </button>
                        </form>
                        <form action="{{ route('superadmin.admins.reject', $admin->id) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-red-600 hover:text-red-800" title="Rejeter">
                                ❌
                            </button>
                        </form>
                        @else
                        <a href="{{ route('superadmin.admins.show', $admin->id) }}"
                            class="text-blue-600 hover:text-blue-800" title="Voir">
                            👁️
                        </a>
                        <a href="{{ route('superadmin.admins.edit', $admin->id) }}"
                            class="text-yellow-600 hover:text-yellow-800" title="Modifier">
                            ✏️
                        </a>
                        <form action="{{ route('superadmin.admins.destroy', $admin->id) }}" method="POST"
                            class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 hover:text-red-700" title="Supprimer">
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
