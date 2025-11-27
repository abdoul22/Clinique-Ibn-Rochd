@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header avec gradient -->
        <div class="gradient-header mb-8">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-4 md:mb-0">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            <i class="fas fa-users-cog mr-3"></i>Gestion des Utilisateurs
                        </h1>
                        <p class="text-blue-100 text-lg">Gérez tous les utilisateurs de l'application</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div
            class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
        @endif

        <!-- Section Superadmins -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="bg-purple-600 dark:bg-purple-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-crown mr-3"></i>Super Administrateurs
                        <span class="ml-3 bg-purple-500 text-white text-sm px-2 py-1 rounded-full">{{
                            $superadmins->count() }}</span>
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Utilisateur</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Fonction</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Dernière connexion</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($superadmins as $superadmin)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                                <i class="fas fa-crown text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{
                                                $superadmin->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Super Admin</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{
                                    $superadmin->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                        <i class="fas fa-crown mr-1"></i>Superadmin
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($superadmin->is_approved)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        <i class="fas fa-check mr-1"></i>Approuvé
                                    </span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                        <i class="fas fa-clock mr-1"></i>En attente
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    @if($superadmin->last_login_at)
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-2 text-gray-400"></i>
                                        <span>{{ $superadmin->last_login_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @else
                                    <span class="text-gray-400 italic">Jamais connecté</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(!$superadmin->is_approved)
                                    <div class="flex justify-end space-x-2">
                                        <form action="{{ route('superadmin.admins.approve', $superadmin->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button
                                                class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 p-1"
                                                title="Approuver">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('superadmin.admins.reject', $superadmin->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-1"
                                                title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('superadmin.admins.show', $superadmin->id) }}"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1"
                                            title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('superadmin.admins.edit', $superadmin->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1"
                                            title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('superadmin.admins.destroy', $superadmin->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1"
                                                title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-info-circle mr-2"></i>Aucun super administrateur trouvé
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section Admins -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="bg-blue-600 dark:bg-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-user-shield mr-3"></i>Administrateurs
                        <span class="ml-3 bg-blue-500 text-white text-sm px-2 py-1 rounded-full">{{ $admins->count()
                            }}</span>
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Utilisateur</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Fonction</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Dernière connexion</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($admins as $admin)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <i class="fas fa-user-shield text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{
                                                $admin->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">Admin</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{
                                    $admin->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('superadmin.admins.assignRole', $admin->id) }}"
                                        method="POST" id="admin-form-{{ $admin->id }}">
                                        @csrf
                                        
                                        <!-- Sélection du Rôle -->
                                        <div class="mb-2">
                                            <label class="text-xs text-gray-600 dark:text-gray-400 font-semibold">Rôle</label>
                                            <select name="user_role" 
                                                class="text-sm border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white w-full"
                                                onchange="toggleMedecinSelect{{ $admin->id }}(this.value)">
                                                <option value="admin" {{ $admin->role?->name === 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="medecin" {{ $admin->role?->name === 'medecin' ? 'selected' : '' }}>Médecin</option>
                                            </select>
                                        </div>

                                        <!-- Sélection de la Fonction (pour admins) -->
                                        <div id="fonction-select-{{ $admin->id }}" style="{{ $admin->role?->name === 'medecin' ? 'display:none' : '' }}">
                                            <label class="text-xs text-gray-600 dark:text-gray-400 font-semibold">Fonction</label>
                                            <select name="fonction"
                                                class="text-sm border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white w-full">
                                                <option value="">-- Choisir --</option>
                                                <option value="Caissier" {{ $admin->fonction == 'Caissier' ? 'selected' : '' }}>Caissier</option>
                                                <option value="RH" {{ $admin->fonction == 'RH' ? 'selected' : '' }}>RH</option>
                                                <option value="Support" {{ $admin->fonction == 'Support' ? 'selected' : '' }}>Support</option>
                                                <option value="Infirmier" {{ $admin->fonction == 'Infirmier' ? 'selected' : '' }}>Infirmier</option>
                                                <option value="Réceptionniste" {{ $admin->fonction == 'Réceptionniste' ? 'selected' : '' }}>Réceptionniste</option>
                                            </select>
                                        </div>

                                        <!-- Sélection du Médecin Associé (pour médecins seulement) -->
                                        <div id="medecin-select-{{ $admin->id }}" style="{{ $admin->role?->name === 'medecin' ? '' : 'display:none' }}">
                                            <label class="text-xs text-gray-600 dark:text-gray-400 font-semibold">Médecin Associé</label>
                                            <select name="medecin_id"
                                                class="text-sm border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white w-full">
                                                <option value="">-- Choisir un médecin --</option>
                                                @foreach($medecinsList as $medecinItem)
                                                    <option value="{{ $medecinItem->id }}" {{ $admin->medecin_id == $medecinItem->id ? 'selected' : '' }}>
                                                        {{ $medecinItem->nom_complet_avec_prenom }} - {{ $medecinItem->specialite ?? 'Médecin' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Bouton Sauvegarder -->
                                        <button type="submit" class="mt-2 w-full text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium py-1 px-2 rounded">
                                            <i class="fas fa-save mr-1"></i>Sauvegarder
                                        </button>
                                    </form>

                                    @if($admin->fonction || $admin->role?->name === 'medecin')
                                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        @if($admin->role?->name === 'medecin')
                                            <i class="fas fa-user-md mr-1"></i>Compte Médecin
                                            @if($admin->medecin)
                                                <br><span class="text-indigo-600 dark:text-indigo-400">→ {{ $admin->medecin->nom_complet_avec_prenom }}</span>
                                            @endif
                                        @else
                                            <i class="fas fa-sync-alt mr-1"></i>Synchronisé avec le personnel
                                        @endif
                                    </div>
                                    @endif

                                    <script>
                                        function toggleMedecinSelect{{ $admin->id }}(role) {
                                            const fonctionDiv = document.getElementById('fonction-select-{{ $admin->id }}');
                                            const medecinDiv = document.getElementById('medecin-select-{{ $admin->id }}');
                                            
                                            if (role === 'medecin') {
                                                fonctionDiv.style.display = 'none';
                                                medecinDiv.style.display = 'block';
                                            } else {
                                                fonctionDiv.style.display = 'block';
                                                medecinDiv.style.display = 'none';
                                            }
                                        }
                                    </script>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($admin->is_approved)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        <i class="fas fa-check mr-1"></i>Approuvé
                                    </span>
                                    @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                        <i class="fas fa-clock mr-1"></i>En attente
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                    @if($admin->last_login_at)
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-2 text-gray-400"></i>
                                        <span>{{ $admin->last_login_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @else
                                    <span class="text-gray-400 italic">Jamais connecté</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(!$admin->is_approved)
                                    <div class="flex justify-end space-x-2">
                                        <form action="{{ route('superadmin.admins.approve', $admin->id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button
                                                class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 p-1"
                                                title="Approuver">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('superadmin.admins.reject', $admin->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-1"
                                                title="Rejeter">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('superadmin.admins.show', $admin->id) }}"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1"
                                            title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('superadmin.admins.edit', $admin->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1"
                                            title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('superadmin.admins.destroy', $admin->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1"
                                                title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-info-circle mr-2"></i>Aucun administrateur trouvé
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-crown text-purple-600 dark:text-purple-400"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Super Admins</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-200">{{ $superadmins->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-shield text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Admins</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-200">{{ $admins->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-200">{{ $superadmins->count() +
                            $admins->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection