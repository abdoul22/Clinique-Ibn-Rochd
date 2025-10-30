@extends('layouts.app')
@section('title', 'Situation Journalière')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Situation Journalière</h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">
                <span class="font-semibold capitalize">{{ $dateCarbon->locale('fr_FR')->dayName }}</span>,
                {{ $dateCarbon->format('d/m/Y') }}
            </p>
        </div>

        <!-- Date Filter & Buttons -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8 border border-gray-200 dark:border-gray-700">
            <form method="GET"
                action="{{ route(auth()->user()->role->name === 'superadmin' ? 'superadmin.situation-journaliere.index' : 'admin.situation-journaliere.index') }}"
                class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[250px]">
                    <label for="date" class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">
                        Sélectionner une date
                    </label>
                    <input type="date" name="date" id="date" value="{{ $date }}"
                        class="w-full px-4 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 dark:focus:ring-violet-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-gradient-to-r from-violet-500 to-purple-600 text-white rounded-lg hover:shadow-lg hover:from-violet-600 hover:to-purple-700 transition-all font-semibold shadow-md">
                    🔍 Filtrer
                </button>
                <button type="button" onclick="window.print()"
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:shadow-lg hover:from-blue-700 hover:to-blue-800 transition-all font-semibold">
                    🖨️ Imprimer
                </button>
            </form>
        </div>

        <!-- Services Tables -->
        @if(empty($servicesData))
        <div
            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6 text-center">
            <p class="text-yellow-800 dark:text-yellow-200">Aucune transaction enregistrée pour cette date</p>
        </div>
        @else
        <div class="space-y-8">
            @foreach($servicesData as $serviceId => $service)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <!-- Service Title -->
                <div class="bg-gradient-to-r from-violet-500 to-purple-600 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white">{{ strtoupper($service['service_name']) }}</h2>
                </div>

                <!-- Service Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                <th class="px-6 py-3 text-left font-semibold text-gray-800 dark:text-gray-200">Médecin
                                </th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-800 dark:text-gray-200">Examen
                                </th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-800 dark:text-gray-200">Nbre
                                    d'actes</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-800 dark:text-gray-200">
                                    Recettes</th>
                                <th class="px-6 py-3 text-center font-semibold text-gray-800 dark:text-gray-200">Part
                                    Médecin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($service['medecins'] as $medecin)
                            <tr
                                class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $medecin['nom'] }}
                                </td>
                                <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                                    @foreach($medecin['examens'] as $examen => $count)
                                    <div class="text-sm">
                                        {{ $examen }} ({{ $count }})
                                    </div>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-center text-gray-800 dark:text-gray-200 font-semibold">{{
                                    $medecin['nombre_actes'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-right bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-right bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr
                                class="bg-violet-50 dark:bg-violet-900/20 border-t-2 border-violet-300 dark:border-violet-700 font-bold">
                                <td colspan="2" class="px-6 py-4 text-gray-900 dark:text-white">TOTAL {{
                                    strtoupper($service['service_name']) }}</td>
                                <td class="px-6 py-4 text-center text-gray-900 dark:text-white">{{
                                    $service['total_actes'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded font-bold text-right bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded font-bold text-right bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endforeach

            <!-- Summary Tables -->
            <div class="space-y-6">
                <!-- Additional Sections Grid - FIRST (Dynamic) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Paiement en Ligne (Only if exists) -->
                    @if($hasOnlinePayments)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <h3
                            class="font-bold text-gray-800 dark:text-white mb-3 text-center border-b border-gray-300 dark:border-gray-600 pb-2">
                            PAIEMENT EN LIGNE</h3>
                        <div class="space-y-2">
                            @if($paiementsEnLigne->has('bankily'))
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">BANKILY</span>
                                <input type="text"
                                    class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-right text-sm bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                            </div>
                            @endif
                            @if($paiementsEnLigne->has('masrvi'))
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">MASRVI</span>
                                <input type="text"
                                    class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-right text-sm bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                            </div>
                            @endif
                            @if($paiementsEnLigne->has('sedad'))
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">SEDAD</span>
                                <input type="text"
                                    class="w-24 px-2 py-1 border border-gray-300 dark:border-gray-600 rounded text-right text-sm bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                            </div>
                            @endif
                            <div
                                class="flex justify-between items-center pt-2 border-t border-gray-300 dark:border-gray-600">
                                <span class="text-sm font-bold text-gray-800 dark:text-white">TOTAL</span>
                                <input type="text"
                                    class="w-24 px-2 py-1 border-2 border-gray-400 dark:border-gray-500 rounded text-right text-sm font-bold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Crédit Assurance (Only if exists) -->
                    @if($creditsAssurance > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <h3
                            class="font-bold text-gray-800 dark:text-white mb-3 text-center border-b border-gray-300 dark:border-gray-600 pb-2">
                            CRÉDIT ASSURANCE</h3>
                        <div class="flex justify-center items-center h-20">
                            <input type="text"
                                class="w-32 px-3 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-bold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    @endif

                    <!-- Crédit Personnel (Only if exists) -->
                    @if($creditsPersonnel > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <h3
                            class="font-bold text-gray-800 dark:text-white mb-3 text-center border-b border-gray-300 dark:border-gray-600 pb-2">
                            CRÉDIT PERSONNEL</h3>
                        <div class="flex justify-center items-center h-20">
                            <input type="text"
                                class="w-32 px-3 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-bold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    @endif

                    <!-- Dépense (Always visible) -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <h3
                            class="font-bold text-gray-800 dark:text-white mb-3 text-center border-b border-gray-300 dark:border-gray-600 pb-2">
                            DÉPENSE</h3>
                        <div class="flex justify-center items-center h-20">
                            <input type="text"
                                class="w-32 px-3 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-bold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Main Summary Table - SECOND -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-violet-600 dark:bg-violet-700">
                                <th class="px-4 py-3 text-center font-bold text-white uppercase">RECETTES</th>
                                @if($totalPartMedecin > 0)
                                <th class="px-4 py-3 text-center font-bold text-white uppercase">PART MÉDECIN</th>
                                @endif
                                <th class="px-4 py-3 text-center font-bold text-white uppercase">DEPENCES</th>
                                <th class="px-4 py-3 text-center font-bold text-white uppercase">CREDIT</th>
                                @if($hasOnlinePayments)
                                <th class="px-4 py-3 text-center font-bold text-white uppercase">PAIEMENT EN LIGNE</th>
                                @endif
                                <th class="px-4 py-3 text-center font-bold text-white uppercase">RESTANT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-semibold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                                @if($totalPartMedecin > 0)
                                <td class="px-4 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-semibold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                                @endif
                                <td class="px-4 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-semibold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-semibold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                                @if($hasOnlinePayments)
                                <td class="px-4 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-semibold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                                @endif
                                <td class="px-4 py-4 text-center">
                                    <input type="text"
                                        class="w-24 px-2 py-2 border-2 border-gray-400 dark:border-gray-500 rounded text-center font-semibold bg-white text-gray-900 dark:bg-gray-700 dark:text-white">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    @media print {

        /* Hide header and footer of the application */
        header,
        nav,
        footer,
        .navbar,
        .header,
        .sidebar,
        [role="navigation"] {
            display: none !important;
        }

        /* Force white background and black text for printing */
        body,
        body * {
            background-color: white !important;
            color: #000 !important;
        }

        /* Hide dark mode backgrounds */
        .dark\:bg-gray-900,
        .dark\:bg-gray-800,
        .dark\:bg-gray-700,
        .dark\:bg-violet-900 {
            background-color: white !important;
        }

        /* Force black text */
        .dark\:text-white,
        .dark\:text-gray-200,
        .dark\:text-gray-300 {
            color: #000 !important;
        }

        /* Fix borders */
        .dark\:border-gray-600,
        .dark\:border-gray-700 {
            border-color: #ddd !important;
        }

        /* Hide date input and buttons */
        input[type="date"],
        button,
        .no-print {
            display: none !important;
        }

        /* Style text inputs for printing */
        input[type="text"] {
            border: none !important;
            border-bottom: 1px solid #333 !important;
            background-color: transparent !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Keep gradient headers visible */
        .bg-gradient-to-r,
        .bg-gradient-to-br {
            background: #8b5cf6 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Fix highlighted fields */
        .bg-violet-100 {
            background-color: #f3e8ff !important;
        }

        .min-h-screen {
            min-height: auto !important;
        }

        /* Remove page margins */
        @page {
            margin: 10mm;
        }
    }
</style>
@endsection
