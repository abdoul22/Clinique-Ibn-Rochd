@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Détails de l'Administrateur</h2>
        <div class="space-x-3">
            <a href="{{ route('superadmin.admins.index') }}"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                ← Retour à la liste
            </a>
            @if(auth()->user()->isSuperAdmin())
            <a href="{{ route('superadmin.admins.edit', $admin->id) }}"
                class="px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded text-sm">
                Modifier
            </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4 p-4 border rounded">
            <h3 class="font-semibold text-lg border-b pb-2">Informations Personnelles</h3>
            <p><strong class="text-gray-600">Nom complet :</strong> {{ $admin->name }}</p>
            <p><strong class="text-gray-600">Email :</strong> {{ $admin->email }}</p>
            <p><strong class="text-gray-600">Date d'inscription :</strong> {{ $admin->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="space-y-4 p-4 border rounded">
            <h3 class="font-semibold text-lg border-b pb-2">Statut Administrateur</h3>
            <p>
                <strong class="text-gray-600">Statut :</strong>
                <span
                    class="px-2 py-1 rounded-full text-xs font-medium {{ $admin->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $admin->is_approved ? 'Actif' : 'En attente' }}
                </span>
            </p>
            <p><strong class="text-gray-600">Fonction :</strong> {{ $admin->function ?? 'Non spécifiée' }}</p>
            <p><strong class="text-gray-600">Dernière connexion :</strong>
                {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Jamais connecté' }}
            </p>
        </div>
    </div>

    @if(auth()->user()->isSuperAdmin())
    <div class="mt-6 pt-4 border-t">
        <form action="{{ route('superadmin.admins.destroy', $admin->id) }}" method="POST"
            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white hover:bg-red-700 rounded text-sm">
                Supprimer cet administrateur
            </button>
        </form>
    </div>
    @endif
</div>
@endsection
