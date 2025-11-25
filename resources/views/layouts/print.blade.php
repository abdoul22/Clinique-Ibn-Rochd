<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Document') - {{ config('clinique.name', 'CLINIQUE IBN ROCHD') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                margin: 0;
                size: auto;
            }

            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background: white !important;
            }

            /* Classes utilitaires pour l'impression */
            .no-print {
                display: none !important;
            }

            .page-break {
                page-break-after: always;
            }

            /* Configuration A4 */
            .format-a4 {
                width: 210mm;
                min-height: 297mm;
                padding: 0;
                /* Padding géré à l'intérieur pour permettre le header full-width */
                margin: 0 auto;
                background: white;
            }

            /* Configuration A5 */
            .format-a5 {
                width: 148mm;
                min-height: 210mm;
                padding: 0;
                /* Padding géré à l'intérieur */
                margin: 0 auto;
                background: white;
            }
        }

        /* Styles pour la visualisation à l'écran */
        body {
            background-color: #f3f4f6;
            min-height: 100vh;
        }

        .print-container {
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            transition: all 0.3s ease;
            overflow: hidden;
            /* Pour le header arrondi ou full-width */
        }

        .format-a4 {
            width: 210mm;
            min-height: 297mm;
        }

        .format-a5 {
            width: 148mm;
            min-height: 210mm;
        }

        /* Contenu interne avec padding */
        .content-wrapper {
            padding: 15mm;
        }

        .format-a5 .content-wrapper {
            padding: 10mm;
        }
    </style>
    @stack('styles')
</head>

<body class="antialiased text-gray-900">

    <!-- Boutons d'action (ne s'impriment pas) -->
    <div class="no-print fixed top-4 right-4 flex gap-2 z-50">
        <!-- Sélecteur de format -->
        <div class="bg-white rounded shadow flex overflow-hidden mr-2">
            <button onclick="setFormat('a5')" id="btn-a5"
                class="px-4 py-2 text-sm font-medium transition-colors hover:bg-gray-100 {{ ($formatClass ?? 'format-a5') === 'format-a5' ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }}">
                A5
            </button>
            <button onclick="setFormat('a4')" id="btn-a4"
                class="px-4 py-2 text-sm font-medium transition-colors hover:bg-gray-100 {{ ($formatClass ?? 'format-a5') === 'format-a4' ? 'bg-blue-100 text-blue-700' : 'text-gray-600' }}">
                A4
            </button>
        </div>

        <button onclick="window.print()"
            class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Imprimer
        </button>
        <button onclick="history.back()"
            class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600 font-medium">
            Retour
        </button>
    </div>

    <!-- Conteneur principal -->
    <div id="print-sheet" class="print-container {{ $formatClass ?? 'format-a5' }}">

        @if(View::hasSection('custom-header'))
        <!-- En-tête Personnalisé (ex: Bloc Bleu Facture) -->
        @yield('custom-header')
        @else
        <!-- En-tête Standard Noir & Blanc -->
        <div class="px-8 pt-8 pb-4 border-b-2 border-gray-800 flex justify-between items-center mx-auto">
            <div class="flex items-center gap-4">
                @if(config('clinique.logo_path'))
                <img src="{{ asset(config('clinique.logo_path')) }}" alt="Logo" class="h-16 w-auto object-contain">
                @endif

                <div>
                    <h1 class="text-2xl font-bold text-gray-900 uppercase tracking-wider"
                        style="color: {{ config('clinique.primary_color', '#1f2937') }}">
                        {{ config('clinique.name', 'CLINIQUE IBN ROCHD') }}
                    </h1>
                    <p class="text-sm text-gray-600">{{ config('clinique.address', 'Nouakchott, Mauritanie') }}</p>
                    <p class="text-sm text-gray-600">{{ config('clinique.phone') }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Date</p>
                <p class="font-semibold">{{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        @endif

        <!-- Contenu avec Padding interne -->
        <div class="content-wrapper">
            <main>
                @yield('content')
            </main>

            <!-- Pied de page Standard (Remplaçable par section footer) -->
            @hasSection('footer')
            @yield('footer')
            @else
            <footer class="mt-8 pt-4 border-t border-gray-300 text-center text-xs text-gray-500">
                <p>Merci pour votre visite. </p>

            </footer>
            @endif
        </div>

    </div>

    <script>
        function setFormat(format) {
            const sheet = document.getElementById('print-sheet');
            const btnA4 = document.getElementById('btn-a4');
            const btnA5 = document.getElementById('btn-a5');

            if (format === 'a4') {
                sheet.classList.remove('format-a5');
                sheet.classList.add('format-a4');

                btnA4.classList.add('bg-blue-100', 'text-blue-700');
                btnA4.classList.remove('text-gray-600');
                btnA5.classList.remove('bg-blue-100', 'text-blue-700');
                btnA5.classList.add('text-gray-600');
            } else {
                sheet.classList.remove('format-a4');
                sheet.classList.add('format-a5');

                btnA5.classList.add('bg-blue-100', 'text-blue-700');
                btnA5.classList.remove('text-gray-600');
                btnA4.classList.remove('bg-blue-100', 'text-blue-700');
                btnA4.classList.add('text-gray-600');
            }
        }
    </script>
</body>

</html>
