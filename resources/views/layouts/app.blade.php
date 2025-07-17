<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Gestion des Patients')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    {{-- Script pour initialiser le dark mode immédiatement (anti-FOUC) --}}
    <script>
        if (
            localStorage.getItem('theme') === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    {{-- Vite CSS/JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html,
        body {
            font-family: 'Poppins', Arial, sans-serif !important;
        }

        .hero-section {
            background-image: linear-gradient(rgba(79, 70, 229, 0.85), rgba(67, 56, 202, 0.9)), url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        @media (max-width: 640px) {
            .hero-section {
                background-attachment: scroll;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
    </style>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-in-out;
        }
    </style>
</head>

<body
    class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100 flex flex-col min-h-screen text-base sm:text-lg">

    {{-- ✅ Navbar --}}
    @include('layouts.navbar')

    {{-- ✅ Contenu principal --}}
    <main
        class="container w-full max-w-7xl mx-auto px-2 py-4 sm:px-4 sm:py-6 flex-grow fade-in dark:bg-gray-900 dark:text-gray-100">

        {{-- ✅ Messages flash --}}
        @foreach (['success' => 'green', 'update' => 'blue', 'delete' => 'red'] as $key => $color)
        @if(session($key))
        <div
            class="bg-{{ $color }}-100 text-{{ $color }}-800 border border-{{ $color }}-200 rounded p-3 mb-4 dark:bg-{{ $color }}-900/20 dark:text-{{ $color }}-200 dark:border-{{ $color }}-800">
            {{ session($key) }}
        </div>
        @endif
        @endforeach

        {{-- ✅ Erreurs de validation et session --}}
        @if($errors->any())
        <div class="alert alert-error mb-4">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-error mb-4">
            <ul class="list-disc list-inside text-sm">
                <li>{{ session('error') }}</li>
            </ul>
        </div>
        @endif

        {{-- ✅ Contenu injecté --}}
        @yield('content')
    </main>

    {{-- ✅ Footer --}}
    @include('layouts.footer')

    {{-- ✅ Scripts éventuels --}}
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
    @push('scripts')

    @endpush
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>
