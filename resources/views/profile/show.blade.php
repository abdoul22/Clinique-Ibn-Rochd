@extends('layouts.app')
@section('title', 'Mon Profil')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Mon Profil</h1>
            <p class="text-gray-600 dark:text-gray-300">Gérez vos informations personnelles</p>
        </div>

        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center space-x-6 mb-6">
                <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4f46e5&color=ffffff&size=128' }}"
                    alt="Profile" class="w-24 h-24 rounded-full object-cover border-4 border-blue-500">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-300">{{ Auth::user()->email }}</p>
                    <span
                        class="inline-flex items-center px-3 py-1 mt-2 text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                        {{ Auth::user()->role?->name ?? 'Utilisateur' }}
                    </span>
                </div>
            </div>

            <!-- Profile Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nom complet</label>
                    <p class="text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <p class="text-gray-900 dark:text-white">{{ Auth::user()->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rôle</label>
                    <p class="text-gray-900 dark:text-white">{{ Auth::user()->role?->name ?? 'Utilisateur' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Membre depuis</label>
                    <p class="text-gray-900 dark:text-white">{{ Auth::user()->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex space-x-4">
            <a href="{{ route('profile.settings') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center transition-colors">
                <i class="fas fa-cog mr-2"></i>
                Paramètres
            </a>
            <a href="{{ route('profile.help') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium flex items-center transition-colors">
                <i class="fas fa-question-circle mr-2"></i>
                Aide
            </a>
        </div>
    </div>
</div>
@endsection








