<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Gestion des Patients')</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- ✅ Pour une meilleure animation --}}
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

<body class="bg-gray-100 text-gray-800 flex flex-col min-h-screen">

    {{-- ✅ Navbar --}}
    @include('layouts.navbar')

    {{-- ✅ Contenu principal --}}
    <main class="container mx-auto px-4 py-6 flex-grow fade-in">

        {{-- ✅ Messages flash --}}
        @foreach (['success' => 'green', 'update' => 'blue', 'delete' => 'red'] as $key => $color)
        @if(session($key))
        <div class="bg-{{ $color }}-100 text-{{ $color }}-800 border border-{{ $color }}-200 rounded p-3 mb-4">
            {{ session($key) }}
        </div>
        @endif
        @endforeach

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
</body>

</html>
