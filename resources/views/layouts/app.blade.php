<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Gestion des Patients')</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                600: '#4f46e5',
                                700: '#4338ca'
                            },
                            secondary: {
                                200: '#fed7aa',
                                500: '#f97316'
                            }
                        },
                        fontFamily: {
                            'sans': ['Poppins', 'sans-serif']
                        }
                    }
                }
            }
    </script>
    <style>
        .hero-section {
            background-image: linear-gradient(rgba(79, 70, 229, 0.85), rgba(67, 56, 202, 0.9)), url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
