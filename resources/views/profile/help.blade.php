@extends('layouts.app')
@section('title', 'Aide')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Centre d'Aide</h1>
                <p class="text-gray-600 dark:text-gray-300">Trouvez des réponses à vos questions</p>
            </div>
            <a href="{{ route('profile.show') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Retour au Profil
            </a>
        </div>

        <!-- Search -->
        <div class="mb-8">
            <div class="relative">
                <input type="text" placeholder="Rechercher dans l'aide..."
                    class="w-full px-4 py-3 pl-10 pr-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Help Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Getting Started -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-rocket mr-2 text-blue-500"></i>Premiers pas
                </h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Comment créer un nouveau
                            patient ?</a></li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Comment planifier un
                            rendez-vous ?</a></li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Comment créer une facture
                            ?</a></li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Navigation dans
                            l'interface</a></li>
                </ul>
            </div>

            <!-- Patients -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-users mr-2 text-green-500"></i>Gestion des Patients
                </h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Créer un dossier
                            patient</a></li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Modifier les
                            informations</a></li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Rechercher un patient</a>
                    </li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Historique médical</a></li>
                </ul>
            </div>

            <!-- Appointments -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-calendar-alt mr-2 text-yellow-500"></i>Rendez-vous
                </h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Planifier un
                            rendez-vous</a></li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Modifier un rendez-vous</a>
                    </li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Annuler un rendez-vous</a>
                    </li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Notifications</a></li>
                </ul>
            </div>

            <!-- Billing -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-cash-register mr-2 text-purple-500"></i>Facturation
                </h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Créer une facture</a></li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Modes de paiement</a></li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Gestion des assurances</a>
                    </li>
                    <li><a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Rapports financiers</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- FAQ -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                <i class="fas fa-question-circle mr-2 text-red-500"></i>Questions Fréquentes
            </h3>
            <div class="space-y-4">
                <details class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <summary
                        class="font-medium text-gray-900 dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400">
                        Comment réinitialiser mon mot de passe ?
                    </summary>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Vous pouvez réinitialiser votre mot de passe en allant dans Paramètres > Sécurité > Changer le
                        mot de passe.
                    </p>
                </details>
                <details class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <summary
                        class="font-medium text-gray-900 dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400">
                        Comment supprimer un patient ?
                    </summary>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Seuls les superadministrateurs peuvent supprimer des patients. Contactez votre administrateur
                        système.
                    </p>
                </details>
                <details class="border-b border-gray-200 dark:border-gray-700 pb-4">
                    <summary
                        class="font-medium text-gray-900 dark:text-white cursor-pointer hover:text-blue-600 dark:hover:text-blue-400">
                        Comment générer un rapport ?
                    </summary>
                    <p class="mt-2 text-gray-600 dark:text-gray-300">
                        Vous pouvez générer des rapports depuis les sections "Récap. Services" et "Récap. Opérateurs"
                        dans votre tableau de bord.
                    </p>
                </details>
            </div>
        </div>

        <!-- Contact Support -->
        <div class="mt-8 text-center">
            <p class="text-gray-600 dark:text-gray-300 mb-4">Vous ne trouvez pas ce que vous cherchez ?</p>
            <a href="mailto:support@clinique-ibnrochd.com"
                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-envelope mr-2"></i>
                Contacter le Support
            </a>
        </div>
    </div>
</div>
@endsection


















