<footer class="bg-white border-t py-4 text-center text-gray-600 dark:bg-gray-800 dark:text-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section principale --}}
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">

            {{-- Nom / Logo --}}
            <div class="text-lg font-semibold text-gray-800 dark:text-white">
                Clinique <br class="mr-2"><strong> Ibn Rochd</strong>
            </div>

            {{-- Liens (superadmin uniquement) --}}
            @auth
            @if(Auth::user()->role?->name === 'superadmin')
            <div class="flex space-x-6 text-sm">
                <a href="#" class="hover:text-blue-600 dark:hover:text-blue-300 transition">Accueil</a>
                <a href="#" class="hover:text-blue-600 dark:hover:text-blue-300 transition">À propos</a>
                <a href="#" class="hover:text-blue-600 dark:hover:text-blue-300 transition">Contact</a>
                <a href="#" class="hover:text-blue-600 dark:hover:text-blue-300 transition">Mentions légales</a>
            </div>
            @endif
            @endauth
        </div>

        {{-- Copyright --}}
        <div class="mt-6 text-center text-xs text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} <strong><br class="mr-2"> Ibn Rochd</strong>. Tous droits réservés.
        </div>
    </div>
</footer>