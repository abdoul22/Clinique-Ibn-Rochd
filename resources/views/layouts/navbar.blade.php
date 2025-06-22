<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <a href="{{ route('dashboard.superadmin') }}" class="text-xl font-bold text-blue-600">Clinique <br class="mr-2"><strong> Ibn Rochd</strong></a>

        @auth
        <!-- Profil avec dropdown -->
        <div class="relative">
            <button onclick="document.getElementById('profile-dropdown').classList.toggle('hidden')"
                class="focus:outline-none">
                <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                    alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-blue-500">
            </button>

            <!-- Dropdown -->
            <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded shadow-lg z-50">
                <div class="px-4 py-2 text-gray-700 border-b">{{ Auth::user()->name }}</div>
                <form action="{{ route('logout') }}" method="POST" class="px-4 py-2">
                    @csrf
                    <button type="submit" class="w-full text-left text-red-500 hover:underline">Déconnexion</button>
                </form>
            </div>
        </div>
        @else
        <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Connexion</a>
        @endauth
    </div>
</nav>
