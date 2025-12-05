<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestion des Patients')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    {{-- PWA Dynamique --}}
    @php
    $cliniqueConfig = config('clinique');
    $themeColor = $cliniqueConfig['primary_color'] ?? '#1e40af';
    $logoPath = $cliniqueConfig['logo_path'] ?? 'images/logo.png';
    $pwaIcon = $cliniqueConfig['pwa_icon_192'] ?? (file_exists(public_path($logoPath)) ? $logoPath : 'pwa-192x192.png');
    @endphp
    <meta name="theme-color" content="{{ $themeColor }}">
    <link rel="apple-touch-icon" href="{{ asset($pwaIcon) }}">
    <link rel="manifest" href="{{ route('manifest') }}">
    {{-- Script pour initialiser le dark mode immédiatement (anti-FOUC) --}}
    <script>
        // Initialisation immédiate du dark mode pour éviter le FOUC
        (function() {
            const isDark = localStorage.getItem('theme') === 'dark' ||
                          (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // S'assurer que le localStorage a une valeur par défaut
            if (!localStorage.getItem('theme')) {
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            }
        })();
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

        /* Améliorations mobile pour les tableaux */
        @media (max-width: 1024px) {
            .mobile-responsive {
                display: block !important;
            }

            .desktop-only {
                display: none !important;
            }
        }

        @media (min-width: 1025px) {
            .mobile-responsive {
                display: none !important;
            }

            .desktop-only {
                display: block !important;
            }
        }

        /* Améliorer la lisibilité sur mobile */
        @media (max-width: 640px) {
            .mobile-card {
                margin: 0.5rem;
                border-radius: 0.75rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            }
        }

        /* Prévention du scroll horizontal global */
        html,
        body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        /* Tableaux responsive avec scroll horizontal */
        .table-container {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            border-radius: 0.75rem;
            border: 1px solid rgb(229 231 235);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            background: white;
        }

        .dark .table-container {
            border-color: rgb(75 85 99);
            background: rgb(31 41 55);
        }

        .table-main {
            width: 100%;
            min-width: 700px;
            /* Largeur minimale pour éviter l'écrasement */
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .table-main {
                min-width: 800px;
                /* Plus large sur mobile pour forcer le scroll */
                font-size: 0.75rem;
                /* Texte plus petit sur mobile */
            }
        }

        .table-header th {
            background: rgb(249 250 251);
            color: rgb(55 65 81);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgb(229 231 235);
            white-space: nowrap;
        }

        .dark .table-header th {
            background: rgb(55 65 81);
            color: rgb(209 213 219);
            border-bottom-color: rgb(75 85 99);
        }

        .table-body tr {
            border-bottom: 1px solid rgb(243 244 246);
            transition: background-color 0.15s ease;
        }

        .table-body tr:hover {
            background: rgb(249 250 251);
        }

        .dark .table-body tr {
            border-bottom-color: rgb(75 85 99);
        }

        .dark .table-body tr:hover {
            background: rgb(55 65 81);
        }

        .table-cell {
            padding: 0.75rem;
            color: rgb(55 65 81);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .dark .table-cell {
            color: rgb(209 213 219);
        }

        .table-cell-medium {
            padding: 0.75rem;
            color: rgb(55 65 81);
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .dark .table-cell-medium {
            color: rgb(209 213 219);
        }

        .table-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            white-space: nowrap;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            transition: all 0.15s ease;
            border: none;
            cursor: pointer;
        }

        .action-btn-view {
            background: rgb(239 246 255);
            color: rgb(37 99 235);
        }

        .action-btn-view:hover {
            background: rgb(219 234 254);
            color: rgb(29 78 216);
        }

        .action-btn-edit {
            background: rgb(238 242 255);
            color: rgb(79 70 229);
        }

        .action-btn-edit:hover {
            background: rgb(224 231 255);
            color: rgb(67 56 202);
        }

        .action-btn-delete {
            background: rgb(254 242 242);
            color: rgb(220 38 38);
        }

        .action-btn-delete:hover {
            background: rgb(252 226 226);
            color: rgb(185 28 28);
        }

        .dark .action-btn-view {
            background: rgb(30 58 138);
            color: rgb(96 165 250);
        }

        .dark .action-btn-edit {
            background: rgb(55 48 163);
            color: rgb(129 140 248);
        }

        .dark .action-btn-delete {
            background: rgb(153 27 27);
            color: rgb(248 113 113);
        }

        /* Indicateur de scroll léger pour mobile */
        @media (max-width: 768px) {
            .table-container::after {
                content: "⟷";
                position: sticky;
                left: 50%;
                bottom: 0.5rem;
                transform: translateX(-50%);
                background: rgba(59 130 246 / 0.8);
                color: white;
                padding: 0.25rem 0.5rem;
                border-radius: 1rem;
                font-size: 0.75rem;
                font-weight: 600;
                text-align: center;
                z-index: 10;
                width: fit-content;
                margin: 0 auto;
                pointer-events: none;
            }

            .dark .table-container::after {
                background: rgba(147 197 253 / 0.8);
                color: rgb(30 41 59);
            }
        }

        /* Styles pour les formulaires responsives */
        .form-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: linear-gradient(to right, rgb(99 102 241), rgb(79 70 229));
            color: white;
            font-weight: 500;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            text-decoration: none;
        }

        .form-button:hover {
            background: linear-gradient(to right, rgb(79 70 229), rgb(67 56 202));
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99 102 241, 0.4);
        }

        .form-button-secondary {
            background: rgb(107 114 128);
            color: white;
        }

        .form-button-secondary:hover {
            background: rgb(75 85 99);
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.625rem 0.75rem;
            border: 1px solid rgb(209 213 219);
            border-radius: 0.5rem;
            background: white;
            color: rgb(31 41 55);
            font-size: 0.875rem;
            transition: all 0.15s ease;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: rgb(99 102 241);
            box-shadow: 0 0 0 3px rgba(99 102 241, 0.1);
        }

        .dark .form-input,
        .dark .form-select {
            background: rgb(55 65 81);
            border-color: rgb(75 85 99);
            color: rgb(243 244 246);
        }

        .dark .form-input:focus,
        .dark .form-select:focus {
            border-color: rgb(129 140 248);
        }

        /* Page title responsive */
        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: rgb(31 41 55);
            margin-bottom: 1.5rem;
        }

        .dark .page-title {
            color: rgb(243 244 246);
        }

        @media (max-width: 640px) {
            .page-title {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
        }

        /* Alertes responsives */
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background: rgb(240 253 244);
            border: 1px solid rgb(34 197 94);
            color: rgb(21 128 61);
        }

        .dark .alert-success {
            background: rgb(20 83 45);
            border-color: rgb(34 197 94);
            color: rgb(134 239 172);
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

        /* Style pour x-cloak (masquer les éléments Alpine.js avant initialisation) */
        [x-cloak] {
            display: none !important;
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
    <script src="{{ asset('js/form-protection.js') }}"></script>
    @stack('scripts')
    @push('scripts')

    @endpush
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/navbar-fixes.js') }}" defer></script>
</body>

</html>
