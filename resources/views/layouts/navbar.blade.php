<nav
    class="bg-white/95 backdrop-blur-md shadow-lg border-b border-gray-200/50 dark:bg-gray-900/95 dark:border-gray-700/50 sticky top-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
        <div class="flex justify-between items-center h-14 sm:h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                @auth
                    @php
                        $userRole = Auth::user()->role?->name;
                        $dashboardRoute = $userRole === 'superadmin' ? 'dashboard.superadmin' : ($userRole === 'admin' ? 'dashboard.admin' : ($userRole === 'medecin' ? 'medecin.dashboard' : 'dashboard.superadmin'));
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="flex items-center space-x-2 group">
                @else
                    <a href="{{ route('login') }}" class="flex items-center space-x-2 group">
                @endauth
                    <div class="my-1 sm:my-2">
                        <img src="{{ asset(config('clinique.logo_path')) }}" alt="Logo {{ config('clinique.name') }}"
                            class="h-12 w-12 p-1 sm:h-20 sm:w-20 sm:p-2">
                    </div>
                    <div class="block">
                        <h1
                            class="text-base sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            {{ config('clinique.name') }}
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 -mt-1">Gestion Médicale</p>
                    </div>
                </a>
            </div>

            <!-- Navigation Links - Desktop -->
            <div class="hidden lg:flex items-center space-x-1">
                @auth
                @if(Auth::user()->role?->name === 'superadmin')
                <a href="{{ route('dashboard.superadmin') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-home mr-2"></i>Dashboard
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('superadmin.patients.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-users mr-2"></i>Patients
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('superadmin.medecins.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-user-md mr-2"></i>Médecins
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('caisses.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-cash-register mr-2"></i>Caisse
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('services.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-stethoscope mr-2"></i>Services
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('pharmacie.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-pills mr-2"></i>Pharmacie
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('rendezvous.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-calendar-alt mr-2"></i>Rendez-vous
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                @elseif(Auth::user()->role?->name === 'admin')
                <a href="{{ route('dashboard.admin') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-home mr-2"></i>Dashboard
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('admin.patients.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-users mr-2"></i>Patients
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('admin.caisses.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-cash-register mr-2"></i>Caisse
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('admin.rendezvous.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-calendar-alt mr-2"></i>Rendez-vous
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                @elseif(Auth::user()->role?->name === 'medecin')
                <a href="{{ route('medecin.dashboard') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-home mr-2"></i>Dashboard
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('medecin.consultations.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-notes-medical mr-2"></i>Rapports
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                <a href="{{ route('medecin.ordonnances.index') }}"
                    class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 relative group">
                    <i class="fas fa-prescription mr-2"></i>Ordonnances
                    <div
                        class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all duration-300">
                    </div>
                </a>
                @endif
                @endauth
            </div>

            <!-- Right side - User menu and dark mode -->
            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Dark Mode Toggle (inspiré de Laravel Breeze, version Alpine.js) -->
                <button x-data="{
                        dark: (localStorage.getItem('theme') === 'dark') || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
                        init() {
                            // S'assurer que l'état initial est correct
                            this.dark = (localStorage.getItem('theme') === 'dark') || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
                            document.documentElement.classList.toggle('dark', this.dark);
                        }
                    }" @click="
                        dark = !dark;
                        document.documentElement.classList.toggle('dark', dark);
                        localStorage.setItem('theme', dark ? 'dark' : 'light');
                        // Émettre un événement pour notifier les autres scripts
                        document.dispatchEvent(new CustomEvent('darkModeChanged', { detail: { isDark: dark } }));
                    "
                    class="relative p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 group focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <!-- Sun icon -->
                    <svg x-show="!dark" x-cloak
                        class="w-5 h-5 text-gray-900 transition-all duration-300 group-hover:scale-110"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                            clip-rule="evenodd" />
                    </svg>
                    <!-- Moon icon -->
                    <svg x-show="dark" x-cloak
                        class="w-5 h-5 text-gray-100 transition-all duration-300 group-hover:scale-110"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                </button>
                @auth

                <!-- User Profile -->
                <div class="relative" x-data="{
                    open: false,
                    init() {
                        // Fermer le menu lors de la navigation
                        document.addEventListener('click', (e) => {
                            const link = e.target.closest('a');
                            if (link && link.href && !link.href.includes('#')) {
                                this.open = false;
                            }
                        });

                        // Fermer le menu lors des soumissions de formulaire
                        document.addEventListener('submit', () => {
                            this.open = false;
                        });
                    }
                }">
                    <button @click="open = !open"
                        class="flex items-center space-x-2 sm:space-x-3 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4f46e5&color=ffffff&size=128' }}"
                            alt="Profile"
                            class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg object-cover border-2 border-blue-500 shadow-md">
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->role?->name ??
                                'Utilisateur' }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': open }"></i>
                    </button>
                    <!-- Dropdown Menu (mobile inclus) -->
                    <div x-show="open" x-cloak @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-72 sm:w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50 max-h-96 overflow-y-auto">
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4f46e5&color=ffffff&size=128' }}"
                                    alt="Profile" class="w-12 h-12 rounded-lg object-cover border-2 border-blue-500">
                                <div>
                                    <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{
                                        Auth::user()->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                                    <span
                                        class="inline-flex items-center px-2 py-1 mt-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                        {{ Auth::user()->role?->name ?? 'Utilisateur' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- Menu Items (liens principaux en mobile inclus ici) -->
                        <div class="py-1">
                            @if(Auth::user()->role?->name === 'superadmin')
                            <a href="{{ route('dashboard.superadmin') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-home mr-3 text-gray-400 w-5"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('superadmin.patients.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-users mr-3 text-gray-400 w-5"></i>
                                Patients
                            </a>
                            <a href="{{ route('superadmin.medecins.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-user-md mr-3 text-gray-400 w-5"></i>
                                Médecins
                            </a>
                            <a href="{{ route('caisses.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-cash-register mr-3 text-gray-400 w-5"></i>
                                Caisse
                            </a>
                            <a href="{{ route('rendezvous.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-calendar-alt mr-3 text-gray-400 w-5"></i>
                                Rendez-vous
                            </a>
                            <a href="{{ route('motifs.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-list-alt mr-3 text-gray-400 w-5"></i>
                                Motifs de consultation
                            </a>
                            <a href="{{ route('services.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-stethoscope mr-3 text-gray-400 w-5"></i>
                                Services
                            </a>
                            <a href="{{ route('pharmacie.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-pills mr-3 text-gray-400 w-5"></i>
                                Pharmacie
                            </a>
                            @elseif(Auth::user()->role?->name === 'admin')
                            <a href="{{ route('dashboard.admin') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-home mr-3 text-gray-400 w-5"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.patients.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-users mr-3 text-gray-400 w-5"></i>
                                Patients
                            </a>
                            <a href="{{ route('admin.caisses.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-cash-register mr-3 text-gray-400 w-5"></i>
                                Caisse
                            </a>
                            <a href="{{ route('admin.rendezvous.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-calendar-alt mr-3 text-gray-400 w-5"></i>
                                Rendez-vous
                            </a>
                            @elseif(Auth::user()->role?->name === 'medecin')
                            <a href="{{ route('medecin.dashboard') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-home mr-3 text-gray-400 w-5"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('medecin.consultations.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-notes-medical mr-3 text-gray-400 w-5"></i>
                                Rapports Médicaux
                            </a>
                            <a href="{{ route('medecin.ordonnances.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-prescription mr-3 text-gray-400 w-5"></i>
                                Ordonnances
                            </a>
                            <a href="{{ route('medecin.patients.index') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-users mr-3 text-gray-400 w-5"></i>
                                Patients
                            </a>
                            @endif
                            <a href="{{ route('profile.show') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-user mr-3 text-gray-400 w-5"></i>
                                Mon Profil
                            </a>
                            <a href="{{ route('profile.settings') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-cog mr-3 text-gray-400 w-5"></i>
                                Paramètres
                            </a>
                            <a href="{{ route('profile.help') }}"
                                class="flex items-center px-4 py-3 text-base text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-question-circle mr-3 text-gray-400 w-5"></i>
                                Aide
                            </a>
                        </div>
                        <!-- Logout -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-3 text-base text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150">
                                    <i class="fas fa-sign-out-alt mr-3 w-5"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <!-- Login Link for guests -->
                <a href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Connexion
                </a>
                @endauth
            </div>
        </div>
    </div>
</nav>