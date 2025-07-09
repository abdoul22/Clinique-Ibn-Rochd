<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture #{{ $caisse->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0.5cm;
            }

            body {
                font-size: 10px;
                line-height: 1.2;
            }

            .no-print {
                display: none !important;
            }

            .print-button {
                display: none !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background: white;
            font-size: 10px;
            line-height: 1.2;
        }

        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 8px 16px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            z-index: 1000;
        }

        .print-button:hover {
            background: #059669;
        }

        .compact-header {
            padding: 15px !important;
        }

        .compact-header h1 {
            font-size: 18px !important;
        }

        .compact-header p {
            font-size: 11px !important;
            margin: 2px 0 !important;
        }

        .compact-content {
            padding: 15px !important;
        }

        .compact-title {
            font-size: 14px !important;
            margin-bottom: 8px !important;
        }

        .compact-text {
            font-size: 10px !important;
            margin: 3px 0 !important;
        }

        .compact-section {
            margin-bottom: 10px !important;
        }

        .compact-grid {
            gap: 10px !important;
        }

        .compact-box {
            padding: 8px !important;
        }

        .compact-box h3 {
            font-size: 12px !important;
            margin-bottom: 5px !important;
            padding-bottom: 3px !important;
        }

        .compact-total {
            font-size: 16px !important;
        }

        .compact-footer {
            padding: 8px !important;
            font-size: 9px !important;
        }
    </style>
</head>

<body>
    <button class="print-button no-print" onclick="window.print()">Imprimer</button>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- En-tête de la clinique -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white compact-header text-center">
            <div class="flex justify-center mb-2">
                <div class="rounded-xl w-12 h-12">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Clinique" class="w-full h-full object-cover">
                </div>
            </div>
            <h1 class="compact-title font-bold">{{ config('app.name', 'Clinique Médicale') }}</h1>
            <p class="compact-text">Reçu d'examen médical</p>
            <p class="compact-text opacity-80">Date d'émission: {{ now()->format('d/m/Y H:i') }}</p>
        </div>

        <!-- Corps du document -->
        <div class="compact-content">
            <div class="flex flex-col md:flex-row justify-between items-start compact-section gap-4">
                <div>
                    <h2 class="compact-title font-semibold text-gray-900">Facture N° {{ $caisse->numero_facture ??
                        $caisse->id }}</h2>
                    <h2 class="compact-title font-semibold text-gray-900">Informations de l'examen</h2>
                    <p class="compact-text text-gray-800"><span class="font-medium">N° d'entrée:</span> {{
                        $caisse->numero_entre ?? 'N/A' }}</p>
                    <p class="compact-text text-gray-800"><span class="font-medium">Date de l'examen:</span> {{
                        $caisse->date_examen ? $caisse->date_examen->format('d/m/Y') : 'N/A' }}</p>
                    <p class="compact-text text-gray-800"><span class="font-medium">Type d'examen:</span> {{
                        $caisse->examen->nom ?? 'N/A' }}</p>
                    <p class="compact-text text-gray-800"><span class="font-medium">Service:</span> {{
                        $caisse->service->nom ?? 'N/A' }}</p>
                </div>

                <div class="bg-gray-100 compact-box rounded-lg">
                    <p class="compact-text font-bold text-center text-gray-900">Total</p>
                    <p class="compact-total font-bold text-blue-700 text-center">{{ number_format($caisse->total, 2) }}
                        MRU</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 compact-grid compact-section">
                <!-- Informations patient -->
                <div class="border border-gray-200 rounded-lg compact-box">
                    <h3 class="font-semibold border-b pb-2 text-gray-900">Informations patient</h3>
                    <p class="compact-text text-gray-800"><span class="font-medium">Nom:</span> {{ $caisse->patient->nom
                        ?? 'N/A' }} {{ $caisse->patient->prenom ?? '' }}</p>
                    <p class="compact-text text-gray-800"><span class="font-medium">Téléphone:</span> {{
                        $caisse->patient->telephone ?? 'N/A' }}</p>
                    <p class="compact-text text-gray-800"><span class="font-medium">Adresse:</span> {{
                        $caisse->patient->adresse ?? 'N/A' }}</p>
                </div>

                <!-- Informations médicales -->
                <div class="border border-gray-200 rounded-lg compact-box">
                    <h3 class="font-semibold border-b pb-2 text-gray-900">Personnel médical</h3>
                    <p class="compact-text text-gray-800"><span class="font-medium">Médecin:</span> {{
                        $caisse->medecin->nom ?? 'N/A' }}</p>
                    @if($caisse->prescripteur)
                    <p class="compact-text text-gray-800"><span class="font-medium">Prescripteur:</span> {{
                        $caisse->prescripteur->nom }}</p>
                    @endif
                    <p class="compact-text text-gray-800"><span class="font-medium">Caissier:</span> {{
                        $caisse->nom_caissier }}</p>
                </div>
            </div>

            @if($caisse->paiements || $caisse->couverture !== null)
            <div class="bg-blue-50 compact-box rounded-lg compact-section">
                <h3 class="compact-title font-semibold text-gray-900">Informations de paiement</h3>
                @if($caisse->paiements)
                <p class="compact-text text-gray-800"><strong>Mode de paiement :</strong> {{
                    ucfirst($caisse->paiements->type ?? '—') }} ({{ number_format($caisse->paiements->montant ?? 0, 0,
                    ',', ' ') }} MRU)</p>
                @endif
                @if($caisse->couverture !== null)
                <p class="compact-text text-gray-800"><strong>Couverture assurance :</strong> {{ $caisse->couverture }}%
                </p>
                @endif
            </div>
            @endif

            <!-- Notes et observations -->
            <div class="bg-blue-50 compact-box rounded-lg compact-section">
                <h3 class="compact-title font-semibold text-gray-900">Notes et observations</h3>
                <p class="compact-text text-gray-800">{{ $caisse->observation ?? 'Aucune observation.' }}</p>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="bg-gray-100 compact-footer text-center text-gray-600">
            <p>{{ config('app.name', 'Clinique Médicale') }} - Tél: +222 XXX XXX XXX</p>
            <p>Adresse: Nouakchott, Mauritanie</p>
        </div>
    </div>
</body>

</html>
