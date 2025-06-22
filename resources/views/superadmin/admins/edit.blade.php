@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Modifier l'Administrateur</h2>
        <a href="{{ route('superadmin.admins.index') }}" class="text-sm text-blue-600 hover:underline">← Retour à la
            liste</a>
    </div>

    <form method="POST" action="{{ route('superadmin.admins.update', $admin->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Colonne de gauche -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>
            </div>

            <!-- Colonne de droite -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fonction</label>
                    <select name="function"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionnez une fonction</option>
                        <option value="Gestionnaire" {{ old('function', $admin->function) == 'Gestionnaire' ? 'selected'
                            : '' }}>Gestionnaire</option>
                        <option value="Responsable" {{ old('function', $admin->function) == 'Responsable' ? 'selected' :
                            '' }}>Responsable</option>
                        <option value="Technique" {{ old('function', $admin->function) == 'Technique' ? 'selected' : ''
                            }}>Technique</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_approved" id="is_approved"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $admin->is_approved
                    ? 'checked' : '' }}>
                    <label for="is_approved" class="ml-2 block text-sm text-gray-700">
                        Compte activé
                    </label>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t flex justify-end space-x-3">
            <a href="{{ route('superadmin.admins.index') }}"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
