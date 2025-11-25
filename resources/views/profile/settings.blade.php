@extends('layouts.app')
@section('title', 'Paramètres')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Paramètres</h1>
                <p class="text-gray-600 dark:text-gray-300">Configurez vos préférences</p>
            </div>
            <a href="{{ route('profile.show') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Retour au Profil
            </a>
        </div>

        <!-- Settings Cards -->
        <div class="space-y-6">
            <!-- Theme Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-palette mr-2"></i>Thème
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mb-4">Choisissez votre thème préféré</p>
                <div class="flex space-x-4">
                    <button onclick="setTheme('light')"
                        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors">
                        <i class="fas fa-sun mr-2"></i>Clair
                    </button>
                    <button onclick="setTheme('dark')"
                        class="flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-moon mr-2"></i>Sombre
                    </button>
                    <button onclick="setTheme('auto')"
                        class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-adjust mr-2"></i>Auto
                    </button>
                </div>
            </div>

            <!-- Notifications Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-bell mr-2"></i>Notifications
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">Notifications de rendez-vous</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Recevoir des notifications pour les
                                nouveaux rendez-vous</p>
                        </div>
                        <input type="checkbox" checked class="toggle-checkbox">
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-900 dark:text-white font-medium">Notifications de facturation</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Recevoir des notifications pour les
                                nouvelles factures</p>
                        </div>
                        <input type="checkbox" checked class="toggle-checkbox">
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-shield-alt mr-2"></i>Sécurité
                </h3>
                <div class="space-y-4">
                    <a href="#"
                        class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-900 dark:text-white font-medium">Changer le mot de passe</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Dernière modification il y a 30
                                    jours</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                    </a>
                    <a href="#"
                        class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-900 dark:text-white font-medium">Sessions actives</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Gérer vos sessions de connexion</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setTheme(theme) {
    if (theme === 'auto') {
        localStorage.removeItem('theme');
        const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', isDark);
    } else {
        localStorage.setItem('theme', theme);
        document.documentElement.classList.toggle('dark', theme === 'dark');
    }
}
</script>
@endsection
































