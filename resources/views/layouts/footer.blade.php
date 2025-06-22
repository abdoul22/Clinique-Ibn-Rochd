<footer class="bg-gray-900 text-gray-300 py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section principale --}}
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">

            {{-- Nom / Logo --}}
            <div class="text-lg font-semibold text-white">
                Clinique <br class="mr-2"><strong> Ibn Rochd</strong>
            </div>

            {{-- Liens --}}
            <div class="flex space-x-6 text-sm">
                <a href="#" class="hover:text-white transition">Accueil</a>
                <a href="#" class="hover:text-white transition">À propos</a>
                <a href="#" class="hover:text-white transition">Contact</a>
                <a href="#" class="hover:text-white transition">Mentions légales</a>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="mt-6 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} <strong><br class="mr-2"> Ibn Rochd</strong>. Tous droits réservés.
        </div>
    </div>
</footer>
