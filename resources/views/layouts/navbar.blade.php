<nav
    class="bg-white/95 backdrop-blur-md shadow-lg border-b border-gray-200/50 dark:bg-gray-900/95 dark:border-gray-700/50 sticky top-0 z-50 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('dashboard.superadmin') }}" class="flex items-center space-x-2 group">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-105">
                        <i class="fas fa-hospital text-white text-lg"></i>
                    </div>
                    <div class="hidden sm:block">
                        <h1
                            class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            Clinique Ibn Rochd
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 -mt-1">Gestion Médicale</p>
                    </div>
                </a>
            </div>

            <!-- Navigation Links - Desktop -->
            <div class="hidden lg:flex items-center space-x-1">
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
            </div>

            <!-- Right side - User menu and dark mode -->
            <div class="flex items-center space-x-3">
                @auth
                <!-- Dark Mode Toggle -->
                <button type="button" onclick="toggleDarkMode()"
                    class="relative p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 group">
                    <span id="darkmode-icon"
                        class="text-lg transition-all duration-300 group-hover:scale-110 group-hover:rotate-12">🌙</span>
                    <span id="darkmode-label"
                        class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-200 whitespace-nowrap shadow-lg">
                        Mode sombre
                    </span>
                </button>

                <!-- Notifications -->
                <button
                    class="relative p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 group">
                    <i
                        class="fas fa-bell text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200"></i>
                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                </button>

                <!-- User Profile -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center space-x-3 p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4f46e5&color=ffffff&size=128' }}"
                            alt="Profile" class="w-8 h-8 rounded-lg object-cover border-2 border-blue-500 shadow-md">
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->role?->name ??
                                'Utilisateur' }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"
                            :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50">

                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center space-x-3">
                                <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=4f46e5&color=ffffff&size=128' }}"
                                    alt="Profile" class="w-10 h-10 rounded-lg object-cover border-2 border-blue-500">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{
                                        Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                                    <span
                                        class="inline-flex items-center px-2 py-1 mt-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                        {{ Auth::user()->role?->name ?? 'Utilisateur' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-1">
                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-user mr-3 text-gray-400"></i>
                                Mon Profil
                            </a>
                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-cog mr-3 text-gray-400"></i>
                                Paramètres
                            </a>
                            <a href="#"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                <i class="fas fa-question-circle mr-3 text-gray-400"></i>
                                Aide
                            </a>
                        </div>

                        <!-- Logout -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-150">
                                    <i class="fas fa-sign-out-alt mr-3"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <!-- Login Link for guests -->
                <a href="{{ route('login') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-sm font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Connexion
                </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="lg:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-bars text-lg" :class="{ 'fa-times': mobileMenuOpen }"></i>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="lg:hidden border-t border-gray-200 dark:border-gray-700 py-4">
            <div class="space-y-2">
                <a href="{{ route('dashboard.superadmin') }}"
                    class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-150">
                    <i class="fas fa-home mr-3 w-5 text-center"></i>Dashboard
                </a>
                <a href="{{ route('superadmin.patients.index') }}"
                    class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-150">
                    <i class="fas fa-users mr-3 w-5 text-center"></i>Patients
                </a>
                <a href="{{ route('superadmin.medecins.index') }}"
                    class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-150">
                    <i class="fas fa-user-md mr-3 w-5 text-center"></i>Médecins
                </a>
                <a href="{{ route('caisses.index') }}"
                    class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-150">
                    <i class="fas fa-cash-register mr-3 w-5 text-center"></i>Caisse
                </a>
                <a href="{{ route('services.index') }}"
                    class="flex items-center px-3 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-150">
                    <i class="fas fa-stethoscope mr-3 w-5 text-center"></i>Services
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- Alpine.js for dropdown functionality -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>